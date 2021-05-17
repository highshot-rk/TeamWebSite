<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */


/**
 * Load more handler class
 */
class Depc_Model_Public_Comment_Loadmore extends Depc_Model_Public_Comment {



	/**
	 * [$validator instance of validate class]
	 * @var [object]
	 */
	public $validator;


	/**
	 * [$ajax_action ajax id]
	 * @var string
	 */
	public $ajax_action  = 'dpr_loadmore';


	/**
	 * [$nounce secutiry nounce name]
	 * @var string
	 */
	private static $nounce  = 'dpr_loadmore';


	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// init validator
		$this->validator = new Depc_Request_Validator;
		// get user defined option from admin panel
		$this->settings = $this->get_user_settings();
		// get default sorting and convert to order ASC or DESC ?
		$this->settings['default_sorting'] = $this->validator->get_comment_order( $this->settings['default_sorting'] );

	}



	public function render() {

		// security check
		check_ajax_referer( self::$nounce, 'security' );

		if ( $_POST['type'] === 'ASC' || $_POST['type'] === 'DESC' ) {
			$comments = $this->load_default_halfcomments( $_POST['id'], $_POST['loaded_cm'] , $_POST['type'] );
		} else {
			$comments = $this->load_filter_halfcomments( $_POST['id'], $_POST['loaded_cm'] , $_POST['type'] );
		}

		// remained comments
		$half_cm = (int) $_POST['half_cm'] - (int) $this->settings['loadmore_count'] ;

		// loaded comment
		$loaded_cm = isset($_POST['loaded_cm']) ? (int)$_POST['loaded_cm'] : 0;
		$loaded_cm = $this->settings['loadmore_count'] + $loaded_cm;

		$loop = Depc_Controller_Public_Comment_Loop::get_comment_loop();
		$response = $loop->load( $comments , $_POST['id'] );

		wp_send_json( array(
			'data' => $response,
			'half_cm' => $half_cm,
			'loaded_cm' => $loaded_cm,
			'type' => $_POST['type']
		));

	}

	public function load_default_halfcomments( $id , $offset , $order ) {

		$args = array(
			'post_id'	=> $id,
			'number'	=> $this->settings['loadmore_count'],
			'status'	=> 'approve',
			'parent'	=> 0,
			'offset'	=> $offset,
			'order'		=> $order
			);

		$depc_query = $this->get_query();
		$comments = $depc_query->query( $args );
		return $comments;

	}

	public function load_filter_halfcomments( $id , $offset , $type ) {

		// Arguments for the query
		$args = array(
			'post_id'	=> $id,
			'number'	=> $this->settings['loadmore_count'],
			'status' => 'approve',
			'parent'	=> 0,
			'offset'	=> $offset
		);

		switch ( $type ) {
			case 'popular':
				// $args['orderby']  = array(
				// 	'like_count' => 'ASC',
				// 	'dislike_count' => 'ASC',
				// 	'dpr_social' => 'ASC',
				// 	'post_date'   => 'ASC',
				// );
				$args['order']  =  'ASC';
			break;

			case 'trending':
				// $args['orderby']  = array(
				// 	'like_count' => 'DESC',
				// 	'dislike_count' => 'DESC',
				// 	'dpr_social' => 'DESC',
				// 	'post_date'   => 'DESC',
				// );
				$args['order']  =  'DESC';
			break;
		}

		$depc_query = $this->get_query();
		$comments = $depc_query->query( $args );
		return $comments;

	}

	/**
	 * @return get parent comment count
	 */
	public static function parent_comments( $id ) {

		global $wpdb;

		$query = "SELECT COUNT(comment_post_id) AS count FROM $wpdb->comments WHERE `comment_approved` = 1 AND `comment_post_ID` = $id AND `comment_parent` = 0";
		$parents = $wpdb->get_row($query);
		return $parents->count;

	}


	public function get_filter_count( $id ) {

		// Arguments for the query
		$args = array(
			'post_id'	=> $id,
			'status' => 'approve',
			'parent'	=> 0,
		);

		$depc_query = $this->get_query();
		$comments = $depc_query->query( $args );
		return count( $comments );

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
				jQuery(".dpr-discu-container_'.$id.' .dpr-discu-main-loop-wrap .dpr-loadmore-btn").depcLoadMore({
					id: "'.$id.'",
					nounce : "'. $nounce .'",
					action: "dpr_loadmore"
				});
			});';

			return $javascript;

	}

}