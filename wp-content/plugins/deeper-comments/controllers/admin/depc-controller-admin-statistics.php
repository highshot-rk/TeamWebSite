<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Admin_Statistics extends Depc_Controller_Admin{

    private static $instances = array();

	/**
	 * Provides access to a single instance of a module using the singleton pattern
	 *
	 * @since    1.0.0
	 */
	public static function get_instance() {
		$classname = get_called_class();

		if ( ! isset( self::$instances[ $classname ] ) ) {
			self::$instances[ $classname ] = new $classname();
		}
		return self::$instances[ $classname ];

	}

	/**
	 * Register callbacks for actions and filters
	 *
	 * @since    1.0.0
	 */
	public function register_hook_callbacks() {

	}

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {
		$this->set_actions();
    }

    /**
	 * Set WP Hooks (Actions | Filters)
	 *
	 * @since    1.0.0
	 */
	protected function set_actions() {
		// 	add_action('deeper_comments_new_comment', [$this, 'send_notification'], 10, 2);
		add_action('admin_menu', [$this, 'setup_menu'], 100);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_assets'], 10);
    }

    /**
	 * Enqueue Admin Styles And Scripts
	 *
	 * @since    1.0.0
	 */
    public function setup_menu () {
		add_submenu_page(
			'deeper_intro',
			__('Analysis', 'depc'),
			__('Analysis', 'depc'),
			'manage_options',
			'deeper_analysis',
			[$this, 'render'] //callback function
		);
	}

    /**
	 * Enqueue Admin Styles And Scripts
	 *
	 * @since    1.0.0
	 */
    public function enqueue_assets () {
		$screen = get_current_screen();
		if($screen->id == 'deeper-comments_page_deeper_analysis') {
			wp_enqueue_script(
				\Depc_Core::DEPC_ID . '-charts-admin-scripts',
				\Depc_Core::get_depc_url() . 'views/js/plugins/Chart.min.js',
				array( 'jquery' ),
				Depc_Core::DEPC_VERSION,
				false
			);
		}
	}

    /**
	 * Render The Statistics Page Content
	 *
	 * @since    1.0.0
	 */
    public function render () {
		echo static::render_template(
			'tpl/analysis.php',
			[],
			'always'
		);
	}
}