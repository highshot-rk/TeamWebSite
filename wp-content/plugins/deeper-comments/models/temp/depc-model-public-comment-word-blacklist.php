<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Model_Public_Comment_Word_Blacklist extends Depc_Model_Public_Comment {



	public $validator;

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// init validator
		$this->validator = new Depc_Request_Validator;

		// get settings
		$this->user_setting =  $this->get_user_settings();

	}

	public function process($id,$comment) {
		$comment_content = strtolower($comment->comment_content);
		$words = \Depc_Core::get_option( 'dc_word_filter', 'Word_Blacklist' ,'');
		$reported = false;
		if ( !empty( $words ) ) {
			$words_e = explode(',' , $words);
			if (!$words_e) {
				$words_e = [$words];
			}

			foreach ($words_e as $w ) {
				if ( strpos(  strtolower($comment_content), trim( strtolower( $w ) ) ) !== false) {

					$reported['reported_user'][] = 'Word Filter' ;
					$reported['count'] = 0;
					update_comment_meta( $comment->comment_ID, 'dpr_inapporpriate_user', $reported );
					update_comment_meta( $comment->comment_ID, 'dpr_inapporpriate_type', 'word_filter' );
					wp_set_comment_status( $comment->comment_ID, 'hold' );
					$reported = true;
					break;
				}
			}
		}

		if ( ! get_option('comment_moderation')  && $reported == false ) {
			wp_set_comment_status( $comment->comment_ID, 'approve' );
		} else if ( get_option('comment_moderation')  &&  $reported == false ) {
			wp_set_comment_status( $comment->comment_ID, 'hold' );
		}
		return $comment;
	}

}
