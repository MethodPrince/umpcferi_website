jQuery(document).ready(function($) {
    function loadMessages() {
        $.post(umpcferiAdmin.ajax_url, { action: 'umpcferi_fetch_messages', _ajax_nonce: umpcferiAdmin.nonce }, function(response) {
            if (response.success) {
                var html = '<table class="widefat fixed striped"><thead><tr><th>User</th><th>Message</th><th>Status</th><th>Date</th></tr></thead><tbody>';
                response.data.forEach(function(msg) {
                    html += '<tr><td>' + msg.user_login + '</td><td>' + msg.message + '</td><td>' + msg.status + '</td><td>' + msg.created_at + '</td></tr>';
                });
                html += '</tbody></table>';
                $('#umpcferi-messages').html(html);
            } else {
                $('#umpcferi-messages').html('<p>' + response.data + '</p>');
            }
        });
    }
    loadMessages();
});
