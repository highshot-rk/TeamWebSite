;
(function ($) {
	$(document).ready(function () {
		var form = $('#loginform, #registerform, #setupform'),
			buttons = $('.depc_login_button'),
			position;

		form.append('<div style="clear:both;"></div>')

		buttons.each(function () {
			position = $(this).data('depc-position');
			if ('top' == position) {
				$('.depc_buttons_block').prependTo(form);
				$('.depc_buttons_block').last().css("margin-bottom", "30px")
			} else if ('bottom' == position) {
				$('.depc_buttons_block').appendTo(form);
				$('.depc_buttons_block').first().css("margin-top", "30px");
			}
		});

		/* Remember me and redirect functionality */
		$('#depc_google_button, #depc_facebook_button, #depc_twitter_button, #depc_linkedin_button').on('click', function (event) {
			event.preventDefault();
			var depc_url = window.location.search.substr(1);
			var redirect_url = null,
				click_url = $(this).attr('href'),
				tmp = [],
				provider = $(this).data('depc-provider');
			location.search
				.substr(1)
				.split("&")
				.forEach(function (item) {
					tmp = item.split("=");
					if ('redirect_to' === tmp[0]) {
						redirect_url = decodeURIComponent(tmp[1]);
					}
				});

			if (!redirect_url && !depc_ajax.is_login_page) {
				redirect_url = window.location.href;
			}
			var remember_checked = $('.forgetmenot input[name="rememberme"]').is(':checked');

			/* Get redirect URI */
			if (click_url || redirect_url || remember_checked) {
				ajax_data = {
					'action': 'depc_remember',
					'depc_provider': provider,
					'depc_nonce': depc_ajax.depc_nonce
				};
				if (redirect_url) {
					ajax_data.depc_url = redirect_url;
				}
				if (remember_checked) {
					ajax_data.depc_remember = 'true';
				}

				$.ajax({
					url: depc_ajax.ajaxurl,
					type: 'POST',
					data: ajax_data,
					success: function (auth_url) {
						window.location.href = click_url;
					},
					error: function () {
						window.location.reload();
					}
				});
			}
		});

	});
})(jQuery);
