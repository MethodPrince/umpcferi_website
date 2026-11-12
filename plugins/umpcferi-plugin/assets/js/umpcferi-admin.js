jQuery(document).ready(function($) {
    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, function(character) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[character];
        });
    }

    var programImageFrame;
    var demoImagePreviews = $('#umpcferi-program-demo-image-previews');
    var demoImageIds = $('#umpcferi_program_demo_images');

    function updateDemoImageIds() {
        var ids = [];
        demoImagePreviews.find('.umpcferi-program-demo-image').each(function() {
            ids.push($(this).data('attachment-id'));
        });
        demoImageIds.val(ids.join(','));
    }

    $('#umpcferi-add-program-demo-images').on('click', function(event) {
        event.preventDefault();
        if (typeof wp === 'undefined' || !wp.media) {
            window.alert('The media library is unavailable. Please reload the page and try again.');
            return;
        }

        if (!programImageFrame) {
            programImageFrame = wp.media({
                title: 'Select program demonstration images',
                button: { text: 'Use selected images' },
                library: { type: 'image' },
                multiple: true
            });
            programImageFrame.on('select', function() {
                var selection = programImageFrame.state().get('selection');
                selection.each(function(attachment) {
                    var image = attachment.toJSON();
                    var imageId = Number(image.id);
                    if (!imageId || demoImagePreviews.find('[data-attachment-id="' + imageId + '"]').length) {
                        return;
                    }
                    var imageUrl = image.sizes && image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url;
                    demoImagePreviews.append(
                        '<span class="umpcferi-program-demo-image" data-attachment-id="' + imageId + '">' +
                            '<img class="umpcferi-program-demo-preview" src="' + escapeHtml(imageUrl) + '" alt="">' +
                            '<button type="button" class="button-link-delete umpcferi-remove-program-demo-image">Remove</button>' +
                        '</span>'
                    );
                });
                updateDemoImageIds();
            });
        }
        programImageFrame.open();
    });

    demoImagePreviews.on('click', '.umpcferi-remove-program-demo-image', function() {
        $(this).closest('.umpcferi-program-demo-image').remove();
        updateDemoImageIds();
    });

    function loadMessages() {
        if (typeof umpcferiAdmin === 'undefined') {
            return;
        }
        $.post(umpcferiAdmin.ajax_url, { action: 'umpcferi_fetch_messages', _ajax_nonce: umpcferiAdmin.nonce }, function(response) {
            if (response.success) {
                var groups = { unanswered: [], answered: [], deleted: [] };
                response.data.forEach(function(msg) {
                    var status = msg.status === 'deleted' ? 'deleted' : (msg.status === 'answered' ? 'answered' : 'unanswered');
                    groups[status].push(msg);
                });

                function renderGroup(title, status, messages) {
                    var collapsible = status !== 'unanswered';
                    var html = collapsible
                        ? '<details class="umpcferi-message-group umpcferi-message-group-' + status + '"><summary>' + title + ' (' + messages.length + ')</summary>'
                        : '<section class="umpcferi-message-group umpcferi-message-group-' + status + '"><h3>' + title + ' <span>(' + messages.length + ')</span></h3>';
                    if (!messages.length) {
                        return html + '<p>No ' + title.toLowerCase() + ' messages.</p>' + (collapsible ? '</details>' : '</section>');
                    }
                    html += '<table class="widefat fixed striped"><thead><tr><th>Name</th><th>Email</th><th>Message</th><th>Date</th><th>Actions</th></tr></thead><tbody>';
                    messages.forEach(function(msg) {
                        var senderName = msg.sender_name || msg.user_login || 'Website visitor';
                        var senderEmail = msg.sender_email || '';
                        var emailCell = senderEmail
                            ? '<a href="mailto:' + encodeURIComponent(senderEmail) + '">' + escapeHtml(senderEmail) + '</a>'
                            : '';
                        var action = status === 'deleted'
                            ? '<button type="button" class="button umpcferi-restore-message" data-message-id="' + Number(msg.id) + '">Restore</button>'
                            : '<button type="button" class="button umpcferi-toggle-message-status" data-message-id="' + Number(msg.id) + '" data-status="' + (status === 'answered' ? 'unanswered' : 'answered') + '">' + (status === 'answered' ? 'Mark unanswered' : 'Mark answered') + '</button> <button type="button" class="button umpcferi-delete-message" data-message-id="' + Number(msg.id) + '">Delete</button>';
                        html += '<tr class="' + (status === 'answered' ? 'umpcferi-message-answered' : '') + '"><td>' + escapeHtml(senderName) + '</td><td>' + emailCell + '</td><td>' + escapeHtml(msg.message) + '</td><td>' + escapeHtml(msg.created_at) + '</td><td>' + action + '</td></tr>';
                    });
                    return html + '</tbody></table>' + (collapsible ? '</details>' : '</section>');
                }

                var html = renderGroup('Unanswered', 'unanswered', groups.unanswered);
                html += renderGroup('Answered', 'answered', groups.answered);
                html += renderGroup('Deleted', 'deleted', groups.deleted);
                $('#umpcferi-messages').html(html);
            } else {
                $('#umpcferi-messages').html('<p>' + response.data + '</p>');
            }
        });
    }

    $('#umpcferi-messages').on('click', '.umpcferi-toggle-message-status, .umpcferi-delete-message, .umpcferi-restore-message', function() {
        var button = $(this);
        var action;
        var data = {
            _ajax_nonce: umpcferiAdmin.nonce,
            message_id: Number(button.data('message-id'))
        };

        if (button.hasClass('umpcferi-toggle-message-status')) {
            action = 'umpcferi_update_message_status';
            data.status = button.data('status');
        } else if (button.hasClass('umpcferi-delete-message')) {
            action = 'umpcferi_delete_message';
        } else {
            action = 'umpcferi_restore_message';
        }

        button.prop('disabled', true);
        data.action = action;
        $.post(umpcferiAdmin.ajax_url, data, function(response) {
            if (response.success) {
                loadMessages();
            } else {
                button.prop('disabled', false);
                window.alert(response.data || 'The message could not be updated.');
            }
        }).fail(function() {
            button.prop('disabled', false);
            window.alert('The message could not be updated.');
        });
    });

    loadMessages();
});
