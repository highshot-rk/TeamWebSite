<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */
echo $scripts;
?>
<?php if( $settings['quick_login'] == 'on' ): ?>
	<a href="#" class="dpr-join-form-login-a"><i class="sl-login"></i> <?php esc_attr_e( 'Login', 'depc' ); ?></a>
<?php endif; ?>
<?php if( get_option( 'users_can_register' ) === '1' ): ?>
	<?php if( $settings['quick_register'] == 'on' ): ?>
		<a href="#" class="dpr-join-form-register-a"><i class="sl-plus"></i> <?php esc_attr_e( 'Register', 'depc' ); ?></a>
		<?php endif; ?>
		<?php endif; ?>
<?php if( \Depc_Core::get_option( 'dc_social_login', 'Login_Register' , 'off' ) == 'on'): ?>
	<div class="depc-social-login-wrap">
		<?php do_action('depc_quick_login_form') ?>
	</div>
<?php endif; ?>
