(function ($) {
	'use strict';

	/**
	 *  Start dpr backend
	 */
	$(document).ready(function ($) {

		$(document).on('click', 'li.dpr-be-group-menu-li.dpr-be-group-menu-parent-li.has-sub', function (event) {
			event.preventDefault();
			var $this = $(this);
			if ($this.hasClass('active')) {
				return false;
			} else {
				$('li.dpr-be-group-menu-li.dpr-be-group-menu-parent-li.has-sub').each(function () {
					$(this).removeClass('active');
					$(this).attr('style', '');
				})
				$this.addClass('active');
				var height = $this.find('.subsection').first().outerHeight(true);
				var linkHeight = $this.find('.dpr-be-group-tab-link-a').first().outerHeight(true);
				$this.attr('style', 'height:' + (linkHeight + height) + 'px');
				$this.find('ul.subsection .dpr-be-group-menu-li').first().find('a').trigger('click');
			}
		});
		/* Save Setting Ajax */
		$(document).on('click', '.dpr-save-btn', function (event) {
			event.preventDefault();
			$('.dpr-be-content[data-mode="current"] .depc_save_ajax').trigger('submit');
		});

		$(document).on('click', '.dpr-reset-btn', function (event) {
			event.preventDefault();
			$.confirm({
				content: dpr_admin.resetContent,
				title: dpr_admin.resetTitle,
				type: 'red',
				animation: 'bottom',
				closeAnimation: 'top',
				theme: 'modern',
				buttons: {
					ok: {
						text: dpr_admin.reset,
						btnClass: 'btn-primary',
						keys: ['enter'],
						action: function () {
							var activeSection = $('.dpr-be-sidebar li.dpr-be-group-menu-li.dpr-be-group-menu-child-li.active > a').attr('id');
							if (!activeSection) {
								return;
							}

							$.ajax({
								type: 'POST',
								url: dpr_admin.adminajax,
								data: {
									action: 'reset_options',
									security: dpr_admin.security,
									section: activeSection,
								},
								success: function (data) {
									$('.dpr-set-overlay').remove();
									$('.dpr-be-notification').addClass('notice-green');
									$('.dpr-be-notification').slideDown();
									$('.dpr-be-notification').text('Section Settings Has Been Reset Successfully. (Refresh the Page)');
									setTimeout(function () {
										window.location.reload();
									}, 2000);
								},
								error: function (XMLHttpRequest, textStatus, errorThrown) {
									$('.dpr-set-overlay').remove();
									$('.dpr-be-notification').removeClass('notice-green');
									$('.dpr-be-notification').addClass('notice-yellow');
									$('.dpr-be-notification').slideDown();
									$('.dpr-be-notification').text('There was a problem when resetting the settings.');
								}
							});
						}
					},
					cancel: {
						text: dpr_admin.cancel,
						action: function () {}
					}
				}
			});
			return false;
		});

		$(document).on('click', '#dpr-reset-all-settings', function (event) {
			event.preventDefault();
			$.confirm({
				content: dpr_admin.resetContent,
				title: dpr_admin.resetTitle,
				type: 'red',
				animation: 'bottom',
				closeAnimation: 'top',
				theme: 'modern',
				buttons: {
					ok: {
						text: dpr_admin.reset,
						btnClass: 'btn-primary',
						keys: ['enter'],
						action: function () {
							$.ajax({
								type: 'POST',
								url: dpr_admin.adminajax,
								data: {
									action: 'reset_all_options',
									security: dpr_admin.security,
								},
								success: function (data) {
									$('.dpr-set-overlay').remove();
									$('.dpr-be-notification').addClass('notice-green');
									$('.dpr-be-notification').slideDown();
									$('.dpr-be-notification').text('All Settings Has Been Reset Successfully. (Refresh the Page)');
									setTimeout(function () {
										window.location.reload();
									}, 2000);
								},
								error: function (XMLHttpRequest, textStatus, errorThrown) {
									$('.dpr-set-overlay').remove();
									$('.dpr-be-notification').removeClass('notice-green');
									$('.dpr-be-notification').addClass('notice-yellow');
									$('.dpr-be-notification').slideDown();
									$('.dpr-be-notification').text('There was a problem when resetting the settings.');
								}
							});
						}
					},
					cancel: {
						text: dpr_admin.cancel,
						action: function () {}
					}
				}
			});
			return false;
		});

		$(document).on('submit', '.dpr-be-content[data-mode="current"] .depc_save_ajax', function (e) {

			e.preventDefault();

			var form = $(this).serializeArray();

			$(this).closest('.dpr-be-main').prepend('<div class="dpr-set-overlay"></div>');

			$.ajax({
				type: 'POST',
				url: dpr_admin.adminajax,
				data: {
					action: 'update_options',
					form: form,
					security: dpr_admin.security,
				},
				success: function (data) {

					$('.dpr-set-overlay').remove();
					$('.dpr-be-notification').addClass('notice-green');
					$('.dpr-be-notification').slideDown();
					$('.dpr-be-notification').text('All Settings Successfully Saved.');
					setTimeout(function () {
						$('.dpr-be-notification').slideUp();
					}, 2000);

				},

				error: function (XMLHttpRequest, textStatus, errorThrown) {

					$('.dpr-set-overlay').remove();
					$('.dpr-be-notification').removeClass('notice-green');
					$('.dpr-be-notification').addClass('notice-yellow');
					$('.dpr-be-notification').slideDown();
					$('.dpr-be-notification').text('Changes Not Saved.');

				}

			});

		});
	});
	/**
	 *  End dpr backend
	 */

})(jQuery);
(function ($) {
	'use strict';
	$(function () {
		console.log($('textarea[name="Custom_CSS[dc_custom_css]"]').length);
		if ($('textarea[name="Custom_CSS[dc_custom_css]"]').length) {
			var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
			editorSettings.codemirror = _.extend({},
				editorSettings.codemirror, {
					mode: 'css',
					lineNumbers: true,
					indentUnit: 2,
					tabSize: 4,
					autoRefresh: true,
				}
			);
			var cm_editor = wp.codeEditor.initialize($('textarea[name="Custom_CSS[dc_custom_css]"]'), editorSettings);
			$(document).on('keyup', '.CodeMirror-code', function () {
				$('textarea[name="Custom_CSS[dc_custom_css]"]').html(cm_editor.codemirror.getValue());
				$('textarea[name="Custom_CSS[dc_custom_css]"]').trigger('change');
			});
		}
	});
})(jQuery);