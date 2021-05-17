<?php
// WP_Query arguments
    $args = array (
        'p'              => $select_timeline,     // GET POST BY SLUG  // IGNORE IF YOU ARE GETTING ERROR ON THIS LINE IN YOUR EDITOR
        'post_type'         => 'timeline-awesome', // YOUR POST TYPE

    );

    // The Query
    $query = new WP_Query( $args );

    // The Loop
    if ( $query->have_posts() && $select_timeline != '' ) {

        wp_enqueue_style( 'ta-timeline-awesome-fontawesome', plugin_dir_url('README.txt') . TIMELINE_AWESOME_NAME . '/public/css/fontawesome.min.css', array(), '', 'all' );
        wp_enqueue_style( 'ta-timeline-awesome', plugin_dir_url('README.txt') . TIMELINE_AWESOME_NAME . '/public/css/timeline-awesome-public.css', array(), '', 'all' );
        wp_enqueue_style( 'ta-timeline-awesome-responsive', plugin_dir_url('README.txt') . TIMELINE_AWESOME_NAME . '/public/css/responsive.css', array(), '', 'all' );

        while ( $query->have_posts() ) {

			$query->the_post();

            //$html_src = get_the_title( get_the_ID() );  // GET HTML SOURCE FROM YOUR POST META

            $timeline_style = carbon_get_post_meta( get_the_ID(), 'timeline_style_choice' );

            if($timeline_style == 'vertical-1') {
                $timeline_style_part = TIMELINE_AWESOME_DIR .'/public/timeline-styles/timeline-vertical-1.php';
            }
            elseif($timeline_style == 'vertical-2') {
                $timeline_style_part = TIMELINE_AWESOME_DIR .'/public/timeline-styles/timeline-vertical-2.php';
            }
            elseif($timeline_style == 'vertical-3') {
                $timeline_style_part = TIMELINE_AWESOME_DIR .'/public/timeline-styles/timeline-vertical-3.php';
            }
            elseif($timeline_style == 'vertical-7') {
                $timeline_style_part = TIMELINE_AWESOME_DIR .'/public/timeline-styles/timeline-vertical-7.php';
            }
            elseif($timeline_style == 'vertical-9') {
                $timeline_style_part = TIMELINE_AWESOME_DIR .'/public/timeline-styles/timeline-vertical-9.php';
            }
            elseif($timeline_style == 'horizontal-6') {
                $timeline_style_part = TIMELINE_AWESOME_DIR .'/public/timeline-styles/timeline-horizontal-6.php';
            }
            elseif($timeline_style == 'horizontal-9') {
                $timeline_style_part = TIMELINE_AWESOME_DIR .'/public/timeline-styles/timeline-horizontal-9.php';
            }
            include $timeline_style_part;

        }

        wp_reset_postdata();
    } else {
        // no posts found
        return esc_html__( 'Sorry no html for this slug...', 'timeline-awesome' );

    }