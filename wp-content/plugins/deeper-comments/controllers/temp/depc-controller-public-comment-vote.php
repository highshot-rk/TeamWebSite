<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Public_Comment_Vote extends Depc_Controller_Public_Comment {

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {}

 	public static function get_vote() {

 		if ( null === self::$comment_vote ) {
 			self::$comment_vote = new self();
 		}
 		return self::$comment_vote;

	}

	public function load() {}

}