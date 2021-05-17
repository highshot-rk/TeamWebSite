<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Module_Login extends Depc_Controller_Public_Comment {

	private $settings;

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {

		$this->model = Depc_Model_Module_Login::get_instance();

		// // get quick register and login options
		$this->settings['quick_login'] 		= Depc_Core::get_option( 'dc_quick_login', 'Login_Register' );
		$this->settings['quick_register']  	= Depc_Core::get_option( 'dc_quick_register', 'Login_Register' );


 	}

	public function load_login(){

		$scripts = static::get_model()->get_script();
		echo static::render_template(
			'tpl/login.php',
			array(
				'scripts'       => $scripts,
				'settings'      => $this->settings
				),
			'always'
			);

	}

}