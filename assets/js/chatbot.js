document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('umpcferi-chatbot-toggle');
    var panel = document.getElementById('umpcferi-chatbot-panel');
    var historyEl = document.getElementById('umpcferi-chatbot-history');
    var presetsEl = document.getElementById('umpcferi-chatbot-presets');

    if (!toggle || !panel) return;

    function appendMessage(author, text) {
        var item = document.createElement('div');
        item.className = 'umpcferi-chatbot-item umpcferi-chatbot-' + (author === 'bot' ? 'bot' : 'user');
        item.innerHTML = '<div class="umpcferi-chatbot-text">' + text + '</div>';
        historyEl.appendChild(item);
        historyEl.scrollTop = historyEl.scrollHeight;
    }

    // Toggle panel
    toggle.addEventListener('click', function () {
        var hidden = panel.getAttribute('aria-hidden') === 'true';
        panel.setAttribute('aria-hidden', hidden ? 'false' : 'true');
        panel.style.display = hidden ? 'block' : 'none';
        if (hidden) {
            // focus first preset if any
            var first = presetsEl.querySelector('button');
            if (first) first.focus();
        }
    });

    // Load presets from localized data
    var data = window.umpcferiChatbotData || { presetQas: [] };
    (data.presetQas || []).forEach(function (qa, idx) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'umpcferi-chatbot-preset';
        btn.textContent = qa.q;
        btn.addEventListener('click', function () {
            appendMessage('user', qa.q);
            setTimeout(function () {
                appendMessage('bot', qa.a);
            }, 300);
        });
        presetsEl.appendChild(btn);
    });

    // If no presets, show a fallback
    if ((data.presetQas || []).length === 0) {
        var p = document.createElement('p');
        p.className = 'umpcferi-chatbot-no-presets';
        p.textContent = 'No preset questions available.';
        presetsEl.appendChild(p);
    }
});
