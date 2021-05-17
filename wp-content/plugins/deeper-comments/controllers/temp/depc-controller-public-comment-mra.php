<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Public_Comment_MRA extends Depc_Controller_Public_Comment {

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {
		// get validator class
		$this->validator = new Depc_Request_Validator;
		// get load more class
		$this->model = Depc_Model_Public_Comment_Loadmore::get_instance();
		// get user setting from admin panel
		$this->settings = $this->get_user_settings();
	}


	public function load() {
		if(!isset($this->user_setting['default_sorting'])) {
			$this->user_setting['default_sorting'] = 'asc';
		}

		echo static::render_template(
			'tpl/most-recent-authors.php',
			array(
				'default'		=> $this->user_setting['default_sorting']
			),
			'always'
		);

	}

}