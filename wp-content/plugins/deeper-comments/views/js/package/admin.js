/**
 * Created by Webnus01 on 5/11/2020.
 */
(function ($) {

    // Admin Scripts
    var DprAdminScripts = {
        notification_listener: {
            run: function () {
                this.listen();
                this.functionality_setup();
            },
            listen: function () {
                console.log('Deeper Comment Listener');
                $.ajax({
                    type: "POST",
                    url: dpr_admin.adminajax,
                    data: {
                        action: 'dpr_comments_listener',
                        security: dpr_admin.security,
                    },
                    dataType: "json",
                    success: function (response) {
                        DprAdminScripts.notification_listener.convert_to_html(response);
                        setTimeout(DprAdminScripts.notification_listener.listen, 30000);
                    }
                });
            },
            convert_to_html: function (response) {
                if (!response || typeof response.comments == 'undefined') {
                    return;
                }

                if (!jQuery('.dpr-comments-admin-notification-wrap').length) {
                    jQuery('body').append('<div class="dpr-comments-admin-notification-wrap"></div>')
                }

                var html = '';
                if (typeof response.comment_count != 'undefined') {
                    html = '<div class="dpr-notification-title">And "' + response.comment_count + '" more.</div>'
                }
                jQuery.each(response.comments, function (index, comment) {
                    html += '<div class="dpr-admin-notification">' + '<span class="close">×</span>' + '<a href="' + comment.url + '" target="_blank"><h4>' + comment.author + '</h4></a>' + '<div class="dpr-cm-content">' + comment.excerpt + '</div>' + '<div class="dpr-cm-data">' + '<span>' + comment.date + '</span>' + '<span class="ip">' + comment.author_IP + '</span>' + '</div>' + '</div>';
                })

                if (jQuery('.dpr-comments-admin-notification-wrap .dpr-admin-notification').length + response.comments.length < 11 && jQuery('.dpr-comments-admin-notification-wrap .dpr-admin-notification').length) {
                    jQuery('.dpr-comments-admin-notification-wrap .dpr-admin-notification').first().before(html);
                } else {
                    jQuery('.dpr-comments-admin-notification-wrap').html(html);
                }
            },
            functionality_setup: function () {
                $(document).on('click', '.dpr-comments-admin-notification-wrap .dpr-admin-notification .close', function () {
                    if (jQuery('.dpr-comments-admin-notification-wrap .dpr-admin-notification').length < 2 && jQuery('.dpr-comments-admin-notification-wrap .dpr-notification-title').length) {
                        jQuery('.dpr-comments-admin-notification-wrap .dpr-notification-title').remove();
                    }
                    $(this).parent().slideUp();
                    $(this).parent().remove();
                });
            }
        }
    };

    /**
     *  When Document is Ready
     */
    $(document).ready(function () {
        DprAdminScripts.notification_listener.run();
    });
}(jQuery));