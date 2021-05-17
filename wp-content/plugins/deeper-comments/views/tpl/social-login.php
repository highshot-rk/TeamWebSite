<?php if( $settings['google_login_enable'] == 'on' ): ?>
	<span class="dpr-join-form-login-txt"><?php esc_attr_e( 'Login With', 'depc' ) ?></span>
<?php endif; ?>

<?php if( $settings['fb_login_enable'] ): ?>
	<a href="#" class="dpr-join-form-login-facebook"><i class="sl-social-facebook"></i> </a>
<?php endif; ?>

<?php if( $settings['tw_login_enable'] ): ?>
	<a href="#" class="dpr-join-form-login-twitter"><i class="sl-social-twitter"></i> </a>
<?php endif; ?>

<?php if( $settings['google_login_enable'] == 'on' ): ?>
	<a href="<?php echo $url; ?>" class="dpr-join-form-login-google" ><i class="sl-social-google"></i> </a>
<?php endif; ?>
