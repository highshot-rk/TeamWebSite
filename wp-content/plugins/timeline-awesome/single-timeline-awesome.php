<?php get_header();

$template = get_template();

global $wp;
if ( have_posts() ):

wp_enqueue_style( 'ta-timeline-awesome-fontawesome', plugin_dir_url(__FILE__ ) . 'public/css/fontawesome.min.css', array(), '', 'all' );
wp_enqueue_style( 'ta-timeline-awesome', plugin_dir_url(__FILE__ ) . 'public/css/timeline-awesome-public.css', array(), '1.0.2', 'all' );
wp_enqueue_style( 'ta-timeline-awesome-responsive', plugin_dir_url(__FILE__ ) . 'public/css/responsive.css', array(), '1.0.2', 'all' );

while ( have_posts() ) : the_post();

	$timeline_style = carbon_get_post_meta( get_the_ID(), 'timeline_style_choice' );

    if($timeline_style == 'vertical-1') {
    	echo '<div class="timeline-container">';
        	include_once dirname( __FILE__ ) .'/public/timeline-styles/timeline-vertical-1.php';
        echo '</div>';
    }
    elseif($timeline_style == 'vertical-2') {
        echo '<div class="timeline-container">';
            include_once  dirname( __FILE__ ) .'/public/timeline-styles/timeline-vertical-2.php';
        echo '</div>';
    }
    elseif($timeline_style == 'vertical-3') {
        echo '<div class="timeline-container">';
            include_once  dirname( __FILE__ ) .'/public/timeline-styles/timeline-vertical-3.php';
        echo '</div>';
    }
    elseif($timeline_style == 'vertical-7') {
        echo '<div class="timeline-container">';
            include_once  dirname( __FILE__ ) .'/public/timeline-styles/timeline-vertical-7.php';
        echo '</div>';
    }
    elseif($timeline_style == 'vertical-9') {
        echo '<div class="timeline-container">';
            include_once  dirname( __FILE__ ) .'/public/timeline-styles/timeline-vertical-9.php';
        echo '</div>';
    }
    elseif($timeline_style == 'horizontal-6') {
        include_once  dirname( __FILE__ ) .'/public/timeline-styles/timeline-horizontal-6.php';
    }
    elseif($timeline_style == 'horizontal-9') {
        echo '<div class="timeline-container">';
            include_once  dirname( __FILE__ ) .'/public/timeline-styles/timeline-horizontal-9.php';
        echo '</div>';
    }

$template = get_template();

endwhile; 
endif;
wp_reset_postdata();
get_footer(); ?>