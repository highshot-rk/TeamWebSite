<?php 
function cfb_admin_assets(){
    // Do not execute on post type other than 'flipboxes'
    // Live preview fixes

    $id = get_the_ID();
    if( get_post_type($id) != 'flipboxes' ){
        return;
    }
    $post_status = get_post_status($id);

    // make sure the widget is already published!
    if( $post_status != 'publish' ){
        return;
    }
    $prefix ='_cfb_';
    $bootstrap = get_post_meta( $id, $prefix . 'bootstrap', true );
    $fontawesome= get_post_meta( $id, $prefix . 'font', true );
    if ($bootstrap=='enable')
    {
        wp_enqueue_style( 'cfb-flexboxgrid-style');
    }
    if ($fontawesome=='enable')
    {
        wp_enqueue_style( 'cfb-fontawesome');
    }
    cfb_enqueue_scripts();
}

function cfb_enqueue_scripts(){    
    wp_enqueue_style( 'cfb-styles');
    wp_enqueue_script( 'cfb-jquery-flip');
    wp_enqueue_script( 'cfb-imagesloader');  
    wp_enqueue_script( 'cfb-custom-js');   
}

function cfb_display_live_preview(){
    $output='';

    if( isset($_REQUEST['post']) && !is_array($_REQUEST['post'])){
    $id = $_REQUEST['post'];
    $type = get_post_meta($id, 'type', true);
        $output='<p><strong class="micon-info-circled"></strong>Backend preview may be a little bit different from frontend / actual view. Add this shortcode on any page for frontend view - <code>[flipboxes id='.$id.']</code></p>'.do_shortcode("[flipboxes id='".$id."']");
        
     return $output;
    }else{
    return  $output='<h4><strong class="micon-info-circled"></strong> Publish to preview the Flip Boxes.</h4>';
    
        }
}

/*
	check admin side post type page
*/
function cfb_get_post_type_page() {
    global $post, $typenow, $current_screen;
    
    if ( $post && $post->post_type ){
        return $post->post_type;
    }elseif( $typenow ){
        return $typenow;
    }elseif( $current_screen && $current_screen->post_type ){
        return $current_screen->post_type;
    }
    elseif( isset( $_REQUEST['post_type'] ) ){
        return sanitize_key( $_REQUEST['post_type'] );
    }
    elseif ( isset( $_REQUEST['post'] ) ) {
    return get_post_type( $_REQUEST['post'] );
    }
    return null;
}