<?php

interface Dpr_Request_Validator
{
	public function __construct();

	public function get_nonce_name();

	public function get_nonce_action();

	public function is_valid( $ajax = FALSE );
}

class Depc_Request_Validator implements Dpr_Request_Validator
{
	private $action;
	private $name;

	public function __construct() {
		// $this->register_hook_callbacks();
	}

	public function register_hook_callbacks() {
		add_action( 'init', array( $this, 'is_user_logged_in' ) );
	}

	public function get_nonce_name() {
		return $this->name;
	}

	public function get_nonce_action() {
		return $this->action;
	}

	public function is_valid( $ajax = FALSE ) {
		return wp_verify_nonce( $_REQUEST[ $this->name ], $this->action );
	}

	public static function is_ajax() {

		return defined( 'DOING_AJAX' ) && DOING_AJAX;

	}

	public function is_registered() {

		$user = wp_get_current_user();

		return $user->exists();

	}

	public function is_guest_allowed() {

		$is_guest_allowed = Depc_Core::get_option( 'dc_allow_guest_comment', 'Comments' );
		$current_user = wp_get_current_user();
		if ( $this->is_registered() == false ) {
			if ( $is_guest_allowed == 'on' ) return 1;
		} elseif ( $this->is_registered() == true ) {
			return 1;
		}


	}

	public function may_i_approve() {

		if ( current_user_can( 'moderate_comments' ) ) return 1;

	}

	public function must_sanitize( &$data , $type ) {

		$data = wp_filter_nohtml_kses( $data );

		switch ( $type ){
			case 'user':
				$data = sanitize_user( $data );
			return $data;

			case 'pass':
				$data = wp_filter_nohtml_kses( $data );
			return $data;

			case 'text':
				$data = sanitize_text_field( urldecode( $data ) );
			return $data;

			case 'email':
				$data = sanitize_email( urldecode( $data ) );
			return $data;
		}

	}

	public function must_validate( $data ) {

		foreach ( $data as $key => $value ) {

			if ( $key == 'name' ) {
				if ( $data[$key] == ' ' || empty( $data[$key] ) ) {
					$throw[] = esc_html__( 'Name is empty', 'depc' );
				} elseif( strlen( $data[$key] ) > 40 ) {
					$throw[] = esc_html__( 'Too long name.', 'depc' );
				} elseif( strlen( $data[$key] ) < 2 ) {
					$throw[] = esc_html__( 'Too short name.', 'depc' );
				}
			}

			if ( $key == 'username' ) {
				if ( $data[$key] == ' ' || empty( $data[$key] ) ) {
					$throw[] = esc_html__( 'Username is empty', 'depc' );
				} elseif ( username_exists( $data[$key] ) ) {
					$throw[] = esc_html__( 'Username is exist.', 'depc' );
				}
			}

			if ( $key == 'email' ) {
				if ( is_email( urldecode( $data[$key] ) ) == false ) {
					update_option( 's',$data[$key]  );
					$throw[] = esc_html__( 'Email in not valid!', 'depc' );
				} elseif ( email_exists( urldecode( $data[$key] ) ) ) {
					$throw[] = esc_html__( 'Email has been registered before!', 'depc' );
				} elseif ( !isset( $data[$key] ) ) {
					$throw[] = esc_html__( 'Email is empty.', 'depc' );
				}
			}

			if ( $key == 'password' ) {
				if ( $data[$key] == ' ' || empty( $data[$key] ) ) {
					$throw[] = esc_html__( 'Password is empty', 'depc' );
				}
			}

			if ( $key == 'cnfrm_password' ) {
				if ( $data[$key] == ' ' || empty( $data[$key] ) ) {
					$throw[] = esc_html__( 'Confirm Password is empty', 'depc' );
				} elseif ( $data[$key] != $data['password'] ) {
					$throw[] = esc_html__( 'Passwords are not match!', 'depc' );
				}
			}

		}

		if ( empty( $throw ) ) {
			return false;
		}

		return $throw;

	}

	public function must_serilize( $data ) {

		$get = explode('&', $data ); // explode with and

		foreach ( $get as $key => $value) {
			$need[ substr( $value, 0 , strpos( $value, '=' ) ) ] =  substr( $value, strpos( $value, '=' ) + 1 ) ;
		}
		return $need;
	}

	public function terms_empty( $data ) {

		if ( sanitize_text_field( $data ) == ' ' || empty( $data ) ) return -1; else return 0;

	}

	public static function is_numeric( $data , $type ) {
		if ( $type == true ) {
			if ( !is_numeric( intval( $data ) ) ) wp_die( 'Comment Id Not Valid.' );
		} else {
			if ( is_numeric( intval( $data ) ) ) return $data; else return false;
		}

	}

	public function allow_delete_comment( $author_id , $has_child = true ) {
		// get current user id
		$current_user = wp_get_current_user();

		if ( $current_user->ID == '0' ) return false;

		if ( $has_child == true ) {
			return false;
		} elseif ( current_user_can( 'moderate_comments' ) ) {
			return true;
		} elseif ( $author_id == $current_user->ID ) {
			return true;
		} else {
			return false;
		}
	}

	public function allow_edit_comment( $author_id , $date ) {
		// get current user id
		$current_user = wp_get_current_user();
		// comment submited date
		$submtted_date = strtotime( $date );
		$now =  strtotime( current_time( 'mysql' ) );
		$difference = $now - $submtted_date;
		$get_expirtion_date = strtotime(  Depc_Core::get_option( 'dc_edit_expiration', 'Comments' ) . ' day' , 0 );

		if ( !is_user_logged_in() ) {
			return false;
		}

		if ( current_user_can( 'moderate_comments' ) == true ) {
			return true;
		} elseif ( $author_id == $current_user->ID && $get_expirtion_date > $difference ) {
			return true;
		} else {
			return false;
		}
	}

	public function get_comment_order( $setting ) {

		switch ( $setting ) {
			case 'oldest':
				return 'ASC';
				break;
			case 'newest':
				return 'DESC';
				break;
			case 'popular':
				return 'popular';
				break;
			case 'trending':
				return 'trending';
				break;
			default:
				# code...
				break;
		}

	}

	public function is_user_logged_in() {
		$user = wp_get_current_user();

		if ( empty( $user->ID ) )
			return false;

		return true;
	}

}