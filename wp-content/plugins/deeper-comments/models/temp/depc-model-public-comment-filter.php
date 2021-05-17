<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Model_Public_Comment_Filter extends Depc_Model_Public_Comment {

	public $validator;
	public $ajax_action  = 'dpr_filter';
	private static $nounce  = 'dpr_filter';

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// init validator
		$this->validator = new Depc_Request_Validator;
		// $this->popular( 1  );
		// get settings
		$this->user_setting =  $this->get_user_settings();

	}

	public function render() {

		check_ajax_referer( self::$nounce, 'security' );
		if ( $_POST['type'] == 'trending' )
			$this->trending( 'trending', $_POST['id'] );
		elseif ( $_POST['type'] == 'popular')
			$this->popular( $_POST['id']  );
		elseif ( $_POST['type'] == 'oldest')
			$this->oldest( $_POST['id'] );
		elseif ( $_POST['type'] == 'newest')
			$this->newest( $_POST['id'] );
		elseif ( $_POST['type'] == 'key')
			$this->keysearch( $_POST['id'] , $_POST['search'] );

	}

	public function popular( $id ) {

		global $wpdb;
		if(!$this->user_setting['filter_count']) {
			$this->user_setting['filter_count'] = 5;
		}
		$comments = $wpdb->get_results('SELECT * , COUNT(comment_ID) as cm_count FROM '. $wpdb->comments. '
		WHERE comment_post_id = \'' . $id . '\' AND comment_parent = 0 AND comment_approved = 1 GROUP BY comment_author_email order by COUNT(comment_ID) desc limit '. $this->user_setting['filter_count']);
		$loop = Depc_Controller_Public_Comment_Loop::get_comment_loop();
		$result = $loop->load( $comments, $id, 'popular' );
		$result = trim( preg_replace( '/\s+/', ' ', $result ) );
		wp_send_json( $result );

	}

	public function trending( $type, $id ) {

		// Arguments for the query
		$args = array(
			'post_id'	=> $id,
			'number' => $this->user_setting['filter_count'],
			'status' => 'approve',
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => 'dpr_social',
					'type' => 'NUMERIC',
					'compare' => 'EXISTS'

				),
				'like_count' =>	array(
					'key'     => 'like_count',
					'type'	  => 'NUMERIC',
					'compare'	  => 'EXISTS'
				),
				array(
					'key'     => 'dislike_count',
					'type'	  => 'NUMERIC',
					'compare'	  => 'EXISTS'
				),
			),
			'order' => 'ASC'
		);

		$depc_query = $this->get_query();
		$comments = $depc_query->query( $args );

		$loop = Depc_Controller_Public_Comment_Loop::get_comment_loop();

		$result = $loop->load( $comments, $id, $type );
		$result = trim( preg_replace( '/\s+/', ' ', $result ) );
		wp_send_json( $result );

	}

	/**
	 * Oldest Sorting
	 *
	 * @param int $id
	 * @return void
	 */
	public function oldest( $id ) {

		// $ARGUMENTS
		$args = array(
			'post_id' => $id,
			'number' => $this->user_setting['filter_count'],
			'status' => 'approve',
			'order'	=> 'ASC'
		);

		$depc_query = $this->get_query();
		$comments = $depc_query->query( $args );

		$loop = Depc_Controller_Public_Comment_Loop::get_comment_loop();

		$result = $loop->load( $comments, $id, 'default' );
		$result = trim(preg_replace('/\s+/', ' ', $result));
		wp_send_json( $result );

	}


	public function newest( $id ) {

		// Arguments for the query
		$args = array(
			'post_id' => $id,
			'number' => $this->user_setting['filter_count'],
			'status' => 'approve',
			'order'	=> 'DESC'
		);

		$depc_query = $this->get_query();
		$comments = $depc_query->query( $args );

		$loop = Depc_Controller_Public_Comment_Loop::get_comment_loop();

		$result = $loop->load( $comments, $id, 'default' );
		$result = trim(preg_replace('/\s+/', ' ', $result));
		wp_send_json( $result );

	}

	public function keysearch( $id , $search ) {

		$search = $this->validator->must_sanitize( $search, 'text');

		// Arguments for the query
		$args = array(
			'post_id' => $id,
			'number' => $this->user_setting['filter_count'],
			'status' => 'approve',
			'search' => $search
		);

		$depc_query = $this->get_query();
		$comments = $depc_query->query( $args );

		$loop = Depc_Controller_Public_Comment_Loop::get_comment_loop();

		$result = $loop->load( $comments, $id, 'keysearch'  );
		$result = trim(preg_replace('/\s+/', ' ', $result));
		wp_send_json( $result );
	}


 	/**
	 * @return js scripts
	 */
	public static function scripts() {

		$class = ( new self );
		$id = $class->post_id();
		$nounce = wp_create_nonce( self::$nounce );

		// Generating javascript code tpl
		$javascript = '
			jQuery(document).ready(function() {
				jQuery(".dpr-discu-container_'.$id.' .dpr-switch-tab-wrap .dpr-switch-tab .dpr-switch-tab-trending .dpr-switch-tab-trending-a").depcFilter({
					id: "'.$id.'",
					nounce : "'. $nounce .'",
					action : "dpr_filter",
					type: "trending"
				});
				jQuery(".dpr-discu-container_'.$id.' .dpr-switch-tab-wrap .dpr-switch-tab .dpr-switch-tab-popular .dpr-switch-tab-popular-a").depcFilter({
					nounce : "'. $nounce .'",
					action : "dpr_filter",
					id: "'.$id.'",
					type: "popular"
				});
				jQuery(".dpr-discu-container_'.$id.' .dpr-switch-tab-wrap .dpr-switch-tab .dpr-switch-tab-oldest .dpr-switch-tab-oldest-a").depcFilter({
					id: "'.$id.'",
					nounce : "'. $nounce .'",
					action : "dpr_filter",
					type: "oldest"
				});
				jQuery(".dpr-discu-container_'.$id.' .dpr-switch-tab-wrap .dpr-switch-tab .dpr-switch-tab-newest .dpr-switch-tab-newest-a").depcFilter({
					id: "'.$id.'",
					nounce : "'. $nounce .'",
					action : "dpr_filter",
					type: "newest"
				});
				jQuery(".dpr-discu-container_'.$id.' .dpr-switch-tab-wrap .dpr-switch-search-wrap .dpr-discu-search").depcFilter({
					id: "'.$id.'",
					nounce : "'. $nounce .'",
					action : "dpr_filter",
					type: "key"
				});
			});';

			return $javascript;

	}

}
