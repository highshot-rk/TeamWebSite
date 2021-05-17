<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Depc_Model_Admin_Inapp extends WP_List_Table {
	/** Class constructor */
	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'comment', 'depc' ), //singular name of the listed records
			'plural'   => __( 'comments', 'depc' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		) );
	}

	/**
	 * Retrieve comment data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_comments( $per_page = 5, $page_number = 1 ) {
		global $wpdb;
		$args = array(
			'meta_key' => 'dpr_inapporpriate_user'
			);
		$count = get_comments($args);
		return $count;
	}
	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_customer( $id ) {
		wp_delete_comment($id);
	}

	/**
	 * Approve a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function approve_customer( $id ) {
		delete_comment_meta($id,'dpr_inapporpriate_user');
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		$args = array(
			'meta_key' => 'dpr_inapporpriate_user',
			'count' => true
			);
		$count = get_comments($args);
		return $count;
	}
	/** Text displayed when no customer data is available */
	public function no_items() {
		_e( 'No comments avaliable.', 'sp' );
	}
	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		$item = get_object_vars($item);
		switch ( $column_name ) {
			case 'author':
			return $item[ $column_name ];
			case 'depc_comment':
			case 'response':
			case 'comment_approved':
			case 'id':
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		$item = get_object_vars($item);
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['comment_ID']
		);
	}
	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_author( $item ) {
		$item = get_object_vars( $item );

		$delete_nonce = wp_create_nonce( 'dpr_delete_inapp' );

		$title = '<a href="'. get_edit_user_link( $item['user_id'] ) .'"><strong>' . $item['comment_author'] . '</strong></a>';

		$actions = array(

			'edit' => sprintf( '<a href="comment.php?action=editcomment&c=%d">Edit</a>', absint( $item['comment_ID'] ) )
		);

		return $title . $this->row_actions( $actions );
	}

	/**
	 * Method for type column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_type( $item ) {
		$item = get_object_vars( $item );

		// $item['comment_ID']
		$type = get_comment_meta( $item['comment_ID'], 'dpr_inapporpriate_type', true );
		$reported = get_comment_meta( $item['comment_ID'], 'dpr_inapporpriate_user', true );
		// die();
		switch ($type) {
			case 'word_filter':
				$type = __('Auto (Word Filter)', 'depc');
				break;
			case 'inappropriate':
				$type = __('Flagged as inappropriate', 'depc') . '(' . count($reported['reported_user']) . ')';
				break;
				default:
				$type = __('Auto (Word Filter)', 'depc');
				break;
		}

		return $type;
	}
	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_depc_comment( $item ) {

		$item = get_object_vars( $item );

		$css = '
		<style>
			.wp-list-table #depc_comment , .wp-list-table .depc_comment {width: 50%;}
		</style>';

		$content =  $item['comment_content'] ;

		return $css . $content;
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_response( $item ) {
		$item = get_object_vars( $item );
		if($item['comment_parent'] > 0) {
			$post = '<a href="'. get_comment_link( $item['comment_parent'] ) .'"><strong>' .get_the_title( $item['comment_post_ID'] ) . '</strong></a>';
		} else {
			$post = '<a href="'. get_comment_link( $item['comment_ID'] ) .'"><strong>' .get_the_title( $item['comment_post_ID'] ) . '</strong></a>';
		}
		return $post;
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_comment_approved( $item ) {
		$item = get_object_vars( $item );
		$post = '<a href="comment.php?action=editcomment&c='.$item['comment_ID'].'"><strong>' .wp_get_comment_status( $item['comment_ID'] ) . '</strong></a>';

		return $post;
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_id( $item ) {
		$item = get_object_vars( $item );

		$id =  $item['comment_ID'] ;

		return $id;
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'      			=> '<input type="checkbox" />',
			'author'   			=> __( 'Author', 'sp' ),
			'depc_comment' 		=> __( 'Comment', 'sp' ),
			'response'    		=> __( 'In Response To', 'sp' ),
			'comment_approved'  => __( 'Status', 'sp' ),
			'type'  			=> __( 'Type', 'sp' ),
			'id'    	  		=> __( 'ID', 'sp' )
		);
		return $columns;
	}
	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'city' => array( 'city', false )
		);
		return $sortable_columns;
	}
	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete' => 'Delete',
			'bulk-approve' => 'Approve'
		);
		return $actions;
	}
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();
		/** Process bulk action */
		$this->process_bulk_action();
		$per_page     = $this->get_items_per_page( 'comment_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();
		// $this->set_pagination_args( [
		// 	'total_items' => $total_items, //WE have to calculate the total number of items
		// 	'per_page'    => $per_page //WE have to determine how many items to show on a page
		// ] );
		$this->items = self::get_comments( $per_page, $current_page );
	}
	public function process_bulk_action() {
		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {
			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
			if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_customer( absint( $_GET['customer'] ) );
		                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		                // add_query_arg() return the current url
						ob_end_clean();
		                wp_redirect( esc_url_raw(add_query_arg()) );
				exit;
			}
		}
		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {
			$delete_ids = esc_sql( $_POST['bulk-delete'] );
			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_customer( $id );
			}
			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        // add_query_arg() return the current url
			ob_end_clean();
		        wp_redirect( esc_url_raw(add_query_arg()) );
			exit;
		}
		else if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-approve' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-approve' )
		) {
			$approve_ids = esc_sql( $_POST['bulk-delete'] );
			// loop over the array of record IDs and delete them
			foreach ( $approve_ids as $id ) {
				self::approve_customer( $id );
			}
			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        // add_query_arg() return the current url


		        wp_redirect( esc_url_raw( add_query_arg() ) );
			exit;
		}{

		}
	}
}