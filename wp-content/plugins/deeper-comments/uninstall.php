<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

function depc_unistall() {
	require_once ( plugin_dir_path( __FILE__ ) . 'lib/depc-loader.php' );
	Depc_Loader::uninstall_plugin();
}

depc_unistall();