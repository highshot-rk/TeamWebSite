<?php
/*-----------------------------------------------------------------------------------*/
/* TImeline Awesome Post Type
/*-----------------------------------------------------------------------------------*/
add_action('init', 'timeline_awesome_register');

function timeline_awesome_register() {

	$labels = array(
		'name'                => esc_html_x( 'Timelines', 'Post Type General Name', 'timeline-awesome' ),
		'singular_name'       => esc_html_x( 'Timelines', 'Post Type Singular Name', 'timeline-awesome' ),
		'menu_name'           => esc_html__( 'Timelines', 'timeline-awesome' ),
		'parent_item_colon'   => esc_html__( 'Parent Timelines:', 'timeline-awesome' ),
		'all_items'           => esc_html__( 'All Timelines', 'timeline-awesome' ),
		'view_item'           => esc_html__( 'View Timelines', 'timeline-awesome' ),
		'add_new_item'        => esc_html__( 'Add New Timelines', 'timeline-awesome' ),
		'add_new'             => esc_html__( 'Add New', 'timeline-awesome' ),
		'edit_item'           => esc_html__( 'Edit Timelines', 'timeline-awesome' ),
		'update_item'         => esc_html__( 'Update Timelines', 'timeline-awesome' ),
		'search_items'        => esc_html__( 'Search Timelines', 'timeline-awesome' ),
		'not_found'           => esc_html__( 'Not found', 'timeline-awesome' ),
		'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'timeline-awesome' ),
	);
	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'query_var'          => 'timelines',
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'rewrite'            => array( 'slug' => 'timelines' ),
		'supports'           => array('title'),
		'menu_position'       => 7,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'menu_icon'           => 'dashicons-editor-ul',
	);

	register_post_type( 'timeline-awesome', $args );

}

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Block;

add_action( 'carbon_fields_register_fields', 'timeline_awesome_field_in_post' );
function timeline_awesome_field_in_post() {

	require dirname( __FILE__ ) .'/timeline-awesome-ctrl.php';

	Container::make( 'post_meta', 'timeline_repeater_cont', esc_html('Timeline Awesome') )
	->where( 'post_type', '=', 'timeline-awesome' )
	->set_priority( 'high' )
	->add_tab(  esc_html__( 'Layout', 'timeline-awesome' ), array(

        Field::make( 'select', 'timeline_style_choice', esc_html__( 'Select Style', 'timeline-awesome' ) )
		->add_options( array(
			'vertical-1' => esc_html__('Vertical 1', 'timeline-awesome'),
			'vertical-2' => esc_html__('Vertical 2', 'timeline-awesome'),
			'vertical-3' => esc_html__('Vertical 3', 'timeline-awesome'),
			'vertical-7' => esc_html__('Vertical 7', 'timeline-awesome'),
			'vertical-9' => esc_html__('Vertical 9', 'timeline-awesome'),
			'horizontal-6' => esc_html__('Horizontal 6', 'timeline-awesome'),
			'horizontal-9' => esc_html__('Horizontal 9', 'timeline-awesome'),
		) ),
    ))
	->add_tab(  __( 'Content' ), array(

		Field::make( 'complex', 'timeline_items', esc_html__( 'Timeline Items', 'timeline-awesome' ) )
		->set_layout( 'tabbed-horizontal' )
		->add_fields( array(
				Field::make( 'text', 'timeline_item_title', esc_html__( 'Title', 'timeline-awesome' ) )
				->set_attribute( 'placeholder', esc_html__('Timeline Title', 'timeline-awesome' ) )
				->set_width( 40 ),

				Field::make( 'text', 'timeline_date', esc_html__( 'Timeline Date', 'timeline-awesome' ) )
				->set_attribute( 'placeholder', '2020' )
				->set_width( 25 ),

				Field::make( 'textarea', 'timeline_item_content', esc_html__( 'Timeline Content', 'timeline-awesome' ) )
				->set_attribute( 'placeholder', esc_html__('Put your text here...', 'timeline-awesome' ) )
				->set_width( 80 ),

				Field::make( 'image', 'timeline_item_img', esc_html__( 'Timeline Image', 'timeline-awesome' ) )
				->set_width( 20 ) ,

				Field::make( 'separator', 'timeline_custom_option', esc_html__('Optional', 'timeline-awesome' ) ),

				Field::make( 'icon', 'social_site_icon', esc_html__( 'Icon', 'timeline-awesome' ) )
				->set_conditional_logic( array(
					array(
						'field' => 'parent.timeline_style_choice',
						'value' => 'vertical-7',
						'compare' => '=',
					)
				) )
				->set_width( 40 ),

				Field::make( 'text', 'timeline_item_subtitle', esc_html__( 'Subtitle', 'timeline-awesome' ) )
				->set_conditional_logic( array(
					array(
						'field' => 'parent.timeline_style_choice',
						'value' => 'vertical-2',
						'compare' => '=',
					)
				) )
				->set_width( 35 ),

				Field::make( 'text', 'timeline_item_full_date', esc_html__( 'Full Date', 'timeline-awesome' ) )
				->set_conditional_logic( array(
					array(
						'field' => 'parent.timeline_style_choice',
						'value' => 'horizontal-4',
						'compare' => '=',
					)
				) )
				->set_width( 35 ),
				Field::make( 'color', 'timeline_horizontal_10_color', esc_html__( 'Timeline Color', 'timeline-awesome' ) )
				->set_conditional_logic( array(
					array(
						'field' => 'parent.timeline_style_choice',
						'value' => 'horizontal-10',
						'compare' => '=',
					)
				) )
				->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) ),
		) )
		->set_default_value( array(
			array(
			),
		) ),
	))
	->add_tab(  __( 'Customize' ), array(
		Field::make( 'html', 'asfafaf' )
   		->set_html( '<p>In order to customize colors, let&#39;s upgrade to pro</p><a href="https://1.envato.market/EmM0W" target="_blank" class="btn-buy">Upgrade to Pro</a>' )
	));

	// For Gutenberg Blocks
	Block::make( esc_html( 'Timeline Awesome' ) )
	->add_fields( array(
		Field::make( 'association', 'timeline_gutenberg_block', esc_html( 'Timeline Awesome Post') )
		->set_min( 1 )
		->set_max( 1 )
		->set_types( array(
			array(
				'type'      => 'post',
				'post_type' => 'timeline-awesome',
			)
		) )
	) )
	->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
		require dirname( __FILE__ ) .'/gutenberg-blocks/timeline-block.php';
	} );
}