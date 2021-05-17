<?php
if( !class_exists( 'CFB_post_type' ) ){
    class CFB_post_type{

        function __construct(){ 
            add_action('init', array($this,'cfb_register_post_type'));        
            add_action( 'cmb2_admin_init', array($this,'cfb_metaboxes'));
			add_action( 'cmb2_admin_init', array($this, 'cfb_general_settings' ));        
			add_action( 'cmb2_admin_init', array($this, 'cfb_advanced_settings' ));   
            add_filter('manage_edit-flipboxes_columns',array($this, 'cfb_add_custom_columns'));
            add_action('manage_flipboxes_posts_custom_column', array($this, 'cfb_columns_content'), 10, 2 );
            add_action( 'add_meta_boxes',array($this, 'cfb_shortcode_metabox' ));
        }
        
        function cfb_register_post_type(){
    		$labels = array(
    			'name'                  => _x( 'Flipboxes', 'Post Type General Name'),
    			'singular_name'         => _x( 'Flipboxes', 'Post Type Singular Name'),
    			'menu_name'             => __( 'Flipboxes'),
    			'name_admin_bar'        => __( 'Flipboxes'),
    			'archives'              => __( 'Item Archives'),
    			'attributes'            => __( 'Item Attributes'),
    			'parent_item_colon'     => __( 'Parent Item:'),
    			'all_items'             => __( 'All Flipboxes'),
    			'add_new_item'          => __( 'Add New Flipbox'),
    			'add_new'               => __( 'Add New'),
    			'new_item'              => __( 'New Item'),
    			'edit_item'             => __( 'Edit Item'),
    			'update_item'           => __( 'Update Item'),
    			'view_item'             => __( 'View Item'),
    			'view_items'            => __( 'View Items'),
    			'search_items'          => __( 'Search Item'),
    			'not_found'             => __( 'Not found'),
    			'not_found_in_trash'    => __( 'Not found in Trash'),
    			'featured_image'        => __( 'Featured Image'),
    			'set_featured_image'    => __( 'Set featured image'),
    			'remove_featured_image' => __( 'Remove featured image'),
    			'use_featured_image'    => __( 'Use as featured image'),
    			'insert_into_item'      => __( 'Insert into item'),
    			'uploaded_to_this_item' => __( 'Uploaded to this item'),
    			'items_list'            => __( 'Items list'),
    			'items_list_navigation' => __( 'Items list navigation'),
    			'filter_items_list'     => __( 'Filter items list'),
    			);
    		$args = array(
    			'label'                 => __( 'Flipboxes'),
    			'description'           => __( 'Post Type Description'),
    			'labels'                => $labels,
    			'supports'              => array('title'),
    			'taxonomies'            => array(),
    			'hierarchical'          => false,
    			'public'                => false,
    			'show_ui'               => true,
    			'show_in_menu'          => true,
    			'menu_position'         => 5,
    			'show_in_admin_bar'     => true,
    			'show_in_nav_menus'     => true,
    			'can_export'            => true,
    			'has_archive'           => true,        
    			'exclude_from_search'   => false,
    			'publicly_queryable'    => false,
    			'capability_type'       => 'page',
    			);
    		register_post_type('flipboxes',$args);
    	}

        /*Define the metabox and field configurations*/
    	function cfb_metaboxes(){
            // Start with an underscore to hide fields from custom fields list
            $prefix = '_cfb_';
            
            $cmb2 = new_cmb2_box( array(
               'id'            => 'cfb_live_preview',
               'title'         => __( 'Flip Boxes Live Preview', 'cmb2' ),
               'object_types'  => array( 'flipboxes'), // Post type
                'context'       => 'normal',
                'priority'      => 'high',
                'show_names'    => true, // Show field names on the left
                // 'cmb_styles' => false, // false to disable the CMB stylesheet
                // 'closed'     => true, // Keep the metabox closed by default
               ) );

            $cmb2->add_field( array(
                'name' => '',
                	'desc' =>cfb_display_live_preview(),
                	'type' => 'title',
                	'id'   => 'cfb_live_preview'
            ) );
            
            /* Initiate the metabox*/
            $flip = new_cmb2_box( array(
                'id'            => 'test_metabox',
                'title'         => __( 'Add Flipboxes', 'cmb2' ),
                'object_types'  => array( 'flipboxes'), // Post type
                'context'       => 'normal',
                'priority'      => 'high',
                'show_names'    => true, // Show field names on the left
            ) );

            $group_field_id = $flip->add_field( array(
                'id'          =>$prefix.'flip_repeat_group',
                'type'        => 'group',
                'description' => __( '', 'cmb2' ),
                'options'     => array(
                    'group_title'   => __( 'Flipbox {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
                    'add_button'    => __( 'Add Another Flipbox', 'cmb2' ),
                    'remove_button' => __( 'Remove Flipbox', 'cmb2' ),
					'sortable'      => true, // beta
					'closed'        => true, // true to have the groups closed by default
                    'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
                ),
            ) );

            // Id's for group's fields only need to be unique for the group. Prefix is not needed.
            $flip->add_group_field( $group_field_id, array(
                'name' => __('Title','c-flipbox'),
                'description' => __('Enter a title for this Flipbox','c-flipbox'),
                'id'   => 'flipbox_title',
                'type' => 'text',
    		) );
            
            $flip->add_group_field( $group_field_id, array(
                'name' => __('Front Description','c-flipbox'),
                'id'   => 'flipbox_label',
                'description' => __('Add Front Description for this Flipbox','c-flipbox'),
                'type' => 'textarea_small',
    		) );
            
            $flip->add_group_field( $group_field_id, array(
                'name'        => __('Back Description','c-flipbox'),
                'description' => __('Add Back Description for this Flipbox','c-flipbox'),
                'id'          => 'flipbox_desc',
                'type'        => 'textarea_small',
    		) );
    		
    		$flip->add_group_field( $group_field_id, array(
                'name'        => __( 'Description Length', 'c-flipbox' ),
                'description' => __('Enter number of characters','c-flipbox'),
                'id'          => 'flipbox_desc_length',
                'type'        => 'text',
                'default'     => '75',
            ) );
           
    		$flip->add_group_field( $group_field_id, array(
                'name'        => __( 'Select Icon', 'c-flipbox' ),
                'description' => __('Choose an Icon for Flipbox Layout','c-flipbox'),
                'id'          => 'flipbox_icon',
                'type'        => 'fontawesome_icon',
            ) );
            $flip->add_group_field( $group_field_id, array(
                'name'        => __( 'Color Scheme', 'c-flipbox' ),
                'description' => __('Choose Color Scheme','c-flipbox'),
                'id'          => 'color_scheme',
                'type'        => 'colorpicker',
    		) );
            
            $flip->add_group_field( $group_field_id, array(
                'name' => __('Image','c-flipbox'),
                'id'   => 'flipbox_image',
                'description' => __('Upload an Image','c-flipbox'),
                'type' => 'file',
            ) );
            
            $flip->add_group_field( $group_field_id, array(
                'name' => __('URL','c-flipbox'),
                'id'   => 'flipbox_url',
                'description' => __('Enter URL for Button','c-flipbox'),
                'type' => 'text_url',
                'protocols' => array('http', 'https'),
            ) );
            
            
            $flip->add_group_field( $group_field_id, array(
                'name' => __('URL Text','c-flipbox'),
                'id'   => 'read_more_link',
                'description' => __('Enter Text For Button','c-flipbox'),
                'type' => 'text',
    		) );
        }

        /*Define the metabox and field configurations.*/
    	function cfb_general_settings(){
    		// Start with an underscore to hide fields from custom fields list
    		$prefix = '_cfb_';
    		
    		/*Initiate the metabox*/
    		$flip = new_cmb2_box( array(
    			'id'            => 'cfb-side-mt',
    			'title'         => __( 'Flipbox General Settings', 'cmb2' ),
    			'object_types'  => array( 'flipboxes', ), // Post type
    			'context'       => 'side',
    			'priority'      => 'low',
    			'show_names'    => true, // Show field names on the left
    		) );

    		// Regular text field
    		$flip->add_field( array(
    			'name'               => __( 'Flipbox layout','cmb2' ),
    			'desc'               => __( 'Select Flipbox Layout','cmb2' ),
    			'id'                 => $prefix . 'flip_layout',
    			'type'               => 'select',
    			'show_option_none'   => false,
    			'default'            => 'dashed-with-icon',
    			'options'            => array(
    				'dashed-with-icon'  => __( 'Layout 1 (Dashed With Icon)', 'cmb2' ),
    				'with-image'        => __( 'Layout 2 (With Image)', 'cmb2' ),
    				'solid-with-icon'   => __( 'Layout 3 (Solid With Icon)', 'cmb2' ),
    				'layout-4'   => __( 'Layout 4', 'cmb2' ),
    				'layout-5'   => __( 'Layout 5', 'cmb2' ),
    				'layout-6'   => __( 'Layout 6', 'cmb2' ),
    				'layout-7'   => __( 'Layout 7', 'cmb2' ),
    				'layout-8'   => __( 'Layout 8', 'cmb2' ),
    				'layout-9'   => __( 'Layout 9', 'cmb2' ),
    			),
    		) );

    		$flip->add_field( array(
    			'name'             => __( 'Flipbox Effect', 'cmb2' ),
    			'desc'             => __( 'Select Flipbox Effect', 'cmb2' ),
    			'id'               => $prefix . 'effect',
    			'type'             => 'select',
    			'show_option_none' => false,
    			'default'          => 'left-to-right',
    			'options'          => array(
    			   'x' => __( 'Bottom To Top', 'cmb2' ),
    				'y' => __( 'Left To Right', 'cmb2' ),
    			),
            ) );
            
    		$flip->add_field( array(
    			'name'             =>  __( 'Number of columns', 'cmb2' ),
    			'desc'             =>  __( 'Select Number of columns', 'cmb2' ),
    			'id'               => $prefix . 'column',
    			'type'             => 'select',
    			'show_option_none' => false,
    			'default'          => 'col-md-4',
    			'options'          => array(
    				'col-md-12'=> __( 'One','cmb2' ),
    				'col-md-6' => __( 'Two', 'cmb2' ),
    				'col-md-4' => __( 'Three', 'cmb2' ),
    				'col-md-3' => __( 'Four', 'cmb2' ),
    				'col-md-2' => __( 'Six', 'cmb2' ),
    			),
    		) );
    		  
    		$flip->add_field( array(
    			'name'        => __( 'Skin Color', 'cmb2' ),
    			'description' => __( 'Choose a skin color', 'cmb2' ),
    			'id'          => $prefix .'skin_color',
    			'type'        => 'colorpicker',
    			'default'     => '#f4bf64',
			) );
			
    		$flip->add_field( array(
    			'name'             =>  __( 'Height', 'cmb2' ),
    			'desc'             =>  __( 'Select height for Flipboxes', 'cmb2' ),
    			'id'               => $prefix . 'height',
    			'type'             => 'select',
    			'show_option_none' => false,
    			'default'          => 'default',
    			'options'          => array(
    				'default'=> __( 'Default(according to content)','cmb2' ),
    				'equal' => __( 'Equal height of each Flipbox', 'cmb2' ),
    			),
    		) );

    		
        }
		
		function cfb_advanced_settings(){
			// Start with an underscore to hide fields from custom fields list
    		$prefix = '_cfb_';
    		
    		/*Initiate the metabox*/
    		$flip = new_cmb2_box( array(
    			'id'            => 'cfb_advanced_settings',
    			'title'         => __( 'Flipbox Advanced Settings', 'cmb2' ),
    			'object_types'  => array( 'flipboxes', ), // Post type
    			'context'       => 'side',
    			'priority'      => 'low',
    			'show_names'    => true, // Show field names on the left
			) );				

			$flip->add_field( array(
    			'name'       => __( 'Number of Flipboxes', 'cmb2' ),
    			'desc'       => __( 'Enter number of flipboxes to show', 'cmb2' ),
    			'id'         => $prefix .'no_of_items',
    			'type'       => 'text',
			) );	

    		$flip->add_field( array(
    			'name'       => __( 'Icon Size(in px)', 'cmb2' ),
    			'desc'       => __( 'Enter icon size', 'cmb2' ),
    			'id'         => $prefix .'icon_size',
    			'type'       => 'text',
    			'default'    => '52px',
			) );					
			
			$flip->add_field( array(
    			'name'             => __( 'Read More link in same tab', 'cmb2' ),
    			'desc'             => __( 'Check if you want to open Read More link in same tab', 'cmb2' ),
    			'id'               => $prefix . 'LinkTarget',
                'type'             => 'checkbox',
			) );

			$flip->add_field( array(
    			'name'    => __( 'Bootstrap', 'cmb2' ),
    			'id'      => $prefix . 'bootstrap',
    			'default' => 'enable',
    			'type'    => 'radio',
    			'options' => array(
    				'enable' => __( 'Enable Bootstrap', 'cmb2' ),
    				'disable' =>__( 'Disable Bootstrap', 'cmb2' ),
    			),
    		) );
    		
    		$flip->add_field( array(
    			'name'    =>__( 'Fontawesome', 'cmb2' ),
    			'id'      => $prefix . 'font',
    			'default' => 'enable',
    			'type'    => 'radio',
    			'options' => array(
    				'enable' =>__( 'Enable Fontawesome', 'cmb2' ),
    				'disable' =>__( 'Disable Fontawesome', 'cmb2' ),
    			),
			) );
    		
		}

        // ADD NEW COLUMN
    	function cfb_add_custom_columns($flip_cols){
    		$new_columns['cb']            =  '<input type="checkbox" />';
    		$new_columns['title']         =  _x('Title', 'column name');
    		$new_columns['flip_layout']   =  _x('Flipbox Layout', 'flipboxes');
    		$new_columns['effect']        =  __('Flipbox Effect','flipboxes');
    		$new_columns['code']          =  __('Shortcode','flipboxes');
    		$new_columns['date']          =  _x('Sort By Date', 'column name');
    		return $new_columns;
        }
        
        function cfb_columns_content($flip_cols,$post){                   
    		$prefix="_cfb_";
    		// global $layouts;
    		$layouts=array(
    			'dashed-with-icon'     => __( 'Dashed With Icons', 'cmb2' ),
    			'with-image'           => __( 'With Image', 'cmb2' ),
    			'solid-with-icon'      => __( 'Solid With Icon', 'cmb2' ),
    			'layout-4'  		   => __( 'Layout 4', 'cmb2' ),
    			'layout-5'  		   => __( 'Layout 5', 'cmb2' ),
    			'layout-6'			   => __( 'Layout 6', 'cmb2' ),
    			'layout-7'			   => __( 'Layout 7', 'cmb2' ),
    			'layout-8'			   => __( 'Layout 8', 'cmb2' ),
    			'layout-9'			   => __( 'Layout 9', 'cmb2' ),
    			);
    		//global $effects;
    		$effects=array(
    				'x'   => __( 'Bottom To Top', 'cmb2' ),
    				'y'   => __( 'Left To Right', 'cmb2' ),
    		);
    			
    		switch ( $flip_cols ){
    			case "flip_layout":
    			$lt = get_post_meta( $post, $prefix . 'flip_layout', true );
    			if(isset($layouts[$lt]))
    			{
    				echo $layouts[$lt];
    			}
    			break;
    			case "effect":
    			$eff= get_post_meta( $post, $prefix . 'effect', true );
    			if(isset($effects[$eff]))
    			{
    				echo $effects[$eff];
    			}
    			break;
    			case "code":
    			global $dynamic_attr;
    			global $id;
    			$dynamic_attr="[flipboxes id=\"{$id}\"]";
    			echo "<input type='text' value='".$dynamic_attr."'>";
    			break;
    			default:
    		    echo esc_html_e('Not Matched','cfb2');
    		  	break;
    		}
        }

        function cfb_shortcode_metabox(){
    		add_meta_box( 'my-meta-box-id', 'Use This Shortcode',array($this,'cfb_shortcode_text'), 'flipboxes', 'side', 'high' );
    	}
    	
    	function cfb_shortcode_text(){
    		$id = get_the_ID();
    		$dynamic_attr='';
    		_e("Paste this shortcode in anywhere (page/post)", "c-flipbox");
    		$dynamic_attr.="[flipboxes id=\"{$id}\"";
    		$dynamic_attr.=']';
    		$prefix="_cfb_";
    		?>
    	    <input type="text" class="regular-small" name="my_meta_box_text" id="my_meta_box_text" value="<?php  echo esc_attr ($dynamic_attr);?>" readonly/>
    		<?php
        }
        

    }
}