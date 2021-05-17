<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Public_Comment_Filter extends Depc_Controller_Public_Comment {

	public $i18n;

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {

		$this->model = Depc_Model_Public_Comment_Filter::get_instance();
		$this->get_i18n();
		$this->user_setting = $this->get_user_settings();

	}

	public function get_i18n() {
		$this->i18n = array(
			'trending'	=> esc_attr__( 'Trending', 'depc' ),
			'popular'	=> esc_attr__( 'Popular', 'depc' ),
			'oldest'	=> esc_attr__( 'Oldest', 'depc' ),
			'newest'	=> esc_attr__( 'Newest', 'depc' ),
			'sort'		=> esc_attr__( 'Sort by ', 'depc' ),
			'item'		=> esc_attr__( ' Items', 'depc' )
		);
	}

	public function scripts() {
		$script = static::get_model()->scripts();
		return $script;
	}

	private function restriction() {
		if ( is_user_logged_in() && $this->user_setting['memberfilter'] == 'on' )
			return true;
		elseif ( !is_user_logged_in() && $this->user_setting['guestfilter'] == 'on' )
			return true;
		else
			return false;
	}


	public function load() {

		echo static::render_template(
			'tpl/filter.php',
			array(
				'i18n'			=> $this->i18n,
				'permit'	 	=> $this->restriction(),
				'default'		=> $this->user_setting['default_sorting']
			),
			'always'
		);

	}

}