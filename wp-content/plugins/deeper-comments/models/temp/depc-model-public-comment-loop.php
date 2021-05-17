<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Model_Public_Comment_Loop extends Depc_Model_Public_Comment {



	public $validator;
	private $query = '';
	public $ajax_flag_action  = 'dpr_flag_comment';
	public $ajax_social_action  = 'dpr_social_comment';
	private static $nounce  = 'dpr_edit_comment';
	private static $settings;


	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// init validator
		$this->validator = new Depc_Request_Validator;
		self::$settings = $this->get_user_settings();
		self::$settings['default_sorting'] = $this->validator->get_comment_order( self::$settings['default_sorting'] );

	}

	/**
	 * @return comment object
	 */
	public static function get_comment() {

		$id = new self();
		$id = $id->post_id();

		$number = -1;
		if(self::$settings['loadmore'] == 'on') {
			$number = self::$settings['dc_defultcnt_comment'];
		}

		$args = array(
			'post_id' => $id,
			'number' => $number,
			'status' => 'approve',
			'parent' => 0,
			'order'	=> self::$settings['default_sorting']
			);

		if(self::$settings['loadmore'] != 'on') {
			unset($args['number']);
		}
		$depc_query = new WP_Comment_Query;
		$comments = $depc_query->query( $args );

		return  $comments;

	}

	/**
	 * @return query for comment parent cols
	 */
	public static function get_comment_parent() {

		global $wpdb;

		$sql = "SELECT comment_parent from $wpdb->comments";
		$result = $wpdb->get_col( $sql );

		$result = array_unique( $result );
		return $result;

	}

	/**
	 * @return query for comment parent cols
	 */
	public static function get_validator() {

		$validator = new Depc_Request_Validator;
		return $validator;
	}


	public function render_flag() {

		// security check
		check_ajax_referer( self::$nounce, 'security' );

		// validate for non editing
		Depc_Request_Validator::is_numeric( $_POST['comment_id'] , true );

		// user who reported
		$current_user = static::get_current_user();
		// get comment user reporter if exist
		$reported = get_comment_meta( trim( $_POST['comment_id'] ), 'dpr_inapporpriate_user', true );
		if(!$reported) {
			$reported = [];
		}
		$reported['reported_user'] = isset( $reported['reported_user'] ) ? $reported['reported_user'] : $reported['reported_user'] = array() ;
		$reported['count'] = isset( $reported['count'] ) ? $reported['count'] : $reported['count'] = 0 ;

		// if reported before do nothing
		if ( in_array( $current_user->ID , $reported['reported_user'] ) == 1 ) {
			wp_send_json( array( 'message' => __( 'You reported this comment before!', 'depc' ) ) );
		}

		// check if not in the list update user id
		if ( in_array( $current_user->ID , $reported['reported_user'] ) != 1 ) {
			// update user id
			$reported['reported_user'][] = $current_user->ID ;
			// update count
			$reported['count'] = ++$reported['count'];
			update_comment_meta( trim(  $_POST['comment_id'] ), 'dpr_inapporpriate_user', $reported );
			update_comment_meta( trim(  $_POST['comment_id'] ), 'dpr_inapporpriate_type', 'inappropriate' );
			// get limit for baning
			$inappropriate_level = Depc_Core::get_option( 'dc_inapp_auto_ban', 'Inappropriate_Comments' );

			if ( $inappropriate_level < $reported['count'] ) {
				if (is_admin()) {
					wp_set_comment_status( trim(  $_POST['comment_id'] ), 'hold' );
				}
			}

			// send result and die
			wp_send_json( array( 'message' => __( 'Comment successfully marked as inapporpriate', 'depc' ) ) );
		}

	}

	public function render_social() {

		// security check
		check_ajax_referer( self::$nounce, 'security' );

		// user who social
		$current_user = static::get_current_user();
		// validate
		$nummeric = $this->validator;
		$nummeric::is_numeric( $_POST['comment_id'] , true );

		// get vote meta data
		$social = get_comment_meta( $_POST['comment_id'], 'dpr_social', true );
		if(!is_array( $social)) {
			$social = [];
		}
		$social['shared_user'] = isset( $social['shared_user'] ) ? $social['shared_user'] : $social['shared_user'] = array() ;
		if(!is_array($social['shared_user'])) {
			$social['shared_user'] = [];
		}
		if ( in_array( $current_user->ID , $social['shared_user'] ) != 1 ) {
			// update user id
			$social['shared_user'][] = $current_user->ID;

			// update count
			$social['shared_count'] = isset( $social['shared_count'] ) ? ++$social['shared_count'] : 1 ;
			update_comment_meta( $_POST['comment_id'] , 'dpr_social', $social );
			wp_send_json( array( 'message' => __( 'Comment Shared!', 'depc' ) , 'resp' => 1 ) );
		} else {
			wp_send_json( array( 'message' => __( 'Comment Not Shared!', 'depc' ) , 'resp' => 0 ) );
		}

	}

}