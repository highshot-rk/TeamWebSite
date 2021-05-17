<?php /*Function include library hybridauth*/
include 'hybrid/autoload.php';
use Hybridauth\Hybridauth;
if ( ! function_exists( 'depc_social_client' ) ) {
	function depc_social_client( $provider_for_auth ) {
		global $depc_options;

		if ( ! isset( $_SESSION ) ) {
			session_start();
		}
		if ( empty( $depc_options ) ) {
			$depc_options = get_option( 'depc_options' );
		}

		$config = array(
			//Location where to redirect users once they authenticate with a provider
			'callback' => home_url( '/wp-login.php' ),
			//Providers specifics
			'providers' => array(
				'Twitter' => array(
					'enabled' => true,	 //Optional: indicates whether to enable or disable Twitter adapter. Defaults to false
					'keys' => array(
						'key'    =>  \Depc_Core::get_option( 'dc_social_login_twitter_consumer_key', 'Login_Register' , '' ), //Required: your Twitter consumer key
						'secret' =>  \Depc_Core::get_option( 'dc_social_login_twitter_consumer_secret', 'Login_Register' , '' )  //Required: your Twitter consumer secret
					),
					'includeEmail' => true,
				),
				"LinkedIn"  => array(
					"enabled" => true,
					"keys"    => array(
						"id"        => \Depc_Core::get_option( 'dc_social_login_linkedin_client_id', 'Login_Register' , '' ),
						"secret"    => \Depc_Core::get_option( 'dc_social_login_linkedin_client_secret', 'Login_Register' , '' )
					),
					"scope"     => ("r_liteprofile r_emailaddress"), // optional
					"fields"    => array( "id", "email-address", "first-name", "last-name" ), // optional
				),
				"Google" => array(
					'enabled' => true,	 //Optional: indicates whether to enable or disable Twitter adapter. Defaults to false
					'keys' => array(
						'id'     => \Depc_Core::get_option( 'dc_social_login_google_client_id', 'Login_Register' , '' ),
						'secret' => \Depc_Core::get_option( 'dc_social_login_google_client_secret', 'Login_Register' , '' )
					),
					"access_type"     => "offline",   // optional
					"approval_prompt" => "force",	 // optional
					"hd"              => "gmail.com" // optional
				),
				"Facebook" => array(
					"enabled" => true,
					"keys"    => array(
						"id" => \Depc_Core::get_option( 'dc_social_login_facebook_app_id', 'Login_Register' , '' ),
						"secret" => \Depc_Core::get_option( 'dc_social_login_facebook_app_secret', 'Login_Register' , '' ),
					),
					"scope"   => array( 'email' ),
				),
			),
			'curl_options' => array( CURLOPT_SSL_VERIFYPEER => false )
		);
		if ( isset( $_REQUEST['hauth_start'] ) || isset( $_REQUEST['hauth_done'] ) ) { //state=HA-7LSMRNCKA2XE9FVUZ1G4QITY6B8WD53HPJ0O&code=4%2FxwEl_tKUuoEApvaUNzIQVfEGHyXwLp9Ftj4Xn7SOB_vhlRRcUH44GKas2hzA8EVwarHCgXiIO_zzp8lCTs6_Vaw&scope=email+profile+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+openid&authuser=0&prompt=consent
			Hybrid_Endpoint::process();
		} else {
			try {
				$hybridauth = new Hybridauth( $config );

				$adapter = $hybridauth->authenticate( $provider_for_auth );

				$redirect = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
				header( 'Location: ' . filter_var( $redirect, FILTER_SANITIZE_URL ) );

				$user_profile = $adapter->getUserProfile();

				$userdata = array(
					'user_login'        => $user_profile->identifier,
					'user_email'        => $user_profile->email,
					'nickname'          => $user_profile->displayName,
					'first_name'        => $user_profile->firstName,
					'display_name'      => $user_profile->displayName,
				);
				$email_is_verified = $user_profile->emailVerified;
				if ( $provider_for_auth == 'Twitter' ) {
					$email_is_verified = true;
				}
				/* checking if user already exists */
				$user = get_user_by( 'login', $user_profile->identifier );
				if ( ! $user && $email_is_verified ) {
					$user = depc_get_user( $user_profile->email, $user_profile->identifier, 'twitter' );
				}
				$hybridauth_per_for_function = depc_registration_enabled();
				if ( ! $user ) {
					if ( $hybridauth_per_for_function ) {
						if ( $email_is_verified ) {
							$default_role = get_option( 'default_role' );
							if ( $depc_options['allow_registration'] == 'allow' ) {
								$userdata['role'] = $depc_options['user_role'];
							} elseif ( $depc_options['allow_registration'] == 'default' ) {
								$userdata['role'] = $default_role;
							}
							$userdata['user_pass'] = wp_generate_password( $length = 12, $include_standard_special_chars = false );
							$user_id = wp_insert_user( $userdata ) ;
							if ( ! is_wp_error( $user_id ) ) {
								depc_login_user( $user_id );
							}
						}
					} else {
						/* redirecting to login page on error with error message - new users registration is disabled */
						wp_redirect( wp_login_url() . "?error=register_disabled" );
						exit();
					}
				} elseif ( $user instanceof WP_User ) {
					depc_login_user( $user->ID );
				}
			} catch ( Exception $e ) {
				wp_redirect( wp_login_url() . "?error=invalid_token" );
				exit();
			}
		}
		return false;
	}
}