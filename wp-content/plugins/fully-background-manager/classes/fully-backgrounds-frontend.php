<?php

final class Fully_background_frontend {

	private static $instance;

	public $color = '';

	public $image = '';

	public $attachment = 'scroll';

	public $repeat = 'repeat';
	
	private $plugin_path;

	public function __construct() {

		add_action( 'after_setup_theme', array( $this, 'add_theme_support' ), 95 );
		
		$this->plugin_path = trailingslashit( plugins_url() )."fully-background-manager/";
		
		add_filter( 'body_class', array( $this, 'fully_background_class' ) );
		
	}
	public function fully_background_class( $classes ) {

		$classes[] = 'fully-background';

		return $classes;

	}	

	public function fbm_background_opacity(){
		return 1;
	}
	public function add_theme_support() {

		if ( !current_theme_supports( 'custom-background' ) )
			return;

		add_action( 'template_redirect', array( $this, 'fully_background_setup' ) );
	}

	public function fully_background_setup() {

		if ( !is_singular() ) {
			return;
		}

		$post    = get_queried_object();
		$post_id = get_queried_object_id();

		if ( !post_type_supports( $post->post_type, 'fully-background-manager' ) ) {
			return;
		}
		
		$this->color = get_post_meta( $post_id, '_fully_background_color', true );

		$is_layout = get_post_meta( $post_id, '_fully_is_layout', true );

		$attachment_id = get_post_meta( $post_id, '_fully_background_image_id', true );
		
		$pattern_image = get_post_meta( $post_id, '_fully_background_pattern', true );
		
		if( $is_layout == "Yes" && $pattern_image!="" ) {

			$image = $this->plugin_path."assets/images/big/".$pattern_image.".jpg";

			$this->image = !empty( $image ) ? esc_url( $image ) : '';
			
		}

		if( $is_layout == "No" ) {
			
			$image = wp_get_attachment_image_src( $attachment_id, 'full' );

			$this->image = !empty( $image ) && isset( $image[0] ) ? esc_url( $image[0] ) : '';

		}

		add_filter( 'theme_mod_background_color', array( $this, 'fbm_background_color' ), 25 );
		add_filter( 'theme_mod_background_image', array( $this, 'fbm_background_image' ), 25 );

		if ( !empty( $this->image ) ) {

			$this->attachment = get_post_meta( $post_id, '_fully_background_attachment', true );
			$this->repeat     = get_post_meta( $post_id, '_fully_background_repeat',     true );

			add_filter( 'theme_mod_background_attachment', array( $this, 'fbm_background_attachment' ), 25 );
			add_filter( 'theme_mod_background_repeat',     array( $this, 'fbm_background_repeat'     ), 25 );
		}
	}

	public function fbm_background_color( $color ) {
		return !empty( $this->color ) ? preg_replace( '/[^0-9a-fA-F]/', '', $this->color ) : $color;
	}

	public function fbm_background_image( $image ) {

		if ( !empty( $this->image ) )
			$image = $this->image;

		elseif ( !empty( $this->color ) )
			$image = '';

		return $image;
	}

	public function fbm_background_repeat( $repeat ) {
		return !empty( $this->repeat ) ? $this->repeat : $repeat;
	}

	public function fbm_background_position_x( $position_x ) {
		return !empty( $this->position_x ) ? $this->position_x : $position_x;
	}

	public function fbm_background_position_y( $position_y ) {
		return !empty( $this->position_y ) ? $this->position_y : $position_y;
	}

	public function fbm_background_attachment( $attachment ) {
		return !empty( $this->attachment ) ? $this->attachment : $attachment;
	}

	public static function get_instance() {

		if ( !self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

Fully_background_frontend::get_instance();

?>
