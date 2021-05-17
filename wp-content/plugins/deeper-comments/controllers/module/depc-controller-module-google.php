<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Module_Google extends Depc_Controller_Public_Comment {

	private $settings;

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {
		$this->model = Depc_Model_Module_Google::get_instance();
		//get social logins
		$this->settings['fb_login_enable'] 		= Depc_Core::get_option( 'dc_fb_login_enable', 'disscustion_settings' );
		$this->settings['tw_login_enable']  	= Depc_Core::get_option( 'dc_tw_login_enable', 'disscustion_settings' );
		$this->settings['google_login_enable']  = Depc_Core::get_option( 'dc_google_login_enable', 'Google' );
 	}

 	public static function get_module_google() {
 		if ( ! isset( self::$instance ) ) {
 			self::$instance = new self();
 		}
 		return self::$instance;
 	}

	public function load(){
		return false;
	}

}