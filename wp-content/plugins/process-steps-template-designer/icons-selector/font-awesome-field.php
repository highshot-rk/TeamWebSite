<?php

  if ( ! class_exists( 'Font_Awesome_Field' ) ) {
    /**
     * Font Awesome Field Class

     **/
    class Font_Awesome_Field {
      /**
       * The availiable icons
       * @var array
       **/
      var $icons;
      /**
       * The screen to get the field
       * @var array
       **/
      var $screens;
      /**
       * Loads up actions and translations for the plugins
       * @return void
      
       **/
      public function __construct()
      {
        // generate the icon array
        $this->generate_icon_array();
        // Set screens
        $this->screens = array('process_posts');
        $posttype = pp_get_current_post_type();
        // These should only be loaded in the admin, and for users that can edit posts
        if (is_admin() && $posttype="process_posts") {

            // Load up the metabox
            add_action('add_meta_boxes', array($this, 'metabox'));
            // Saves the data
            add_action('save_post', array($this, 'save'));
            // Load up plugin styles and scripts
            add_action('admin_enqueue_scripts', array($this, 'styles_and_scripts'));
            // Add a pretty font awesome modal
            add_action('admin_footer', array($this, 'modal'));

          // Load scripts and/or styles in the front-end
          add_action('wp_enqueue_scripts', array($this, 'front_scripts'));

          // Add a shortcode
          add_shortcode('fa', array($this, 'shortcode'));
        }
      }
      /**
       * Font Awesome Shortcode
       * @param array|string $atts Shortcode attributes
       * @return string The formatted shortcode
       * 
       **/
      function shortcode( $atts ) {
        $atts = extract( shortcode_atts( array( 'icon' => '' ), $atts ) );
        if ( ! $icon ) {
          global $post;
          $post_id = $post->ID;
          $icon    = $this->retrieve( $post_id );
          if ( ! $icon ) {
            return;
          }
        }
        return '<i class="fa ' . esc_attr($icon) . '"></i>';
      }
      /**
       * Retrieve an icon from a post
       * @param integer $post_id The post ID
       * @param bool $format Format the output
       * @return string The icon, either formatted as HTML, or just the name
       * 
       **/
      public function retrieve( $post_id = null, $format = false ) {
            if ( ! $post_id ) {
              global $post;
              if ( ! is_object( $post ) ) {
                return;
              }
              $post_id = $post->ID;
            }
            $icon = get_post_meta( $post_id, 'fa_field_icon', true );
            if ( ! $icon ) {
              return;
        }
        if ( $format ) {
          $output = '<i class="fa ' . esc_attr($icon) . '"></i>';
        } else {
          $output =$icon;
        }
        return $output;
      }

      /**
       * Loads scripts and/or styles in the front-end
       * @return void
       * 
       **/
      public function front_scripts() {
        if ( apply_filters( 'fa_field_load_styles', true ) ) {
          wp_enqueue_style( 'font-awesome', COOL_FA_URL . 'css/icons-selector/css/font-awesome.min.css' );
        }
      }
      /**
       * Adds the icon modal
       * @return void Echoes the modal
       * 
       **/
      public function modal() {
        ?>

        <div class="fa-field-modal" id="fa-field-modal" style="display:none">
          <div class="fa-field-modal-close">&times;</div>
          <h1 class="fa-field-modal-title"><?php _e( 'Select Font Awesome Icon', 'fa-field' ); ?></h1>

          <div class="fa-field-modal-icons">
            <?php if ( $this->icons ) : ?>

              <?php foreach ( $this->icons as $icon ) : ?>

                <div class="fa-field-modal-icon-holder" data-icon="<?php echo esc_attr($icon['class']); ?>">
                  <div class="icon">
                    <i class="fa <?php echo esc_attr($icon['class']); ?>"></i>
                  </div>
                  <div class="label">
                    <?php echo esc_attr($icon['class']); ?>
                  </div>
                </div>

              <?php endforeach; ?>

            <?php endif; ?>
          </div>
        </div>

      <?php
      }
      /**
       * Loads up styles and scripts
       * @return void
       * 
       **/
      public function styles_and_scripts() {
        // only load the styles for eligable post types
        if ( in_array( get_current_screen()->post_type, $this->screens ) ) {
          // load up font awesome
          wp_enqueue_style( 'fa-field-fontawesome-css', COOL_FA_URL . 'css/font-awesome/css/all.min.css' );
          // load up plugin css
          wp_enqueue_style( 'fa-field-css', COOL_FA_URL . 'css/fa-field.css' );
          // load up plugin js
          wp_enqueue_script( 'fa-field-js', COOL_FA_URL . 'js/fa-field.js', array( 'jquery' ) );
        }
      }
      /**
       * Loads up actions and translations for the plugins
       * @return void
       * 
       **/
      public function metabox() {
        // which screens to add the metabox to, by default all public post types are added
        //$screens = $this->screens;
        /**
         * // change for all post types
         **/
      //  $screens = get_post_types();
        $screens=array('cool_timeline','process_posts');
        foreach ( $screens as $screen ) {
          add_meta_box( 'fa_field', __( 'Select Step Icon', 'fa-field' ), array(
            $this,
            'populate_metabox'
          ), $screen, 'normal','high' );
        }
      }
      /**
       * Prints metabox content
       * @param object $post The post object
       * @return void Echoes the metabox contents
       * 
       **/
      public function populate_metabox( $post ) {
        $icon = get_post_meta( $post->ID, 'fa_field_icon', true );
        ?>
        <div class="fa-field-metabox">
          <div class="fa-field-current-icon">
            <div class="icon">
              <?php 
              if ( $icon ) : 
                if(strpos($icon, '-o') !==false){
                  $icon="fa ".$icon;
                }else if(strpos($icon, 'fas')!==false || strpos($icon, 'fab') !==false) {
                  $icon=$icon;
                }else{
                  $icon="fa ".$icon;
                } 
                ?>
                <i class="<?php echo esc_attr($icon); ?>"></i>
              <?php endif; ?>
            </div>
            <div class="delete <?php echo esc_attr($icon) ? 'active' : ''; ?>">&times;</div>
          </div>
          <input type="hidden" name="fa_field_icon" id="fa_field_icon" value="<?php echo esc_attr($icon); ?>">
          <?php wp_nonce_field( 'fa_field_icon', 'fa_field_icon_nonce' ); ?>

          <button class="button-primary add-fa-icon"><?php _e( 'Add Icon', 'cool-timeline' ); ?></button>
        </div>
        <div class="fa-field-modal" id="fa-field-modal" style="display:none">
          <div class="fa-field-modal-close">&times;</div>
          <h1 class="fa-field-modal-title"><?php _e( 'Select Font Awesome Icon', 'cool-timeline' ); ?></h1>
         <div class="icon_search_container">
          <input type="text" id="searchicon" onkeyup="ctlSearchIcon()" placeholder="Search Icon..">
           </div>
          <div id="ctl_icon_wrapper" class="fa-field-modal-icons">
            <?php if ( $this->icons ) : ?>
              <?php foreach ( $this->icons as $icon ) : ?>
                <div class="fa-field-modal-icon-holder" data-icon="<?php echo esc_attr($icon['class']); ?>">
                  <div class="icon">
                    <i  data-icon-name="<?php echo esc_attr($icon['class']); ?>" class="<?php echo esc_attr($icon['class']); ?>"></i>
                  </div>
                </div>
              <?php endforeach; ?>

            <?php endif; ?>
          </div>
        </div>       
      <?php
      }

      /**
       * Saves the data
       * @param int $post_id The ID of the saved post
       * @return void
       * 
       **/
      public function save( $post_id ) {
        /**
         *  check post type
         **/
       if ( get_post_type( $post_id)!="process_posts") {
          return;
        }
        if(!isset( $_POST['fa_field_icon_nonce'] ) || 
        !wp_verify_nonce( $_POST['fa_field_icon_nonce'], 'fa_field_icon' ) )
        {
          return;
        }
        if ( isset( $_POST['fa_field_icon'] ) ) {
          update_post_meta( $post_id, 'fa_field_icon',sanitize_text_field($_POST['fa_field_icon']));
        }
      }

      /**
       * Get an instance of the plugin
       * @return object The instance
       * 
       **/
      public function instance() {
        return new self();
      }
      /**
       * Generates an array of all icons in Font Awesome by reading it from the file and then storing it in the database.
       * @return void
       * 
       **/
      private function generate_icon_array() {
        $icons = get_option( 'fa_icons_v2' );
        if ( ! $icons ) {
              $all_icons=json_decode(file_get_contents(COOL_FA_DIR.'fontawesome-5.json'),true);
              foreach ( $all_icons as $icon ) {
                $icons[] = array( 'class' =>$icon );
                } 
                update_option( 'fa_icons_v2', $icons ); 
            }
            $this->icons = $icons;
      }
    } // END class Font_Awesome_Field
    /**
     * Add an instance of our plugin to WordPress
     **/
    //add_action( 'plugins_loaded', array( 'Font_Awesome_Field', 'instance' ) );
  }

?>