<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Public extends Depc_Controller {

	private $recaptcha;

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {

		$this->register_hook_callbacks();
		$this->user_setting = $this->get_user_settings();
	}

	/**
	 * Register callbacks for actions and filters
	 *
	 * @since    1.0.0
	 */
	protected function register_hook_callbacks() {
		// set scripts and styles
		Depc_Actions_Filters::add_action( 'wp_enqueue_scripts', $this, 'enqueue_styles', 9999 );
		Depc_Actions_Filters::add_action( 'wp_enqueue_scripts', $this, 'enqueue_scripts' );
		Depc_Actions_Filters::add_filter( 'comments_template',  $this, 'comments_template' );

		// get user setting
		$this->recaptcha['recaptcha'] = Depc_Core::get_option( 'dc_recaptcha_type', 'Recaptcha' );
		$this->recaptcha['recptcha_gsitekey']  = Depc_Core::get_option( 'dc_recptcha_gsitekey', 'Recaptcha' );
		$this->recaptcha['captcha_theme']  = Depc_Core::get_option( 'dc_recaptcha_theme', 'Recaptcha' );
		$this->recaptcha['captcha_size']  = Depc_Core::get_option( 'dc_recaptcha_size', 'Recaptcha' );


	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		// wp_enqueue_style(
		// 	Depc_Core::DEPC_ID . 'jquery',
		// 	Depc_Core::get_depc_url() . 'views/css/package/jquery-confirm.css',
		// 	array(),
		// 	Depc_Core::DEPC_VERSION,
		// 	'all'
		// );

		wp_enqueue_style(
			'deeper-icon',
			Depc_Core::get_depc_url() . 'views/css/package/iconfonts.css',
			array(),
			Depc_Core::DEPC_VERSION,
			'all'
		);

		wp_enqueue_style(
			Depc_Core::DEPC_ID,
			Depc_Core::get_depc_url() . 'views/css/deeper.min.css',
			array(),
			Depc_Core::DEPC_VERSION,
			'all'
		);

		if(Depc_Core::get_option( 'dc_dpr_discu_theme_mode', 'Skin' ) === 'dark') {
			wp_enqueue_style(
				'deeper-dark-mode',
				Depc_Core::get_depc_url() . 'views/css/dark-mode.css',
				array(),
				Depc_Core::DEPC_VERSION,
				'all'
			);
		}

		 // Add dynamic style if a single page is displayed
	    if ( is_single() ) {
	        $color_ =  Depc_Core::get_option( 'dc_fields_color', 'Comments' , '#437df9');
	        $custom_css = "

	        .dpr-join-form-login-register a.dpr-discu-submit {
			    background: {$color_} !important;
			    box-shadow: 0 3px 14px -4px {$color_} !important;
			}

			.dpr-switch-tab a.dpr-active-tab , .dpr-switch-tab a:hover ,
				.dpr-join-form-login-register a.dpr-form-active, .dpr-join-form-login-register a:hover, .dpr-user-nas a:hover{
			    color: {$color_} !important;
			}
			.dpr-join-form-login-register a.dpr-discu-submit:hover{
				color:#fff !important;
			}
			.dpr-preloader{
				border-top-color: {$color_} !important;;
				border-bottom-color: {$color_} !important;;
			}
			img.wp-smiley, img.emoji{
				pointer-events: none !important;
			}
			";
	        wp_add_inline_style( 'custom-css', $custom_css );
	    }

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( is_singular() ) {

			if ( comments_open() ) {

				// enque recaptcha if needed
				if ( $this->recaptcha['recaptcha'] == 'google' ) {
					$locale = $this->get_current_language();
					wp_enqueue_script(
						Depc_Core::DEPC_ID . 'recaptcha',
						'//www.google.com/recaptcha/api.js?hl='.str_replace('_', '-', $locale),
						array( 'jquery' ),
						Depc_Core::DEPC_VERSION,
						false
					);
				}

				wp_enqueue_script(
					'dpr_tinymce_js',
					includes_url( 'js/tinymce/' ) . 'wp-tinymce.php',
					array( 'jquery' ),
					false,
					false
				);

				wp_enqueue_script(
					Depc_Core::DEPC_ID .'clipboard' ,
					Depc_Core::get_depc_url() . 'views/js/package/clipboard.js',
					array( 'jquery' ),
					Depc_Core::DEPC_VERSION,
					false
				);

				wp_enqueue_script(
					Depc_Core::DEPC_ID .'jconfirm',
					Depc_Core::get_depc_url() . 'views/js/package/jquery-confirm.js',
					array( 'jquery' ),
					Depc_Core::DEPC_VERSION,
					false
				);

				if ( Depc_Core::get_option( 'dc_use_emoji_tinymce', 'Comments' , 'on') == 'on' ) {
					wp_enqueue_script(
						'deeper_emoji_tinymce',
						Depc_Core::get_depc_url() . '/views/js/plugins/emoji/plugin.min.js',
						array( 'jquery' ),
						Depc_Core::DEPC_VERSION
					);
				}

				wp_enqueue_script(
					Depc_Core::DEPC_ID,
					Depc_Core::get_depc_url() . 'views/js/package/deeper.min.js',
					array( 'jquery' ),
					Depc_Core::DEPC_VERSION,
					false
				);

				$this->inline();
				$this->loclize();
			}
		}

	}

	/**
	 * Show defult comment template
	 *
	 * @since    1.0.0
	 */
	public function comments_template(){

		return Depc_Core::get_depc_path() . '/views/tpl/tpl.php';

	}

	/**
	 * Define some loclization to js.
	 *
	 * @since    1.0.0
	 */
	public function loclize(){

		require_once( ABSPATH . 'wp-includes/pluggable.php' );

		$term_link = '';

		// check if term link is on and selected
		if ( $this->user_setting['term_onoff'] === 'on' ) {
			$term_link = get_page_link( $this->user_setting['term_pages'] );
		}

		// check for not logged in user
		$logged_in = ( is_user_logged_in() ) ? 'yes' : 'no';
		if($logged_in == 'yes') {
			$nl = (\Depc_Core::get_option( 'dc_enable_user_notifications', 'Notifications' , 'on' ) == 'on') ? 'true' : 'false';
		} else {
			$nl = false;
		}
		wp_localize_script( Depc_Core::DEPC_ID , 'dpr', array(
			'editor_title'      	=> __( 'Add Your Comment' , 'depc' ),
			'post_comment'      	=> __( 'Submit' , 'depc' ),
			'cancel'            	=> __( 'Cancel' , 'depc' ),
			'ok'           			=> __( 'ok' , 'depc' ),
			'sure'					=> __( 'sure' , 'depc' ),
			'duplicate'         	=> __( 'Duplicate Comment!' , 'depc' ),
			'spam_error'			=> __( 'Spam Error!' , 'depc' ),
			'error'					=> __( 'Error!' , 'depc' ),
			'save_comment'      	=> __( 'Send' , 'depc' ),
			'empty'     			=> __( 'Please write your comment here.' , 'depc' ),
			'sure_delete'     		=> __( 'Are you sure you want to delete this comment?' , 'depc' ),
			'delete_cm'     		=> __( 'Delete Comment' , 'depc' ),
			'delete'     			=> __( 'Delete' , 'depc' ),
			'name'     				=> __( 'FullName' , 'depc' ),
			'email'     			=> __( 'Email' , 'depc' ),
			'username'     			=> __( 'UserName' , 'depc' ),
			'usernameOrEmail'  		=> __( 'Username / Email Address' , 'depc' ),
			'password'     			=> __( 'Password' , 'depc' ),
			'confirm_password'  	=> __( 'Retype-Password' , 'depc' ),
			'login'     			=> __( 'Login' , 'depc' ),
			'lost_pass'         	=> __( 'Forgot password?' , 'depc' ),
			'signup'         		=> __( 'Signup' , 'depc' ),
			'refresh'         		=> __( 'Refresh' , 'depc' ),
			'flag_cm'         		=> __( 'Inappropriate Comment' , 'depc' ),
			'sure_flag'         	=> __( 'Are you sure you want to mark this comment as inappropriate?' , 'depc' ),
			'nocomment'         	=> __( 'I can\'t find the comment you mean!' , 'depc' ),
			'copy_message'			=> __( 'The comment link  copied successfully!' , 'depc' ),
			'copy_message_title'	=> __( 'Info' , 'depc' ),
			'term_link'         	=> $term_link,
			'lost_url'          	=> wp_lostpassword_url(),
			'adminajax'         	=> admin_url( 'admin-ajax.php' ),
			'rtl'					=> is_rtl(),
			'captcha'				=> $this->recaptcha['recaptcha'],
			'recaptcha'				=> $this->recaptcha['recptcha_gsitekey'],
			'recaptcha_theme'		=> $this->recaptcha['captcha_theme'],
			'recaptcha_size'		=> $this->recaptcha['captcha_size'],
			// 'fbswitch'			=> $this->user_setting['share_fb'],
			'vkswitch'				=> $this->user_setting['share_vk'],
			'tumblrswitch'			=> $this->user_setting['share_tumblr'],
			'getpocketswitch'		=> $this->user_setting['share_getpocket'],
			'pinterestswitch'		=> $this->user_setting['share_pinterest'],
			'redditswitch'			=> $this->user_setting['share_reddit'],
			'whatsappswitch'		=> $this->user_setting['share_whatsapp'],
			'telegramswitch'		=> $this->user_setting['share_telegram'],
			'notification_listener'	=> $nl,
			'logged_in'				=> $logged_in,
			'p_length'				=> get_option('thread_comments_depth'),
		));

	}

	/**
	 * Add inline styles.
	 *
	 * @since    1.0.0
	 */
	private function inline(){
		// get vote inline script
		$vote = Depc_Model_Public_Comment_Vote::get_instance();
		$javascript = $vote::scripts();

		// get comment inline script
		$comment = Depc_Controller_Public_Comment::get_instance();
		$javascript .= $comment->scripts();

		// get filter comment inline script
		$filter = Depc_Controller_Public_Comment_Filter::get_instance();
		$javascript .= $filter->scripts();

		// get load more script
		$filter = Depc_Controller_Public_Comment_Loadmore::get_instance();
		$javascript .= $filter->scripts();

		// make filter for inline script
		apply_filters( 'depc_inline_comment_script', $javascript );
		// add scripts
		wp_add_inline_script( Depc_Core::DEPC_ID , $javascript, 'after' );

	}

	/**
	 * Get current language of WordPress
	 * @author Webnus <info@webnus.biz>
	 * @return string
	 */
	public function get_current_language() {
		return apply_filters('plugin_locale', get_locale(), 'depc');
	}

}