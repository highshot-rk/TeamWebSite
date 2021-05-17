<?php

if (!class_exists('ProcessSteps')) {

    class ProcessSteps {

        /**
         * The Constructor
         */
        public function __construct() {
            // register actions
            add_action('init', array(&$this, 'pp_posttype'));
            add_filter('manage_edit-process_posts_columns', array(&$this, 'add_new_process_posts_columns'));
            add_action('manage_process_posts_posts_custom_column', array(&$this, 'pp_custom_columns'), 10, 2);
            add_action('init', array(&$this, 'pp_taxonomy'), 0);
            add_action('init', array(&$this, 'pp_insert_category'), 0);

            add_action( 'save_post_process_posts',array(&$this,'pp_set_default_object_terms' ),100 ,2);
            add_filter('parse_query',array(&$this, 'pp_convert_id_to_term_in_query'));
            add_action('restrict_manage_posts',array(&$this, 'pp_filter_post_type_by_taxonomy'));
        }

// END public function __construct())
        // Register Custom Post Type
        function pp_posttype() {

            $labels = array(
                'name' => _x('Process Steps', 'Post Type General Name', 'cool-timeline'),
                'singular_name' => _x('Process Infographic', 'Post Type Singular Name', 'cool-timeline'),
                'menu_name' => __('Process Steps', 'cool-timeline'),
                'name_admin_bar' => __('Process Steps', 'cool-timeline'),
                'parent_item_colon' => __('Parent Item:', 'cool-timeline'),
                'all_items' => __('All Process', 'cool-timeline'),
                'add_new_item' => __('Add New Process', 'cool-timeline'),
                'add_new' => __('Add New', 'cool-timeline'),
                'new_item' => __('New Process', 'cool-timeline'),
                'edit_item' => __('Edit Process', 'cool-timeline'),
                'update_item' => __('Update Process', 'cool-timeline'),
                'view_item' => __('View Process', 'cool-timeline'),
                'search_items' => __('Search Process', 'cool-timeline'),
                'not_found' => __('Not found', 'cool-timeline'),
                'not_found_in_trash' => __('Not found in Trash', 'cool-timeline'),
            );
            $args = array(
                'label' => __('process_posts', 'cool-timeline'),
                'description' => __('Process Infographic Post Type', 'cool-timeline'),
                'labels' => $labels,
                'supports' => array('title', 'editor', 'thumbnail', 'author'),
                'taxonomies' => array(),
                'hierarchical' => false,
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 5,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => true,
                'has_archive' => true,
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'rewrite' => array('slug' => 'process_posts'),
                //'menu_icon'=>COOL_TIMELINE_PLUGIN_URL.'/images/cooltimeline.png',
            );
            register_post_type('process_posts', $args);
        }

        // Register Custom Taxonomy
        function pp_taxonomy() {

            $labels = array(
                'name' => _x('Categories', 'Taxonomy General Name', 'cool-timeline'),
                'singular_name' => _x('Category', 'Taxonomy Singular Name', 'cool-timeline'),
                'menu_name' => __('Categories', 'cool-timeline'),
                'all_items' => __('All Items', 'cool-timeline'),
                'parent_item' => __('Parent Item', 'cool-timeline'),
                'parent_item_colon' => __('Parent Item:', 'cool-timeline'),
                'new_item_name' => __('New Item Name', 'cool-timeline'),
                'add_new_item' => __('Add New Item', 'cool-timeline'),
                'edit_item' => __('Edit Item', 'cool-timeline'),
                'update_item' => __('Update Item', 'cool-timeline'),
                'view_item' => __('View Item', 'cool-timeline'),
                'separate_items_with_commas' => __('Separate items with commas', 'cool-timeline'),
                'add_or_remove_items' => __('Add or remove items', 'cool-timeline'),
                'choose_from_most_used' => __('Choose from the most used', 'cool-timeline'),
                'popular_items' => __('Popular Items', 'cool-timeline'),
                'search_items' => __('Search Items', 'cool-timeline'),
                'not_found' => __('Not Found', 'cool-timeline'),
                'no_terms' => __('No items', 'cool-timeline'),
                'items_list' => __('Items list', 'cool-timeline'),
                'items_list_navigation' => __('Items list navigation', 'cool-timeline'),
            );
            $args = array(
                'labels' => $labels,
                'hierarchical' => true,
                'public' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => true,
                'show_tagcloud' => true,
                'query_var' => true,
                //'rewrite'               => array( 'slug' => 'categories' ),
            );
            register_taxonomy('process-categories', array('process_posts'), $args);
        }

        public function pp_insert_category() {
            if(!term_exists( 'default-process', 'process-categories' )){
                $r=   wp_insert_term(
                    'Default process', // the term
                    'process_posts', // the taxonomy
                    array(
                        'description' => 'All process stories.',
                        'slug' => 'default-process',
                        // 'parent' => 0
                    ) );
            }
        }

        function pp_set_default_object_terms($post_id, $post) {
            if ('process_posts' === $post->post_type) {
                if ('publish' === $post->post_status) {
                    $defaults = array(
                        'process-categories' => array('default-process')
                    );
                    $taxonomies = get_object_taxonomies($post->post_type);
                    foreach ((array) $taxonomies as $taxonomy) {
                        $terms = wp_get_post_terms($post_id, $taxonomy);
                        if (empty($terms) && array_key_exists($taxonomy, $defaults)) {
                            wp_set_object_terms($post_id, $defaults[$taxonomy], $taxonomy);
                        }
                    }
                }
            }
        }

        function add_new_process_posts_columns($gallery_columns) {
            $new_columns['cb'] = '<input type="checkbox" />';

            $new_columns['title'] = _x('Title', 'column name', 'cool-timeline');
            $new_columns['label'] = __('Label', 'column name', 'cool-timeline');
            $new_columns['order'] = _x('Order', 'column name', 'cool-timeline');
            $new_columns['content'] = _x('Content', 'column name', 'cool-timeline');
            $new_columns['category'] = _x('Process Category', 'column name', 'cool-timeline');
            $new_columns['images'] = __('Process Image', 'cool-timeline');
            $new_columns['date'] = _x('Published Date', 'column name', 'cool-timeline');

            return $new_columns;
        }

        function pp_custom_columns($column, $post_id) {
            global   $post ;
            switch ($column) {
                case "label":
                    $pp_label = get_post_meta($post_id, 'pp_post_lbl', true);
                    echo"<p><strong>" . esc_html($pp_label) . "</strong></p>";
                    break;
                case "order":
                    $pp_order = get_post_meta($post_id, 'pp_post_order', true);
                    echo"<p><strong>" . esc_html($pp_order) . "</strong></p>";
                    break;
                case "images":
                    $post_image_id = get_post_thumbnail_id(get_the_ID());
                    if ($post_image_id) {
                        $thumbnail = wp_get_attachment_image_src($post_image_id, array(150, 150), false);
                        if ($thumbnail)
                            (string) $thumbnail = $thumbnail[0];
                        echo '<img width="150" height="150" src="' . $thumbnail . '" alt="" />';
                    }
                    break;
             case "content":
                    echo $content = get_the_excerpt();
                    break;


                /* If displaying the 'genre' column. */
                case 'category' :

                    /* Get the genres for the post. */
                    $terms = get_the_terms( $post_id, 'process-categories' );

                    /* If terms were found. */
                    if ( !empty( $terms ) ) {

                        $out = array();

                        /* Loop through each term, linking to the 'edit posts' page for the specific term. */
                        foreach ( $terms as $term ) {
                            $out[] = sprintf( '<a href="%s">%s</a>',
                                esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'ctl-stories' => $term->slug ), 'edit.php' ) ),
                                esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'ctl-stories', 'display' ) )
                            );
                        }

                        /* Join the terms, separating them with a comma. */
                        echo join( ', ', $out );
                    }

                    /* If no terms were found, output a default message. */
                    else {
                        _e( '' );
                    }
                    break;
            }
    }


// end add_custom_rewrite_rule


    /**
     * Display a custom taxonomy dropdown in admin
     * @author coolhappy
     *
     */

    function pp_filter_post_type_by_taxonomy() {
        global $typenow;
        $post_type = 'process_posts'; // change to your post type
        $taxonomy  = 'process-categories'; // change to your taxonomy
        if ($typenow == $post_type) {
           $selected      = isset($_GET[$taxonomy]) ? intval($_GET[$taxonomy]) : '';
            $info_taxonomy = get_taxonomy($taxonomy);
            wp_dropdown_categories(array(
                'show_option_all' => __("Show All {$info_taxonomy->label}"),
                'taxonomy'        => $taxonomy,
                'name'            => $taxonomy,
                'orderby'         => 'name',
                'selected'        => $selected,
                'show_count'      => true,
                'hide_empty'      => true,
            ));
        };
    }



    /**
     * Filter posts by taxonomy in admin
     * @author  coolhappy
     *
     */

    function pp_convert_id_to_term_in_query($query) {
        global $pagenow;
        $post_type = 'process_posts'; // change to your post type
        $taxonomy  = 'process-categories'; // change to your taxonomy
        $q_vars    = &$query->query_vars;
        if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
            $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
            $q_vars[$taxonomy] = $term->slug;
        }
    }


} //class end

} // main



