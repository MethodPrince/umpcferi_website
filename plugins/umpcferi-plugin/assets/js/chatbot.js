document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('umpcferi-chatbot-toggle');
    var panel = document.getElementById('umpcferi-chatbot-panel');
    var historyEl = document.getElementById('umpcferi-chatbot-history');
    var presetsEl = document.getElementById('umpcferi-chatbot-presets');
    var introEl = presetsEl ? presetsEl.querySelector('.chatbot-intro') : null;
    var inputEl = document.getElementById('umpcferi-chatbot-input');
    var bodyEl = historyEl && historyEl.parentElement ? historyEl.parentElement : null;
    var sendEl = document.getElementById('umpcferi-chatbot-send');
    var closeEl = document.getElementById('umpcferi-chatbot-close');

    if (!toggle || !panel) {
        return;
    }

    var faqData = [];
    var localizedData = window.umpcferiChatbotData || {};
    if (localizedData.presetQas && localizedData.presetQas.length) {
        faqData = localizedData.presetQas;
    } else {
        var faqDataElement = document.getElementById('umpcferi-faq-data');
        if (faqDataElement) {
            try {
                faqData = JSON.parse(faqDataElement.textContent || faqDataElement.innerText || '[]');
            } catch (error) {
                faqData = [];
            }
        }
    }

    function scrollToLatestMessage() {
        if (historyEl) {
            historyEl.scrollTop = historyEl.scrollHeight;
        }
        if (bodyEl) {
            bodyEl.scrollTop = bodyEl.scrollHeight;
        }
    }

    function appendMessage(author, text) {
        var item = document.createElement('div');
        item.className = 'umpcferi-chatbot-item umpcferi-chatbot-' + (author === 'bot' ? 'bot' : 'user');

        if (author !== 'bot' && presetsEl) {
            presetsEl.open = false;
            if (introEl) {
                introEl.style.display = 'none';
            }
        }

        var textEl = document.createElement('div');
        textEl.className = 'umpcferi-chatbot-text';
        textEl.textContent = text;
        item.appendChild(textEl);

        historyEl.appendChild(item);
        setTimeout(scrollToLatestMessage, 0);
    }

    function openPanel() {
        panel.setAttribute('aria-hidden', 'false');
        panel.style.display = 'block';
        if (inputEl) {
            setTimeout(function () {
                inputEl.focus();
            }, 150);
        }
    }

    function closePanel() {
        panel.setAttribute('aria-hidden', 'true');
        panel.style.display = 'none';
    }

    function getGenericResponse() {
        var contactEmail = localizedData.contactEmail || 'cferi_bdo@ump.ac.za';
        return 'For specific help, please contact ' + contactEmail + ' or visit the CFERI contact page.';
    }

    function findAnswer(query) {
        var normalizedQuery = (query || '').toLowerCase();
        if (!normalizedQuery) {
            return null;
        }

        for (var i = 0; i < faqData.length; i++) {
            var faq = faqData[i];
            if (!faq) {
                continue;
            }

            var question = (faq.question || faq.q || '').toLowerCase();
            var answer = faq.answer || faq.a || '';
            if (!question || !answer) {
                continue;
            }

            var score = 0;
            var keywords = ['what', 'how', 'does', 'provide', 'offer', 'center', 'cferi', 'funding', 'services', 'participate', 'society', 'entrepreneurship'];
            keywords.forEach(function (keyword) {
                if (normalizedQuery.indexOf(keyword) !== -1 && question.indexOf(keyword) !== -1) {
                    score++;
                }
            });

            if (score >= 1 || normalizedQuery.indexOf(question) !== -1 || question.indexOf(normalizedQuery) !== -1) {
                return answer;
            }
        }

        return null;
    }

    function sendMessage() {
        if (!inputEl) {
            return;
        }

        var message = inputEl.value.trim();
        if (!message) {
            return;
        }

        appendMessage('user', message);
        inputEl.value = '';
        if (sendEl) {
            sendEl.disabled = true;
        }

        var response = findAnswer(message) || getGenericResponse();
        setTimeout(function () {
            appendMessage('bot', response);
            if (sendEl) {
                sendEl.disabled = false;
            }
            inputEl.focus();
        }, 400);
    }

    toggle.addEventListener('click', function () {
        var hidden = panel.getAttribute('aria-hidden') === 'true';
        if (hidden) {
            openPanel();
        } else {
            closePanel();
        }
    });

    if (closeEl) {
        closeEl.addEventListener('click', closePanel);
    }

    if (sendEl) {
        sendEl.addEventListener('click', sendMessage);
    }

    if (inputEl) {
        inputEl.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        });
    }

    if (presetsEl) {
        var quickLinks = presetsEl.querySelectorAll('.faq-quick-link');
        quickLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                var index = link.getAttribute('data-faq-index');
                if (index === null || !faqData[index]) {
                    return;
                }
                var item = faqData[index];
                appendMessage('user', item.question || item.q || '');
                setTimeout(function () {
                    appendMessage('bot', item.answer || item.a || '');
                }, 300);
            });
        });
    }

    if (presetsEl && faqData.length > 0 && !presetsEl.querySelector('.faq-quick-link')) {
        var quickLinksEl = presetsEl.querySelector('.faq-quick-links');
        if (!quickLinksEl) {
            quickLinksEl = document.createElement('div');
            quickLinksEl.className = 'faq-quick-links';
            presetsEl.appendChild(quickLinksEl);
        }

        faqData.forEach(function (faq) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'umpcferi-chatbot-preset';
            button.textContent = faq.question || faq.q || '';
            button.addEventListener('click', function () {
                appendMessage('user', button.textContent);
                setTimeout(function () {
                    appendMessage('bot', faq.answer || faq.a || '');
                }, 300);
            });
            quickLinksEl.appendChild(button);
        });
    }

    closePanel();
});
