<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */


class Depc_i18n {

	/**
	 * @since    1.0.0.0
	 * @access   private
	 */
	private $domain;

	/**
	 * Load the depc text domain for translation.
	 *
	 * @since    1.0.0.0
	 */
	public function textdomain() {

		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

	/**
	 * Set the domain name..
	 *
	 * @since    1.0.0.0
	 */
	public function set_domain( $domain ) {
		$this->domain = $domain;
	}

}
