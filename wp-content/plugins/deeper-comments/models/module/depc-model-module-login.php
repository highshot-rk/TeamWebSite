<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Model_Module_Login extends Depc_Model_Public_Comment {



	private $ajax_login  = 'dpr_login';
	private $ajax_register  = 'dpr_register';
	private static $nounce  = 'dpr_login_register';

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( Depc_Request_Validator::is_ajax() ) {
			Depc_Actions_Filters::add_action( "wp_ajax_$this->ajax_login", $this, 'render_login' );
			Depc_Actions_Filters::add_action( "wp_ajax_nopriv_$this->ajax_login", $this, 'render_login' );
			Depc_Actions_Filters::add_action( "wp_ajax_$this->ajax_register", $this, 'render_register' );
			Depc_Actions_Filters::add_action( "wp_ajax_nopriv_$this->ajax_register", $this, 'render_register' );
		}

	}

	public function render_login() {

		check_ajax_referer( self::$nounce, 'security' );
		$validator = new Depc_Request_Validator;

		//sanitize
		$username = $validator->must_sanitize( $_POST['dpr_username'], 'user' );
		$password = $validator->must_sanitize( $_POST['dpr_password'], 'pass' );

		// ready for login
		$creds = array();
		$creds['user_login'] = $username;
		$creds['user_password'] = $password;
		$creds['remember'] = true;

		$this->init_before_signon();
		$user = wp_signon( $creds, false );

		if ( is_wp_error($user) ) {
			wp_send_json( array( 'error' => true , 'message' => $user->get_error_message() ) );
		} else {
			wp_send_json( array( 'error' => false , 'message' => esc_attr__( 'Congratulations!', 'depc' ) ) );
		}

		wp_die();

	}

	public function render_register() {

		check_ajax_referer( self::$nounce, 'security' );
		if( get_option( 'users_can_register' ) !== '1' ) {
			wp_send_json( array( 'error' => true , 'message' => __('Permission Denied!', 'depc') ) );
			exit();
		}
		//sanitize
		$validator = new Depc_Request_Validator;
		$info = array();

		// serialize
		$info = $validator->must_serilize( $_POST['register'] );

		// validate
		$validate = $validator->must_validate( $info );

		if ( $validate != false ) {
			wp_send_json( array( 'error' => true , 'message' => [__('The information you entered did not pass the verification process. Please check your registration data and try again.', 'dpr')] ) );
			return;
		}

		// ready for register
		$info['user_nicename'] = $info['nickname'] = $info['display_name'] = $info['first_name'] = $validator->must_sanitize( $info['name'], 'text' );
		$info['user_email'] = $validator->must_sanitize( $info['email'], 'email' );
		$info['user_login'] = $validator->must_sanitize( $info['username'], 'user' );
		$info['user_pass'] = $validator->must_sanitize( $info['password'], 'pass' );
		$info['cnfrm_password'] = $validator->must_sanitize( $info['cnfrm_password'], 'pass' );
		$info['user_login'] = $info['username'];

		unset( $info['name'] , $info['email'] , $info['username'], $info['password'] , $info['cnfrm_password'] );

		// check for register or not
		$user_id = wp_insert_user( $info );
		wp_new_user_notification($user_id);
		wp_send_new_user_notifications($user_id);
		if ( is_numeric( $user_id ) ) {

			$creds = array();
			$creds['user_login'] = $info['user_login'];
			$creds['user_password'] = $info['user_pass'];
			$creds['remember'] = false;

			$this->init_before_signon();
			$user = wp_signon( $creds, false );

			// check if error is created
			if ( is_wp_error($user) ) {
				wp_send_json( array( 'error' => true , 'message' => $user->get_error_message() ) );
			} else {
				wp_send_json( array( 'error' => false , 'message' => esc_attr__( 'Congratulations!', 'depc' ) . ' ' . esc_attr__( 'Click to reload.', 'depc' ) ) );
			}

		}

		wp_send_json( array( 'error' => false , 'message' => esc_attr__( 'Try again later.', 'depc' ) ) );

		wp_die();

	}

	public static function get_script() {
		return self::scripts();
	}

	private static function scripts() {

		$id = new self();
		$id = $id->post_id();

		$nounce = wp_create_nonce( self::$nounce );
		// Generating javascript code tpl
		$javascript = '<script type="text/javascript">
		jQuery(document).ready(function() {

			jQuery(".dpr-discu-container_'.$id.' .dpr-join-form-login-register .dpr-join-form-login-a").depcLoginForm({
				id: "'.$id.'",
				nounce: "'.$nounce.'",
				register: false
			});

			jQuery(".dpr-discu-container_'.$id.' .dpr-join-form-login-register .dpr-join-form-register-a").depcLoginForm({
				id: "'.$id.'",
				nounce: "'.$nounce.'",
				register: true
			});

		});
		</script>';

		return $javascript;

	}

}