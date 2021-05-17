<?php
/*
  Plugin Name:Process Steps Template Designer
  Plugin URI:http://process.cooltimeline.com/
  Description:Process Steps Template Designer plugin allow you to show your workflow business process in a creative step by step design template.
  Version:1.3.2
  Author:Cool Plugins 
  Author URI:https://coolplugins.net/
  License: GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Domain Path: /languages
  Text Domain: cool_process
 */


/** Configuration * */
if (!defined('COOL_PROCESS_VERSION_CURRENT'))
    define('COOL_PROCESS_VERSION_CURRENT', '1.3.2');
     define('COOL_PROCESS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
     define('COOL_PROCESS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
 	 defined( 'COOL_FA_DIR' ) or define( 'COOL_FA_DIR', plugin_dir_path( __FILE__ ).'/icons-selector/' );
	defined( 'COOL_FA_URL' ) or define( 'COOL_FA_URL', plugin_dir_url( __FILE__ ).'/icons-selector/'  );
if (!class_exists('CoolProcess')) {

    class CoolProcess {

        /**
         * Construct the plugin object
         */
        public function __construct() {
            // Initialize Settings
            $this->plugin_path = plugin_dir_path(__FILE__);
         
            add_action( 'plugins_loaded', array( $this, 'pp_language_translation' ) );
           
			/*
             * Process post type
             */

            require_once COOL_PROCESS_PLUGIN_DIR . 'includes/process_posttype.php';
            new ProcessSteps();


            if(is_admin()){   
                require_once COOL_PROCESS_PLUGIN_DIR . '/includes/feedback/admin-feedback-form.php';
                //include the main class file
                require_once COOL_PROCESS_PLUGIN_DIR . "meta-box-class/my-meta-box-class.php";
                /*
                *  custom meta boxes 
                */
                $this->pp_meta_boxes();    
                // add a tinymce button that generates our shortcode for the user
                add_action('after_setup_theme', array($this, 'pp_add_tinymce'));
                
                add_action( 'admin_notices',array($this,'pp_admin_messages'));
                add_action( 'wp_ajax_hideRating',array($this,'pp_HideRating' ));
         }


             require_once COOL_PROCESS_PLUGIN_DIR . 'includes/process_functions.php';
             require_once COOL_PROCESS_PLUGIN_DIR . 'includes/process_shortcode.php';
             require_once COOL_PROCESS_PLUGIN_DIR .'icons-selector/font-awesome-field.php';
             // Include other PHP scripts
             add_action( 'init', array( $this, 'include_files' ) );
             new Font_Awesome_Field();
             new ProcessShortcode();
        }

        public function pp_language_translation(){
            load_plugin_textdomain('cool_process', false, basename(dirname(__FILE__)) . '/languages/');
        }

		   public function pp_meta_boxes() {
            /*
             * configure your meta box
             */
            $config = array(
                'id' => 'pp_meta_boxes', // meta box id, unique per meta box
                'title' => __('Process fields', 'apc'), // meta box title
                'pages' => array('process_posts'), // post types, accept custom post types as well, default is array('post'); optional
                'context' => 'normal', // where the meta box appear: normal (default), advanced, side; optional
                'priority' => 'default', // order of meta box: high (default), low; optional
                'fields' => array(), // list of meta fields (can be added by field arrays) or using the class's functions
                'local_images' => false, // Use local or hosted images (meta box images for add/remove)
                'use_with_theme' => false            //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
            );

            /*
             * Initiate your meta box
             */
            $my_meta = new AT_Meta_Box($config);

            //text field
            $my_meta->addText('pp_post_lbl',array('name'=> 'Label'));
          //  $my_meta->addText('pp_post_text',array('name'=> 'Text'));
            //$my_meta->addText('pp_post_order',array('name'=> 'Step Order'));
            for($i=1;$i<=100;$i++){
                $all_orders[$i]=$i;
            }
            $my_meta->addSelect('pp_post_order',$all_orders, array('name' => __('Step Order ', 'apc'), 'std' =>'', 'desc' => __('', 'apc')));
			
            //Finish Meta Box Deceleration
            $my_meta->Finish();
        }

		
		 /**
         * Include other PHP scripts for the plugin
         * @return void
         *
         **/
        public function include_files() {
            // Files specific for the front-ned
            if ( ! is_admin() ) {
                // Load template tags (always last)
                require_once COOL_PROCESS_PLUGIN_DIR .'icons-selector/includes/template-tags.php';
            }
            if(get_option('pp-flush-rewrite')){
                flush_rewrite_rules('pp-flush-rewrite');
                delete_option('pp-flush-rewrite');
            }
        }
		
         public function pp_add_tinymce() {
            global $typenow;
         if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
              return;
        }

        if ( get_user_option('rich_editing') == 'true' ) {
            add_filter('mce_external_plugins', array(&$this, 'pp_add_tinymce_plugin'));
            add_filter('mce_buttons', array(&$this, 'pp_add_tinymce_button'));
            }

            
        }
		
		 // inlcude the js for tinymce
        public function pp_add_tinymce_plugin($plugin_array) {
            $plugin_array['cool_process'] =COOL_PROCESS_PLUGIN_URL.'assets/js/process-button-script.js';
            return $plugin_array;
        }

        // Add the button key for address via JS
        function pp_add_tinymce_button($buttons) {
            array_push($buttons, 'cool_process_shortcode_button');
        
            return $buttons;
        }
         
            
       	/**
         * Activate the plugin
         */
        public static function activate() {
             update_option("pp-v",COOL_PROCESS_VERSION_CURRENT);
              update_option("pp-type","FREE");
              update_option("pp-installDate",date('Y-m-d h:i:s') );
              update_option("pp-ratingDiv","no");
              update_option("pp-flush-rewrite","yes");
        }

		// END public static function activate

        /**
         * Deactivate the plugin
         */
        public static function deactivate() {
            // Do nothing
        }     
         

        public function pp_admin_messages() {
  
         if( !current_user_can( 'update_plugins' ) ){
            return;
         }

        $install_date = get_option('pp-installDate' );
        $ratingDiv =get_option( 'pp-ratingDiv' )!=false?get_option( 'pp-ratingDiv'):"no";
        $display_date = date( 'Y-m-d h:i:s' );
        $install_date= new DateTime( $install_date );
        $current_date = new DateTime( $display_date );
    
        $difference = $install_date->diff($current_date);
        $df_days=$difference->days;
        $dynamic_msz='';

        if ( $df_days >=15 && $ratingDiv== "no" ) {
            $dynamic_msz ="for more than 2 weeks.";
        echo '<div class="cool_fivestar update-nag" style="box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);">
            <p>Awesome, you\'ve been using <strong>Process Steps Template Designer</strong> '.$dynamic_msz .' Hopefully you\'re happy with it. <br> May I ask you to give it a <strong>5-star rating</strong> on Wordpress? 
                This will help to spread its popularity and to make this plugin a better one.
                <br><br>Your help is much appreciated.Thank you very much!
                <ul>
                    <li class="float:left"><a href="https://wordpress.org/support/plugin/process-steps-template-designer/reviews/#new-post" class="thankyou button button-primary" target="_new" title="I Like Process Steps" style="color: #ffffff;-webkit-box-shadow: 0 1px 0 #256e34;box-shadow: 0 1px 0 #256e34;font-weight: normal;float:left;margin-right:10px;">I like Process Steps</a></li>
                    <li><a href="javascript:void(0);" class="coolHideRating button" title="I already did" style="">I already rated it</a></li>
                    <li><a href="javascript:void(0);" class="coolHideRating" title="No, not good enough" style="">No, not good enough, i do not like to rate it!</a></li>
                </ul>
            </div>
            <script>
            jQuery( document ).ready(function( $ ) {

            jQuery(\'.coolHideRating\').click(function(){
                var data={\'action\':\'hideRating\'}
                    jQuery.ajax({
                
                url: "' . admin_url( 'admin-ajax.php' ) . '",
                type: "post",
                data: data,
                dataType: "json",
                async: !0,
                success: function(e) {
                    if (e=="success") {
                    jQuery(\'.cool_fivestar\').slideUp(\'fast\');
                
                    }
                }
                });
                })
            
            });
            </script>';
     }
   }   
   public function pp_HideRating() {
      update_option( 'pp-ratingDiv','yes' );
      echo json_encode( array("success") );
      exit;
      }
  

        } //end class

    }

	
	function pp_get_current_post_type() {
    global $post, $typenow, $current_screen;

    //we have a post so we can just get the post type from that
    if ( $post && $post->post_type )
        return $post->post_type;

    //check the global $typenow - set in admin.php
    elseif( $typenow )
        return $typenow;

    //check the global $current_screen object - set in sceen.php
    elseif( $current_screen && $current_screen->post_type )
        return $current_screen->post_type;

    //lastly check the post_type querystring
    elseif( isset( $_REQUEST['post_type'] ) )
        return sanitize_key( $_REQUEST['post_type'] );

    //we do not know the post type!
    return null;
}

    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('CoolProcess', 'activate'));
    register_deactivation_hook(__FILE__, array('CoolProcess', 'deactivate'));

    // instantiate the plugin class
	
    $cool_process = new CoolProcess();
    ?>
