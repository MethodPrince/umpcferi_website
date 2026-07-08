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

    function parseProgramData(element) {
        var data = $(element).data('program');
        if (typeof data === 'string') {
            try {
                data = JSON.parse(data);
            } catch (e) {
                data = {};
            }
        }
        return data || {};
    }

    function getSocialPlatformDetails(platform) {
        var key = platform.toLowerCase();
        switch (key) {
            case 'facebook':
            case 'eventfacebook':
                return { label: 'Facebook', icon: 'fab fa-facebook-f', className: 'facebook' };
            case 'twitter':
                return { label: 'Twitter', icon: 'fab fa-twitter', className: 'twitter' };
            case 'instagram':
            case 'cferiinstagram':
            case 'eventinstagram':
                return { label: 'Instagram', icon: 'fab fa-instagram', className: 'instagram' };
            case 'linkedin':
            case 'sweeplinkedin':
                return { label: 'LinkedIn', icon: 'fab fa-linkedin-in', className: 'linkedin' };
            case 'youtube':
                return { label: 'YouTube', icon: 'fab fa-youtube', className: 'youtube' };
            case 'tiktok':
                return { label: 'TikTok', icon: 'fab fa-tiktok', className: 'tiktok' };
            default:
                return { label: platform.charAt(0).toUpperCase() + platform.slice(1), icon: 'fas fa-link', className: 'social' };
        }
    }

    function openProgramModal(data) {
        var modal = $('.program-modal');
        if (!modal.length) {
            return;
        }

        modal.find('#program-modal-title').text(data.title || 'Program details');
        modal.find('.modal-subtitle').text(data.description || '');

        var details = '';
        if (data.fullDetails) {
            details += '<p>' + data.fullDetails + '</p>';
        }
        if (data.fullDescription) {
            details += '<div class="modal-section"><h3>About</h3><p style="white-space: pre-line;">' + data.fullDescription + '</p></div>';
        }
        if (data.goals && data.goals.length) {
            details += '<div class="modal-section"><h3>Program Goals</h3><ul class="modal-list">';
            data.goals.forEach(function(item) {
                details += '<li>' + item + '</li>';
            });
            details += '</ul></div>';
        }
        if (data.benefits && data.benefits.length) {
            details += '<div class="modal-section"><h3>Benefits</h3><ul class="modal-list">';
            data.benefits.forEach(function(item) {
                details += '<li>' + item + '</li>';
            });
            details += '</ul></div>';
        }
        if (data.eligibility && data.eligibility.length) {
            details += '<div class="modal-section"><h3>Eligibility</h3><ul class="modal-list">';
            data.eligibility.forEach(function(item) {
                details += '<li>' + item + '</li>';
            });
            details += '</ul></div>';
        }
        if (data.partners && data.partners.length) {
            details += '<div class="modal-section"><h3>Partners</h3><p>' + data.partners.join(', ') + '</p></div>';
        }

        if (data.subPrograms && data.subPrograms.length) {
            details += '<div class="modal-section"><h3>Sub Programs</h3>';
            data.subPrograms.forEach(function(sub) {
                details += '<div class="modal-subprogram"><h4>' + (sub.name || 'Sub program') + '</h4>';
                if (sub.description) {
                    details += '<p>' + sub.description + '</p>';
                }
                if (sub.fullDetails) {
                    details += '<p>' + sub.fullDetails + '</p>';
                }
                if (sub.fullDescription) {
                    details += '<p style="white-space: pre-line;">' + sub.fullDescription + '</p>';
                }
                if (sub.benefits && sub.benefits.length) {
                    details += '<div class="modal-section"><h4>Benefits</h4><ul class="modal-list">';
                    sub.benefits.forEach(function(item) {
                        details += '<li>' + item + '</li>';
                    });
                    details += '</ul></div>';
                }
                if (sub.eligibility && sub.eligibility.length) {
                    details += '<div class="modal-section"><h4>Eligibility</h4><ul class="modal-list">';
                    sub.eligibility.forEach(function(item) {
                        details += '<li>' + item + '</li>';
                    });
                    details += '</ul></div>';
                }
                if (sub.contact) {
                    details += '<p><strong>Contact:</strong> ' + sub.contact + '</p>';
                }
                if (sub.externalLink) {
                    details += '<p><a class="external-link-btn" href="' + sub.externalLink + '" target="_blank" rel="noopener noreferrer">Visit website</a></p>';
                }
                details += '</div>';
            });
            details += '</div>';
        }

        if (details === '') {
            details = '<p>No further details available.</p>';
        }

        modal.find('.modal-details').html(details);

        var contactHtml = '';
        if (data.contactInfo) {
            var hasContactDetails = false;
            var sectionHtml = '<div class="modal-section"><h3>Contact Information</h3>';
            if (data.contactInfo.website) {
                sectionHtml += '<p><strong>Website:</strong> <a href="' + data.contactInfo.website + '" target="_blank" rel="noopener noreferrer">' + data.contactInfo.website + '</a></p>';
                hasContactDetails = true;
            }
            if (data.contactInfo.contact) {
                sectionHtml += '<p><strong>Contact page:</strong> <a href="' + data.contactInfo.contact + '" target="_blank" rel="noopener noreferrer">' + data.contactInfo.contact + '</a></p>';
                hasContactDetails = true;
            }
            if (data.contactInfo.social) {
                var socialButtons = '';
                Object.keys(data.contactInfo.social).forEach(function(platform) {
                    var link = data.contactInfo.social[platform];
                    if (link) {
                        var details = getSocialPlatformDetails(platform);
                        socialButtons += '<a class="social-link-prog ' + details.className + '" href="' + link + '" target="_blank" rel="noopener noreferrer" title="' + details.label + '"><i class="' + details.icon + '"></i><span>' + details.label + '</span></a>';
                    }
                });
                if (socialButtons) {
                    sectionHtml += '<div class="program-social-links">' + socialButtons + '</div>';
                    hasContactDetails = true;
                }
            }
            sectionHtml += '</div>';
            if (hasContactDetails) {
                contactHtml += sectionHtml;
            }
        }
        var socialData = data.social || data.social_links || (data.contactInfo ? data.contactInfo.social : {}) || {};
        var socialButtons = '';
        Object.keys(socialData).forEach(function(platform) {
            var link = socialData[platform];
            if (link) {
                var details = getSocialPlatformDetails(platform);
                socialButtons += '<a class="social-link-prog ' + details.className + '" href="' + link + '" target="_blank" rel="noopener noreferrer" title="' + details.label + '"><i class="' + details.icon + '"></i><span>' + details.label + '</span></a>';
            }
        });
        if (socialButtons) {
            contactHtml += '<div class="modal-section"><h3>Social</h3><div class="program-social-links">' + socialButtons + '</div></div>';
        }
        if (data.contact && !contactHtml) {
            contactHtml = '<p><strong>Contact:</strong> ' + data.contact + '</p>';
        }

        modal.find('.modal-contact').html(contactHtml);
        modal.find('.modal-extra').html(data.externalLink ? '<p><a class="external-link-btn" href="' + data.externalLink + '" target="_blank" rel="noopener noreferrer">Visit website</a></p>' : '');

        modal.addClass('active');
        modal.attr('aria-hidden', 'false');
        $('body').addClass('program-modal-open');
    }

    function closeProgramModal() {
        var modal = $('.program-modal');
        if (!modal.length) {
            return;
        }
        modal.removeClass('active');
        modal.attr('aria-hidden', 'true');
        $('body').removeClass('program-modal-open');
    }

    // Ensure button clicks always open the modal, and card clicks do too
    $(document).on('click', '.learn-more-btn', function(event) {
        event.preventDefault();
        event.stopPropagation();
        var card = $(this).closest('.category-program-card');
        var data = parseProgramData(card);
        openProgramModal(data);
    });

    // Clicking the card (but not its inner button) also opens the modal
    $(document).on('click', '.category-program-card', function(event) {
        // If the click originated from the learn-more button, ignore here
        if ($(event.target).closest('.learn-more-btn').length) {
            return;
        }
        event.preventDefault();
        var data = parseProgramData(this);
        openProgramModal(data);
    });

    $(document).on('click', '.program-modal-overlay', function() {
        closeProgramModal();
    });

    $(document).on('click', '.quick-nav-btn', function(event) {
        var target = $(this).data('target');
        if (target) {
            var el = document.getElementById(target);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });

    $(document).on('click', '.program-modal', function(event) {
        if ($(event.target).is('.program-modal')) {
            closeProgramModal();
        }
    });

    $(document).on('click', '.modal-close', function() {
        closeProgramModal();
    });

    $(document).on('keyup', function(event) {
        if (event.key === 'Escape') {
            closeProgramModal();
        }
    });
});
