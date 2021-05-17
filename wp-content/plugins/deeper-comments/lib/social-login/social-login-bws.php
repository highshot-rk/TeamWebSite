<?php
/*
Plugin Name: Social Login by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/wordpress/plugins/depc-social-login/
Description: Add social media login, registration, and commenting to your WordPress website.
Author: BestWebSoft
Text Domain: depc-social-login-bws
Domain Path: /languages
Version: 1.4.3
Author URI: https://bestwebsoft.com/
License: GPLv2 or later
*/

/*  © Copyright 2020  BestWebSoft  ( https://support.bestwebsoft.com )

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! function_exists( 'add_depc_admin_menu' ) ) {
	function add_depc_admin_menu() {
		$settings = add_menu_page( __( 'Social Login Settings', 'depc-social-login-bws' ), 'Social Login', 'manage_options', 'depc-social-login.php', 'depc_settings_page' );

		add_submenu_page( 'depc-social-login.php', __( 'Social Login Settings', 'depc-social-login-bws' ), __( 'Settings', 'depc-social-login-bws' ), 'manage_options', 'depc-social-login.php', 'depc_settings_page' );

		add_submenu_page( 'depc-social-login.php', 'BWS Panel', 'BWS Panel', 'manage_options', 'depc-bws-panel', 'bws_add_menu_render' );

		add_action( 'load-' . $settings, 'depc_add_tabs' );
	}
}

if ( ! function_exists( 'depc_plugins_loaded' ) ) {
	function depc_plugins_loaded() {
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'depc-social-login-bws', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists( 'depc_init' ) ) {
	function depc_init() {
		global $depc_plugin_info, $depc_options;

		if ( empty( $depc_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$depc_plugin_info = get_plugin_data( __FILE__ );
		}

		/* add general functions */
		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );

		/* check compatible with current WP version */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $depc_plugin_info, '4.5' );

		$is_admin = is_admin() && ! defined( 'DOING_AJAX' );
		/* Get/Register and check settings for plugin */
		if (
			! $is_admin ||
			( isset( $_GET['page'] ) && 'depc-social-login.php' == $_GET['page'] ) || /* plugin settings page */
			defined( 'IS_PROFILE_PAGE' ) || /* defined on profile.php(set to true) and user-edit.php(set to false) pages */
			( defined( 'BWS_ENQUEUE_ALL_SCRIPTS' ) && BWS_ENQUEUE_ALL_SCRIPTS )
		) {
			if ( ! session_id() ) {
				session_start();
			}
			depc_settings();
		}

		if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			$admin_url = ( function_exists( 'get_admin_url' ) ) ? get_admin_url( null, 'plugins.php' ) : esc_url( '/wp-admin/plugins.php' );
			wp_die(
				sprintf(
					"<strong>%s</strong> %s <strong>PHP %s</strong> %s <br /><br />%s <a href='%s'>%s</a>.",
					$depc_plugin_info['Name'],
					__( 'requires', 'depc-social-login-bws' ),
					'5.4.0',
					__( 'or higher, that is why it has been deactivated! Please upgrade your PHP version and try again.', 'depc-social-login-bws' ),
					__( 'Back to the WordPress', 'depc-social-login-bws' ),
					$admin_url,
					__( 'Plugins page', 'depc-social-login-bws' )
				)
			);
		}

		require_once( dirname( __FILE__ ) . '/includes/social-client.php' );

		/* Additional check for email existance in usermeta of registered users */
		add_filter( 'registration_errors', 'depc_registration_errors', 9, 3 );
		if ( is_multisite() ) {
			add_filter( 'wpmu_validate_user_signup', 'depc_validate_multisite_user_signup' );
			add_filter( 'wpmu_validate_blog_signup', 'depc_validate_multisite_user_signup' );
		}

		if ( isset( $_GET['provider'] ) && in_array( $_GET['provider'], array( 'google', 'facebook', 'twitter', 'linkedin', ) ) ) {
			$_SESSION['provider'] = ( 'linkedin' == $_GET['provider'] ) ? 'LinkedIn' : ucfirst( $_GET['provider'] );
			depc_social_client( $_SESSION['provider'] );
		}

		if ( ( isset( $_GET['state'] ) && isset( $_GET['code'] ) ) || ( isset( $_GET['oauth_token'] ) && isset( $_GET['oauth_verifier'] ) ) && isset( $_SESSION['provider'] ) ) {
			depc_social_client( $_SESSION['provider'] );
		}
	}
}

/* Function for admin_init */
if ( ! function_exists( 'depc_admin_init' ) ) {
	function depc_admin_init() {
		/* Add variable for bws_menu */
		global $bws_plugin_info, $depc_plugin_info;

		/* Function for bws menu */
		if ( empty( $bws_plugin_info ) ) {
			$bws_plugin_info = array( 'id' => '640', 'version' => $depc_plugin_info['Version'] );
		}
	}
}

/* Function for settings setup */
if ( ! function_exists( 'depc_settings' ) ) {
	function depc_settings() {
		global $depc_options, $depc_providers, $depc_plugin_info;

		/* Install the option defaults */
		if ( ! get_option( 'depc_options' ) ) {
			$options_default = depc_get_default_options();
			add_option( 'depc_options', $options_default );
		}

		/* Get options from the database */
		$depc_options = get_option( 'depc_options' );

		if ( ! isset( $depc_options['plugin_option_version'] ) || $depc_options['plugin_option_version'] != $depc_plugin_info['Version'] ) {
			$options_default = depc_get_default_options();
			$depc_options = array_merge( $options_default, $depc_options );

			/**
			* @deprecated 1.4.2
			* @todo Remove function after 01.12.2020
			*/
			if ( ! in_array( $depc_options['button_display_google'], array( 'dark', 'light' ) ) )
				$depc_options['button_display_google'] = $options_default['button_display_google'];

			$depc_options['plugin_option_version'] = $depc_plugin_info['Version'];
			$update_option = true;
			depc_plugin_activate();
		}

		if ( isset( $update_option ) ) {
			update_option( 'depc_options', $depc_options );
		}

		$depc_providers = array(
			'google' 	=> 'Google',
			'facebook' 	=> 'Facebook',
			'twitter'	=> 'Twitter',
			'linkedin'	=> 'LinkedIn',
		);
	}
}

/* Function for getting_default_options */
if ( ! function_exists( 'depc_get_default_options' ) ) {
	function depc_get_default_options( $is_network_admin = false ) {
		global $depc_plugin_info;

		$default_options = array(
			'plugin_option_version'                 => $depc_plugin_info['Version'],
			'google_is_enabled'                     => 0,
			'google_client_id'                      => '',
			'google_client_secret'                  => '',
			'facebook_is_enabled'                   => 0,
			'facebook_client_id'                    => '',
			'facebook_client_secret'                => '',
			'twitter_is_enabled'                    => 0,
			'twitter_client_id'                     => '',
			'twitter_client_secret'                 => '',
			'linkedin_is_enabled'                   => 0,
			'linkedin_client_id'                    => '',
			'linkedin_client_secret'                => '',
			'login_form'                            => 1,
			'register_form'                         => 1,
			'comment_form'                          => 1,
			'loginform_buttons_position'            => 'middle', /* top | middle | bottom */
			'display_settings_notice'               => 1,
			'first_install'                         => strtotime( 'now' ),
			'suggest_feature_banner'                => 1,
			'user_role'                             => get_option( 'default_role' ),
			'button_display_google'                 => 'dark',
			'button_display_facebook'               => 'long',
			'button_display_twitter'                => 'long',
			'button_display_linkedin'               => 'long',
			'linkedin_button_name'                  => sprintf( __( 'Sign in with %s', 'depc-social-login-bws' ), 'LinkedIn' ),
			'twitter_button_name'                   => sprintf( __( 'Sign in with %s', 'depc-social-login-bws' ), 'Twitter' ),
			'facebook_button_name'                  => sprintf( __( 'Sign in with %s', 'depc-social-login-bws' ), 'Facebook' ),
			'google_button_name'                    => sprintf( __( 'Sign in with %s', 'depc-social-login-bws' ), 'Google' ),
			'allow_registration'                    => 'default',
			'delete_metadata'                       => 0,
		);

		return $default_options;
	}
}

/* Function for plugin_activate */
if ( ! function_exists( 'depc_plugin_activate' ) ) {
	function depc_plugin_activate() {
		if ( is_multisite() ) {
			switch_to_blog( 1 );
			register_uninstall_hook( __FILE__, 'depc_delete_options' );
			restore_current_blog();
		} else {
			register_uninstall_hook( __FILE__, 'depc_delete_options' );
		}
	}
}

if ( ! function_exists( 'depc_settings_page' ) ) {
	function depc_settings_page() {
		if ( ! class_exists( 'Bws_Settings_Tabs' ) )
			require_once( dirname( __FILE__ ) . '/bws_menu/class-bws-settings.php' );
		require_once( dirname( __FILE__ ) . '/includes/class-depc-settings.php' );
		$page = new depc_Settings_Tabs( plugin_basename( __FILE__ ) ); ?>
		<div class="wrap">
			<h1><?php _e( 'Social Login Settings', 'depc-social-login-bws' ); ?></h1>
			<noscript>
				<div class="error below-h2">
					<p><strong><?php _e( 'WARNING', 'depc-social-login-bws' ); ?>
							:</strong> <?php _e( 'The plugin works correctly only if JavaScript is enabled.', 'depc-social-login-bws' ); ?>
					</p>
				</div>
			</noscript>
			<?php $page->display_content(); ?>
		</div>
	<?php }
}

/* Check if specified page is login page. Uses current page URL if $url is empty */
if ( ! function_exists( 'depc_is_login_page' ) ) {
	function depc_is_login_page( $url = '' ) {
		$login_pages_array = apply_filters( 'depc_login_urls', array( wp_login_url() ) );
		if ( empty( $url ) ) {
			$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		} else {
			$url = preg_replace( '~^((.)*://)~U', '', $url );
		}
		foreach ( $login_pages_array as $login_page ) {
			$pattern = preg_replace( '~^((.)*://)~U', '', $login_page );
			$pattern = '~^' . preg_quote( $pattern ) . '~U';
			if ( @preg_match( $pattern, $url ) ) {
				return true;
			}
		}
		return false;
	}
}

/* Check if specified page is wp-signup.php page */
if ( ! function_exists( 'depc_is_signup_page' ) ) {
	function depc_is_signup_page() {
		if ( ( strpos( $_SERVER['PHP_SELF'], 'wp-signup.php' ) ) ) {
			return true;
		} else {
			return false;
		}
	}
}

/* Function for enqueue_scripts */
if ( ! function_exists( 'depc_enqueue_scripts' ) ) {
	function depc_enqueue_scripts() {
		global $depc_options, $depc_providers, $depc_plugin_info;

		if (  is_admin() ) {
			/*Adding styles for dashicons*/
			wp_enqueue_style( 'depc_admin_page_stylesheet', plugins_url( 'css/admin_page.css', __FILE__ ) );
		}
		if ( isset( $_GET['page'] ) && 'depc-social-login.php' == $_GET['page'] ) {
			/*Adding styles for buttons*/
			wp_enqueue_style( 'depc_login_style', plugins_url( 'css/style-login.css', __FILE__ ) );

			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();

		} elseif ( depc_is_login_page() || depc_is_signup_page() || ! is_admin() && is_singular() && comments_open() && ! is_user_logged_in() && ! empty( $depc_options["comment_form"] ) ) {
			/* Adding style to pages with comments and custom login pages */
			foreach ( $depc_providers as $provider => $provider_name ) {
				if ( ! empty( $depc_options["{$provider}_is_enabled"] ) ) {
					$enqueue_style = true;
				}
			}
			if ( ! empty( $enqueue_style ) ) {
				depc_login_enqueue_scripts( true );
				wp_enqueue_style( 'depc_style', plugins_url( 'css/style.css', __FILE__ ), array( 'dashicons' ), $depc_plugin_info['Version'] );
			}
			if ( ! empty( $_SESSION['depc_userdata'] ) ) {
				/* userdata is set, filling data into the comment form */
				add_filter( 'wp_get_current_commenter', 'depc_get_current_commenter' );
			}
		} elseif ( defined( 'BWS_ENQUEUE_ALL_SCRIPTS' ) ) {
			wp_enqueue_style( 'depc_style', plugins_url( 'css/style.css', __FILE__ ), array( 'dashicons' ), $depc_plugin_info['Version'] );
			depc_login_enqueue_scripts( true );
		}
	}
}

/* Check if the registration is enabled */
if ( ! function_exists( 'depc_registration_enabled' ) ) {
	function depc_registration_enabled() {
		global $depc_options;
		if ( 'default' == $depc_options['allow_registration'] ) {
			$anyone_can_register = get_option( 'users_can_register' );
			$is_registration_enabled = ! empty( $anyone_can_register );
		} elseif ( 'allow' == $depc_options['allow_registration'] ) {
			$is_registration_enabled = true;
		} elseif ( 'deny' == $depc_options['allow_registration'] ) {
			$is_registration_enabled = false;
		} else {
			$is_registration_enabled = false;
		}
		return $is_registration_enabled;
	}
}

/* Function for registration_errors */
if ( ! function_exists( 'depc_registration_errors' ) ) {
	function depc_registration_errors( $errors, $sanitized_user_login = '', $user_email = '' ) {
		$user = depc_get_user( $user_email );
		$error_codes = $errors->get_error_codes();
		/* Add error message only if such message still isn't added */
		if ( false !== $user && ! in_array( 'email_exists', $error_codes ) ) {
			$error_message = sprintf(
				'<strong>%1$s</strong>: %2$s',
				__( 'ERROR', 'depc-social-login-bws' ),
				__( 'This email is already registered, please choose another one.', 'depc-social-login-bws' )
			);
			$errors->add( 'depc_email_exists', $error_message );
		}
		return $errors;
	}
}

/* Function for validating_multisite_user_signup */
if ( ! function_exists ( 'depc_validate_multisite_user_signup' ) ) {
	function depc_validate_multisite_user_signup( $results ) {
		global $current_user;

		/**
		 * Prevent email checkig if user is already logged in on the "register site" step for multisite
		 */
		if ( ! empty( $current_user->data->ID ) )
			return $results;

		$user = depc_get_user( $results['user_email'] );
		$error_codes = $results['errors']->get_error_codes();
		/* Add error message only if such message still isn't added */
		if ( false !== $user && ! in_array( 'email_exists', $error_codes ) ) {
			$error_message = sprintf(
				'<strong>%1$s</strong>: %2$s',
				__( 'ERROR', 'depc-social-login-bws' ),
				__( 'This email is already registered, please choose another one.', 'depc-social-login-bws' )
			);
			$results['errors']->add( 'depc_email_exists', $error_message );
		}

		return $results;
	}
}

/**
 * Check if user with specified email is already exist and return user or false.
 * @param        string        $email                email
 * @param        string        $login                login
 * @param        string        $provider_slug        provider slug
 * @return        WP_User || false
 */
if ( ! function_exists( 'depc_get_user' ) ) {
	function depc_get_user( $email = '', $login = '', $provider_slug = '' ) {
		global $depc_providers;

		$user = false;

		if ( '' == $email && '' == $login )
			return $user;

		if ( '' != $email )
			$user = get_user_by( 'email', $email );

		if ( ! $user ) {
			$meta_query_array = array( 'relation' => 'OR' );

			$providers = ( empty( $depc_providers ) ) ? array(
				'google'     => 'Google',
				'facebook'   => 'Facebook',
				'twitter'    => 'Twitter',
				'linkedin'   => 'LinkedIn',
			) : $depc_providers;
			foreach ( $providers as $provider => $provider_name ) {
				$meta_value = array();

				if ( '' != $email ) {
					$meta_value[] = $email;
				}

				if ( $provider == $provider_slug && '' != $login ) {
					$meta_value[] = $login;
				}

				if ( ! empty( $meta_value ) ) {
					$meta_query_array[] = array(
						'key'       => 'depc_' . $provider . '_login',
						'value'     => $meta_value,
						'compare'   => 'IN'
					);
				}
			}

			if ( count( $meta_query_array ) > 1 ) {
				$users = get_users( array( 'meta_query' => $meta_query_array, 'number' => '1' ) );
			}

			if ( ! empty( $users ) ) {
				$user = $users[0];
			}
		}
		return apply_filters( 'depc_get_user', $user );
	}
}

/* Function to find the user by social email and return main user email */
if ( ! function_exists( 'depc_get_user_email' ) ) {
	function depc_get_user_email( $email = '' ) {
		$user = depc_get_user( $email );

		if ( $user instanceof WP_User ) {
			$email = $user->user_email;
			return $email;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'depc_show_user_registration_setting_notice' ) ) {
	function depc_show_user_registration_setting_notice() {
		$ms_class = is_multisite() ? 'notice notice-error' : 'updated';
		$error_class = is_multisite() ? 'WARNING' : 'Notice'; ?>
		<div id="depc_allow_user_registration_notice" class="below-h2 <?php echo $ms_class; ?>" style="display:none">
			<p>
				<strong><?php _e( $error_class, 'depc-social-login-bws' ); ?></strong>: <?php _e( "You're going to allow user registration via social buttons regardless WordPress default settings. Make sure that you understand the consequences. Check the following", 'depc-social-login-bws' ); ?>
				<a target="_blank" href="https://support.bestwebsoft.com/hc/en-us/articles/360000371546"><?php _e( 'article', 'depc-social-login-bws' ); ?></a>
			</p>
		</div>
		<div id="depc_deny_user_registration_notice" class="below-h2 <?php echo $ms_class; ?>" style="display:none">
			<p>
				<strong><?php _e( $error_class, 'depc-social-login-bws' ); ?></strong>: <?php _e( "You're going to deny user registration via social buttons regardless WordPress default settings. Make sure that you understand the consequences. Check the following", 'depc-social-login-bws' ); ?>
				<a target="_blank" href="https://support.bestwebsoft.com/hc/en-us/articles/360000371546"><?php _e( 'article', 'depc-social-login-bws' ); ?></a>
			</p>
		</div>
	<?php }
}

/* Function for getting_current_commenter */
if ( ! function_exists( 'depc_get_current_commenter' ) ) {
	function depc_get_current_commenter() {
		$userdata = $_SESSION['depc_userdata'];
		$comment_author         = $userdata['display_name'];
		$comment_author_email   = $userdata['user_email'];
		$comment_author_url     = '';
		return compact( 'comment_author', 'comment_author_email', 'comment_author_url' );
	}
}

/* add a class with theme name */
if ( ! function_exists ( 'depc_theme_body_classes' ) ) {
	function depc_theme_body_classes( $classes ) {
		if ( function_exists( 'wp_get_theme' ) ) {
			$current_theme = wp_get_theme();
			$classes[] = 'depc_' . basename( $current_theme->get( 'ThemeURI' ) );
		}
		return $classes;
	}
}

/* Login form scripts */
if ( ! function_exists( 'depc_login_enqueue_scripts' ) ) {
	function depc_login_enqueue_scripts( $is_custom_login = false ) {
		global $depc_plugin_info, $depc_providers, $depc_options;

		foreach ( $depc_providers as $provider => $provider_name ) {
			if ( ! empty( $depc_options["{$provider}_is_enabled"] ) ) {
				$enqueue_script = true;
				if ( ! $is_custom_login &&
					(
						/* Adding styles to the login page */
						( ! isset( $_REQUEST['action'] ) && ! empty( $depc_options['login_form'] ) ) ||
						/* Adding styles to the register page */
						( ! empty( $_REQUEST['action'] ) && 'register' == $_REQUEST['action'] && ! empty( $depc_options['register_form'] ) )
					)
				) {
					$enqueue_style = true;
				}
			}
		}

		if ( ! empty( $enqueue_style ) ) {
			wp_enqueue_style( 'depc_login_style', plugins_url( 'css/style-login.css', __FILE__ ), array( 'dashicons', 'bws-admin-css' ), $depc_plugin_info['Version'] );
		}
		if ( ! empty( $_SESSION['provider'] ) && 'Google' == $_SESSION['provider'] ) {
			$provider_google = $_SESSION['provider'];
		} else {
			$provider_google = '';
		}
		if ( ! empty( $enqueue_script ) ) {
			wp_enqueue_style( 'bws-admin-css', bws_menu_url( 'css/general_style.css' ) );
			wp_enqueue_script( 'depc_login_script', plugins_url( 'js/script-login.js', __FILE__ ), array( 'jquery' ), $depc_plugin_info['Version'] );
			wp_localize_script( 'depc_login_script', 'depc_ajax',
				array(
					'ajaxurl'       => admin_url( '/admin-ajax.php' ),
					'depc_nonce'  => wp_create_nonce( plugin_basename( __FILE__ ), 'depc_nonce' ),
					'is_login_page' => depc_is_login_page(),
					'provider'      => $provider_google
				)
			);
		}
	}
}

/* New user social registration, register or authenticate users */
if ( ! function_exists( 'depc_social_regiser' ) ) {
	function depc_social_regiser( $userinfo, $provider_name = '' ) {
		global $depc_options;
		$userdata = array(
			'user_login'        => $userinfo->id,
			'user_email'        => $userinfo->email,
			'nickname'          => $userinfo->name,
			'first_name'        => $userinfo->name,
			'display_name'      => $userinfo->name,
			'user_nicename'     => $userinfo->name,
		);
		$email_is_verified = $userinfo->email;
		$user = get_user_by( 'login', $userinfo->id );
		if ( ! $user && $email_is_verified ) {
			$user = depc_get_user( $userinfo->email, $userinfo->id, $provider_name );
		}

		$depc_func_per = depc_registration_enabled();

		if ( ! $user ) {
			if ( $depc_func_per ) {
				if ( $email_is_verified ) {
					$default_role = get_option( 'default_role' );
					if ( $depc_options['allow_registration'] == 'allow' ) {
						$userdata['role'] = $depc_options['user_role'];
					}
					if ( $depc_options['allow_registration'] == 'default' ) {
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
	}
}

/*function for adding quotes. Using for twitter auth */
if ( ! function_exists( 'depc_add_quotes' ) ) {
	function depc_add_quotes( $str ) {
		return '"' . $str . '"';
	}
}

/* adding error message to the login form */
if ( ! function_exists( 'depc_login_error' ) ) {
	function depc_login_error( $message = '' ) {
		global $error;
		if ( ! empty( $_REQUEST['error'] ) ) {
			$messages = array(
				'access_denied'             => __( 'please allow the access to your profile information.', 'depc-social-login-bws' ),
				'register_error'            => __( 'failed to register new user.', 'depc-social-login-bws' ),
				'register_disabled'         => __( 'new users registration is disabled.', 'depc-social-login-bws' ),
				'verify_email'              => __( 'you need to verify your Account Email.', 'depc-social-login-bws' ),
				'insufficient_user_data'    => __( 'user data is insufficient for registration.', 'depc-social-login-bws' ),
				'invalid_token_data'        => __( 'provided token data is invalid.', 'depc-social-login-bws' ),
				'invalid_token'             => __( 'provided token is invalid.', 'depc-social-login-bws' ),
				'login_error'               => __( 'login failed.', 'depc-social-login-bws' )
			);

			$error_message = isset( $messages[ $_REQUEST['error'] ] ) ? $messages[ $_REQUEST['error'] ] : esc_html( esc_attr( $_REQUEST['error'] ) );

			$error = sprintf(
				'<strong>%1$s</strong>: %2$s',
				__( 'Error', 'depc-social-login-bws' ),
				$error_message
			);
		}
		return $message;
	}
}

/* Prepare and return login button for specified provider */
if ( ! function_exists( 'depc_get_button' ) ) {
	function depc_get_button( $provider = '', $echo = false ) {
		global $depc_options;

		$button = '';
		if ( 'google' == $provider ) {
			$authUrl = wp_login_url() . "?provider=google";
			$dashicon_for_button = 'dashicons-googleplus';
			$button_html = \Depc_Core::get_option( 'dc_social_login_google_button_style', 'Login_Register' , 'dark' );
			$button_text = $depc_options['google_button_name'];
			if ( isset( $_GET['provider'] ) && 'google' == $_GET['provider'] ) {
				depc_social_client( 'Google' );
			}
		}
		if ( 'facebook' == $provider ) {
			$authUrl = wp_login_url() . "?provider=facebook";
			$dashicon_for_button = 'dashicons-facebook';
			$button_html = $depc_options['button_display_facebook'];
			$button_text = $depc_options['facebook_button_name'];
			if ( isset( $_GET['provider'] ) && 'facebook' == $_GET['provider'] ) {
				depc_social_client( 'Facebook' );
			}
		}
		if ( 'twitter' == $provider ) {
			$authUrl = wp_login_url() . "?provider=twitter";
			$dashicon_for_button = 'dashicons-twitter';
			$button_html = $depc_options['button_display_twitter'];
			$button_text = $depc_options['twitter_button_name'];
			if ( isset( $_GET['provider'] ) && 'twitter' == $_GET['provider'] ) {
			    depc_social_client( 'Twitter' );
			}
		}
		if ( 'linkedin' == $provider ) {
			$authUrl = wp_login_url() . "?provider=linkedin";
			$dashicon_for_button = 'bws-icons';
			$button_html = $depc_options['button_display_linkedin'];
			$button_text = $depc_options['linkedin_button_name'];
			if ( isset( $_GET['provider'] ) && 'linkedin' == $_GET['provider'] ) {
				depc_social_client( 'LinkedIn' );
			}
		}

		if ( 'google' == $provider ) {
			if ( 'dark' == $button_html ) {
				$button .=  sprintf(
					'<a href="%1$s" id="depc_%3$s_button" data-depc-position="%2$s" data-depc-provider="%4$s" class="depc_login_button depc_google_button depc_google_dark_btn">
						<span class="depc_icon"></span>
						<span class="depc_buttonText">%3$s</span>
					</a>',
					$authUrl,
					$depc_options['loginform_buttons_position'],
					$button_text,
					$provider
				);
			} else {
				$button .=	sprintf(
					'<a href="%1$s" id="depc_%3$s_button" data-depc-position="%2$s" data-depc-provider="%4$s" class="depc_login_button depc_google_button">
						<span class="depc_icon"></span>
						<span class="depc_buttonText">%3$s</span>
					</a>',
					$authUrl,
					$depc_options['loginform_buttons_position'],
					$button_text,
					$provider
				);
			}
		} else if ( 'long' == $button_html ) {
			$button .=	sprintf(
				'<a href="%1$s" class="depc_login_button depc_button_%2$s depc_login_button_long depc_%5$s_button" id="depc_%5$s_button" data-depc-position="%2$s" data-depc-provider="%5$s">
					<span class="dashicons %3$s"></span>
					<span class="depc_button_text">%4$s</span>
				</a>',
				$authUrl,
				$depc_options['loginform_buttons_position'],
				$dashicon_for_button,
				$button_text,
				$provider
			);
		} else if ( 'short' == $button_html ) {
			$button .=	sprintf(
				'<a href="%1$s" class="depc_login_button depc_login_button_icon depc_%5$s_button_admin depc_login_button_short depc_button_%2$s depc_%5$s_button" data-depc-position="%2$s" data-depc-provider="%5$s" id="depc_%5$s_button">
					<span class="dashicons %3$s depc_span_icon"></span>
				</a>',
				$authUrl,
				$depc_options['loginform_buttons_position'],
				$dashicon_for_button,
				$button_text,
				$provider
			);
		}
		$button_text = apply_filters( 'depc_button_text', $button_text );
		$button = apply_filters( 'depc_' . $provider . '_button', $button );
		$button = apply_filters( 'depc_button', $button );

		if ( $echo ) {
			echo $button;
		}
		return $button;
	}
}

/* Adding Sign In buttons to the Login form page */
if ( ! function_exists( 'depc_login_form' ) ) {
	function depc_login_form() {
		global $depc_options, $depc_providers;

		if ( ! is_user_logged_in() ) {
			depc_display_all_buttons( 'login_form' );
			$buttons_short = $buttons_long = array();
			foreach ( $depc_providers as $provider => $provider_name ) {
				if( \Depc_Core::get_option( 'dc_social_login_'.$provider, 'Login_Register' , 'off' ) == 'on') {
					if ( 'long' == $depc_options["button_display_{$provider}"] ) {
						$buttons_long[ $provider ] = depc_get_button( $provider );
					} else {
						$buttons_short[ $provider ] = depc_get_button( $provider );
					}
				}
			}
			if ( ! empty( $depc_options["login_form"] ) ){
				if ( ! empty( $buttons_short ) ) {
					$buttons_short = implode( '', $buttons_short );
					printf(
						'<div class="depc_buttons_block">%s</div>',
						$buttons_short
					);
				}
				if ( ! empty( $buttons_long ) ) {
					$buttons_long = implode( '', $buttons_long );
					printf(
						'<div class="depc_buttons_block">%s</div>',
						$buttons_long
					);
				}
			}
		}
	}
}

/* Adding Sign In buttons to the Register form page */
if ( ! function_exists( 'depc_register_form' ) ) {
	function depc_register_form() {
		global $depc_options, $depc_providers;

		if ( ! is_user_logged_in()  ) {
			$buttons_short = $buttons_long = array();

			foreach ( $depc_providers as $provider => $provider_name ) {
				if ( ! empty( $depc_options["{$provider}_is_enabled"] ) ) {
					if ( 'long' == $depc_options["button_display_{$provider}"] ) {
						$buttons_long[ $provider ] = depc_get_button( $provider );
					} else {
						$buttons_short[ $provider ] = depc_get_button( $provider );
					}
				}
			}
			if ( ! empty( $depc_options["register_form"] ) ) {
				if ( !empty( $buttons_short ) ) {
					$buttons_short = implode( '', $buttons_short );
					printf(
						'<div class="depc_buttons_block">%s</div>',
						$buttons_short
					);
				}
				if ( !empty( $buttons_long ) ) {
					$buttons_long = implode('', $buttons_long );
					printf(
						'<div class="depc_buttons_block">%s</div>',
						$buttons_long
					);
				}
			}
		}
	}
}

/* Adding Sign In buttons to the comment form */
if ( ! function_exists( 'depc_comment_form' ) ) {
	function depc_comment_form() {
		global $depc_options, $depc_providers;
		if ( comments_open() && ! is_user_logged_in() ) {
			depc_display_all_buttons( 'comment_form' );
			$buttons_short = $buttons_long = array();
			if ( ! empty( $_SESSION['depc_userdata'] ) ) {
				unset( $_SESSION['depc_userdata'] );
			}
			foreach ( $depc_providers as $provider => $provider_name ) {
				if ( !empty( $depc_options["{$provider}_is_enabled"] ) ) {
					if ( 'long' == $depc_options["button_display_{$provider}"] || !empty($depc_options["comment_form"] ) ) {
						$buttons_long[$provider] = depc_get_button( $provider );
					} else {
						$buttons_short[$provider] = depc_get_button( $provider );
					}
				}
			}
			if ( ! empty ( $depc_options["comment_form"] ) ) {
				if ( !empty( $buttons_short ) ) {
					$buttons_short = implode('', $buttons_short );
					printf(
						'<div class="depc_buttons_block">%s</div>',
						$buttons_short
					);
				}
				if ( !empty( $buttons_long ) ) {
					$buttons_long = implode('', $buttons_long );
					printf(
						'<div class="depc_buttons_block">%s
						</div>',
						$buttons_long
					);
				}
			}
		}
	}
}

/* Display all available buttons */
if ( ! function_exists( 'depc_display_all_buttons' ) ) {
	function depc_display_all_buttons( $form = '' ) {
		global $depc_options, $depc_providers;

		if ( ! is_user_logged_in() ) {
			$buttons_short = $buttons_long = array();
			foreach ( $depc_providers as $provider => $provider_name ) {
				if ( ! empty( $depc_options["{$provider}_is_enabled"] ) ) {
					if ( 'long' == $depc_options["button_display_{$provider}"] ) {
						$buttons_long[ $provider ] = depc_get_button( $provider );
					} else {
						$buttons_short[ $provider ] = depc_get_button( $provider );
					}
				}
			}
			if ( 'comment_form' == $form && ! empty( $depc_options["comment_form"] ) ) {
				$buttons_long = apply_filters( 'depc_sort_comment_buttons', $buttons_long );
				$buttons_short = apply_filters( 'depc_sort_comment_buttons', $buttons_short );
			}

			if ( 'login_form' == $form && ! empty( $depc_options["login_form"] ) ) {
				$buttons_long = apply_filters( 'depc_sort_login_buttons', $buttons_long );
				$buttons_short = apply_filters( 'depc_sort_login_buttons', $buttons_short );
			}

			if ( 'register_form' == $form ) {
				$buttons_long = apply_filters( 'depc_sort_register_buttons', $buttons_long );
				$buttons_short = apply_filters( 'depc_sort_register_buttons', $buttons_short );
			}
		}
	}
}

/* Logging user in */
if ( ! function_exists( 'depc_login_user' ) ) {
	function depc_login_user( $id ) {
		$remember = ( isset( $_REQUEST['depc_remember'] ) ) ? true : false;
		wp_clear_auth_cookie();
		wp_set_current_user( $id );
		wp_set_auth_cookie( $id, $remember );
		$redirect = admin_url();
		if ( ! empty( $_SESSION['depc_redirect'] ) ) {
			/* redirecting to the referrer page */
			if ( wp_login_url() == $redirect ) {
				$redirect = $_SESSION['depc_redirect'];
			}
			unset( $_SESSION['depc_redirect'] );
		}
		wp_redirect( $redirect );
		exit();
	}
}

/* adding social to allowed domains array */
if ( ! function_exists( 'depc_allow_redirect' ) ) {
	function depc_allow_redirect( $allowed ) {
		$allowed[] = 'www.google.com';
		$allowed[] = 'www.facebook.com';
		$allowed[] = 'www.twitter.com';
		$allowed[] = 'www.linkedin.com';
		return $allowed;
	}
}

/* Adding "Social Login" block to the user profile page */
if ( ! function_exists( 'depc_user_profile' ) ) {
	function depc_user_profile() {
		global $depc_options, $depc_providers;
		$user_id = isset( $_REQUEST['user_id'] ) ? intval( $_REQUEST['user_id'] ) : get_current_user_id();

		$description_string = __( 'Enter %s to enable sign in with Social Login button.', 'depc-social-login-bws' );

		$fields = array(
			'google'         => array(
				'description'    => sprintf(
					$description_string,
					__( 'existing Gmail address', 'depc-social-login-bws' )
				),
				'field_type'     => 'email'
			),
			'facebook'       => array(
				'description'    => sprintf(
					$description_string,
					__( 'existing email address of Facebook account', 'depc-social-login-bws' )
				),
				'field_type'     => 'email'
			),
			'twitter'        => array(
				'description'   => sprintf(
					$description_string,
					__( 'existing email address of Twitter account', 'depc-social-login-bws' )
				),
				'field_type'    => 'email'
			),
			'linkedin'       => array(
				'description'   => sprintf(
					$description_string,
					__( 'existing email address of LinkedIn account', 'depc-social-login-bws' )
				),
				'field_type'    => 'email'
			)
		);
		if ( empty( $depc_options ) ) {
			depc_settings();
		}
		if ( 0 != $depc_options['google_is_enabled'] || 0 != $depc_options['facebook_is_enabled'] || 0 != $depc_options['twitter_is_enabled'] || 0 != $depc_options['linkedin_is_enabled'] ) { ?>
			<h2><?php _e( 'Social Login Accounts', 'depc-social-login-bws' ); ?></h2>
			<table class="form-table depc-form-table">
				<?php foreach ( $depc_providers as $provider => $provider_name ) {
					$provider_login = get_user_meta( $user_id, 'depc_' . $provider . '_login', true );
					if ( $depc_options[ $provider . '_is_enabled'] ) { ?>
						<tr class="depc_<?php echo $provider; ?>_email_field">
							<th>
								<?php echo $provider_name; ?>
							</th>
							<td>
								<input type="<?php echo $fields[ $provider ]['field_type']; ?>" class="depc_login_field" name="<?php echo 'depc_' . $provider . '_login'; ?>" id="<?php echo 'depc_' . $provider . '_login'; ?>" value="<?php echo $provider_login; ?>">
								<p class="description">
									<?php echo $fields[ $provider ]['description']; ?>
								</p>
							</td>
						</tr>
					<?php }
				}?>
			</table>
		<?php }
	}
}

/* updating user information */
if ( ! function_exists( 'depc_user_profile_update' ) ) {
	function depc_user_profile_update() {
		global $depc_options, $depc_providers;
		$user_id = isset( $_REQUEST['user_id'] ) ? intval( $_REQUEST['user_id'] ) : get_current_user_id();

		if ( empty( $depc_options ) ) {
			depc_settings();
		}

		foreach ( $depc_providers as $provider => $provider_name ) {
			if ( isset( $_POST['depc_' . $provider . '_login'] ) ) {
				$provider_login = sanitize_user( $_POST['depc_' . $provider . '_login'] );
				if ( ! empty( $provider_login ) ) {
					if ( is_email( $provider_login ) ) { /* preg_match is used for PHP versions older than 5.3 */
						$user = depc_get_user( $provider_login, '', $provider );

						if ( false === $user || $user_id === $user->ID ) {
							update_user_meta( $user_id, 'depc_' . $provider . '_login', $provider_login );
						}
					}
				} else {
					delete_user_meta( $user_id, 'depc_' . $provider . '_login' );
				}
			}
		}
	}
}

/* Adding errors on profile update */
if ( ! function_exists( 'depc_user_profile_update_errors' ) ) {
	function depc_user_profile_update_errors( $errors, $update = null, $user = null ) {
		global $depc_options, $depc_providers;

		$user_id = isset( $_REQUEST['user_id'] ) ? intval( $_REQUEST['user_id'] ) : get_current_user_id();

		if ( empty( $depc_options ) ) {
			depc_settings();
		}

		$providers_data = array(
			'general'       => array(
				'messages'      => array(
					'in_use'        => __( 'This email is already registered, please choose another one.', 'depc-social-login-bws' )
				)
			),
			'google'         => array(
				'type'          => 'email',
				'messages'      => array(
					'in_use'        => sprintf(
						__( 'The %1$s you specified for %2$s Account is already used by another user.', 'depc-social-login-bws' ),
						__( 'email address', 'depc-social-login-bws' ),
						'Google'
					),
					'invalid'       => sprintf(
						__( 'Please enter valid %1$s Account %2$s', 'depc-social-login-bws' ),
						'Google',
						__( 'email', 'depc-social-login-bws' )
					)
				)
			),
			'facebook'       => array(
				'type'          => 'email',
				'messages'      => array(
					'in_use'       => sprintf(
						__( 'The %1$s you specified for %2$s Account is already used by another user.', 'depc-social-login-bws' ),
						__( 'email address', 'depc-social-login-bws' ),
						'Facebook'
					),
					'invalid'      => sprintf(
						__( 'Please enter valid %1$s Account %2$s', 'depc-social-login-bws' ),
						'Facebook',
						__( 'email', 'depc-social-login-bws' )
					)
				)
			),
			'twitter'        => array(
				'type'          => 'email',
				'messages'      => array(
					'in_use'        => sprintf(
						__( 'The %1$s you specified for %2$s Account is already used by another user.', 'depc-social-login-bws' ),
						__( 'email address', 'depc-social-login-bws' ),
						'Twitter'
					),
					'invalid'       => sprintf(
						__( 'Please enter valid %1$s Account %2$s', 'depc-social-login-bws' ),
						'Twitter',
						__( 'email', 'depc-social-login-bws' )
					)
				)
			),
			'linkedin'       => array(
				'type'          => 'email',
				'messages'      => array(
					'in_use'        => sprintf(
						__( 'The %1$s you specified for %2$s Account is already used by another user.', 'depc-social-login-bws' ),
						__( 'email address', 'depc-social-login-bws' ),
						'LinkedIn'
					),
					'invalid'       => sprintf(
						__( 'Please enter valid %1$s Account %2$s', 'depc-social-login-bws' ),
						'LinkedIn',
						__( 'email', 'depc-social-login-bws' )
					)
				)
			)
		);

		if ( isset( $_POST['email'] ) ) {
			$error_codes = $errors->get_error_codes();
			if ( ! in_array( 'email_exists', $error_codes ) ) {
				$user_email = sanitize_email( $_POST['email'] );
				$user = depc_get_user( $user_email );
				if ( false !== $user && $user_id != $user->ID ) {
					$error_message = sprintf(
						'<strong>%1$s</strong>: %2$s',
						__( 'ERROR', 'depc-social-login-bws' ),
						$providers_data['general']['messages']['in_use']
					);
					$errors->add( 'depc_email_exists', $error_message );
				}
			}
		}

		foreach ( $depc_providers as $provider => $provider_name ) {
			if ( isset( $_POST['depc_' . $provider . '_login'] ) ) {
				$provider_login = sanitize_user( $_POST['depc_'. $provider . '_login'] );
				if ( ! empty( $provider_login ) && 'email' == $providers_data[ $provider ][ 'type' ] && ! is_email( $provider_login ) ) {
					$error_message = sprintf(
						'<strong>%1$s</strong>: %2$s',
						__( 'ERROR', 'depc-social-login-bws' ),
						$providers_data[ $provider ]['messages']['invalid']
					);
					$errors->add( 'depc_' . $provider . '_login_validation_error', $error_message );
				}

				if ( ! empty( $provider_login ) ) {
					$user = depc_get_user( $provider_login );
					if ( false !== $user && $user_id != $user->ID ) {
						$error_message = sprintf(
							'<strong>%1$s</strong>: %2$s',
							__( 'ERROR', 'depc-social-login-bws' ),
							$providers_data[ $provider ]['messages']['in_use']
						);
						$errors->add( 'depc_' . $provider . '_login_unavailable', $error_message );
					}
				}
			}
		}
	}
}

/* The function receives data from AJAX */
if ( ! function_exists( 'depc_ajax_data' ) ) {
	function depc_ajax_data() {
		check_ajax_referer( plugin_basename( __FILE__ ), 'depc_nonce' );

		/* Get redirect url to session variable */
		if ( ! empty( $_POST['depc_url'] ) ) {
			$_SESSION['depc_redirect'] = esc_url_raw( strval( $_POST['depc_url'] ) );
		}

		wp_die();
	}
}

/* Functions creates other links on plugins page. */
if ( ! function_exists( 'depc_action_links' ) ) {
	function depc_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			/* Static so we don't call plugin_basename on every plugin row. */
			static $this_plugin;
			if ( ! $this_plugin ) {
				$this_plugin = plugin_basename( __FILE__ );
			}
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=depc-social-login.php">' . __( 'Settings', 'depc-social-login-bws' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

if ( ! function_exists( 'depc_links' ) ) {
	function depc_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[]	=   '<a href="admin.php?page=depc-social-login.php">' . __( 'Settings', 'depc-social-login-bws' ) . '</a>';
			$links[]	=   '<a href="http://wordpress.org/plugins/depc-social-login-bws/faq/" target="_blank">' . __( 'FAQ', 'depc-social-login-bws' ) . '</a>';
			$links[]	=   '<a href="https://support.bestwebsoft.com">' . __( 'Support', 'depc-social-login-bws' ) . '</a>';
		}
		return $links;
	}
}

/* add help tab  */
if ( ! function_exists( 'depc_add_tabs' ) ) {
	function depc_add_tabs() {
		$screen = get_current_screen();
		$args = array(
			'id'			=> 'depc',
			'section'	   => ''
		);
		bws_help_tab( $screen, $args );
	}
}

if ( ! function_exists( 'depc_plugin_banner' ) ) {
	function depc_plugin_banner() {
		global $hook_suffix, $depc_plugin_info;

		if ( 'plugins.php' == $hook_suffix || ( isset( $_REQUEST['page'] ) && 'depc-social-login.php' == $_REQUEST['page'] ) ) {
			if ( 'plugins.php' == $hook_suffix ) {
				if ( ! is_network_admin() ) {
					bws_plugin_banner_to_settings( $depc_plugin_info, 'depc_options', 'depc-social-login-bws', 'admin.php?page=depc-social-login.php' );
				}
			} else {
				bws_plugin_suggest_feature_banner( $depc_plugin_info, 'depc_options', 'depc-social-login-bws' );
			}
		}
	}
}

/* Function for delete options */
if ( ! function_exists( 'depc_delete_options' ) ) {
	function depc_delete_options() {
		global $depc_providers, $depc_options;
		depc_settings();
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			global $wpdb;
			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				if ( $depc_options['delete_metadata'] ) {
					foreach ( $depc_providers as $provider => $provider_name ) {
						delete_metadata( 'user', 1, 'depc_' . $provider . '_login', false, true );
					}
				}
				delete_option( 'depc_options' );
			}
			switch_to_blog( $old_blog );
		} else {
			if ( $depc_options['delete_metadata'] ) {
				foreach ( $depc_providers as $provider => $provider_name ) {
					delete_metadata( 'user', 1, 'depc_' . $provider . '_login', false, true );
				}
			}
			delete_option( 'depc_options' );
		}

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );
	}
}

register_activation_hook( __FILE__, 'depc_plugin_activate' );

/* Calling a function add administrative menu. */
// add_action( 'admin_menu', 'add_depc_admin_menu' );
add_action( 'plugins_loaded', 'depc_plugins_loaded' );
add_action( 'init', 'depc_init' );
add_action( 'admin_init', 'depc_admin_init' );

/* Adding stylesheets */
// add_action( 'admin_enqueue_scripts', 'depc_enqueue_scripts' );
/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'depc_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'depc_links', 10, 2 );
/* Adding banner */
// add_action( 'admin_notices', 'depc_plugin_banner' );

add_action( 'depc_quick_login_form', 'depc_login_form' );
add_filter( 'login_message', 'depc_login_error' );
add_filter( 'depc_get_user_filter', 'depc_get_user', 10, 3 );
add_filter( 'depc_get_user_email', 'depc_get_user_email', 10, 1 );
add_filter( 'sbscrbr_get_user_email', 'depc_get_user_email', 10, 1 );
add_action( 'depc_quick_register_form', 'depc_register_form' );
/* Adding to 'signup_extra_fields' hook form signup.php */
add_action( 'signup_extra_fields', 'depc_register_form' );
add_action( 'comment_form_top', 'depc_comment_form' );
add_action( 'depc_login_form', 'depc_login_form' );
add_action( 'depc_register_form', 'depc_register_form' );
add_action( 'depc_comment_form', 'depc_comment_form' );
add_action( 'depc_display_all_buttons', 'depc_display_all_buttons' );

/* Adding stylesheets */
add_action( 'wp_enqueue_scripts', 'depc_enqueue_scripts' );
add_action( 'login_enqueue_scripts', 'depc_login_enqueue_scripts' );
add_filter( 'allowed_redirect_hosts','depc_allow_redirect' );
/* Adding to 'signup_extra_fields' hook form signup.php */
add_action( 'signup_extra_fields', 'depc_enqueue_scripts' );
add_action( 'signup_extra_fields', 'depc_login_enqueue_scripts' );

/* add theme name as class to body tag */
add_filter( 'body_class', 'depc_theme_body_classes' );

/* adding custom fields to the user profile page*/
add_action( 'show_user_profile', 'depc_user_profile' );
add_action( 'edit_user_profile', 'depc_user_profile' );
/* update user profile information */
add_action( 'edit_user_profile_update', 'depc_user_profile_update' );
add_action( 'personal_options_update', 'depc_user_profile_update' );
add_action( 'user_profile_update_errors', 'depc_user_profile_update_errors' );

/* Adding AJAX*/
add_action( 'wp_ajax_depc_remember', 'depc_ajax_data' );
add_action( 'wp_ajax_nopriv_depc_remember', 'depc_ajax_data' );