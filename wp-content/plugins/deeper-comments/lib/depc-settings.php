<?php


class Depc_Settings {

	private $settings;

	public function __construct() {
		$this->register_hook_callbacks();
	}

	public function register_hook_callbacks() {
		Depc_Actions_Filters::add_action( 'plugins_loaded', $this, 'get_user_option' );
	}

	public function get_user_option() {

		$this->settings['memberfilter'] = Depc_Core::get_option( 'dc_enable_filter_member', 'Comment_Sorting_Bar' );
		$this->settings['guestfilter']  = Depc_Core::get_option( 'dc_enable_filter_guest', 'Comment_Sorting_Bar' );
		$this->settings['default_sorting']  = Depc_Core::get_option( 'dc_default_sorting', 'Comment_Sorting_Bar' );
		$this->settings['filter_count']  = Depc_Core::get_option( 'dc_enable_filter_count', 'Comment_Sorting_Bar' );
		$this->settings['generate_avatar']  = Depc_Core::get_option( 'dc_generate_avatar', 'Avatar' );
		// $this->settings['defultcnt_comment']  = Depc_Core::get_option( 'dc_defultcnt_comment', 'Comments' );
		if(empty( $this->settings['defultcnt_comment'] ) && get_option('page_comments')) {
			$this->settings['defultcnt_comment'] = get_option('comments_per_page');
		}
		$this->settings['loadmore']  = Depc_Core::get_option( 'dc_enable_loadmore', 'Load_More' );
		$this->settings['loadmore_count']  = Depc_Core::get_option( 'dc_enable_loadmore_count', 'Load_More' );
		$this->settings['dc_defultcnt_comment']  = Depc_Core::get_option( 'dc_defultcnt_comment', 'Load_More' );
		$this->settings['skins']  = Depc_Core::get_option( 'dc_skins', 'Skin' );
		$this->settings['theme_mode']  = Depc_Core::get_option( 'dc_dpr_discu_theme_mode', 'Skin' );
		$this->settings['term_pages']  = Depc_Core::get_option( 'dc_term_pages', 'Login_Register' );
		$this->settings['term_onoff']  = Depc_Core::get_option( 'dc_term_onoff', 'Login_Register' );
		$this->settings['recaptcha'] = Depc_Core::get_option( 'dc_recaptcha_type', 'Recaptcha' );
		$this->settings['recptcha_gsitekey']  = Depc_Core::get_option( 'dc_recptcha_gsitekey', 'Recaptcha' );
		$this->settings['captcha_theme']  = Depc_Core::get_option( 'dc_recaptcha_theme', 'Recaptcha' );
		$this->settings['captcha_size']  = Depc_Core::get_option( 'dc_recaptcha_size', 'Recaptcha' );
		$this->settings['share_fb']  = Depc_Core::get_option( 'dc_social_share_fb', 'Social_Share' );
		$this->settings['share_vk']  = Depc_Core::get_option( 'dc_social_share_vk', 'Social_Share' );
		$this->settings['share_tumblr']  = Depc_Core::get_option( 'dc_social_share_tumblr', 'Social_Share' );
		$this->settings['share_pinterest']  = Depc_Core::get_option( 'dc_social_share_pinterest', 'Social_Share' );
		$this->settings['share_getpocket']  = Depc_Core::get_option( 'dc_social_share_getpocket', 'Social_Share' );
		$this->settings['share_reddit']  = Depc_Core::get_option( 'dc_social_share_reddit', 'Social_Share' );
		$this->settings['share_whatsapp']  = Depc_Core::get_option( 'dc_social_share_whatsapp', 'Social_Share' );
		$this->settings['share_telegram']  = Depc_Core::get_option( 'dc_social_share_telegram', 'Social_Share' );
		// $this->settings['share_fb_id']  = Depc_Core::get_option( 'dc_social_share_fb_id', 'Social_Share' );

		return $this->settings;
	}
}