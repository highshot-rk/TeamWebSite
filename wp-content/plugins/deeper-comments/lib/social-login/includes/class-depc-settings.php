<?php
/**
 * Displays the content on the plugin settings page
 */
if ( ! class_exists( 'depc_Settings_Tabs' ) ) {
	class depc_Settings_Tabs extends Bws_Settings_Tabs {
		private $forms, $array_role;

		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $depc_options, $depc_plugin_info;

			$tabs = array(
				'settings'		=> array( 'label' => __( 'Settings', 'depc-social-login-bws' ) ),
				'misc'			=> array( 'label' => __( 'Misc', 'depc-social-login-bws' ) ),
				'custom_code'	=> array( 'label' => __( 'Custom Code', 'depc-social-login-bws' ) )
			);

			parent::__construct( array(
				'plugin_basename'		=> $plugin_basename,
				'plugins_info'			=> $depc_plugin_info,
				'prefix'				=> 'depc',
				'default_options'		=> depc_get_default_options(),
				'options'				=> $depc_options,
				'tabs'					=> $tabs,
				'wp_slug'				=> 'depc-social-login-bws',
				'doc_link'				=> 'https://docs.google.com/document/d/1jS1pGbaIyhR9-6wsvWFueMqd8ZJYKRQAJGkOc8j5lWE/'
			) );

			$this->forms = array(
				'login_form'		=> __( 'Login form', 'depc-social-login-bws' ),
				'register_form'		=> __( 'Registration form', 'depc-social-login-bws' ),
				'comment_form'		=> __( 'Comments form', 'depc-social-login-bws' )
			);

			$this->array_role = get_editable_roles();

			add_action( get_parent_class( $this ) . '_additional_misc_options_affected', array( $this, 'additional_misc_options_affected' ) );
		}

		public function save_options() {
			global $depc_providers;

			$message = $notice = $error = '';

			foreach ( $depc_providers as $provider => $provider_name ) {
				if ( ! empty( $_REQUEST["depc_{$provider}_is_enabled"] ) ) {
					$this->options['button_display_' . $provider] = ( isset( $_REQUEST['depc_' . $provider . '_display_button'] ) && in_array( $_REQUEST['depc_' . $provider . '_display_button'], array( 'long', 'short', 'dark', 'light' ) ) ) ? $_REQUEST['depc_' . $provider . '_display_button'] : $this->default_options['button_display_' . $provider];
					$this->options[ $provider . '_button_name'] = sanitize_text_field( wp_unslash( $_REQUEST['depc_' . $provider . '_button_text'] ) );
					$this->options["{$provider}_is_enabled"] = 1;

					if ( ! empty( $_REQUEST["depc_{$provider}_client_id"]  ) ) {
						$this->options["{$provider}_client_id"] = trim( stripslashes( sanitize_text_field( $_REQUEST["depc_{$provider}_client_id"] ) ) );
					} else {
						$error .= sprintf( __( 'Please fill the Client ID for %s.', 'depc-social-login-bws' ), $provider_name ) . '<br />';
					}

					if ( ! empty( $_REQUEST["depc_{$provider}_client_secret"] ) ) {
						$this->options["{$provider}_client_secret"] = trim( stripslashes( sanitize_text_field( $_REQUEST["depc_{$provider}_client_secret"] ) ) );
					} else {
						$error .= sprintf( __( 'Please fill the Client secret for %s.', 'depc-social-login-bws' ), $provider_name ) . '<br />';
					}
				} else {
					$this->options["{$provider}_is_enabled"] = 0;
				}
			}

			foreach ( $this->forms as $form_slug => $form ) {
				$this->options[ $form_slug ] = isset( $_REQUEST["depc_{$form_slug}"] ) ? 1 : 0;
			}
			$this->options['loginform_buttons_position'] = ( isset( $_REQUEST['depc_loginform_buttons_position'] ) && in_array( $_REQUEST['depc_loginform_buttons_position'], array( 'top', 'middle', 'bottom' ) ) ) ? $_REQUEST['depc_loginform_buttons_position'] : $this->options['loginform_buttons_position'];
			$this->options['user_role'] = ( isset( $_REQUEST['depc_role'] ) && array_key_exists( $_REQUEST['depc_role'], $this->array_role ) ) ? $_REQUEST['depc_role'] : $this->options['user_role'];
			$this->options['allow_registration'] = ( isset( $_REQUEST['depc_register_option'] ) && in_array( $_REQUEST['depc_register_option'], array( 'default', 'allow', 'deny' ) ) ) ? $_POST['depc_register_option'] : 'default';
			$this->options['delete_metadata'] = isset( $_POST['depc_delete_metadata'] ) ? 1 : 0;

			update_option( 'depc_options', $this->options );

			$message = __( 'Settings saved', 'depc-social-login-bws' );

			return compact( 'message', 'notice', 'error' );
		}

		public function tab_settings() {
			global $depc_providers;

			$php_version_is_proper = ( version_compare( phpversion(), '5.3', '>=' ) ) ? true : false; ?>
			<h3><?php _e( 'Social Login Settings', 'depc-social-login-bws' ); ?></h3>
            <?php $this->help_phrase(); ?>
            <hr>
            <div class="bws_tab_sub_label"><?php _e( 'General', 'depc-social-login-bws' ); ?></div>
			<table class="form-table depc-form-table">
				<tr>
					<th><?php _e( 'Buttons', 'depc-social-login-bws' ); ?></th>
					<td>
						<fieldset>
							<?php foreach ( $depc_providers as $provider => $provider_name ) { ?>
								<label>
									<input type="checkbox" value="1" name="depc_<?php echo $provider; ?>_is_enabled"<?php checked( $this->options[ $provider . '_is_enabled'] ); disabled( ! $php_version_is_proper ); ?> class="bws_option_affect" data-affect-show=".depc_<?php echo $provider; ?>_client_data" />
									<?php echo $provider_name; ?>
								</label>
								<br />
							<?php } ?>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Enable Social Login for', 'depc-social-login-bws' ); ?></th>
					<td>
						<p>
							<i><?php _e( 'WordPress default', 'depc-social-login-bws' ); ?></i>
						</p>
						<br>
						<fieldset>
							<?php foreach ( $this->forms as $form_slug => $form ) { ?>
								<label>
									<input type="checkbox" value="1" name="<?php echo "depc_{$form_slug}"; ?>"<?php checked( $this->options[ $form_slug ], 1 ); ?> class="<?php echo "depc_{$form_slug}_checkbox"; ?>" />
									<?php echo $form; ?>
								</label>
								<br />
							<?php } ?>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'Buttons Position', 'depc-social-login-bws' ); ?>
					</th>
					<td>
						<select name="depc_loginform_buttons_position" >
							<option value="top" <?php selected( $this->options['loginform_buttons_position'], 'top' ); ?>>
								<?php _e( 'Top', 'depc-social-login-bws' ) ?>
							</option>
							<option value="middle" <?php selected( $this->options['loginform_buttons_position'], 'middle' ); ?>>
								<?php _e( 'Before the submit button', 'depc-social-login-bws' ) ?>
							</option>
							<option value="bottom" <?php selected( $this->options['loginform_buttons_position'], 'bottom' ); ?>>
								<?php _e( 'Bottom', 'depc-social-login-bws' ) ?>
							</option>
						</select>
						<div class="bws_info">
							<?php _e( 'Choose the buttons position in the form. This option is available only for Login and Registration forms.', 'depc-social-login-bws' ); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'User Registration', 'depc-social-login-bws' ); ?>
					</th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="depc_register_option" value="default" <?php checked( 'default' == $this->options['allow_registration'] ); ?> class="bws_option_affect" data-affect-hide="#depc_allow_user_registration_notice, #depc_deny_user_registration_notice" /> <?php _e( 'Default', 'depc-social-login-bws' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="depc_register_option" value="allow" <?php checked( 'allow' == $this->options['allow_registration'] ); ?> class="bws_option_affect" data-affect-show="#depc_allow_user_registration_notice" data-affect-hide="#depc_deny_user_registration_notice" /> <?php _e( 'Allow', 'depc-social-login-bws' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="depc_register_option" value="deny" <?php checked( 'deny' == $this->options['allow_registration'] ); ?> class="bws_option_affect" data-affect-show="#depc_deny_user_registration_notice" data-affect-hide="#depc_allow_user_registration_notice" /> <?php _e( 'Deny', 'depc-social-login-bws' ); ?>
							</label>
						</fieldset>
						<div class="bws_info" style="display: inline;">
							<?php printf( __( 'Allow or deny user registration using social buttons regardless %s.', 'depc-social-login-bws' ),
								'<a href="options-general.php" target="_blank" nohref="nohref">' . __( 'WordPress General settings', 'depc-social-login-bws' ) . '</a>'
							); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'New User Default Role', 'depc-social-login-bws' ); ?>
					</th>
					<td>
						<fieldset>
							<?php if ( function_exists( 'get_editable_roles' ) ) {
								$default_role = get_option( 'default_role' ); ?>
								<select name="depc_role" >
									<?php foreach ( $this->array_role as $role => $fields ) {
										printf(
											'<option value="%1$s" %2$s >
											%3$s%4$s
											</option>',
											$role,
											selected( $this->options['user_role'], $role ),
											translate_user_role( $fields['name'] ),
											( $role == $default_role ) ? ' (' . __( 'Default', 'depc-social-login-bws' ) . ')' : ''
										);
									} ?>
								</select>
							<?php } ?>
						</fieldset>
						<div class="bws_info">
							<?php _e( 'Choose a default role for newly registered users.', 'depc-social-login-bws' ); ?>
						</div>
					</td>
				</tr>
			</table>
				<?php /*GOOGLE*/ ?>
			<div class="bws_tab_sub_label depc_google_client_data">Google</div>
			<table class="form-table depc_google_client_data">
				<tr>
					<th><?php _e( 'Client ID', 'depc-social-login-bws' ); ?></th>
					<td>
						<input type="text" name="depc_google_client_id" value="<?php echo $this->options['google_client_id']; ?>" size="20" />
						<div class="bws_info">
							<?php _e( 'You need to create your own credentials in order to use google API.', 'depc-social-login-bws' ); ?> <a href="https://docs.google.com/document/d/1jS1pGbaIyhR9-6wsvWFueMqd8ZJYKRQAJGkOc8j5lWE/edit#heading=h.ly70c5c1dj07" target="_blank" nohref="nohref"><?php _e( 'Learn More', 'depc-social-login-bws' ); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Client Secret', 'depc-social-login-bws' ); ?></th>
					<td>
						<input type="text" name="depc_google_client_secret" value="<?php echo $this->options['google_client_secret']; ?>" size="20">
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Button Style', 'depc-social-login-bws' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="depc_google_display_button" value="dark" <?php checked( 'dark' == $this->options['button_display_google'] ); ?> />
								<?php _e( 'Dark', 'depc-social-login-bws' ); ?>
							</label>
							<br/>
							<label>
								<input type="radio" name="depc_google_display_button" value="light" <?php checked( 'light' == $this->options['button_display_google'] ); ?> />
								<?php _e( 'Light', 'depc-social-login-bws' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Button Label Text', 'depc-social-login-bws' ); ?></th>
					<td>
						<input type="text" name="depc_google_button_text" value="<?php echo $this->options['google_button_name']; ?>"/>
					</td>
				</tr>
			</table>
				<?php /*FACEBOOK*/ ?>
			<div class="bws_tab_sub_label depc_facebook_client_data">Facebook</div>
			<table class="form-table depc-form-table depc_facebook_client_data">
				<tr>
					<th><?php _e( 'App ID', 'depc-social-login-bws' ); ?></th>
					<td>
						<input type="text" name="depc_facebook_client_id" value="<?php echo $this->options['facebook_client_id']; ?>" size="20"/>
						<div class="bws_info">
							<?php _e( 'You need to create your own credentials in order to use Facebook API.', 'depc-social-login-bws' ); ?> <a href="https://docs.google.com/document/d/1jS1pGbaIyhR9-6wsvWFueMqd8ZJYKRQAJGkOc8j5lWE/edit#heading=h.5xcmcz2zjjtl" target="_blank" nohref="nohref"><?php _e( 'Learn More', 'depc-social-login-bws' ); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'App Secret', 'depc-social-login-bws' ); ?></th>
					<td>
						<input type="text" name="depc_facebook_client_secret" value="<?php echo $this->options['facebook_client_secret']; ?>" size="20" />
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Button Display', 'depc-social-login-bws' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="depc_facebook_display_button" value="long" <?php checked( 'long' == $this->options['button_display_facebook'] ); ?> />
							</label>
							<div class="depc_login_button depc_login_button_long depc_facebook_button" id="depc_facebook_button">
								<span class="dashicons dashicons-facebook"></span>
								<span class="depc_button_text"><input type="text" name="depc_facebook_button_text" value="<?php echo $this->options['facebook_button_name']; ?>" /></span>
							</div>
							<span class="dashicons dashicons-welcome-write-blog"></span>

							<br/>
							<label>
								<input type="radio" name="depc_facebook_display_button" value="short" <?php checked( 'short' == $this->options['button_display_facebook'] ); ?> />
							</label>
							<div class="depc_login_button depc_login_button_short depc_facebook_button depc_login_button_icon">
								<span class="depc_span_icon dashicons dashicons-facebook"></span>
							</div>
						</fieldset>
					</td>
				</tr>
			</table>
				<?php /*TWITTER*/ ?>
			<div class="bws_tab_sub_label depc_twitter_client_data">Twitter</div>
			<table class="form-table depc-form-table depc_twitter_client_data">
				<tr>
					<th><?php _e( 'Consumer Key (API Key)', 'depc-social-login-bws' ); ?></th>
					<td>
						<input type="text" name="depc_twitter_client_id" value="<?php echo $this->options['twitter_client_id']; ?>" size="20" />
						<div class="bws_info">
							<?php _e( 'You need to create your own credentials in order to use twitter API.', 'depc-social-login-bws' ); ?> <a href="https://docs.google.com/document/d/1jS1pGbaIyhR9-6wsvWFueMqd8ZJYKRQAJGkOc8j5lWE/edit#heading=h.fnl0icuiiahq" target="_blank" nohref="nohref"><?php _e( 'Learn More', 'depc-social-login-bws' ); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Consumer Secret (API Secret)', 'depc-social-login-bws' ); ?></th>
					<td>
						<input type="text" name="depc_twitter_client_secret" value="<?php echo $this->options['twitter_client_secret']; ?>" size="20">
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Button Display', 'depc-social-login-bws' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="depc_twitter_display_button" value="long" <?php checked( 'long' == $this->options['button_display_twitter'] ); ?> />
							</label>
								<div class="depc_login_button depc_login_button_long depc_twitter_button" id="depc_twitter_button">
								<span class="dashicons dashicons-twitter"></span>
								<span class="depc_button_text"><input type="text" name="depc_twitter_button_text" value="<?php echo $this->options['twitter_button_name']; ?>" /></span>
							</div>
							<span class="dashicons dashicons-welcome-write-blog"></span>
							</div>
							<br/>
							<label>
								<input type="radio" name="depc_twitter_display_button" value="short" <?php checked( 'short' == $this->options['button_display_twitter'] ); ?> />
							</label>
							<div class="depc_login_button depc_login_button_short depc_twitter_button depc_login_button_icon">
								<span class="depc_span_icon dashicons dashicons-twitter"></span>
							</div>
						</fieldset>
					</td>
				</tr>
			</table>
				<?php /*LINKEDIN*/ ?>
			<div class="bws_tab_sub_label depc_linkedin_client_data">LinkedIn</div>
			<table class="form-table depc-form-table depc_linkedin_client_data">
				<tr>
					<th><?php _e( 'Client ID', 'depc-social-login-bws' ); ?></th>
					<td>
						<input type="text" name="depc_linkedin_client_id" value="<?php echo $this->options['linkedin_client_id']; ?>" size="20" />
						<div class="bws_info">
							<?php _e( 'You need to create your own credentials in order to use linkedin API.', 'depc-social-login-bws' ); ?> <a href="https://docs.google.com/document/d/1jS1pGbaIyhR9-6wsvWFueMqd8ZJYKRQAJGkOc8j5lWE/edit#heading=h.vgel2zwdelzu" target="_blank" nohref="nohref"><?php _e( 'Learn More', 'depc-social-login-bws' ); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Client Secret', 'depc-social-login-bws' ); ?></th>
					<td>
						<input type="text" name="depc_linkedin_client_secret" value="<?php echo $this->options['linkedin_client_secret']; ?>" size="20">
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Button Display', 'depc-social-login-bws' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="depc_linkedin_display_button" value="long" <?php checked( 'long' == $this->options['button_display_linkedin'] ); ?> />
							</label>
							<div class="depc_login_button depc_login_button_long depc_linkedin_button" id="depc_linkedin_button">
								<span class="dashicons bws-icons depc_linkedin_button_admin"></span>
								<span class="depc_button_text" ><input type="text" name="depc_linkedin_button_text" value="<?php echo $this->options['linkedin_button_name']; ?>" /></span>
							</div>
							<span class="dashicons dashicons-welcome-write-blog"></span>
							</div>
							<br/>
							<label>
								<input type="radio" name="depc_linkedin_display_button" value="short" <?php checked( 'short' == $this->options['button_display_linkedin'] ); ?> />
							</label>
							<div class="depc_login_button depc_linkedin_button depc_login_button_short depc_login_button_icon">
								<span class="depc_span_icon dashicons bws-icons depc_linkedin_button_admin"></span>
							</div>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php }

		public function additional_misc_options_affected(){ ?>
            <tr>
				<th>
					<?php _e( 'Delete User Metadata', 'depc-social-login-bws' ); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" value="1" name="depc_delete_metadata"<?php checked( $this->options['delete_metadata'], 1 ); ?> class="depc_delete_metadata_checkbox" />
						<span class="bws_info">
							<?php _e( 'Enable to delete all user metadata when deleting the plugin.', 'depc-social-login-bws' ); ?>
						</span>
					</label>
				</td>
			</tr>
        <?php }

	}
} ?>