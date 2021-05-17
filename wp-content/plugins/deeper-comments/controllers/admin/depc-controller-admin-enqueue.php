<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Admin_Enqueue extends Depc_Controller_Admin {

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {

		$this->register_hook_callbacks();

	}

	/**
	 * Register callbacks for actions and filters
	 *
	 * @since    1.0.0
	 */
	protected function register_hook_callbacks() {

		Depc_Actions_Filters::add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles' );
		Depc_Actions_Filters::add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style(
			Depc_Core::DEPC_ID . '_admin',
			Depc_Core::get_depc_url() . 'views/admin/css/' . Depc_Core::DEPC_ID . '_admin.css',
			array(),
			Depc_Core::DEPC_VERSION,
			'all'
		);

		wp_enqueue_style(
			'deeper-icon',
			Depc_Core::get_depc_url() . 'views/css/package/iconfonts.css',
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
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_style(
			Depc_Core::DEPC_ID . 'confirm',
			Depc_Core::get_depc_url() . 'views/css/package/jquery-confirm.css',
			array(),
			Depc_Core::DEPC_VERSION,
			'all'
		);

		wp_enqueue_script(
			Depc_Core::DEPC_ID .'jconfirm',
			Depc_Core::get_depc_url() . 'views/js/package/jquery-confirm.js',
			array( 'jquery' ),
			Depc_Core::DEPC_VERSION,
			false
		);

		wp_enqueue_script(
			Depc_Core::DEPC_ID . '_admin',
			Depc_Core::get_depc_url() . 'views/admin/js/' . Depc_Core::DEPC_ID . '_admin.js',
			array( 'jquery' ),
			Depc_Core::DEPC_VERSION,
			false
		);

		wp_localize_script( Depc_Core::DEPC_ID . '_admin' , 'dpr_admin', array(
			'adminajax'	=> admin_url( 'admin-ajax.php' ),
			'security'	=> wp_create_nonce( 'dpr_admin_nonce' ),
			'reset'	=> __('Reset', 'depc'),
			'resetContent'	=> __('Are you sure you want to confirm the "reset action" for the Deeper Comments Settings?', 'depc'),
			'resetTitle'	=> __('Reset The Deeper Comments Settings', 'depc'),
			'cancel'	=> __('Cancel', 'depc'),
		));

		if(isset($_GET['page']) && $_GET['page'] === 'deeper_settings') {
			wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
			wp_enqueue_script( 'js-code-editor');
		}
	}

}