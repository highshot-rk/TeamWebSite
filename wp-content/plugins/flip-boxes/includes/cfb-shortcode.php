<?php
if( !class_exists( 'CFB_Shortcode' ) ){

    class CFB_Shortcode 
	{
        function __construct()  
		{            	
            
            add_shortcode( 'flipboxes',array($this,'cfb_shortcode'));
            add_action( 'wp_enqueue_scripts',array($this,'cfb_register_frontend_assets')); //registers js and css for frontend
            add_action( 'admin_enqueue_scripts',array($this,'cfb_register_frontend_assets'));
        }
        
        

        function cfb_shortcode($atts)
		{
            $atts = shortcode_atts(array(
                'id' => '',
            ), $atts, 'ccpw');

			$id = $atts['id'];

        
			$prefix   = "_cfb_";
			$title          = get_the_title($id);
			$flip_layout    = get_post_meta( $id, $prefix . 'flip_layout', true );
			$effect         = get_post_meta( $id, $prefix . 'effect', true );
			$height         = get_post_meta( $id, $prefix . 'height', true );
			
			$height= $height!=""?$height:'default';
			
			$iconsize      = get_post_meta( $id, $prefix .'icon_size', true );
			$icon_size=isset($iconsize )&&!empty($iconsize)?$iconsize:"52px";
			$skincolor= get_post_meta( $id, $prefix .'skin_color', true );
			$skincolor=isset($skincolor)&& !empty($skincolor)?$skincolor:"#f4bf64";

			$cols= get_post_meta( $id, $prefix . 'column', true );
			$bootstrap = get_post_meta( $id, $prefix . 'bootstrap', true );
			$fontawesome= get_post_meta( $id, $prefix . 'font', true );
			$noitems = get_post_meta( $id, $prefix . 'no_of_items', true );
			$no_of_items = isset($noitems)&& !empty($noitems)?$noitems:9999;
			$entries = get_post_meta( $id, $prefix .'flip_repeat_group', true );
            
            $LinkTarget = get_post_meta( $id, $prefix .'LinkTarget', true );
            $link_target = isset($LinkTarget)&& !empty($LinkTarget)?$LinkTarget: false;
            $dynamic_target='';
            if($link_target){
                $dynamic_target = '_self';
            }
            else{
                $dynamic_target = '_blank';
            }
            global $post; 
			
			//enqueue fontawesome and flexgrid
				if ($bootstrap=='enable'){
					wp_enqueue_style( 'cfb-flexboxgrid-style');
				}
				if ($fontawesome=='enable'){
					wp_enqueue_style( 'cfb-fontawesome');
				}
			//enqueue other scripts and styles files					
				cfb_enqueue_scripts();
			
				if( is_array( $entries ) && count($entries)>-1 )
				{
					$i=1;
					$flipbox_html = ''; 
					
					$flipbox_html .='<div id="flipbox-widget-'.esc_attr($id).'" class="cfb_wrapper '.esc_attr($flip_layout).' flex-row" data-flipboxid="flipbox-widget-'.esc_attr($id).'">';
					foreach ( $entries as $entry ) 
					{

						if($i>$no_of_items){
						break;
						}
						
						$flipbox_title         =isset($entry['flipbox_title'])?$entry ['flipbox_title']:'';
						$back_desc          	=isset($entry['flipbox_desc'])?$entry['flipbox_desc']:'';
                        $flipbox_desc_length   =isset($entry['flipbox_desc_length']) && !empty($entry['flipbox_desc_length'])?$entry ['flipbox_desc_length']:'75';
                        $back_desc             =mb_strimwidth($back_desc  ,0,$flipbox_desc_length,"...");
						$single_f_c  		   =isset($entry['color_scheme'])?$entry['color_scheme']:"";
						$flipbox_icon          =isset($entry['flipbox_icon'])?$entry['flipbox_icon']:'';
						$flipbox_image         =isset($entry['flipbox_image'])?$entry['flipbox_image']:'';
				        $flipbox_url           =isset($entry['flipbox_url'])?$entry['flipbox_url']:'';
						$front_desc             =isset($entry['flipbox_label'])?$entry['flipbox_label']:'';
						$front_desc         	=mb_strimwidth($front_desc ,0,$flipbox_desc_length,"...");
						$read_more_text        =isset($entry['read_more_link'])?$entry['read_more_link']:'';
						
					
					if($single_f_c!==""){
						$flipbox_color_scheme=$single_f_c;
					}else{
						$flipbox_color_scheme=$skincolor;
					}

						if ($flip_layout=="dashed-with-icon"){
							$flip_layout = "layout-1";							
						}
						elseif ($flip_layout=="with-image"){
							$flip_layout = "layout-2";							
						}
						elseif ($flip_layout=="solid-with-icon"){
							$flip_layout = "layout-3";
						}
						
						require CFB_DIR_PATH. '/layouts/'.$flip_layout.'.php';
						$flipbox_html .= $layout_html;				
												
						$i++;	
					}	// end of foreach
					$flipbox_html .='</div>';
					return $flipbox_html;	
				}else{
					return __('No flipbox content added','c-flipboxes');
				}
						
			
        }

        function cfb_register_frontend_assets() 
		{
			wp_register_script( 'cfb-custom-js', CFB_URL . 'assets/js/flipboxes-custom.min.js', array('jquery'), CFB_VERSION );
			
			wp_register_style( 'cfb-fontawesome',CFB_URL . 'assets/css/font-awesome.min.css', array(), CFB_VERSION);

			wp_register_script( 'cfb-jquery-flip', CFB_URL . 'assets/js/jquery.flip.min.js', array('jquery'), CFB_VERSION );
			
			wp_register_style( 'cfb-flexboxgrid-style',CFB_URL . 'assets/css/flipboxes-flexboxgrid.min.css', array(), CFB_VERSION);
			wp_register_style( 'cfb-styles',CFB_URL . 'assets/css/flipboxes-styles.min.css', array(), CFB_VERSION);
			
			wp_register_script( 'cfb-imagesloader', CFB_URL . 'assets/js/jquery-imagesloader.min.js', array('jquery'), CFB_VERSION );
			
			global $post; 
			
			if(is_page()){
				if( is_a( $post, 'WP_Post' )&& has_shortcode( $post->post_content, 'flipboxes')){  								
					cfb_enqueue_scripts();					
				}
			}
			
			
		}
        

    }

}