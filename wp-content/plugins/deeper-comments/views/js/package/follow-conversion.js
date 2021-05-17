/**
 * Created by Webnus on 4 / 4 / 2020.
 */
(function ($) {
    $(document).on('click', 'a.dpr-discu-follow', function () {
        var $comment_id = $(this).data('comment-id');
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: dpr.adminajax,
            data: {
                action: 'dpr_follow_comment',
                comment_id: $comment_id
            },
            success: function (response) {
                $this.before(response);
                $this.remove();
            }
        });
        return false;
    });
    $(document).on('click', 'a.dpr-discu-unfollow', function () {
        var $comment_id = $(this).data('comment-id');
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: dpr.adminajax,
            data: {
                action: 'dpr_unfollow_comment',
                comment_id: $comment_id
            },
            success: function (response) {
                $this.before(response);
                $this.remove();
            }
        });
        return false;
    })

    $(document).on('mouseover', '[data-hover-class]', function () {
        if ($(this).attr('data-hover-class')) {
            $(this).addClass($(this).attr('data-hover-class'));
        }
    });
    $(document).on('mouseleave', '[data-hover-class]', function () {
        if ($(this).attr('data-hover-class')) {
            $(this).removeClass($(this).attr('data-hover-class'));
        }
    });

    // DPR Scripts
    var DprScripts = {
        notification_listener: {
            run: function () {
                this.listen();
                this.functionality_setup();
            },
            listen: function () {
                if (typeof (dpr) == 'undefined') {
                    return;
                }
                if (dpr.notification_listener != "true") {
                    return;
                }
                jQuery.ajax({
                    type: "POST",
                    url: dpr.adminajax,
                    data: {
                        action: 'dpr_user_comments_listener',
                    },
                    dataType: "json",
                    success: function (response) {
                        DprScripts.notification_listener.convert_to_html(response);
                        setTimeout(DprScripts.notification_listener.listen, 10000);
                    }
                });
            },
            convert_to_html: function (response) {

                if (!response || typeof response.comments == 'undefined') {
                    return;
                }

                if (!jQuery('.dpr-comments-notification-wrap').length) {
                    jQuery('body').append('<div class="dpr-comments-notification-wrap"></div>')
                }

                var html = '';
                if (typeof response.comment_count != 'undefined') {
                    html = '<div class="dpr-notification-title">And "' + response.comment_count + '" more.</div>'
                }
                jQuery.each(response.comments, function (index, comment) {
                    html += '<div class="dpr-admin-notification">' + '<span class="close">×</span>' + '<a href="' + comment.url + '" target="_blank"><h4>' + comment.author + '</h4></a>' + '<div class="dpr-cm-content">' + comment.excerpt + '</div>' + '<div class="dpr-cm-data">' + '<span>' + comment.date + '</span></div></div>';
                })

                if (jQuery('.dpr-comments-notification-wrap .dpr-admin-notification').length + response.comments.length < 11 && jQuery('.dpr-comments-notification-wrap .dpr-admin-notification').length) {
                    jQuery('.dpr-comments-notification-wrap .dpr-admin-notification').first().before(html);
                } else {
                    jQuery('.dpr-comments-notification-wrap').html(html);
                }
            },
            functionality_setup: function () {
                $(document).on('click', '.dpr-comments-notification-wrap .dpr-admin-notification .close', function () {
                    if (jQuery('.dpr-comments-notification-wrap .dpr-admin-notification').length < 2 && jQuery('.dpr-comments-notification-wrap .dpr-notification-title').length) {
                        jQuery('.dpr-comments-notification-wrap .dpr-notification-title').remove();
                    }
                    $(this).parent().slideUp();
                    $(this).parent().remove();
                });
            }
        }
    };

    /**
     *  When Document is ready
     */
    $(document).ready(function () {
        DprScripts.notification_listener.run();
    });

}(jQuery));