<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Public_Comment_Edit extends Depc_Controller_Public_Comment {
	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {

		$this->model = Depc_Model_Public_Comment_Edit::get_instance();

 	}

	public function load(){

		$scripts = static::get_model()->get_script();
		echo static::render_template(
			'tpl/edit.php',
			array(
				'scripts'       => $scripts
				),
			'always'
			);

	}

}