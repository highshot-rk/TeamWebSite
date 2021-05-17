<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class timeline_awesome_post_block extends Widget_Base {

	public function get_name() {
		return 'timeline_awesome-post-block';
	}

	public function get_title() {
		return __( 'Timelines', 'timeline-awesome' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return [ 'timeline_awesome-general-category' ];
	}

	protected function _register_controls() {
		/*-----------------------------------------------------------------------------------
			POST BLOCK INDEX
			1. POST SETTING
		-----------------------------------------------------------------------------------*/

		/*-----------------------------------------------------------------------------------*/
		/*  1. POST SETTING
		/*-----------------------------------------------------------------------------------*/
		$this->start_controls_section(
			'section_timeline_awesome_post_block_post_setting',
			[
				'label' => __( 'Post Setting', 'timeline-awesome' ),
			]
		);

		$this->add_control(
			'select_timeline',
			[
				'label' => __( 'Select Timeline', 'timeline-awesome' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'date',
				'options' => timeline_awesome_select_timeline_post(),
				'description' => __( 'Select post order by (default to latest post).', 'timeline-awesome' ),
			]
		);

		$this->end_controls_section();
		/*-----------------------------------------------------------------------------------
			end of post block post setting
		-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
		'section_timeline_awesome_block_style_setting',
			[
				'label' => __( 'Typography', 'timeline-awesome' ),
			]
		);

		$this->add_control(
			'section_timeline_awesome_fff_setting',
			[
				'name' => 'fff_schemes_notice',
				'type' => Controls_Manager::RAW_HTML,
				'raw' => sprintf( __( '<p>In order to customize fonts, let&#39;s upgrade to pro</p><br /><a href="https://1.envato.market/EmM0W" class="btn-buy" target="_blank">Upgrade to Pro</a>', 'timeline-awesome' ), Settings::get_url() ),
				'content_classes' => 'fasgag',
				'render_type' => 'ui',
			]
		);

	}

	protected function render() {

		$instance = $this->get_settings();

		/*-----------------------------------------------------------------------------------*/
		/*  VARIABLES LIST
		/*-----------------------------------------------------------------------------------*/

		/* POST SETTING VARIBALES */
		$select_timeline 			= ! empty( $instance['select_timeline'] ) ? $instance['select_timeline'] : '';


		/* end of variables list */


		/*-----------------------------------------------------------------------------------*/
		/*  THE CONDITIONAL AREA
		/*-----------------------------------------------------------------------------------*/

		include ( plugin_dir_path(__FILE__).'tpl/timelines-block.php' );

		/*-----------------------------------------------------------------------------------
		  end of conditional end of post block.
		-----------------------------------------------------------------------------------*/

		?>

		<?php

	}

	protected function content_template() {}

	public function render_plain_content( $instance = [] ) {}

}

Plugin::instance()->widgets_manager->register_widget_type( new timeline_awesome_post_block() );