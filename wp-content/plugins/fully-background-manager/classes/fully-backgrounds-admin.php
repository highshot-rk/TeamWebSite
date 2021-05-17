<?php

final class Fully_background_admin{

	private static $instance;
	
	private $plugin_path;

	public $theme_has_callback = false;

	public function __construct() {

		if ( !current_user_can( 'fbm_edit_background' ) && !current_user_can( 'edit_theme_options' ) )
			return;
		add_action( 'load-post.php',     array( $this, 'fbm_functions' ) );
		add_action( 'load-post-new.php', array( $this, 'fbm_functions' ) );

		$this->plugin_path = trailingslashit( plugins_url() )."fully-background-manager/";
	}

	public function fbm_functions() {
		$screen = get_current_screen();
		if ( !current_theme_supports( 'custom-background' ) || !post_type_supports( $screen->post_type, 'fully-background-manager' ) )
			return;

		$wp_head_callback = get_theme_support( 'fully-background-manager', 'wp-head-callback' );

		$this->theme_has_callback = empty( $wp_head_callback ) || 'fully_background_callback' === $wp_head_callback ? false : true;

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		add_action( 'save_post', array( $this, 'save_post_type' ), 10, 2 );
	}

	public function enqueue_scripts( $hook_suffix ) {

		if ( !in_array( $hook_suffix, array( 'post-new.php', 'post.php' ) ) )
			return;

		wp_localize_script(
			'fully-background-manager',
			'fbm_custom_backgrounds',
			array(
				'title'  => __( 'Set Background Image', 'fully-background-manager' ),
				'button' => __( 'Set background image', 'fully-background-manager' )
			)
		);

		wp_enqueue_script( 'fully-background-manager' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	function add_meta_boxes( $post_type ) {
		add_meta_box(
			'fbm-fully-background-manager',
			__( 'Fully Background', 'fully-background-manager' ),
			array( $this, 'do_meta_box' ),
			$post_type,
			'side',
			'core'
		);
	}

	function do_meta_box( $post ) {
		
		$is_layout = get_post_meta( $post->ID, '_fully_is_layout', true );
		
		if( empty($is_layout) ) {
			add_post_meta( $post->ID, '_fully_is_layout', 'Yes', true );
		}
		
		$color = trim( get_post_meta( $post->ID, '_fully_background_color', true ), '#' );

		$attachment_id = get_post_meta( $post->ID, '_fully_background_image_id', true );
		
		$pattern_id = get_post_meta( $post->ID, '_fully_background_pattern', true );
		
		$is_layout = get_post_meta( $post->ID, '_fully_is_layout', true );
		
		$cbEnable = ($is_layout=="Yes") ? 'selected' : '';
		$cbDisable = ($is_layout=="No") ? 'selected' : '';
		$radio1 = ($is_layout=="Yes") ? 'checked' : '';
		$radio2 = ($is_layout=="No") ? 'checked' : '';
		$layout = ($is_layout=="Yes") ? '' : 'style="display:none;"';
		$bgimage = ($is_layout=="No") ? '' : 'style="display:none;"';
		
		$image1 = ($pattern_id=="image1") ? 'class="image"' : '';
		$image2 = ($pattern_id=="image2") ? 'class="image"' : '';
		$image3 = ($pattern_id=="image3") ? 'class="image"' : '';
		$image4 = ($pattern_id=="image4") ? 'class="image"' : '';
		$image5 = ($pattern_id=="image5") ? 'class="image"' : '';
		$image6 = ($pattern_id=="image6") ? 'class="image"' : '';
		$image7 = ($pattern_id=="image7") ? 'class="image"' : '';
		$image8 = ($pattern_id=="image8") ? 'class="image"' : '';

		if ( !empty( $attachment_id ) )
			$image = wp_get_attachment_image_src( absint( $attachment_id ), 'post-thumbnail' );
		$url = !empty( $image ) && isset( $image[0] ) ? $image[0] : '';

		$attachment = get_post_meta( $post->ID, '_fully_background_attachment', true );
		$repeat     = get_post_meta( $post->ID, '_fully_background_repeat',     true );

		$theme_attachment = get_theme_mod( 'background_attachment', 'scroll' );
		$theme_repeat     = get_theme_mod( 'background_repeat',     'repeat' );

		$attachment = !empty( $attachment ) ? $attachment : $theme_attachment;
		$repeat     = !empty( $repeat )     ? $repeat     : $theme_repeat;

		$attachment_options = array( 
			'scroll' => __( 'Scroll', 'fully-background-manager' ), 
			'fixed'  => __( 'Fixed',  'fully-background-manager' ),
		);
		
		$repeat_options = array( 
			'no-repeat' => __( 'No Repeat',           'fully-background-manager' ), 
			'repeat'    => __( 'Repeat',              'fully-background-manager' ),
			'repeat-x'  => __( 'Repeat Horizontally', 'fully-background-manager' ),
			'repeat-y'  => __( 'Repeat Vertically',   'fully-background-manager' ),
		);

		?>

		<?php wp_nonce_field( plugin_basename( __FILE__ ), 'fbm_meta_nonce' ); ?>
		<input type="hidden" name="fbm-background-image" id="fbm-background-image" value="<?php echo esc_attr( $attachment_id ); ?>" />
		<input type="hidden" name="fbm-pattern" id="fbm-pattern" value="<?php echo esc_attr( $pattern_id ); ?>" />
		<p>
			<label for="fbm-background-color"><?php _e( 'Color', 'fully-background-manager' ); ?></label>
			<input type="text" name="fbm-background-color" id="fbm-backround-color" class="fbm-wp-color-picker" value="#<?php echo esc_attr( $color ); ?>" />
		</p>
		<div class="field switch">
			<h4 class="hndle"><span>Would you like layout in background</span></h4>
			<input type="radio" id="radio1" class="fbm-hidden" name="is_layout" value="Yes" <?php echo $radio1; ?> />
			<input type="radio" id="radio2" class="fbm-hidden" name="is_layout" value="No" <?php echo $radio2; ?> />
			<label for="radio1" class="cb-enable <?php echo $cbEnable; ?>"><span>Yes</span></label>
			<label for="radio2" class="cb-disable <?php echo $cbDisable; ?>"><span>No</span></label>
		</div>	
		<div class="clear">&nbsp;</div>	
		<div id="layout" <?php echo $layout; ?>>
			<label for="fbm-layout"><?php _e( 'Layout', 'fully-background-manager' ); ?></label>
			<div class="patterns">
				<a id="image1" href="javascript:void(0);" <?php echo $image1; ?>>
					<img width="40" height="40" alt="" src="<?php echo $this->plugin_path."assets/images/image1.jpg"; ?>"></a>
				<a id="image2" href="javascript:void(0);" <?php echo $image2; ?>>
					<img width="40" height="40" alt="" src="<?php echo $this->plugin_path."assets/images/image2.jpg"; ?>"></a>
				<a id="image3" href="javascript:void(0);" <?php echo $image3; ?>>
					<img width="40" height="40" alt="" src="<?php echo $this->plugin_path."assets/images/image3.jpg"; ?>"></a>
				<a id="image4" href="javascript:void(0);" <?php echo $image4; ?>>
					<img width="40" height="40" alt="" src="<?php echo $this->plugin_path."assets/images/image4.jpg"; ?>"></a>
				<a id="image5" href="javascript:void(0);" <?php echo $image5; ?>>
					<img width="40" height="40" alt="" src="<?php echo $this->plugin_path."assets/images/image5.jpg"; ?>"></a>
				<a id="image6" href="javascript:void(0);" <?php echo $image6; ?>>
					<img width="40" height="40" alt="" src="<?php echo $this->plugin_path."assets/images/image6.jpg"; ?>"></a>
				<a id="image7" href="javascript:void(0);" <?php echo $image7; ?>>
					<img width="40" height="40" alt="" src="<?php echo $this->plugin_path."assets/images/image7.jpg"; ?>"></a>
				<a id="image8" href="javascript:void(0);" <?php echo $image8; ?>>
					<img width="40" height="40" alt="" src="<?php echo $this->plugin_path."assets/images/image8.jpg"; ?>"></a>
			</div>
			<div class="clear"><input type="button" id="clear-pattern" value="Clear Layout" class="widefat button" /></div>
        </div>
        <div id="background-image" <?php echo $bgimage; ?>>
		<p>
			<a href="#" class="fbm-add-media fbm-add-media-img"><img class="fbm-background-image-url" src="<?php echo esc_url( $url ); ?>" style="max-width: 100%; max-height: 200px; display: block;" /></a>
			<a href="#" class="fbm-add-media fbm-add-media-text"><?php _e( 'Set background image', 'fully-background-manager' ); ?></a> 
			<a href="#" class="fbm-remove-media"><?php _e( 'Remove background image', 'fully-background-manager' ); ?></a>
		</p>
		<div class="fbm-background-image-options">
			<p>
				<label for="fbm-attachment"><?php _e( 'Attachment', 'fully-background-manager' ); ?></label>
				<select class="widefat" name="fbm-attachment" id="fbm-attachment">
				<?php foreach( $attachment_options as $option => $label ) { ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $attachment, $option ); ?> /><?php echo esc_html( $label ); ?></option>
				<?php } ?>
				</select>
			</p>
			<p>
				<label for="fbm-repeat"><?php _e( 'Repeat', 'fully-background-manager' ); ?></label>
				<select class="widefat" name="fbm-repeat" id="fbm-repeat">
				<?php foreach( $repeat_options as $option => $label ) { ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $repeat, $option ); ?> /><?php echo esc_html( $label ); ?></option>
				<?php } ?>
				</select>
			</p>
		</div>
		</div>
	<?php }
	function save_post_type( $post_id, $post ) {

		if ( !isset( $_POST['fbm_meta_nonce'] ) || !wp_verify_nonce( $_POST['fbm_meta_nonce'], plugin_basename( __FILE__ ) ) )
			return;

		$post_type = get_post_type_object( $post->post_type );

		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		if ( 'revision' == $post->post_type )
			return;

		$color = preg_replace( '/[^0-9a-fA-F]/', '', $_POST['fbm-background-color'] );

		$image_id = absint( $_POST['fbm-background-image'] );
		
		$pattern_id = esc_attr( $_POST['fbm-pattern'] );
		
		$is_layout = esc_attr( $_POST['is_layout'] );

		if ( 0 >= $image_id ) {

			$repeat = $position_x = $position_y = $attachment = '';

		} else {

			if ( !empty( $image_id ) ) {

				$is_fully_header = get_post_meta( $image_id, '_wp_attachment_is_fully_background', true );

				if ( $is_fully_header !== get_stylesheet() )
					update_post_meta( $image_id, '_wp_attachment_is_fully_background', get_stylesheet() );
			}

			$attachmentArr = array( 'scroll', 'fixed' );
			$repeatArr     = array( 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' );
			$attachment = in_array( $_POST['fbm-attachment'], $attachmentArr ) ? $_POST['fbm-attachment'] : '';
			$repeat     = in_array( $_POST['fbm-repeat'],     $repeatArr )     ? $_POST['fbm-repeat']     : '';
		}

		$meta = array(
			'_fully_is_layout'			   => $is_layout,
			'_fully_background_pattern'    => $pattern_id,
			'_fully_background_color'      => $color,
			'_fully_background_image_id'   => $image_id,
			'_fully_background_repeat'     => $repeat,
			'_fully_background_attachment' => $attachment
		);

		foreach ( $meta as $meta_key => $new_meta_value ) {

			$meta_value = get_post_meta( $post_id, $meta_key, true );

			if ( $new_meta_value && $meta_value == '' )
				add_post_meta( $post_id, $meta_key, $new_meta_value, true );

			elseif ( $new_meta_value && $new_meta_value != $meta_value )
				update_post_meta( $post_id, $meta_key, $new_meta_value );

			elseif ( '' == $new_meta_value && $meta_value )
				delete_post_meta( $post_id, $meta_key, $meta_value );
		}
	}

	public static function get_instance() {

		if ( !self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

Fully_background_admin::get_instance();

?>
