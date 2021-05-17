<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://themesawesome.com/
 * @since             1.0.0
 * @package           Timeline_Awesome
 *
 * @wordpress-plugin
 * Plugin Name:       Timeline Awesome
 * Plugin URI:        https://timeline.themesawesome.com/
 * Description:       Timeline and Story WordPress plugin.
 * Version:           1.0.3
 * Author:            Themes Awesome
 * Author URI:        https://themesawesome.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       timeline-awesome
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.3 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TIMELINE_AWESOME_VERSION', '1.0.3' );

define( 'TIMELINE_AWESOME', __FILE__ );

define( 'TIMELINE_AWESOME_BASENAME', plugin_basename( TIMELINE_AWESOME ) );

define( 'TIMELINE_AWESOME_NAME', trim( dirname( TIMELINE_AWESOME_BASENAME ), '/' ) );

define( 'TIMELINE_AWESOME_DIR', untrailingslashit( dirname( TIMELINE_AWESOME ) ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-timeline-awesome-activator.php
 */
function activate_timeline_awesome() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-timeline-awesome-activator.php';
	Timeline_Awesome_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-timeline-awesome-deactivator.php
 */
function deactivate_timeline_awesome() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-timeline-awesome-deactivator.php';
	Timeline_Awesome_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_timeline_awesome' );
register_deactivation_hook( __FILE__, 'deactivate_timeline_awesome' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-timeline-awesome.php';

require plugin_dir_path( __FILE__ ) . 'timeline-awesome-post-type.php';

require_once plugin_dir_path( __FILE__ ).'includes/element-helper.php';

function timeline_awesome_new_elements(){
  require_once plugin_dir_path( __FILE__ ).'elementor-widgets/timelines/timelines-control.php';
}

add_action('elementor/widgets/widgets_registered','timeline_awesome_new_elements');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_timeline_awesome() {

	$plugin = new Timeline_Awesome();
	$plugin->run();

}
run_timeline_awesome();

// Shortcode Function
add_filter('manage_timeline-awesome_posts_columns', function($columns) {
	return array_merge($columns, ['shortcode' => __('Shortcode', 'timeline-awesome')]);
});
 
add_action('manage_timeline-awesome_posts_custom_column', function($column_key, $post_id) {
	echo '<pre"><code>[timeline_awesome id="'. intval($post_id) .'"]</code></pre>';
}, 10, 2);

add_filter( 'single_template', 'timeline_awesome_post_custom_template', 50, 1 );
function timeline_awesome_post_custom_template( $template ) {

	if ( is_singular( 'timeline-awesome' ) ) {
		$template = TIMELINE_AWESOME_DIR . '/single-timeline-awesome.php';
	}
	
	return $template;
}


add_action( 'after_setup_theme', 'timeline_awesome_crb_load' );
function timeline_awesome_crb_load() {
	require_once( 'vendor/autoload.php' );
	\Carbon_Fields\Carbon_Fields::boot();
}

function timeline_awesome( $atts ) {

	// Get Attributes
	extract( shortcode_atts(
			array(
				'id' => ''   // DEFAULT SLUG SET TO EMPTY
			), $atts )
	);

	// WP_Query arguments
	$args = array (
		'page_id'              =>  $id,     // GET POST BY SLUG  // IGNORE IF YOU ARE GETTING ERROR ON THIS LINE IN YOUR EDITOR
		'post_type'         => 'timeline-awesome', // YOUR POST TYPE

	);
	//ob_start();

	// The Query
	$query = new WP_Query( $args );

	// The Loop
	if ( $query->have_posts() && $id != '' ) {

		wp_enqueue_style( 'ta-timeline-awesome-fontawesome', plugin_dir_url(__FILE__ ) . 'public/css/fontawesome.min.css', array(), '', 'all' );
        wp_enqueue_style( 'ta-timeline-awesome', plugin_dir_url(__FILE__ ) . 'public/css/timeline-awesome-public.css', array(), '1.0.3', 'all' );
        wp_enqueue_style( 'ta-timeline-awesome-responsive', plugin_dir_url(__FILE__ ) . 'public/css/responsive.css', array(), '1.0.3', 'all' );

		while ( $query->have_posts() ) {

			$query->the_post();
				
			$timeline_style = carbon_get_post_meta( get_the_ID(), 'timeline_style_choice' );

			if($timeline_style == 'vertical-1') {
				$timeline_style_part = dirname( __FILE__ ) .'/public/timeline-styles/timeline-vertical-1.php';
			}
			elseif($timeline_style == 'vertical-2') {
				$timeline_style_part = dirname( __FILE__ ) .'/public/timeline-styles/timeline-vertical-2.php';
			}
			elseif($timeline_style == 'vertical-3') {
				$timeline_style_part = dirname( __FILE__ ) .'/public/timeline-styles/timeline-vertical-3.php';
			}
			elseif($timeline_style == 'vertical-7') {
				$timeline_style_part = dirname( __FILE__ ) .'/public/timeline-styles/timeline-vertical-7.php';
			}
			elseif($timeline_style == 'vertical-9') {
				$timeline_style_part = dirname( __FILE__ ) .'/public/timeline-styles/timeline-vertical-9.php';
			}
			elseif($timeline_style == 'horizontal-6') {
				$timeline_style_part = dirname( __FILE__ ) .'/public/timeline-styles/timeline-horizontal-6.php';
			}
			elseif($timeline_style == 'horizontal-9') {
				$timeline_style_part = dirname( __FILE__ ) .'/public/timeline-styles/timeline-horizontal-9.php';
			}

			ob_start();
			include $timeline_style_part;

			$content = ob_get_clean();
			return $content;

		}
	} else {
		// no posts found
		return esc_html__( 'Sorry no html for this slug...', 'timeline-awesome' );

	}


	// Restore original Post Data
	wp_reset_postdata();
	//return ob_get_clean();
}
add_shortcode( 'timeline_awesome', 'timeline_awesome' );

function timeline_awesome_select_timeline_post() {
	$timelines_array = array();

	$args = array(
		'posts_per_page' => -1,
		'post_type' => 'timeline-awesome',
	);

	$timelines = get_posts($args);

	foreach( $timelines as $post ) { setup_postdata( $post );
		$timelines_array[$post->ID] = $post->post_title;
	}

	return $timelines_array;

	wp_reset_postdata();
}


add_action('wp_head', 'timeline_awesome_color_custom_styles', 100);
function timeline_awesome_color_custom_styles()
{
	$timeline_awesome_custom_args = array(
	'post_type'         => 'timeline-awesome',
	'posts_per_page'    => -1,
	);
	$timeline_awesome_custom = new WP_Query($timeline_awesome_custom_args);
	if ($timeline_awesome_custom->have_posts()) : ?>
   
   <style>
		<?php while($timeline_awesome_custom->have_posts()) : $timeline_awesome_custom->the_post();

		$timeline_title_color = carbon_get_post_meta( get_the_ID(), 'timeline_title_color' );
		$timeline_date_color = carbon_get_post_meta( get_the_ID(), 'timeline_date_color' );
		$timeline_content_color = carbon_get_post_meta( get_the_ID(), 'timeline_content_color' );
		$timeline_border_color = carbon_get_post_meta( get_the_ID(), 'timeline_border_color' );
		$timeline_dot_color = carbon_get_post_meta( get_the_ID(), 'timeline_dot_color' );
		$timeline_icon_color = carbon_get_post_meta( get_the_ID(), 'timeline_icon_color' );
		$timeline_border_icon_color = carbon_get_post_meta( get_the_ID(), 'timeline_border_icon_color' );
		$timeline_pag_color = carbon_get_post_meta( get_the_ID(), 'timeline_pag_color' );
		$timeline_pag_active_color = carbon_get_post_meta( get_the_ID(), 'timeline_pag_active_color' );
		$timeline_item_height_container = carbon_get_post_meta( get_the_ID(), 'timeline_item_height_container' );
		$timeline_bag_icon_color = carbon_get_post_meta( get_the_ID(), 'timeline_bag_icon_color' );

		?>
			
			<?php if(!empty($timeline_title_color)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-title, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .events-content h2, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> h1, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-4-content h3, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline .swiper-slide .timeline-title, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline__section .the-title, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-ver-7-timeline__content h2, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline h3.timeline-title, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .event input[type="radio"]:checked ~ .content-perspective .content-inner h3, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .member-infos .member-title {
				color: <?php echo esc_html($timeline_title_color); ?>;
			}
			<?php } ?>

			<?php if(!empty($timeline_date_color)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-dots button, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-info, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> #mainCont, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .tl-nav li div, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .tl-year p, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .vertical-10-item:before, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline .swiper-slide .timeline-year, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline__section .milestone, .vertical-4.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline__content-title, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-ver-7-timeline__date, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .thumb span, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .event input[type="radio"]:checked ~ .thumb span {
				color: <?php echo esc_html($timeline_date_color); ?>;
			}
			<?php } ?>

			<?php if(!empty($timeline_content_color)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> p, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-text, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> #mainCont p, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline .swiper-slide-active .timeline-text, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline__section p, .vertical-4.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline__content-desc, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-ver-7-timeline__content p, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .event input[type="radio"]:checked ~ .content-perspective .content-inner p {
				color: <?php echo esc_html($timeline_content_color); ?>;
			}
			<?php } ?>

			<?php if(!empty($timeline_border_color)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .filling-line, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-marker:after, .vertical-4.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-background:before, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline .swiper-pagination::before, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-ver-7-timeline__container::before {
				background-color: <?php echo esc_html($timeline_border_color); ?>;
			}
			<?php } ?>
			
			<?php if(!empty($timeline_dot_color)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .events a.selected::after {
				background-color: <?php echo esc_html($timeline_dot_color); ?>;
				border-color: <?php echo esc_html($timeline_dot_color); ?>;
			}
			<?php } ?>

			<?php if(!empty($timeline_dot_color)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-marker:before, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline .swiper-pagination-bullet::before {
				background: <?php echo $timeline_dot_color; ?>;
			}
			<?php } ?>

			<?php if(!empty($timeline_dot_color)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .events a.older-event::after {
				border-color: <?php echo esc_html($timeline_dot_color); ?>;
				border-color: <?php echo esc_html($timeline_dot_color); ?>;
			}
			<?php } ?>
			
			<?php if(!empty($timeline_icon_color)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-horizontal-2-navigation a, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-ver-7-timeline__img .icon-vertical7 {
				color: <?php echo esc_html($timeline_icon_color); ?>;
			}
			<?php } ?>
			
			<?php if(!empty($timeline_border_icon_color)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-horizontal-2-navigation a {
				border-color: <?php echo esc_html($timeline_border_icon_color); ?>;
			}
			<?php } ?>

			<?php if(!empty($timeline_title_color)) { ?>
			.vertical-4.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-item:before {
				color: <?php echo esc_html($timeline_title_color); ?>;
				border-color: <?php echo esc_html($timeline_title_color); ?>;
			}
			<?php } ?>          

			<?php if(!empty($timeline_pag_color)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline .swiper-pagination-bullet, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline__nav ul li {
				color: <?php echo esc_html($timeline_pag_color); ?>;
			}
			<?php } ?>

			<?php if(!empty($timeline_pag_active_color)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline .swiper-pagination-bullet-active, .timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline__nav ul li.active {
				color: <?php echo esc_html($timeline_pag_active_color); ?>;
			}
			<?php } ?>

			<?php if(!empty($timeline_item_height_container)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline .swiper-container {
				height: <?php echo esc_html($timeline_item_height_container); ?>px;
			}
			<?php } ?>

			<?php if(!empty($timeline_bag_icon_color)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-ver-7-timeline__img.timeline-ver-7-timeline__img--picture {
				background: <?php echo $timeline_bag_icon_color; ?>;
			}
			<?php } ?>

			<?php if(!empty($timeline_border_icon_color)) { ?>
			.timeline-post-<?php echo esc_attr(get_the_ID()); ?> .timeline-ver-7-timeline__img {
				-webkit-box-shadow: 0 0 0 4px <?php echo $timeline_border_icon_color; ?>, inset 0 2px 0 rgba(0, 0, 0, 0.08), 0 3px 0 4px rgba(0, 0, 0, 0.05);
				box-shadow: 0 0 0 4px <?php echo $timeline_border_icon_color; ?>, inset 0 2px 0 rgba(0, 0, 0, 0.08), 0 3px 0 4px rgba(0, 0, 0, 0.05);
			}
			<?php } ?>

		<?php endwhile; wp_reset_postdata(); ?>
	</style>

	<?php endif;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Block;

add_action( 'carbon_fields_register_fields', 'timeline_awesome_add_menu' );
function timeline_awesome_add_menu() {

	Container::make( 'theme_options', __( 'Documentation' ) )
    ->set_page_parent( 'edit.php?post_type=timeline-awesome' ) // identificator of the "Appearance" admin section
    ->add_fields( array(
    ) );

    Container::make( 'theme_options', __( 'Go Pro' ) )
    ->set_page_parent( 'edit.php?post_type=timeline-awesome' ) // identificator of the "Appearance" admin section
    ->add_fields( array(
    ) );
}