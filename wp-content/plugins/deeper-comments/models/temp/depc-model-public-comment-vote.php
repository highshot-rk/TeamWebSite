<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Model_Public_Comment_Vote extends Depc_Model_Public_Comment {



	public $validator;
	public $ajax_vote_action  = 'dpr_vote';
	private static $nounce  = 'dpr_vote';
	private $settings;

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// init validator
		$this->validator = new Depc_Request_Validator;

		// set action vote cockie for guest
		// Depc_Actions_Filters::add_action( 'init', $this, 'set_coockie' );
		Depc_Actions_Filters::add_action( 'wp_print_scripts', $this, 'scripts' );
		// get expirtions settings
		$this->settings['user_date']  	= Depc_Core::get_option( 'dc_vote_user_date', 'Voting' );
		$this->settings['guest_date']  	= Depc_Core::get_option( 'dc_vote_guest_date', 'Voting' );

	}

	/**
	 *
	 */
	public function render() {

		// security check
		check_ajax_referer( self::$nounce, 'security' );

		// get validator object
		$logged_in = $this->validator;

		// process guest users
		if ( Depc_Core::get_option( 'dc_vote_guest_enable', 'Voting', 'on') == 'off'  && $logged_in->is_registered() == false ) {
			wp_send_json( array( 'title' => 'Voting Message', 'message' => __( 'To like or dislike, please log in or register first!', 'depc' ) , 'resp' => -1 ) );
		}

		// process logged in users
		$response = $this->update_user_vote( trim( $_POST['comment_id'] ) );
		wp_send_json( $response );
	}

	/**
	 * Set coockie for vote
	 */
	public function set_coockie() {

		$logged_in = $this->validator;
		if ( $logged_in->is_registered() == false ) {
			if( !isset( $_COOKIE['dpr_vote'] ) ) {

				setcookie( 'dpr_vote', '', 30 * DAYS_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
			}
		}

	}

	/**
	 * @return js scripts
	 */
	public static function scripts() {

		$id = new self();
		$id = $id->post_id();

		$nounce = wp_create_nonce( self::$nounce );
		// Generating javascript code tpl
		$javascript = '
			jQuery(document).ready(function() {
				jQuery(".dpr-discu-container_'.$id.' .dpr-discu-box .dpr-discu-box-footer-metadata-like .dpr-cont-discu-like .dpr-discu-like").depcVote({
					id: "'.$id.'",
					nounce : "'. $nounce .'",
					like: "like"
				});
				jQuery(".dpr-discu-container_'.$id.' .dpr-discu-box .dpr-discu-box-footer-metadata-like .dpr-cont-discu-dislike .dpr-discu-dislike").depcVote({
					id: "'.$id.'",
					nounce : "'. $nounce .'",
					like: "dislike"
				});
			});';

			return $javascript;

	}

	/**
	 * @return Ip Address
	 */
	public function get_client_ip() {

		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;

	}


	/**
	 * [update_user_vote description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function update_user_vote( $id ) {

		// user who voted
		$current_user = static::get_current_user();
		if($current_user->ID == 0) {
			$current_user->ID = str_replace( '.', '', $this->get_client_ip());
		}
		// get vote user data
		$voted = get_comment_meta( $id, 'dpr_voted_user', true );
		if (empty($voted)) {
			$voted = [];
		}
		$voted['liked_user'] = isset( $voted['liked_user'] ) ? $voted['liked_user'] : $voted['liked_user'] = '' ;
		$voted['disliked_user'] = isset( $voted['disliked_user'] ) ? $voted['disliked_user'] : $voted['disliked_user'] = '' ;
		if(!isset($this->settings['user_date']) || !$this->settings['user_date']) {
			$this->settings['user_date'] = 1;
		}
		// process logged in users
		if ( $_POST['like'] == 'like' ) {
			// get likes count
			$likes_vote = get_comment_meta( $id, 'like_count', true );

			if(isset($voted['disliked_user'][$current_user->ID])) {
				return 0;
			}

			// check if user liked before or not
			if ( !isset($voted['liked_user'][$current_user->ID]) ) {

				// update user id and time
				if ( empty( $voted['liked_user'] ) ) {
					$voted['liked_user'] = array( $current_user->ID );
				} else {
					$voted['liked_user'][] = $current_user->ID;
				}

				$voted['liked_user'][$current_user->ID]['time'] = current_time( 'mysql' );

				// update count
				$likes_vote = isset( $likes_vote ) ? ++$likes_vote : 1 ;

				// update liked count
				update_comment_meta( $id , 'like_count', $likes_vote );
				// update voted user
				update_comment_meta( $id , 'dpr_voted_user', $voted );

				return 1;

			} else {

				// calculate for date diffrence
				$from = strtotime( $voted['liked_user'][$current_user->ID]['time'] );
				$today =  strtotime( current_time( 'mysql' ) );
				$difference = time() - ($today - $from);
				$get_expirtion_date = strtotime( $this->settings['user_date'] . 'day' , time() );
				if ( $difference > $get_expirtion_date ) {

					// update liked user
					$voted['liked_user'][$current_user->ID]['time'] = current_time( 'mysql' );

					$likes_vote = isset( $likes_vote ) ? ++$likes_vote : 1 ;

					// update voted user
					update_comment_meta( $id , 'like_count', $likes_vote );
					// update count
					update_comment_meta( $id , 'dpr_voted_user', $voted );
					return 1;
				} else {

					unset($voted['liked_user'][$current_user->ID]);
					// update liked user
					$likes_vote = isset( $likes_vote ) ? --$likes_vote : 1 ;

					// update voted user
					update_comment_meta( $id , 'like_count', $likes_vote );
					// update count
					update_comment_meta( $id , 'dpr_voted_user', $voted );

					return -1;
				}
			}

		} elseif (  $_POST['like'] == 'dislike' ) {

			// get disliket count
			$dislikes_vote = get_comment_meta( $id, 'dislike_count', true );

			if ( ! is_array( $voted ) ) {
				$voted = [];
			}
			if(isset($voted['liked_user'][$current_user->ID])) {
				return 0;
			}
			if ( !isset($voted['disliked_user'][$current_user->ID]) ) {
				// update user id
				if ( empty($voted['disliked_user']) ) {
					$voted['disliked_user'] 	= array( $current_user->ID  );
				} else {
					$voted['disliked_user'][] = $current_user->ID;
				}

				$voted['disliked_user'][$current_user->ID]['time'] = current_time( 'mysql' );

				$dislikes_vote = isset( $dislikes_vote ) ? ++$dislikes_vote : 1 ;

				if ( ! isset( $voted['dislike_count'] ) ) {
					$voted['dislike_count'] = 0;
				}
				// update count
				$voted['dislike_count'] = ++$voted['dislike_count'];

				// update count
				update_comment_meta( $id , 'dislike_count', $dislikes_vote );
				// update voted user
				update_comment_meta( $id , 'dpr_voted_user', $voted );

				return 1;

			} else {

				// calculate for date diffrence
				$from = strtotime( $voted['disliked_user'][$current_user->ID]['time'] );
				$today =  strtotime( current_time( 'mysql' ) );
				$difference = time() - ($today - $from);

				$get_expirtion_date = strtotime( $this->settings['user_date'] . 'day' , time() );
				if ( $difference > $get_expirtion_date ) {
					// update liked user
					$voted['disliked_user'][$current_user->ID]['time'] = current_time( 'mysql' );

					$dislikes_vote = isset( $dislikes_vote ) ? ++$dislikes_vote : 1 ;
					// update voted user
					update_comment_meta( $id , 'dislike_count', $dislikes_vote );
					// update count
					update_comment_meta( $id , 'dpr_voted_user', $voted );
					return 1;
				} else {
					// update liked user
					$dislikes_vote = isset( $dislikes_vote ) ? --$dislikes_vote : 1 ;
					unset($voted['disliked_user'][$current_user->ID]);
					// update voted user
					update_comment_meta( $id , 'dislike_count', $dislikes_vote );
					// update count
					update_comment_meta( $id , 'dpr_voted_user', $voted );

					return -1;
				}
				// calculate for date diffrence
				$from = strtotime( $voted['disliked_user'][$current_user->ID]['time'] );
				$today =  strtotime( current_time( 'mysql' ) );
				$difference = $today - $from;
				$get_expirtion_date = strtotime( $this->settings['user_date'] . 'day', time() );

				if ( !$from || $difference > $get_expirtion_date ) {

					if(empty( $voted['disliked_user'] )) {
						$voted['disliked_user'] = [];
					}
					$voted['disliked_user'][$current_user->ID]['time'] = current_time( 'mysql' );

					// update count
					$dislikes_vote = isset( $dislikes_vote ) ? ++$dislikes_vote : 1 ;

					// update count
					update_comment_meta( $id , 'dislike_count', $dislikes_vote );
					// update voted user
					update_comment_meta( $id , 'dpr_voted_user', $voted );
					return 1;
				} else {
					// update liked user
					unset($voted['disliked_user'][$current_user->ID]);
					$dislikes_vote = isset( $dislikes_vote ) ? --$dislikes_vote : 1 ;

					// update voted user
					update_comment_meta( $id , 'dislike_count', $dislikes_vote );
					// update count
					update_comment_meta( $id , 'dpr_voted_user', $voted );

					return -1;
				}
			}

		}
	}

}