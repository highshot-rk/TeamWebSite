<?php
/*
 Plugin Name:Flip Boxes 
 Plugin URI:https://coolplugins.net/
 Description:Use animated Flip Boxes WordPress plugin to highlight your content inside your page in a great way. Use shortcode to add anywhere.
 Version:1.7.1
 License:GPL2
 Author:Cool Plugins
 Author URI:https://coolplugins.net/
 License URI:https://www.gnu.org/licenses/gpl-2.0.html
 Domain Path: /languages 
 Text Domain:c-flipboxes
*/
defined( 'ABSPATH' ) or die( "No script kiddies please!" );
if( !defined( 'CFB_VERSION' ) ) {
    define( 'CFB_VERSION', '1.7.1' );
}
if( !defined( 'CFB_DIR_PATH' ) ) {
	define( 'CFB_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if( !defined( 'CFB_URL' ) ) {
    define( 'CFB_URL', plugin_dir_url( __FILE__ ));	
}


if( !class_exists( 'CflipBoxes' ) ){

	class CflipBoxes{
		
		/* Initializes the plugin functions*/
		function __construct(){
            if(is_admin()){   
                require_once CFB_DIR_PATH . '/feedback/admin-feedback-form.php';           
            }			            
            $this-> cfb_includes();		
            add_action( 'admin_enqueue_scripts','cfb_admin_assets');
		}
    
       
        public function cfb_includes(){
            require_once CFB_DIR_PATH . '/includes/cfb-functions.php';
			require_once CFB_DIR_PATH . '/includes/cfb-shortcode.php';
			new CFB_Shortcode();			

			if(is_admin()){  
				require_once CFB_DIR_PATH . '/admin/cfb-post-type.php';
				new CFB_post_type();
				require_once CFB_DIR_PATH . '/includes/cfb-feedback-notice.php';
            	new cfb_CoolPlugins_Review_Notice();
			}

            if(is_admin() && cfb_get_post_type_page()=="flipboxes"){
                if ( file_exists( CFB_DIR_PATH . '/admin/CMB2/init.php' ) ) {
                    require_once CFB_DIR_PATH . '/admin/CMB2/init.php';
                    require_once CFB_DIR_PATH . '/admin/CMB2/cmb2-fontawesome-picker.php';
                }
							
			} 
		}

		/**
         * Activating plugin and adding some info
         */
        public static function activate() {
	          update_option("Flip-Boxes-v",CFB_VERSION);
	          update_option("Flip-Boxes-type","FREE");
	          update_option("Flip-Boxes-installDate",date('Y-m-d h:i:s') );
              if(!get_option("Flip-Boxes-ratingDiv")){
                update_option("Flip-Boxes-ratingDiv","no");
            }
        }
		// END public static function activate

        /**
         * Deactivate the plugin
         */
        public static function deactivate() {
            // Do nothing
        } 
    
		
	}// end class

}

// Installation and uninstallation hooks
register_activation_hook(__FILE__, array('CflipBoxes', 'activate'));
register_deactivation_hook(__FILE__, array('CflipBoxes', 'deactivate'));
	
$CflipBoxes_obj = new CflipBoxes(); //initialization of plugin
	