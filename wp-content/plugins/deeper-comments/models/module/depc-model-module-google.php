<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Model_Module_Google extends Depc_Model_Public_Comment {



	private $ajax_login  = 'dpr_login';
	private $appname;
	private $client_id;
	private $client_secret ;

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if(!isset($_SESSION)) { 

			session_start();
		}
		$this->appname       = Depc_Core::get_option( 'dc_social_login_g_appname', 'disscustion_settings' );
		$this->client_id 	 = Depc_Core::get_option( 'dc_social_login_g_client', 'disscustion_settings' );
		$this->client_secret = Depc_Core::get_option( 'dc_social_login_g_secret', 'disscustion_settings' );

		if ( Depc_Request_Validator::is_ajax() ) {
			Depc_Actions_Filters::add_action('wp_ajax_googleplus_oauth_callback', $this , 'googleplus_oauth_callback');
			Depc_Actions_Filters::add_action('wp_ajax_nopriv_googleplus_oauth_callback', $this , 'googleplus_oauth_callback');
		}

	}

	public static function get_script() {
		return self::scripts();
	}

	public function google_api_init() {

		global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;
		require_once Depc_Core::get_depc_path() . 'lib/app/libraries/googleplusoauth/apiClient.php';
		require_once Depc_Core::get_depc_path() . 'lib/app/libraries/googleplusoauth/contrib/apiPlusService.php';

		$client = new apiClient();
		$client->setApplicationName( $this->appname );
		$client->setScopes(array( 'https://www.googleapis.com/auth/plus.me','https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/plus.login', 'https://www.googleapis.com/auth/userinfo.profile' ) );

		$plus = new apiPlusService($client);
		$authUrl = $client->createAuthUrl();

		if (filter_var($authUrl, FILTER_VALIDATE_URL) === FALSE) {
			$authUrl = '#';
		}

		$_SESSION['dpr_uri'] = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		return $authUrl;
	}

	public function googleplus_oauth_callback() {

		global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;

		require_once Depc_Core::get_depc_path() . 'lib/app/libraries/googleplusoauth/apiClient.php';
		require_once Depc_Core::get_depc_path() . 'lib/app/libraries/googleplusoauth/contrib/apiPlusService.php';

		$client = new apiClient();
		$client->setApplicationName( $this->appname );
		$client->setScopes( array('https://www.googleapis.com/auth/plus.me','https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/plus.login', 'https://www.googleapis.com/auth/userinfo.profile' ) );

		$plus = new apiPlusService($client);
		$client->authenticate();
		$_SESSION['access_token'] = $client->getAccessToken();
		$client->setAccessToken($_SESSION['access_token']);
		$redirect = $_SESSION['dpr_uri'];
		unset($_SESSION['access_token']);
		unset($_SESSION['dpr_uri']);

		// get back info
		$me = $plus->people->get('me');
		if ( is_user_logged_in() ) {
			wp_logout();
		}

		// check for email free for register
		$password = wp_generate_password( 8, false );
		if ( email_exists( $me['emails'][0]['value'] ) ) {
			// get user id
			$user_id = self::user_id( $me['emails'][0]['value'] );
			// update password
			wp_update_user(  array( 'ID' => $user_id, 'user_pass' => $password ) );
			// login
			self::login( $user_id, $me['emails'][0]['value'], $password );
			// redirect to last page
			wp_redirect( $redirect );
			exit;
		}

		// data for register
		$info = array(
			'display_name'	=> $me['displayName'],
			'first_name' 	=> $me['name']['givenName'],
			'last_name'		=> $me['name']['familyName'],
			'user_email' 	=> $me['emails'][0]['value'],
			'user_login' 	=> $me['emails'][0]['value'],
			'user_pass'		=> $password
			);

		// register new user
		$user_id = wp_insert_user( $info );
		if ( is_numeric( $user_id ) ) {
			self::login( $user_id, $me['emails'][0]['value'], $password );
		}

		wp_redirect( $redirect );
		exit;
	}


	private static function login( $user_id , $login , $pass ) {

		wp_new_user_notification( $user_id );
		$creds = array();
		$creds['user_login'] = $login;
		$creds['user_password'] = $pass;
		$creds['remember'] = true;
		wp_signon( $creds, true );
		// wp_set_auth_cookie( $creds['user_login'] , $creds['remember'] );

	}

	private static function user_id( $login ) {

		$user = get_user_by( 'email', $login );
		$userId = $user->ID;
		return $userId;

	}

}