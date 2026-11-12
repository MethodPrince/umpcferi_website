const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');

function createMockElement(id) {
  return {
    id,
    children: [],
    attributes: {},
    value: '',
    textContent: '',
    innerText: '',
    className: '',
    type: 'button',
    disabled: false,
    style: {},
    scrollHeight: 0,
    scrollTop: 0,
    parentElement: null,
    appendChild(child) {
      this.children.push(child);
      return child;
    },
    setAttribute(name, value) {
      this.attributes[name] = value;
    },
    getAttribute(name) {
      return this.attributes[name];
    },
    focus() {},
    querySelector(selector) {
      if (this.querySelectors && selector in this.querySelectors) {
        return this.querySelectors[selector];
      }
      if (selector === '.faq-quick-link') {
        return null;
      }
      if (selector === '.umpcferi-chatbot-preset') {
        return null;
      }
      return null;
    },
    querySelectorAll(selector) {
      return [];
    },
    addEventListener(eventName, handler) {
      this.listeners = this.listeners || {};
      this.listeners[eventName] = handler;
    },
    dispatchEvent(eventName) {
      if (this.listeners && this.listeners[eventName]) {
        this.listeners[eventName]({ preventDefault() {}, stopPropagation() {} });
      }
    },
    click() {
      if (this.listeners && this.listeners.click) {
        this.listeners.click({ preventDefault() {}, stopPropagation() {} });
      }
    },
    append(text) {
      this.textContent += text;
    },
    tagName: 'DIV'
  };
}

function createMockDocument() {
  const elements = {};
  const document = {
    body: createMockElement('body'),
    addEventListener(eventName, handler) {
      if (eventName === 'DOMContentLoaded') {
        this.domContentLoadedHandler = handler;
      }
    },
    getElementById(id) {
      if (!elements[id]) {
        elements[id] = createMockElement(id);
      }
      return elements[id];
    },
    createElement(tagName) {
      const element = createMockElement(tagName.toLowerCase());
      element.tagName = tagName.toUpperCase();
      return element;
    }
  };

  return { document, elements };
}

function loadChatbotScript(document, windowObject) {
  const scriptPath = path.join(__dirname, '..', 'assets', 'js', 'chatbot.js');
  const source = fs.readFileSync(scriptPath, 'utf8');
  const context = {
    window: windowObject,
    document: document,
    console,
    setTimeout: (fn) => fn(),
    clearTimeout: () => {},
    globalThis: null
  };
  context.globalThis = context;
  vm.createContext(context);
  vm.runInContext(source, context);
  return context;
}

const { document, elements } = createMockDocument();
const windowObject = {
  umpcferiChatbotData: {
    presetQas: [{ q: 'What is this?', a: 'A test answer.' }],
    contactEmail: 'test@example.com'
  }
};

const toggle = document.getElementById('umpcferi-chatbot-toggle');
const panel = document.getElementById('umpcferi-chatbot-panel');
const historyEl = document.getElementById('umpcferi-chatbot-history');
const bodyEl = document.getElementById('umpcferi-chatbot-body');
const presetsEl = document.getElementById('umpcferi-chatbot-presets');
const introEl = document.createElement('p');
const quickLinksEl = document.createElement('div');
const inputEl = document.getElementById('umpcferi-chatbot-input');
const sendEl = document.getElementById('umpcferi-chatbot-send');
const closeEl = document.getElementById('umpcferi-chatbot-close');

panel.setAttribute('aria-hidden', 'true');
panel.style.display = 'none';
historyEl.parentElement = bodyEl;
presetsEl.querySelectors = {
  '.chatbot-intro': introEl,
  '.faq-quick-links': quickLinksEl
};
bodyEl.scrollHeight = 120;
historyEl.scrollHeight = 120;

elements.toggle = toggle;
elements.panel = panel;
elements.history = historyEl;
elements.presets = presetsEl;
elements.input = inputEl;
elements.send = sendEl;
elements.close = closeEl;

loadChatbotScript(document, windowObject);
if (typeof document.domContentLoadedHandler !== 'function') {
  throw new Error('DOMContentLoaded handler was not registered');
}
document.domContentLoadedHandler();

inputEl.value = 'What is this?';
sendEl.click();

const historyText = historyEl.children
  .map((child) => (child.children && child.children[0] ? child.children[0].textContent : child.textContent))
  .join('|');
assert.ok(historyText.includes('A test answer.'), 'Expected the bot to reply to user input');
assert.strictEqual(historyEl.scrollTop, 120, 'Expected the chat history to scroll to the latest message');
assert.strictEqual(bodyEl.scrollTop, 120, 'Expected the chat body to scroll to the latest message');
assert.notStrictEqual(presetsEl.style.display, 'none', 'Expected the question dropdown to remain available');
assert.strictEqual(presetsEl.open, false, 'Expected the question dropdown to close after sending a question');
assert.strictEqual(introEl.style.display, 'none', 'Expected the introductory text to hide after the first question');
assert.strictEqual(quickLinksEl.children.length, 1, 'Expected FAQ choices to be available in the dropdown');

quickLinksEl.children[0].click();
const selectedQuestionHistory = historyEl.children
  .map((child) => (child.children && child.children[0] ? child.children[0].textContent : child.textContent))
  .join('|');
assert.ok(selectedQuestionHistory.includes('A test answer.'), 'Expected the dropdown selection to receive an answer');
assert.strictEqual(historyEl.children.length, 4, 'Expected selecting a FAQ to add a question and answer');
console.log('chatbot regression test passed');
