<?php
/*
Plugin Name: Full Background Image Manager WordPress Plugin
Plugin URI: http://www.perceptionsystem.com
Description: Full Background Image Manager WordPress Plugin allows you to set separate background image of each page  
Version: 2.1
Author: Perception System Pvt.Ltd.
Author URI: http://www.perceptionsystem.com
Contributors: Perception System Pvt.Ltd.
*/
 
 
final class Fully_background {

	private static $instance;

	private $directory_path;

	private $directory_uri;

	public function __construct() {
		
		add_action( 'plugins_loaded', array( $this, 'path_setup' ), 1 );

		add_action( 'plugins_loaded', array( $this, 'class_includes' ), 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ), 3 );
		
		add_action( 'wp_enqueue_scripts', array( $this, 'register_css_front' ), 4 );

		add_action( 'init', array( $this, 'support_post_type' ) );
		
		register_activation_hook( __FILE__, array( __CLASS__, 'add_capability' ) );

		add_theme_support( 'custom-background' );
	}

	public function path_setup() {
		$this->directory_path = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->directory_uri  = trailingslashit( plugin_dir_url(  __FILE__ ) );
	}

	public function class_includes() {

		if ( !is_admin() ) {
			require_once( $this->directory_path."classes/fully-backgrounds-frontend.php" );
		}

		if ( is_admin() ) {
			require_once( $this->directory_path."classes/fully-backgrounds-admin.php" );
		}
	}
	public static function add_capability() {
		$role = get_role( 'administrator' );

		if ( !empty( $role ) ) {
			$role->add_cap( 'fbm_edit_background' );
		}
	}

	public function support_post_type() {
		add_post_type_support( 'post', 'fully-background-manager' );
		add_post_type_support( 'page', 'fully-background-manager' );
	}

	public function register_scripts() {

		if(is_admin()) {
			wp_register_style( 'fully-background-manager-css', $this->directory_uri."assets/css/fbm.css");
			wp_enqueue_style( 'fully-background-manager-css' );
			wp_register_script('fully-background-manager', $this->directory_uri."assets/js/fully-background.min.js", array( 'wp-color-picker', 'media-views' ),'',true);
			wp_enqueue_script('fbm_js', $this->directory_uri."assets/js/fbm.js", array( 'jquery' ),'',true);
		}

	}
	
	public function register_css_front() {
		wp_register_style( 'front-css', $this->directory_uri."assets/css/fbm_front.css");
		wp_enqueue_style( 'front-css' );
	}

	public static function get_instance() {
		if ( !self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

Fully_background::get_instance();

?>
