jQuery(document).ready(function($) {
    var messageForm = $('#umpcferi-message-form');
    if (messageForm.length) {
        messageForm.on('submit', function(e) {
            var message = $('#umpcferi_message').val().trim();
            if (!message) {
                e.preventDefault();
                alert('Please enter a message.');
            }
        });
    }
});
