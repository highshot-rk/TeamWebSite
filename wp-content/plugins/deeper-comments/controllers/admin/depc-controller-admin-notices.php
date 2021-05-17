<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Admin_Notices extends Depc_Controller_Admin_Settings {

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {

		$this->register_hook_callbacks();
		$this->model = Depc_Model_Admin_Notices::get_instance();

	}

	/**
	 * Register callbacks for actions and filters
	 *
	 * @since    1.0.0
	 */
	public function register_hook_callbacks() {

		Depc_Actions_Filters::add_action( 'admin_notices', $this, 'show_admin_notices' );

	}

	/**
	 * Show admin notices
	 *
	 * @since    1.0.0
	 */
	public function show_admin_notices() {

		return static::get_model()->show_admin_notices();

	}

	/**
	 * Add admin notices
	 *
	 * @since    1.0.0
	 */
	public static function add_admin_notice( $notice_text ) {

		$notice = static::render_template(
			'errors/admin-notice.php',
			array(
				'admin_notice' => esc_attr( $notice_text )
			)
		);

		return static::get_model()->add_admin_notice( $notice );

	}

}