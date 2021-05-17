<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

abstract class Depc_Controller_Admin extends Depc_Controller {

	/**
	 * Render a template
	 *
	 * @since    1.0.0
	 */
	protected static function render_template( $default_template_path = false, $variables = array(), $require = 'once' ){

		if ( ! $template_path = locate_template( basename( $default_template_path ) ) ) {
			$template_path = Depc_Core::get_depc_path() . '/views/admin/' . $default_template_path;
		}

		if ( is_file( $template_path ) ) {
			extract( $variables );
			ob_start();
			if ( 'always' == $require ) {
				require( $template_path );
			} else {
				require_once( $template_path );
			}
			$template_content = apply_filters( 'depc_template_content', ob_get_clean(), $default_template_path, $template_path, $variables );
		} else {
			echo $template_path.'<br>';
			$template_content = '';
		}

		return $template_content;
	}

}