<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Loader {

	/**
	 * DEPC Instance
	 */
	private static $instance;


	/**
	 * DEPC Instance
	 */
	public $name;

	/**
	 * Provides access to a single instance of a module using the singleton pattern
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {

		spl_autoload_register( array( $this, 'depc_load' ) );

		$this->set_locale();
		$this->register_hook_callbacks();

	}

	public function __get( $var ) {
		return $this->name = $var;
	}

	/**
	 * Loads all Plugin dependencies
	 *
	 * @since    1.0.0
	 */
	private function depc_load( $class ) {

		if ( false !== strpos( $class, Depc_Core::CLASS_PREFIX ) ) {

			$cname = str_replace( '_', '-', strtolower( $class ) ) . '.php';
			$folder        = '/';

			if ( false !== strpos( $class, '_Admin' ) ) {
				$folder .= 'admin/';
			}
			if ( false !== strpos( $class, '_Comment' ) ) {
				$folder .= 'temp/';
			}

			if ( false !== strpos( $class, '_Module' ) ) {
				$folder .= 'module/';
			}

			if ( false !== strpos( $class, Depc_Core::CLASS_PREFIX . 'Controller' ) ) {
				$path = Depc_Core::get_depc_path() . 'controllers' . $folder . $cname;
				require_once( $path );
			} elseif ( false !== strpos( $class, Depc_Core::CLASS_PREFIX . 'Model' ) ) {
				$path = Depc_Core::get_depc_path() . 'models' . $folder . $cname;
				require_once( $path );
			} else {
				$path = Depc_Core::get_depc_path() . 'lib/' . $cname;
				require_once( $path );
			}

		}

	}

	/**
	 * Define the locale for depc for internationalization.
	 *
	 * @since    1.0.0
	 */
	private function set_locale() {

		$plugin_i18n = new Depc_i18n();
		$plugin_i18n->set_domain( 'depc' );

		Depc_Actions_Filters::add_action( 'plugins_loaded', $plugin_i18n, 'textdomain' );

	}

	/**
	 * Register callbacks for actions and filters
	 *
	 * @since    1.0.0
	 */
	public function register_hook_callbacks() {

		register_activation_hook(   Depc_Core::get_depc_path() . 'deeper-comments.php', array( $this, 'activate' ) );
		register_deactivation_hook( Depc_Core::get_depc_path() . 'deeper-comments.php', array( $this, 'deactivate' ) );
		Depc_Controller_Admin_Inapp::get_instance();
		register_deactivation_hook( 'init', $this, 'activate' );

	}


	/**
	 * Prepares sites to use the plugin during single or network-wide activation
	 *
	 * @since    1.0.0
	 * @param bool $network_wide
	 */
	public function activate() {


		$option_keys =  $this->option_setting_keys();
		$count = count( $option_keys );
		for ( $i=0; $i < $count ; $i++ ) {
			$funcname = $option_keys[$i];
			if(!method_exists($this, $funcname)) {
				continue;
			}
			$options = $this->$funcname();
			foreach ( $options as $option => $optionfield ) {
				if ( get_option( $option ) == false ) {
					update_option(  $option , $optionfield );
				}
			}
		}

	}


	/**
	 * [option_setting_keys all of they option keys]
	 * @return [array]
	 */
	private function option_setting_keys() {
		$options = array( 'Comments', 'Inappropriate_Comments', 'Social_Share', 'Voting', 'Avatar', 'Login_Register', 'Google', 'Recaptcha', 'Comment_Sorting_Bar', 'Load_More', 'Skin' );

		return $options;
	}


	/**
	 * [Comments comment setting default options]
	 * @return [array]]
	 */
	public function Comments() {

		$options = array(
			'Comments' => array(
				'dc_delete_member' 		=> 'on',
				'dc_edit_member'		=> 'on',
				'dc_edit_expiration' 	=> '10',
				'dc_link_member' 		=> 'on',
				'dc_link_guest' 		=> 'on',
				'dc_collapse_member' 	=> 'on',
				'dc_collapse_guest' 	=> 'on',
				'dc_allow_guest_comment'=> 'on'
			)
		);

		return $options;
	}


	/**
	 * [inappropriate_comments inapp settings]
	 * @return [array]
	 */
	private function inappropriate_comments() {

		$options = array(
			'Inappropriate_Comments' => array(
				'dc_inappropriate_members' 	=> 'on',
				'dc_inappropriate_guest' 	=> 'on',
				'dc_inapp_auto_ban' 		=> '5'
			)
		);

		return $options;
	}


	/**
	 * [social_share social options default]
	 * @return [array]
	 */
	private function social_share() {

		$options = array(
			'Social_Share'	=> array(
				'dc_social_enable' 		=> 'on',
				'dc_social_share_fb' 	=> 'off',
				'dc_social_share_vk' 	=> 'off',
				'dc_social_share_tumblr' 	=> 'off',
				'dc_social_share_pinterest' 	=> 'off',
				'dc_social_share_getpocket' 	=> 'off',
				'dc_social_share_reddit' 	=> 'off',
				'dc_social_share_whatsapp' 	=> 'off',
				'dc_social_share_telegram' 	=> 'off',
				'dc_social_share_tw' 	=> 'on',
				'dc_social_share_mail' 	=> 'on'
			)
		);

		return $options;
	}


	/**
	 * [vote_options vote option defaults]
	 * @return [array]
	 */
	private function Voting() {

		$options = array(
			'Voting'	=> array(
				'dc_vote_user_enable' 	=> 'on',
				'dc_vote_user_date' 	=> '10',
				'dc_vote_guest_enable' 	=> 'on',
				'dc_vote_guest_date' 	=> '10'
			)
		);

		return $options;
	}


	/**
	 * [avatar avatar deafaults option]
	 * @return [array]
	 */
	private function avatar() {

		$options = array(
			'Avatar'	=> array(
				'dc_generate_avatar' 	=> 'on'
			)
		);

		return $options;
	}


	/**
	 * [login_register login register options]
	 * @return [array]
	 */
	private function login_register() {

		$options = array(
			'Login_Register'		=> array(
				'dc_quick_login' 	=> 'on',
				'dc_quick_register' => 'on',
				'dc_term_onoff' => 'off'
			)
		);

		return $options;
	}


	/**
	 * [google goolg option defaults]
	 * @return [array]
	 */
	private function google() {

		$options = array(
			'Google'	=> array(
				'dc_google_login_enable' 		=> 'off',
				'dc_social_login_g_appname' 	=> '',
				'dc_social_login_g_client' 		=> '',
				'dc_social_login_g_secret' 		=> ''
			)
		);

		return $options;
	}


	/**
	 * [recaptcha option default]
	 * @return [array]
	 */
	private function recaptcha() {

		$options = array(
			'Recaptcha'	=> array(
				'dc_recaptcha_type' 		=> 'none',
				'dc_recptcha_gsitekey' 		=> '',
				'dc_recptcha_gsecretkey' 	=> '',
				'dc_recaptcha_addcm' 		=> 'off',
				'dc_recaptcha_theme' 		=> 'light',
				'dc_recaptcha_size' 		=> 'normal'
			)
		);

		return $options;
	}


	/**
	 * [Skin option default]
	 * @return [array]
	 */
	private function skin() {

		$options = array(
			'Skin'	=> array(
				'dc_skins' 		=> 'default',
				'dc_dpr_discu_theme_mode' 		=> 'light'
			)
		);

		return $options;
	}


	/**
	 * [comment_filter_bar option defaults]
	 * @return [array]
	 */
	private function comment_filter_bar() {

		$options = array(
			'Comment_Sorting_Bar'	=> array(
				'dc_enable_filter_member' 	=> 'off',
				'dc_enable_filter_guest' 	=> 'off',
				'dc_default_sorting' 		=> 'newest',
				'dc_enable_filter_count'	=> '5'
			)
		);

		return $options;
	}


	/**
	 * [load_more option defaults]
	 * @return [array]
	 */
	private function load_more() {

		$options = array(
			'Load_More'	=> array(
				'dc_enable_loadmore' 		=> 'off',
				'dc_defultcnt_comment' 	    => '10',
				'dc_enable_loadmore_count' 	=> '5'
			)
		);

		return $options;
	}

	/**
	 * Rolls back activation procedures when de-activating the plugin
	 *
	 * @since    1.0.0
	 */
	public function deactivate() {

		Depc_Model_Admin_Notices::remove_admin_notices();

	}

	/**
	 * Fired when user uninstalls the plugin, called in unisntall.php file
	 *
	 * @since    1.0.0
	 */
	public static function uninstall_plugin() {

		require_once dirname( plugin_dir_path( __FILE__ ) ) . '/lib/depc-core.php';
		require_once dirname( plugin_dir_path( __FILE__ ) ) . '/models/depc-model.php';
		require_once dirname( plugin_dir_path( __FILE__ ) ) . '/models/admin/depc-model-admin.php';
		require_once dirname( plugin_dir_path( __FILE__ ) ) . '/models/admin/depc-model-admin-settings.php';

		Depc_Model_Admin_Settings::delete_settings();
	}
}
