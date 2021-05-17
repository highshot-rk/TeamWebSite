/**
 * Created by Webnus01 on 5 / 11 / 2020.
 */
(function ($) {
    /**
     *  Start Shake
     */
    jQuery.fn.shake = function (intShakes, intDistance, intDuration) {
        this.each(function () {
            $(this).css({
                position: "relative"
            });
            for (var x = 1; x <= intShakes; x++) {
                $(this).animate({
                    left: (intDistance * -1)
                }, (((intDuration / intShakes) / 4))).animate({
                    left: intDistance
                }, ((intDuration / intShakes) / 2)).animate({
                    left: 0
                }, (((intDuration / intShakes) / 4)));
            }
        });
        return this;
    };
    /**
     *  End Shake
     */

    (function ($) {
        $.fn.drags = function (opt) {

            opt = $.extend({
                handle: "",
                cursor: "move"
            }, opt);

            if (opt.handle === "") {
                var $el = this;
            } else {
                var $el = this.find(opt.handle);
            }

            return $el.css('cursor', opt.cursor).on("mousedown", function (e) {
                if (opt.handle === "") {
                    var $drag = $(this).addClass('draggable');
                } else {
                    var $drag = $(this).addClass('active-handle').parent().addClass('draggable');
                }
                var z_idx = $drag.css('z-index'),
                    drg_h = $drag.outerHeight(),
                    drg_w = $drag.outerWidth(),
                    pos_y = $drag.offset().top + drg_h - e.pageY,
                    pos_x = $drag.offset().left + drg_w - e.pageX;
                $drag.css('z-index', 1000).parents().on("mousemove", function (e) {
                    $('.draggable').offset({
                        top: e.pageY + pos_y - drg_h,
                        left: e.pageX + pos_x - drg_w
                    }).on("mouseup", function () {
                        $(this).removeClass('draggable').css('z-index', z_idx);
                    });
                });
                e.preventDefault(); // disable selection
            }).on("mouseup", function () {
                if (opt.handle === "") {
                    $(this).removeClass('draggable');
                } else {
                    $(this).removeClass('active-handle').parent().removeClass('draggable');
                }
            });

        }
    })(jQuery);
    /**
     *  Start Submit Comment
     */
    $.fn.depcSubmitComment = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            editor: 0,
            nounce: '',
            reply: true,
            selector: '',
            logged: '',
            captcha: null

        }, options);

        // Init Sliders
        if (settings.reply === false) {
            initAddComment();
        } else if (settings.reply === true) {
            initAddReplyComment();
        } else if (settings.reply === 1) {
            commentReplySaveButton();
        }

        function initAddComment() {

            $('.dpr-submit-form-fields-c').on('click', function () {
                $(this).attr('style', '');
            });

            $(".dpr-discu-container_" + settings.id + " " + settings.selector).on('click', function () {
                var $this = $(this),
                    recaptcha = '',
                    gemail, gname, gwebsite, getcaptcha;

                var html_data;

                // get comment content
                if (typeof (human_data) === "undefined" || human_data === null) {
                    human_data = '';
                }

                // get comment data
                html_data = tinyMCE.activeEditor.getContent();

                // check comment
                if (html_data === '' || html_data === 'undefined') {
                    $('.mce-tinymce').css('border', '1px solid red');
                    $('.mce-tinymce').shake(2, 13, 250);
                    return false;
                } else {
                    $('.mce-tinymce').attr('style', '');
                }

                // validation
                if (dpr.logged_in === 'no') {
                    gname = $('.dpr-submit-form-fields-c-name').val();
                    gemail = $('.dpr-submit-form-fields-c-email').val();
                    gwebsite = $('.dpr-submit-form-fields-c-website').val();
                    getcaptcha = $('#dpr-submit-captcha .g-recaptcha-response').val();

                    // check email
                    if (gname === '' || gname === 'undefined') {
                        $('.dpr-submit-form-fieldswrap-name').css('border', '1px solid red');
                        $('.dpr-submit-form-fieldswrap-name').shake(2, 13, 250);
                        return false;
                    }

                    // check name
                    if (gemail === '' || gemail === 'undefined') {
                        $('.dpr-submit-form-fieldswrap-email').css('border', '1px solid red');
                        $('.dpr-submit-form-fieldswrap-email').shake(2, 13, 250);
                        return false;
                    }

                    // email validationg
                    var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
                    if (!testEmail.test(gemail)) {
                        $('.dpr-submit-form-fieldswrap-email').css('border', '1px solid red');
                        $('.dpr-submit-form-fieldswrap-email').shake(2, 13, 250);
                        return false;
                    }

                    // check if captha is on come on runt it
                    if (dpr.captcha === 'on') {
                        // check for google recaptcha
                        if ($('#dpr-submit-captcha .g-recaptcha-response').length) {
                            var response = grecaptcha.getResponse();
                            if (response == 0) {
                                $('.dpr-submit-form-captcha').css('border', '1px solid red');
                                $('.dpr-submit-form-captcha').shake(2, 13, 250);
                                return false;
                            }
                        }
                    }
                }


                $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'block');

                var jqXhr =
                    $.ajax({
                        type: "POST",
                        url: dpr.adminajax,
                        data: {
                            action: 'dpr_add_comment',
                            security: settings.nounce,
                            post_id: settings.id,
                            comment_data: html_data,
                            gemail: gemail,
                            gname: gname,
                            gwebsite: gwebsite,
                            recaptcha: getcaptcha
                        },
                    });

                $.when(jqXhr).done(function (data) {
                    // display block
                    $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');

                    // if publish
                    if (data.status === 'publish') {
                        // empty tiny mce
                        tinyMCE.activeEditor.setContent('');
                        setTimeout(function () {
                            $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
                            $(data.data).prependTo(".dpr-discu-container_" + settings.id + " .dpr-discu-main-loop-wrap");
                        }, 400);
                        $('.dpr-submit-form-editor').removeAttr('style');

                        setTimeout(function () {
                            var target = $("#comments-" + data.newcomment_id);
                            target = target.length ? target : $('[name=' + this.hash.substr(1) + ']');
                            if (target.length) {
                                $('html,body').animate({
                                    scrollTop: target.offset().top - 60
                                }, 800);
                                return false;
                            }
                        }, 401);

                    } else {
                        $.alert({
                            text: dpr.error,
                            title: data,
                            content: data,
                        });
                    }
                });

            });
        }


        function initAddReplyComment() {
            $(document).on('click', ".dpr-discu-container_" + settings.id + " " + settings.selector, function (e) {
                e.preventDefault();
                var $this = $(this);
                bindAddReplyComment($this);
            });
        }

        function bindAddReplyComment($this) {
            var selector_spn = '',
                selector_btn = '';
            if ($this.attr('data-clicked') === 'not') {
                $this.attr('data-clicked', 'clicked');
                var comment_id = $this.data('id'),
                    parent_id = $this.data('parent'),
                    recaptcha = '',
                    fields = '';
                if (comment_id === parent_id) {
                    selector_btn = '.dpr-tinymce-button:first';
                    selector_spn = '.dpr-tinymce-replies:first';
                } else {
                    selector_spn = '.dpr-tinymce-replies';
                    selector_btn = '.dpr-tinymce-button';
                }

                // init tinymce
                dpr_tinymce(".dpr-discu-wrap_" + comment_id + " .dpr-discu-replies-wrap:first " + selector_spn, 100, false, 'small');
                tinymce.execCommand('mceFocus', false, ".dpr-discu-wrap_" + comment_id + " .dpr-discu-replies-wrap:first " + selector_spn);

                // check for user if  user is guest show captcha
                if (dpr.captcha === 'on' && dpr.logged_in === 'no') recaptcha = '<div class="dpr-replies-captcha" id="dpr-captcha-' + comment_id + '"></div>';
                // check for user if  user is guest show captcha
                if (dpr.logged_in === 'no') {
                    fields = '<div class="dpr-submit-form-fields"><span class="dpr-submit-form-fields-c dpr-submit-form-fieldswrap-name"><i class="sl-user"></i><input class="dpr-submit-form-fields-c-name" type="text" name="name" placeholder="Name"></span><span class="dpr-submit-form-fields-c dpr-submit-form-fieldswrap-email"><i class="sl-envelope-open"></i><input class="dpr-submit-form-fields-c-email" type="email" name="email" placeholder="Email"></span><span class="dpr-submit-form-fields-c dpr-submit-form-fieldswrap-website"><i class="sl-compass"></i><input class="dpr-submit-form-fields-c-website" type="text" name="website" placeholder="Website"></span></div>';
                }

                // append button
                $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-replies-wrap:first " + selector_btn).append(fields + recaptcha + '<a href="#" data-id="' + comment_id + '" data-parent="' + parent_id + '" class="dpr_add_reply_comment">' + dpr.save_comment + '</a> <a href="#" data-id="' + comment_id + '" data-parent="' + parent_id + '" class="dpr_cancel_reply_comment">' + dpr.cancel + '</a>');

                // check for settings
                if (dpr.captcha === 'on' && dpr.logged_in === 'no') {
                    if (dpr.recaptcha) {
                        setTimeout(() => {
                            grecaptcha.render('dpr-captcha-' + comment_id, {
                                'sitekey': dpr.recaptcha,
                                'theme': dpr.recaptcha_theme,
                                'size': dpr.recaptcha_size
                            });
                        }, 500);
                    }
                }

                $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-replies-wrap:first .mce-container").show();
                $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-replies-wrap:first .dpr-tinymce-button").show();

            } else {
                var comment_id = $this.data('id');
                $this.attr('data-clicked', 'not');
                // remove name and mail & website
                $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-replies-wrap:first .dpr-submit-form-fields").remove();
                // remove cancel and edit comment
                $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-replies-wrap:first " + selector_btn + " .dpr_add_reply_comment , .dpr-discu-wrap_" + comment_id + " .dpr-discu-replies-wrap:first " + selector_btn + " .dpr_cancel_reply_comment , .dpr-discu-wrap_" + comment_id + " .dpr-discu-replies-wrap:first " + selector_btn + " #dpr-captcha-" + comment_id).hide();
                $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-replies-wrap:first .mce-container").hide();
                $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-replies-wrap:first .dpr-tinymce-button").hide();
            };
        }

        function commentReplySaveButton() {

            $(document).on('click', ".dpr-discu-container_" + settings.id + " " + settings.selector, function (event) {
                event.preventDefault();
                // define some vars
                var comment_id = $(this).data('id');
                var parents = $(this).parents('.dpr-discu-box').length;
                if ($(this).parents('.dpr-discu-box').length > dpr.p_length) {
                    comment_id = jQuery($(this).parents('.dpr-discu-box').get(parents - dpr.p_length)).find('.dpr-discu-box-header').data('id');
                }
                var parent_id = $(this).data('parent');
                var recaptcha = '';
                // get edited data from tiny mce
                html_data = tinyMCE.activeEditor.getContent();

                // validate reply
                var selector = '#comments-' + comment_id + ' .dpr-discu-replies-wrap ';
                gname = $(selector + '.dpr-submit-form-fields-c-name').val();
                gemail = $(selector + '.dpr-submit-form-fields-c-email').val();
                gwebsite = $(selector + '.dpr-submit-form-fields-c-website').val();
                recaptcha = $('#dpr-captcha-' + comment_id + ' .g-recaptcha-response').val();

                var validate = validate_forms(dpr.logged_in, dpr.captcha, selector, html_data, gname, gemail, recaptcha);
                if (validate === false) {
                    return false;
                }


                // loading Class
                $('#comments-' + parent_id + ' > .dpr-preloader-wrap').css('display', 'block');

                $.ajax({
                    type: "POST",
                    url: dpr.adminajax,
                    data: {
                        action: 'dpr_add_comment',
                        security: settings.nounce,
                        comment_id: comment_id,
                        parents: parents,
                        post_id: settings.id,
                        comment_data: html_data,
                        recaptcha: recaptcha,
                        gname: gname,
                        gemail: gemail,
                        gwebsite: gwebsite
                    },
                    success: function (data) {
                        // Remove the loading Class
                        $('#comments-' + parent_id + ' > .dpr-preloader-wrap').css('display', 'none');
                        if (data.status === 'publish') {
                            setTimeout(function () {
                                $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-box-footer .dpr-discu-replies-box").first().find('.dpr-tinymce-button').first().after($(data.data))
                                // $(data.data).appendAfter(".dpr-discu-wrap_" + comment_id + " .dpr-discu-box-footer .dpr-discu-replies-wrap:first");
                            }, 400);
                        } else {
                            $.alert({
                                text: data,
                                title: dpr.error,
                                content: data,
                            });
                        }
                        // remove tiny mce
                        if (comment_id === parent_id) $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-box-footer .dpr-discu-reply-btn-wrap .dpr-discu-reply-btn").first().trigger('click');
                        else $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-box-footer .dpr-discu-reply-btn-wrap .dpr-discu-reply-btn").trigger('click');
                        $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-replies-wrap .dpr-tinymce-replies p").html('');

                        // remove form field
                        remove_form(selector);
                        if (data.status === 'publish') {
                            setTimeout(function () {
                                var target = $("#comments-" + data.newcomment_id);
                                target = target.length ? target : $('[name=' + this.hash.substr(1) + ']');
                                if (target.length) {
                                    $('html,body').animate({
                                        scrollTop: target.offset().top
                                    }, 800);
                                    return false;
                                }
                            }, 401);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // Remove the loading Class
                        setTimeout(function () {
                            $('#comments-' + parent_id + ' > .dpr-preloader-wrap').css('display', 'none');
                        }, 100);
                    }
                });

            });
        }

    };
    /**
     *  End Submit Comment
     */

    /**
     *  Start Edit Comment
     */
    $.fn.depcEditComment = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            save: true,
            nounce: ''
        }, options);

        if (settings.save == false) {
            commentEditButton();
        } else if (settings.save === true) {
            commentEditSaveButton();
        } else if (settings.save === null) {
            commentDeleteButton();
        } else if (settings.save === -1) {
            commentFlagButton();
        } else if (settings.save === -2) {
            commentSocialButton();
        }

        function commentEditButton() {

            $(document).on('click', ".dpr-discu-container_" + settings.id + " .dpr-discu-wrap .dpr-discu-box-header-icons .dpr-discu-edit", function (event) {
                event.preventDefault();
                var $this = $(this);
                toggledit($this);
            });

        }

        function toggledit($this) {
            if (!$this.hasClass('display-true')) {
                var comment_id = $this.data('id');
                dpr_tinymce(".dpr-discu-wrap_" + comment_id + " .dpr-discu-text:first .dpr-discu-comment-content", 100, false, 'small');
                $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-text:first").append('<a href="#" data-id="' + comment_id + '" class="dpr_edit_comment">' + dpr.save_comment + '</a> <a href="#" data-id="' + comment_id + '" data-parent="' + comment_id + '" class="dpr_cancel_comment">' + dpr.cancel + '</a>');
                tinymce.execCommand('mceFocus', false, "dpr-discu-wrap_" + comment_id + " .dpr-discu-text:first .dpr-discu-comment-content");
                $this.addClass('display-true');
            } else {
                var comment_id = $this.data('id');
                $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-text:first .dpr_edit_comment , .dpr-discu-wrap_" + comment_id + " .dpr-discu-text:first .dpr_cancel_comment").remove();
                tinymce.remove(".dpr-discu-wrap_" + comment_id + " .dpr-discu-text:first .dpr-discu-comment-content");
                var content = jQuery(".dpr-discu-wrap_" + comment_id + " .dpr-discu-text:first").html();
                jQuery(".dpr-discu-wrap_" + comment_id + " .dpr-discu-text:first").html('<div class="dpr-discu-comment-content">' + content + '</div>');

                $this.removeClass('display-true');
            }
        }

        function commentEditSaveButton() {

            $(document).on('click', '.dpr-discu-container_' + settings.id + ' .dpr-discu-wrap .dpr_edit_comment', function (event) {
                event.preventDefault();
                var $this = $(this);
                var comment_id = $this.data('id');
                html_data = tinyMCE.activeEditor.getContent();

                // loading Class
                $('#comments-' + comment_id + ' > .dpr-preloader-wrap').css('display', 'block');

                $.ajax({
                    type: "POST",
                    url: dpr.adminajax,
                    data: {
                        action: 'dpr_edit_comment',
                        security: settings.nounce,
                        comment_id: comment_id,
                        content: html_data
                    },
                    success: function (data) {
                        // Remove the loading Class
                        $('#comments-' + comment_id + ' > .dpr-preloader-wrap').css('display', 'none');
                        $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-text .dpr_edit_comment , .dpr-discu-wrap_" + comment_id + " .dpr-discu-text .dpr_cancel_comment").remove();
                        tinymce.remove(".dpr-discu-wrap_" + comment_id + " .dpr-discu-text p");
                        $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-text").empty();
                        $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-text").append(html_data);

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // Remove the loading Class
                        setTimeout(function () {
                            $('#comments-' + comment_id + ' > .dpr-preloader-wrap').css('display', 'none');
                        }, 100);
                    }
                });

            });
        }

        function commentDeleteButton() {

            $(document).on('click', '.dpr-discu-container_' + settings.id + ' .dpr-discu-wrap .dpr-discu-delete', function (event) {
                event.preventDefault();
                var $this = $(this);
                var comment_id = $this.data('id');

                $.confirm({
                    content: dpr.sure_delete,
                    title: dpr.delete_cm,
                    type: 'red',
                    rtl: dpr.rtl,
                    animation: 'bottom',
                    closeAnimation: 'top',
                    theme: 'modern',
                    buttons: {
                        ok: {
                            text: dpr.delete,
                            btnClass: 'btn-primary',
                            keys: ['enter'],
                            action: function () {
                                // loading Class
                                $('#comments-' + comment_id + ' > .dpr-preloader-wrap').css('display', 'block');

                                $.ajax({
                                    type: "POST",
                                    url: dpr.adminajax,
                                    data: {
                                        action: 'dpr_delete_comment',
                                        security: settings.nounce,
                                        comment_id: comment_id
                                    },
                                    success: function (data) {
                                        // Remove the loading Class
                                        $('#comments-' + comment_id + ' > .dpr-preloader-wrap').css('display', 'none');
                                        $('.dpr-discu-wrap_' + comment_id).fadeOut(800);

                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        // Remove the loading Class
                                        setTimeout(function () {
                                            $('#comments-' + comment_id + ' > .dpr-preloader-wrap').css('display', 'none');

                                        }, 100);
                                    }
                                });
                            }
                        },
                        cancel: {
                            text: dpr.cancel,
                            action: function () {}
                        }
                    }
                });

            });
        }

        function commentFlagButton() {

            $(document).on('click', '.dpr-discu-container_' + settings.id + ' .dpr-discu-wrap .dpr-discu-flag', function (event) {
                event.preventDefault();

                var $this = $(this);
                var comment_id = $this.parents('.dpr-discu-box-header').first().data('id');

                $.confirm({
                    content: dpr.sure_flag,
                    title: dpr.flag_cm,
                    type: 'red',
                    rtl: dpr.rtl,
                    animation: 'bottom',
                    closeAnimation: 'top',
                    theme: 'modern',
                    buttons: {
                        ok: {
                            text: dpr.sure,
                            btnClass: 'btn-primary',
                            keys: ['enter'],
                            action: function () {
                                // loading Class
                                $('#comments-' + comment_id + ' > .dpr-preloader-wrap').css('display', 'block');
                                $.ajax({
                                    type: "POST",
                                    url: dpr.adminajax,
                                    data: {
                                        action: 'dpr_flag_comment',
                                        security: settings.nounce,
                                        comment_id: comment_id
                                    },
                                    success: function (data) {
                                        // Remove the loading Class
                                        $('#comments-' + comment_id + ' > .dpr-preloader-wrap').css('display', 'none');
                                        $.alert({
                                            text: dpr.ok,
                                            title: dpr.flag_cm,
                                            content: data.message,
                                        });
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        // Remove the loading Class
                                        setTimeout(function () {
                                            $('#comments-' + comment_id + ' > .dpr-preloader-wrap').css('display', 'none');

                                        }, 100);
                                    }
                                });
                            }
                        },
                        cancel: {
                            text: dpr.cancel,
                            action: function () {

                            }
                        }
                    }
                });

            });

        }

        function commentSocialButton() {

            $(document).on('click', '.dpr-discu-container_' + settings.id + ' .dpr-discu-metadata-share-wrap .dpr-discu-sharing .dpr-discu-social-icon a:not(.email,.whatsapp)', function (event) {
                event.preventDefault();
                var $this = $(this),
                    comment_id = $this.data('id');

                var child = parseInt($('.dpr-discu-wrap .dpr-c-contents .dpr-discu-metadata-share-wrap .dpr-discu-share .dpr-discu-share-count-' + comment_id).html());
                $.ajax({
                    type: "POST",
                    url: dpr.adminajax,
                    data: {
                        action: 'dpr_social_comment',
                        security: settings.nounce,
                        comment_id: comment_id
                    },
                    success: function (data) {
                        if (data.resp == 0) {
                            $('.dpr-discu-wrap .dpr-discu-metadata-share-wrap .dpr-discu-share .dpr-discu-share-count-' + comment_id).text(child - 0);
                        } else {
                            $('.dpr-discu-wrap .dpr-c-contents .dpr-discu-metadata-share-wrap .dpr-discu-share .dpr-discu-share-count-' + comment_id).text(child + 1);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                    }
                });

            });

        }

    };
    /**
     *  End Edit Comment
     */

    /**
     *  Start Login Form
     */
    $.fn.depcLoginForm = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            nounce: '',
            register: ''
        }, options);

        // Init Sliders
        if (settings.register == false) initLoginForm();
        else initRegisterForm();

        function initLoginForm() {

            $(".dpr-discu-container_" + settings.id + " .dpr-join-form-login-a").on('click', function (e) {
                e.preventDefault();
                var $this = $(this);
                var rcp = '';
                if (dpr.captcha == 'google') {
                    rcp = '<div class="dpr-submit-form-captcha dpr-login-submit-form-captcha"><div class="dpr-submit-form-captcha-container"><div id="dpr-login-submit-captcha"></div></div></div>';
                }
                var sing =
                    $.confirm({
                        content: "<div class='dpr-modal-cntt'><div class='dpr-preloader-wrap'><div class='dpr-preloader'></div></div><form id='login' action='login' method='post'><input placeholder='" + dpr.usernameOrEmail + "' required='required' id='dpr_username' type='text' name='username'><input placeholder='" + dpr.password + "' required='required' id='dpr_password' type='password' name='password'>" + rcp + "</form></div>",
                        title: dpr.login,
                        closeIcon: true,
                        type: 'red',
                        rtl: dpr.rtl,
                        animation: 'bottom',
                        closeAnimation: 'top',
                        theme: 'modern',
                        onOpen: function () {
                            if (dpr.recaptcha) {
                                setTimeout(() => {
                                    grecaptcha.render('dpr-login-submit-captcha', {
                                        'sitekey': dpr.recaptcha,
                                        'theme': dpr.recaptcha_theme,
                                        'size': dpr.recaptcha_size
                                    });

                                    
                                }, 500);
                            }
                            setTimeout(() => {
                                $('.dpr-modal-cntt form input').keypress(function (e) {                                                    
                                    if ((e.keyCode == 13) && (e.target.type != "textarea")) {
                                        e.preventDefault();
                                        
                                        $(e.currentTarget).parents('.jconfirm-box').find('.btn-primary').click();
                                    }
                                });
                            },500);
                        },
                        buttons: {
                            ok: {
                                text: dpr.login,
                                btnClass: 'btn-primary',
                                keys: ['enter'],
                                action: function () {

                                    var username, password;
                                    username = $('#dpr_username').val();
                                    password = $('#dpr_password').val();
                                    captcha = $('#g-recaptcha-response').val();

                                    // check username
                                    if (username === '' || username === 'undefined') {
                                        $('#dpr_username').css('border', '1px solid red');
                                        $('.jconfirm-content-pane').shake(2, 13, 250);
                                        return false;
                                    }
                                    // check password
                                    if (password === '' || password === 'undefined') {
                                        $('#dpr_password').css('border', '1px solid red');
                                        $('.jconfirm-content-pane').shake(2, 13, 250);
                                        return false;
                                    }

                                    if (dpr.recaptcha) {
                                        if ($('#dpr-login-submit-captcha .g-recaptcha-response').length) {
                                            var response = grecaptcha.getResponse();
                                            if (response == 0) {
                                                $('.dpr-login-submit-form-captcha').css('border', '1px solid red');
                                                $('.dpr-login-submit-form-captcha').shake(2, 13, 250);
                                                return false;
                                            }
                                        }
                                    }
                                    
                                    // start preloader after validation
                                    $('.dpr-modal-cntt .dpr-preloader-wrap').css('display', 'block');

                                    $.ajax({
                                        type: "POST",
                                        url: dpr.adminajax,
                                        data: {
                                            action: 'dpr_login',
                                            security: settings.nounce,
                                            post_id: settings.id,
                                            dpr_username: username,
                                            dpr_password: password,
                                            "g-recaptcha-response": captcha
                                        },
                                        success: function (data) {
                                            $('.dpr-modal-cntt .dpr-preloader-wrap').css('display', 'none');
                                            if (data.error == true) {
                                                $('.dpr-notify').remove();
                                                sing.setContentAppend('<div class="dpr-notify-error" >' + data.message + '</div>');
                                            } else {
                                                $('.dpr-notify').remove();
                                                sing.setContentAppend('<div class="dpr-notify">' + data.message + '</div>');
                                                setTimeout(function () {
                                                    location.reload();
                                                }, 500);
                                            }

                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {
                                            // Remove the loading Class to the button
                                            $('.dpr-modal-cntt .dpr-preloader-wrap').css('display', 'none');
                                        }
                                    });
                                    return false;

                                }
                            },
                            cancel: {
                                text: dpr.cancel,
                                action: function () {

                                }
                            }
                        }
                    });
            });
        }

        function isEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }

        function initRegisterForm() {
            $(".dpr-discu-container_" + settings.id + " .dpr-join-form-register-a").on('click', function () {
                var $this = $(this),
                    term = '';
                // term link check if is on
                if (dpr.term_link !== '') {
                    term = "<label class='checkbox rememberme'><input name='rememberme' type='checkbox' id='rememberme' value='forever'><span>I have read and accept the <a href='" + dpr.term_link + "' target='_blank' class='dpr-modal-terms'>Terms and Conditions</a></span></label>";
                }

                var rcp = '';
                if (dpr.captcha == 'google') {
                    rcp = '<div class="dpr-submit-form-captcha dpr-register-submit-form-captcha"><div class="dpr-submit-form-captcha-container"><div id="dpr-register-submit-captcha"></div></div></div>';
                }

                var sing =
                    $.confirm({
                        content: "<div class='dpr-modal-cntt'><div class='dpr-preloader-wrap'><div class='dpr-preloader'></div></div><form id='dpr-register' action='login' method='post'><input type='text' name='name' required='required' id='name' placeholder=" + dpr.name + "><input type='email' name='email' required='required' id='email' placeholder=" + dpr.email + "><input type='text' name='username' required='required' id='username' placeholder=" + dpr.username + "><input id='inputPassword' type='password' required='required' name='password' placeholder=" + dpr.password + "><input type='password' name='cnfrm_password' required='required' placeholder=" + dpr.confirm_password + "><span id='complexity' class='default'></span>" + term + rcp + "<div class='dpr-modal-footer'><span class='dpr-modal-login-span'>Already have an account? <a href='#' class='dpr-modal-login-a'>" + dpr.login + "</a></span></div></form></div>",
                        title: dpr.signup,
                        closeIcon: true,
                        type: 'red',
                        rtl: dpr.rtl,
                        animation: 'bottom',
                        closeAnimation: 'top',
                        theme: 'modern',
                        onOpen: function () {
                            jQuery("#inputPassword").depcPassStrength();
                            if (dpr.recaptcha) {
                                setTimeout(() => {
                                    grecaptcha.render('dpr-register-submit-captcha', {
                                        'sitekey': dpr.recaptcha,
                                        'theme': dpr.recaptcha_theme,
                                        'size': dpr.recaptcha_size
                                    });

                                }, 500);
                            }
                            setTimeout(() => {
                                $('.dpr-modal-cntt form input').keypress(function (e) {                                                    
                                    if ((e.keyCode == 13) && (e.target.type != "textarea")) {
                                        e.preventDefault();
                                        
                                        $(e.currentTarget).parents('.jconfirm-box').find('.btn-primary').click();
                                    }
                                });
                            },500);
                        },
                        buttons: {
                            ok: {
                                text: dpr.signup,
                                btnClass: 'btn-primary',
                                keys: ['enter'],
                                action: function () {

                                    // check name
                                    if ($('#name').val() === '' || $('#name').val() === 'undefined') {
                                        $('#name').css('border', '1px solid red');
                                        $('.jconfirm-content-pane').shake(2, 13, 250);
                                        return false;
                                    }


                                    // check email
                                    if ($('#email').val() === '' || $('#email').val() === 'undefined' || !isEmail($('#email').val())) {
                                        $('#email').css('border', '1px solid red');
                                        $('.jconfirm-content-pane').shake(2, 13, 250);
                                        return false;
                                    }
                                    // check user name
                                    if ($('#username').val() === '' || $('#username').val() === 'undefined') {
                                        $('#username').css('border', '1px solid red');
                                        $('.jconfirm-content-pane').shake(2, 13, 250);
                                        return false;
                                    }
                                    // check password
                                    if ($('#inputPassword').val() === '' || $('#inputPassword').val() === 'undefined') {
                                        $('#inputPassword').css('border', '1px solid red');
                                        $('.jconfirm-content-pane').shake(2, 13, 250);
                                        return false;
                                    }

                                    if ($('#inputPassword').val() !== $('#inputPassword').siblings('input[name="cnfrm_password"]').val()) {
                                        $('#inputPassword').siblings('input[name="cnfrm_password"]').css('border', '1px solid red');
                                        $('.jconfirm-content-pane').shake(2, 13, 250);
                                        return false;
                                    }

                                    if (dpr.recaptcha) {
                                        if ($('#dpr-register-submit-captcha .g-recaptcha-response').length) {
                                            var response = grecaptcha.getResponse();
                                            if (response == 0) {
                                                $('.dpr-register-submit-form-captcha').css('border', '1px solid red');
                                                $('.dpr-register-submit-form-captcha').shake(2, 13, 250);
                                                return false;
                                            }
                                        }
                                    }

                                    if (dpr.term_link !== '') {
                                        // check temr and confiction
                                        if (!jQuery("#rememberme").is(":checked")) {
                                            $('.rememberme').css('border', '1px solid red');
                                            $('.jconfirm-content-pane').shake(2, 13, 250);
                                            return false;
                                        }
                                    }


                                    $('.dpr-modal-cntt .dpr-preloader-wrap').css('display', 'block');

                                    var register = $('#dpr-register').serialize();

                                    $.ajax({
                                        type: "POST",
                                        url: dpr.adminajax,
                                        data: {
                                            action: 'dpr_register',
                                            security: settings.nounce,
                                            post_id: settings.id,
                                            register: register
                                        },
                                    }).done(function (data) {
                                        $('.dpr-modal-cntt .dpr-preloader-wrap').css('display', 'none');
                                        if (data.error == true) {
                                            $('.dpr-notify').remove();
                                            $.each(data.message, function (index, el) {
                                                sing.setContentAppend('<div class="dpr-notify">' + el + '</div>');
                                            });
                                        } else {
                                            $('.dpr-notify').remove();
                                            $('#dpr-register').hide();
                                            $('.jconfirm-box .jconfirm-buttons .btn-primary').hide();
                                            $('.jconfirm-box .jconfirm-buttons .btn-default').text(dpr.refresh).on('click', function (e) {
                                                e.preventDefault();
                                                location.reload();
                                                return false;
                                            });
                                            sing.setContentAppend('<div class="dpr-notify" id="dpr-registration-notify">' + data.message + '</div>');
                                            jQuery('#dpr-registration-notify').on('click', function () {
                                                location.reload();
                                            })
                                        }
                                    }).fail(function () {
                                        $('.dpr-modal-cntt .dpr-preloader-wrap').css('display', 'none');
                                    });

                                    return false;
                                }
                            },
                            cancel: {
                                text: dpr.cancel,
                                action: function () {

                                }
                            }
                        }
                    });
            });
        }
    };
    /**
     *  End Login Form
     */

    /**
     *  Start Password strength
     */
    $.fn.depcPassStrength = function () {
        // Default Options
        var strPassword;
        var charPassword;
        var complexity = $("#complexity");
        var minPasswordLength = 8;
        var baseScore = 0,
            score = 0;

        var num = {};
        num.Excess = 0;
        num.Upper = 0;
        num.Numbers = 0;
        num.Symbols = 0;

        var bonus = {};
        bonus.Excess = 3;
        bonus.Upper = 4;
        bonus.Numbers = 5;
        bonus.Symbols = 5;
        bonus.Combo = 0;
        bonus.FlatLower = 0;
        bonus.FlatNumber = 0;

        outputResult();
        $("#inputPassword").bind("keyup", checkVal);

        function checkVal() {
            init();

            if (charPassword.length >= minPasswordLength) {
                baseScore = 50;
                analyzeString();
                calcComplexity();
            } else {
                baseScore = 0;
            }

            outputResult();
        }

        function init() {

            strPassword = $("#inputPassword").val();
            charPassword = strPassword.split("");

            num.Excess = 0;
            num.Upper = 0;
            num.Numbers = 0;
            num.Symbols = 0;
            bonus.Combo = 0;
            bonus.FlatLower = 0;
            bonus.FlatNumber = 0;
            baseScore = 0;
            score = 0;
        }

        function analyzeString() {
            for (i = 0; i < charPassword.length; i++) {
                if (charPassword[i].match(/[A-Z]/g)) {
                    num.Upper++;
                }
                if (charPassword[i].match(/[0-9]/g)) {
                    num.Numbers++;
                }
                if (charPassword[i].match(/(.*[!,@,#,$,%,^,&,*,?,_,~])/)) {
                    num.Symbols++;
                }
            }

            num.Excess = charPassword.length - minPasswordLength;

            if (num.Upper && num.Numbers && num.Symbols) {
                bonus.Combo = 25;
            } else if ((num.Upper && num.Numbers) || (num.Upper && num.Symbols) || (num.Numbers && num.Symbols)) {
                bonus.Combo = 15;
            }

            if (strPassword.match(/^[\sa-z]+$/)) {
                bonus.FlatLower = -15;
            }

            if (strPassword.match(/^[\s0-9]+$/)) {
                bonus.FlatNumber = -35;
            }
        }

        function calcComplexity() {
            score = baseScore + (num.Excess * bonus.Excess) + (num.Upper * bonus.Upper) + (num.Numbers * bonus.Numbers) + (num.Symbols * bonus.Symbols) + bonus.Combo + bonus.FlatLower + bonus.FlatNumber;

        }

        function outputResult() {

            if ($("#inputPassword").val() == "") {
                complexity.html("").removeClass("weak strong stronger strongest").addClass("default");
            } else if (charPassword.length < minPasswordLength) {
                complexity.html("At least " + minPasswordLength + " characters please!").removeClass("strong stronger strongest").addClass("weak");
            } else if (score < 50) {
                complexity.html("Weak!").removeClass("strong stronger strongest").addClass("weak");
            } else if (score >= 50 && score < 75) {
                complexity.html("Average!").removeClass("stronger strongest").addClass("strong");
            } else if (score >= 75 && score < 100) {
                complexity.html("Strong!").removeClass("strongest").addClass("stronger");
            } else if (score >= 100) {
                complexity.html("Secure!").addClass("strongest");
            }

        }

    };
    /**
     *  End Password strength
     */


    /**
     *  Start Like Dislike
     */
    $.fn.depcVote = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            nounce: '',
            like: true
        }, options);
        var voteReq = false;

        // Init vote systems
        initLikeDislike();

        function initLikeDislike() {

            $(document).on('click', ".dpr-discu-container_" + settings.id + " .dpr-discu-box .dpr-discu-box-footer-metadata-like .dpr-cont-discu-" + settings.like + " .dpr-discu-" + settings.like, function (e) {
                if (voteReq) {
                    return false;
                }
                voteReq = true;
                e.preventDefault();
                var $this = $(this);
                var comment_id = $this.parent().parent().data('id');
                var child = parseInt($this.children(".dpr-discu-" + settings.like + "-count").text());

                $.ajax({
                    type: "POST",
                    url: dpr.adminajax,
                    data: {
                        action: 'dpr_vote',
                        security: settings.nounce,
                        comment_id: comment_id,
                        like: settings.like
                    },
                }).done(function (data) {
                    voteReq = false;
                    if (data == 1) {
                        $this.children(".dpr-discu-" + settings.like + "-count").text(child + 1);
                    } else if (data == -1) {
                        $this.children(".dpr-discu-" + settings.like + "-count").text(child - 1);
                    }
                    if (data.message) {
                        $.alert({
                            text: dpr.ok,
                            title: data.title,
                            content: data.message,
                        });
                    }
                }).fail(function () {
                    voteReq = false;
                    $this.children(".dpr-discu-" + settings.like + "-count").text(child - 0);
                });


            });
        }
    };
    /**
     *  End Like Dislike
     */

    /**
     *  Start Comments Filter
     */
    $.fn.depcFilter = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            nounce: '',
            action: '',
            type: '',
            cache_trending: false,
            cache_popular: false,
            cache_oldest: false,
            cache_newest: false

        }, options);

        // Init Search and Filter
        if (settings.type === 'trending') {
            initTrending();
        } else if (settings.type === 'popular') {
            initPopular();
        } else if (settings.type === 'oldest') {
            initOldest();
        } else if (settings.type === 'newest') {
            initNewest();
        } else if (settings.type === 'key') {
            initKeySearch();
        }

        function initTrending() {

            $(".dpr-discu-container_" + settings.id + " .dpr-switch-tab-wrap .dpr-switch-tab .dpr-switch-tab-trending .dpr-switch-tab-trending-a").on('click', function (e) {
                e.preventDefault();

                var $this = $(this);

                //do cache
                if (settings.cache_trending != false) {
                    do_cache($this, settings.cache_trending, settings.id, 'trending');
                    return;
                }

                $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'block');

                $.ajax({
                    type: "POST",
                    url: dpr.adminajax,
                    data: {
                        action: settings.action,
                        id: settings.id,
                        security: settings.nounce,
                        type: settings.type
                    },
                }).done(function (data) {
                    $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
                    $('.dpr-container .dpr-switch-tab-wrap .dpr-switch-tab li a').removeClass('dpr-active-tab');
                    $this.addClass('dpr-active-tab');
                    $('.dpr-discu-main-loop-wrap').empty();
                    $('.dpr-discu-main-loop-wrap').append(data);
                    $(".dpr-discu-container_" + settings.id + " .dpr-discu-main-loop-wrap .dpr-loadmore-btn").attr('data-type', 'trending');
                    // set our cache to on
                    settings.cache_trending = data;
                }).fail(function () {
                    // $this.children( ".dpr-discu-"+settings.like+"-count" ).text( child - 0 );
                    $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
                });

            });
        }

        function initPopular() {

            $(".dpr-discu-container_" + settings.id + " .dpr-switch-tab-wrap .dpr-switch-tab .dpr-switch-tab-popular .dpr-switch-tab-popular-a").on('click', function (e) {
                e.preventDefault();

                var $this = $(this);

                //do cache
                if (settings.cache_popular != false) {
                    do_cache($this, settings.cache_popular, settings.id, 'popular');
                    return;
                }

                $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'block');

                $.ajax({
                    type: "POST",
                    url: dpr.adminajax,
                    data: {
                        id: settings.id,
                        action: settings.action,
                        security: settings.nounce,
                        type: settings.type
                    },
                }).done(function (data) {
                    $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
                    $('.dpr-container .dpr-switch-tab-wrap .dpr-switch-tab li a').removeClass('dpr-active-tab');
                    $this.addClass('dpr-active-tab');
                    $('.dpr-discu-main-loop-wrap').empty();
                    $('.dpr-discu-main-loop-wrap').append(data);
                    $(".dpr-discu-container_" + settings.id + " .dpr-discu-main-loop-wrap .dpr-loadmore-btn").attr('data-type', 'popular');
                    // set our cache to on
                    settings.cache_popular = data;
                }).fail(function () {
                    $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
                });

            });
        }

        function initOldest() {

            $(".dpr-discu-container_" + settings.id + " .dpr-switch-tab-wrap .dpr-switch-tab .dpr-switch-tab-oldest .dpr-switch-tab-oldest-a").on('click', function (e) {
                e.preventDefault();

                var $this = $(this);

                //do cache
                if (settings.cache_oldest != false) {
                    do_cache($this, settings.cache_oldest, settings.id, 'ASC');
                    return;
                }

                $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'block');

                $.ajax({
                    type: "POST",
                    url: dpr.adminajax,
                    data: {
                        action: settings.action,
                        security: settings.nounce,
                        type: settings.type,
                        id: settings.id
                    },
                }).done(function (data) {

                    $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
                    $('.dpr-container .dpr-switch-tab-wrap .dpr-switch-tab li a').removeClass('dpr-active-tab');
                    $this.addClass('dpr-active-tab');
                    $('.dpr-discu-main-loop-wrap').empty();
                    $('.dpr-discu-main-loop-wrap').append(data);
                    $(".dpr-discu-container_" + settings.id + " .dpr-discu-main-loop-wrap .dpr-loadmore-btn").attr('data-type', 'ASC');
                    // set our cache to on
                    settings.cache_oldest = data;
                }).fail(function () {
                    $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
                });

            });
        }

        function initNewest() {

            $(".dpr-discu-container_" + settings.id + " .dpr-switch-tab-wrap .dpr-switch-tab .dpr-switch-tab-newest .dpr-switch-tab-newest-a").on('click', function (e) {
                e.preventDefault();

                var $this = $(this);

                if (settings.cache_newest != false) {
                    do_cache($this, settings.cache_newest, settings.id, 'DESC');
                    return;
                }

                $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'block');

                $.ajax({
                    type: "POST",
                    url: dpr.adminajax,
                    data: {
                        action: settings.action,
                        security: settings.nounce,
                        type: settings.type,
                        id: settings.id
                    },
                }).done(function (data) {
                    $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
                    $('.dpr-container .dpr-switch-tab-wrap .dpr-switch-tab li a').removeClass('dpr-active-tab');
                    $this.addClass('dpr-active-tab');
                    $('.dpr-discu-main-loop-wrap').empty();
                    $('.dpr-discu-main-loop-wrap').append(data);
                    $(".dpr-discu-container_" + settings.id + " .dpr-discu-main-loop-wrap .dpr-loadmore-btn").attr('data-type', 'DESC');
                    settings.cache_newest = data;
                }).fail(function () {
                    $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
                });

            });
        }


        function initKeySearch() {
            var selector = ".dpr-discu-container_" + settings.id + " .dpr-switch-tab-wrap .dpr-switch-search-wrap .dpr-discu-search",
                search_val = '';


            var typingTimer;
            var doneTypingInterval = 1000;

            //on keyup, start the countdown
            $(selector).on('keyup', function () {
                clearTimeout(typingTimer);
                search_val = $(this).val();
                typingTimer = setTimeout(function () {
                    doneTyping(search_val);
                }, doneTypingInterval);
            });

            //on keydown, clear the countdown
            $(selector).on('keydown', function () {
                clearTimeout(typingTimer);
            });
        }

        //user is "finished typing," do something
        function doneTyping(search_val) {

            $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'block');
            $.ajax({
                type: 'POST',
                url: dpr.adminajax, //get ajax url from loclized script
                data: {
                    action: settings.action, // wp ajax function action'
                    security: settings.nounce,
                    type: settings.type,
                    search: search_val,
                    id: settings.id
                },
                success: function (data) {
                    $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
                    $('.dpr-discu-main-loop-wrap').empty();
                    $('.dpr-discu-main-loop-wrap').append(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
                    $('.dpr-discu-main-loop-wrap').empty();
                    $('.dpr-discu-main-loop-wrap').append(dpr.nocomment);

                }
            });
        }


        function do_cache($this, data, id, order) {
            // check the cache
            $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
            $('.dpr-container .dpr-switch-tab-wrap .dpr-switch-tab li a').removeClass('dpr-active-tab');
            $this.addClass('dpr-active-tab');
            $('.dpr-discu-main-loop-wrap').empty();
            $('.dpr-discu-main-loop-wrap').append(data);
            $(".dpr-discu-container_" + id + " .dpr-discu-main-loop-wrap .dpr-loadmore-btn").attr('data-type', order);

        }

    };
    /**
     *  End Comments Filter
     */


    /**
     *  Start Load More
     */
    $.fn.depcLoadMore = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            nounce: '',
            action: ''
        }, options);

        // Init load more
        initLoadMore();

        function initLoadMore() {

            $(document).on('click', ".dpr-discu-container_" + settings.id + " .dpr-discu-main-loop-wrap .dpr-loadmore-btn", function (e) {
                e.preventDefault();

                var $this = $(this);
                var half_cm = $this.data('comments');
                var loaded_cm = $this.data('loaded');
                var type = $this.data('type');

                $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'block');

                $.ajax({
                    type: "POST",
                    url: dpr.adminajax,
                    data: {
                        id: settings.id,
                        action: settings.action,
                        security: settings.nounce,
                        half_cm: half_cm,
                        loaded_cm: loaded_cm,
                        type: type
                    },
                }).done(function (data) {
                    $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
                    $(data.data).insertAfter('.dpr-container .dpr-discu-main-loop-wrap .dpr-insertafter:last');
                    if (0 >= parseInt(data.half_cm)) {
                        $this.remove();
                    }
                    $this.data('comments', data.half_cm).attr('data-comments', data.half_cm);
                    $this.data('loaded', data.loaded_cm).attr('data-loaded', data.loaded_cm);
                    $this.data('type', data.type).attr('data-type', data.type);
                }).fail(function () {
                    $this.css('display', 'block');
                    $('.dpr-wrap > .dpr-preloader-wrap').css('display', 'none');
                });

            });
        }
    };
    /**
     *  End Load More
     */


    /**
     *  Start Handy JS
     */
    $.fn.depcHandyJs = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            nounce: '',
            action: '',
            mode: '',
            selector: ''
        }, options);

        // Init copy link
        if (settings.mode === 'copylink') {
            initCopyLink();
        } else if (settings.mode === 'facebook') {
            initFacebook();
        }

        initVK();
        initTumblr();
        initPinterest();
        initGetpocket();
        initReddit();
        initTelegram();

        function initCopyLink() {

            $(document).on('click', ".dpr-discu-container_" + settings.id + " .dpr-discu-main-loop-wrap .dpr-discu-wrap .dpr-discu-box .dpr-discu-link", function (e) {
                e.preventDefault();
                $.alert({
                    text: dpr.copy_message,
                    title: dpr.copy_message_title,
                    content: dpr.copy_message,
                });
            });
            // copy to clipboard
            new ClipboardJS('.dpr-discu-main-loop-wrap .dpr-discu-wrap .dpr-discu-box .dpr-discu-link');

        }

        function initFacebook() {

            $(document).on('click', settings.selector, function (e) {
                e.preventDefault();

                var $this = $(this),
                    url = $this.attr('data-link');
                title = $this.attr('data-title');

                var ShareUrl = 'https://www.facebook.com/sharer/sharer.php?';
                if (url) {
                    ShareUrl += 'u=' + encodeURIComponent(url);
                }
                window.open(ShareUrl, '', 'toolbar=0,status=0,width=626,height=436');
                return false;

            });
        }

        function initVK() {

            $(document).on('click', '.dpr-discu-social-icon a.vk', function (e) {
                e.preventDefault();

                var $this = $(this),
                    url = $this.attr('data-link');
                title = $this.attr('data-title');

                var ShareUrl = 'http://vk.com/share.php?';
                if (url) {
                    ShareUrl += 'url=' + encodeURIComponent(url);
                }
                if (title) {
                    ShareUrl += '&title=' + encodeURIComponent(title);
                }
                ShareUrl += '&noparse=true';
                window.open(ShareUrl, '', 'toolbar=0,status=0,width=626,height=436');

                return false;
            });
        }

        function initTumblr() {

            $(document).on('click', '.dpr-discu-social-icon a.tumblr', function (e) {
                e.preventDefault();

                var $this = $(this),
                    url = $this.attr('data-link');
                title = $this.attr('data-title');

                var ShareUrl = 'http://www.tumblr.com/share/link?';
                if (url) {
                    ShareUrl += 'url=' + encodeURIComponent(url);
                }
                if (title) {
                    ShareUrl += '&title=' + encodeURIComponent(title);
                }

                window.open(ShareUrl, '', 'toolbar=0,status=0,width=626,height=436');
                return false;
            });
        }

        function initPinterest() {

            $(document).on('click', '.dpr-discu-social-icon a.pinterest', function (e) {
                e.preventDefault();

                var $this = $(this),
                    url = $this.attr('data-link');
                title = $this.attr('data-title');

                var ShareUrl = 'http://pinterest.com/pin/create/link/?';
                if (url) {
                    ShareUrl += 'url=' + encodeURIComponent(url);
                }
                if (title) {
                    ShareUrl += '&description=' + encodeURIComponent(title);
                }

                window.open(ShareUrl, '', 'toolbar=0,status=0,width=626,height=436');
                return false;
            });
        }

        function initReddit() {

            $(document).on('click', '.dpr-discu-social-icon a.reddit', function (e) {
                e.preventDefault();

                var $this = $(this),
                    url = $this.attr('data-link');
                title = $this.attr('data-title');

                var ShareUrl = 'http://www.reddit.com/submit?';
                if (url) {
                    ShareUrl += 'url=' + encodeURIComponent(url);
                }
                if (title) {
                    ShareUrl += '&title=' + encodeURIComponent(title);
                }

                window.open(ShareUrl, '', 'toolbar=0,status=0,width=626,height=436');
                return false;
            });
        }

        function initTelegram() {

            $(document).on('click', '.dpr-discu-social-icon a.telegram', function (e) {
                e.preventDefault();

                var $this = $(this),
                    url = $this.attr('data-link');
                title = $this.attr('data-title');

                var ShareUrl = 'https://telegram.me/share/url?';
                if (url) {
                    ShareUrl += 'url=' + encodeURIComponent(url);
                }
                if (title) {
                    ShareUrl += '&text=' + encodeURIComponent(title);
                }

                window.open(ShareUrl, '', 'toolbar=0,status=0,width=626,height=436');
                return false;
            });
        }

        function initGetpocket() {

            $(document).on('click', '.dpr-discu-social-icon a.getpocket', function (e) {
                e.preventDefault();

                var $this = $(this),
                    url = $this.attr('data-link');

                var ShareUrl = 'https://getpocket.com/edit.php?';
                if (url) {
                    ShareUrl += 'url=' + encodeURIComponent(url);
                }

                window.open(ShareUrl, '', 'toolbar=0,status=0,width=626,height=436');
                return false;
            });
        }

    };
    /**
     *  End Handy JS
     */
    $(document).on('DOMNodeInserted', function (e) {
        $('[aria-label]').each(function () {
            if ($(this).attr('aria-label').trim() == 'Insert/edit image') {
                $(this).find('.mce-label').each(function () {
                    if ($(this).text().trim() == "Source") {
                        $(this).text('Image URL');
                        $(this).attr('title', 'Please insert uploaded image url');
                    }
                });
            }
        });
    });

    /**
     *  Start tiny dom actions
     */
    $(document).ready(function ($) {
        $('.dpr-submit-form-wrap').addClass('hidden-form');
        dpr_tinymce('.dpr-add-editor', 100, false, 'small');

        $(document).on('click', '.dpr-discu-box .dpr-discu-collapse', function (event) {
            event.preventDefault();
            var comment_id = $(this).data('id');
            $('.dpr-discu-wrap_' + comment_id + ' .dpr-c-contents').toggle('fast');
        });

        $(document).on('click', '.dpr-modal-login-a', function (event) {
            event.preventDefault();
            jQuery('.jconfirm-closeIcon').trigger('click');
            jQuery('.dpr-join-form-login-a').trigger('click');
            return false;
        });

        // cancel button on comments
        $(document).on('click', '.dpr-discu-wrap .dpr-discu-text .dpr_cancel_comment', function (event) {
            event.preventDefault();
            var $this = $(this),
                comment_id = $this.data('id'),
                parent_id = $this.data('parent'),
                selector = '';
            $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-box-header .dpr-discu-edit").first().trigger('click');
        });

        // cancel button on reply comments
        $(document).on('click', '.dpr-discu-wrap .dpr-discu-replies-wrap .dpr_cancel_reply_comment', function (event) {
            event.preventDefault();
            var $this = $(this),
                comment_id = $this.data('id'),
                parent_id = $this.data('parent'),
                selector = '';
            if (comment_id === parent_id) $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-box-footer .dpr-discu-reply-btn-wrap .dpr-discu-reply-btn").first().trigger('click');
            else $(".dpr-discu-wrap_" + comment_id + " .dpr-discu-box-footer .dpr-discu-reply-btn-wrap .dpr-discu-reply-btn").trigger('click');
        });

        // rid out red border on typing
        $(document).on('keypress', '#dpr-gname, #dpr-gemail, #dpr_username, #dpr_password, #name, #email, #username, #inputPassword, #rememberme, .dpr-submit-form-fields-c-name, .dpr-submit-form-fields-c-email', function () {
            $(this).removeAttr('style');
        });

        $(document).on('keypress', '.dpr-submit-form-fields-c-name, .dpr-submit-form-fields-c-email ', function (event) {
            $(this).parent().removeAttr('style');
        });

        $('.dpr-join-form .dpr-join-form-area .comment-toggle').on('click', function () {
            if ($('.dpr-submit-form-wrap').hasClass('hidden-form')) {
                $('.dpr-submit-form-wrap').css('display', 'block').removeClass('hidden-form');
                $('.dpr-join-form-login-register .dpr-discu-submit').css('display', 'inline-block');
                tinymce.execCommand('mceFocus', false, 'dpr-add-editor');
                // set google recaptcha
                if (typeof grecaptcha != 'undefined' && dpr.captcha === 'google' && dpr.logged_in === 'no') {
                    if (dpr.recaptcha) {
                        setTimeout(() => {
                            grecaptcha.render('dpr-submit-captcha', {
                                'sitekey': dpr.recaptcha,
                                'theme': dpr.recaptcha_theme,
                                'size': dpr.recaptcha_size
                            });
                        }, 500);
                    }
                }
            } else {
                $('.dpr-submit-form-wrap').css('display', 'none').addClass('hidden-form');
                $('.dpr-join-form-login-register .dpr-discu-submit').css('display', 'none');
            }
        });

        $('.dpr-join-form input').keypress(function (e) {            
            if ((e.keyCode == 13) && (e.target.type != "textarea")) {

                e.preventDefault();
                $('.dpr-discu-submit').trigger('click');
            }
        });

    });
    /**
     *  End tiny dom actions
     */

    /**
     *  Start tiny MCE Config
     */
    function dpr_tinymce(selector, height, status, size) {
        dc_image = '';
        if (typeof dc_use_images == 'undefined') {
            var dc_use_images = 'off';
        }
        // if (typeof dc_use_tinymce == 'undefined') {
        //     var dc_use_tinymce = 'off';
        // }
        if (dc_use_images == 'on') {
            dc_image = 'image';
        }
        if (dc_use_tinymce != 'on') {
            var tinymce_config = {
                selector: selector,
                remove_linebreaks: true,
                resize: true,
                height: height,
                branding: false,
                statusbar: status,
                plugins: ['paste'],
                theme: 'modern',
                menubar: false,
                toolbar: false

            };
        } else if (dc_use_emoji_tinymce == 'on') {
            var tinymce_config = {
                selector: selector,
                height: height,
                branding: false,
                theme: 'modern',
                plugins: [
                    'paste',
                    'emoji',
                    'link',
                    'hr',
                    'lists',
                    dc_image,
                ],
                // toolbar1: "emoji | italic | bold | strikethrough | underline | hr | link | " + dc_image,
                toolbar: [
                    'emoji bold italic strikethrough blockquote underline hr link unlink numlist styleselect charmap ' + dc_image + ' undo redo ',
                ],
                menubar: false,
                toolbar_items_size: size,
                resize: true,
                statusbar: true
            }
        } else {
            var tinymce_config = {
                selector: selector,
                remove_linebreaks: true,
                statusbar: true,
                height: height,
                branding: false,
                theme: 'modern',
                plugins: [
                    'paste',
                    'emoji',
                    'link',
                    'hr',
                    'lists',
                    dc_image,
                ],

                toolbar: 'bold italic strikethrough blockquote underline hr link unlink numlist styleselect charmap ' + dc_image + ' undo redo ',

                menubar: true,
                toolbar_items_size: size,
                resize: true,
                statusbar: true
            }
        }
        tinymce_config['content_style'] = '#tinymce blockquote {border-left: solid 2px #ddd;padding-left: 5px;} #tinymce pre,#tinymce code {background-color: #eee;border-radius: 2px;padding: 5px 10px;}';
        tinymce_config['setup'] = function (ed) {
            ed.on("keydown", function (e) {
                var key = e.keyCode || e.charCode;

                if (key == 8 || key == 46) {
                    return true;
                }

                var content = ed.getContent();
                var tempContent = content + '-#-';
                if (key == 13) {
                    if (tempContent.search('<br>-#-') > 0) {
                        return false;
                    } else if (tempContent.search(/<p><\/p>-#-/g) > 0) {
                        return false;
                    } else if (tempContent.search(/<p>&nbsp;<\/p>-#-/g) > 0) {
                        return false;
                    } else if (content.match(/<p><br(.*?)><\/p>/g) > 1) {
                        return false;
                    }
                }
            });
        };

        tinymce.init(tinymce_config);
    }
    /**
     *  End tiny MCE Config
     */


    /**
     *  Start form field
     */
    function validate_forms(logged, captcha, selector, html_data, gname, gemail, getcaptcha) {

        // check comment
        if (html_data === '' || html_data === 'undefined') {
            $(selector + '.mce-tinymce').css('border', '1px solid red');
            $(selector + '.mce-tinymce').shake(2, 13, 250);
            remove_mce_style(selector);
            return false;
        } else {
            $(selector + '.mce-tinymce').attr('style', '');
        }

        // validation
        if (logged === 'no') {
            // gname = $( selector + '.dpr-submit-form-fields-c-name').val();
            // gemail = $( selector + '.dpr-submit-form-fields-c-email').val();
            // gwebsite = $( selector + '.dpr-submit-form-fields-c-website').val();
            // getcaptcha = $( selector + '#dpr-submit-captcha .g-recaptcha-response').val();

            // check email
            if (gname === '' || gname === 'undefined') {
                $(selector + '.dpr-submit-form-fieldswrap-name').css('border', '1px solid red');
                $(selector + '.dpr-submit-form-fieldswrap-name').shake(2, 13, 250);
                return false;
            }

            // check name
            if (gemail === '' || gemail === 'undefined') {
                $(selector + '.dpr-submit-form-fieldswrap-email').css('border', '1px solid red');
                $(selector + '.dpr-submit-form-fieldswrap-email').shake(2, 13, 250);
                return false;
            }

            // email validationg
            var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
            if (!testEmail.test(gemail)) {
                $(selector + '.dpr-submit-form-fieldswrap-email').css('border', '1px solid red');
                $(selector + '.dpr-submit-form-fieldswrap-email').shake(2, 13, 250);
                return false;
            }

            // check if captha is on come on runt it
            if (captcha === 'google') {
                // check for google recaptcha
                if ($(selector + '#dpr-submit-captcha .g-recaptcha-response').length) {
                    var response = grecaptcha.getResponse();
                    if (response == 0) {
                        $(selector + '.dpr-submit-form-captcha').css('border', '1px solid red');
                        $(selector + '.dpr-submit-form-captcha').shake(2, 13, 250);
                        return false;
                    }
                }
            }
        }

        return true;
    }


    function remove_form(selector) {

        // check comment
        if (html_data === '' || html_data === 'undefined') {
            $(selector + '.mce-tinymce').css('border', '1px solid red');
            $(selector + '.mce-tinymce').shake(2, 13, 250);
            return false;
        } else {
            $(selector + '.mce-tinymce').attr('style', '');
        }
        gname = $(selector + '.dpr-submit-form-fields').remove();
    }

    function remove_mce_style(input) {
        tinyMCE.activeEditor.on('keypress', function (ed, e) {
            $(input + '.mce-tinymce').css('border', '1px solid #e3e3e3');
        });
    }
    /**
     *  End form field
     */


}(jQuery));