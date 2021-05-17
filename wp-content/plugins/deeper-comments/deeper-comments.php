<?php

/**
 *
 * @link              http://webnus.net
 * @since             1.0.0
 * @package           Deeper Comments
 *
 * Plugin Name:       Deeper Comments
 * Plugin URI:        http://deeperforums.com/
 * Description:       Deeper Comments plugin is a revolutionary move in WordPress comment subject that will bring new experience for user which was never like this before. This is a free WordPress plugin which will be replaced for WordPress native commenting system. This plugin has the most modern design methods. It comes with complete set of commenting functions and features.
 * Version:           2.0.2
 * Author:            Webnus
 * Author URI:        http://webnus.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       depc
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action('init', 'dpr_do_output_buffer');

function dpr_do_output_buffer() {
	ob_start();
}

/**
*  Run main steps for deeper comments
*/
class Depc {

	/**
	 *  Php version required
	 */
	const  DEPC_PHP_VERSION = '5.2';

	/**
	 * Wp Version required
	 */
	const  DEPC_REQUIRED_WP_VERSION = '3.6';

	/**
	 * Activation Constructor
	 */
	public function __construct() {

		//Check requirements and load main class
		if ( $this->depc_requirements_needs() ) {

			require_once plugin_dir_path( __FILE__ ) . 'lib/depc-core.php';
			$plugin = Depc_Core::get_instance();

		} else {

			add_action( 'admin_notices', array( &$this, 'depc_requirements_error' ) );
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			deactivate_plugins( plugin_basename( __FILE__ ) );

		}
	}

	/**
	 * Checks if the system requirements are met
	 *
	 * @since    1.0.0
	 * @return bool True if system requirements are met, false if not
	 */
	public function depc_requirements_needs() {

		global $wp_version;

		if ( version_compare( self::DEPC_PHP_VERSION ,  PHP_VERSION, '>' ) ) {
			return false;
		}

		if ( version_compare( $wp_version, self::DEPC_REQUIRED_WP_VERSION, '<' ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Prints an error that the system requirements weren't met.
	 *
	 * @since    1.0.0
	 */
	public function depc_requirements_error() {

		global $wp_version;
		require_once( dirname( __FILE__ ) . '/views/admin/errors/requirements-error.php' );

	}
}

new Depc;