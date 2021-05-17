<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

if ( !comments_open() ) {
	return;
}

$comment_registration = get_option( 'comment_registration' );
$customstyle = '';
$textarea_customstyle = '';
if( \Depc_Core::get_option( 'dc_generate_avatar', 'Avatar' , 'on' ) != 'on' ){
	$customstyle = ' pl0';
	$textarea_customstyle = 'w-100';
}

$validator = new Depc_Request_Validator;
$comment_status = 'hold';

// guest checking
$comment_allow_status = 'guest-allowed';
if ( $validator->is_guest_allowed() == 0 ) {
	$comment_allow_status = 'guest-not-allowed';
}

?>


<!-- Primary Page Layout
	================================================== -->
	<div class="<?php echo $skin; ?>">
		<div class='dpr-preloader-wrap'>
			<div class='dpr-preloader'></div>
		</div>
		<div class="dpr-container dpr-discu-container dpr-discu-container_<?php echo $post; ?> <?php echo $comment_allow_status; ?>">
			<?php
			$edit->load();
			?>
			<div class="dpr-join-form-wrap"><!-- deeper dpr-join-form-wrap /start -->
				<div class="dpr-join-form">
				<?php if ( ! $comment_registration ): ?>
					<div class="dpr-join-form-area">
					<?php if( \Depc_Core::get_option( 'dc_generate_avatar', 'Avatar' , 'on' ) == 'on' ):  ?>
						<i class="sl-user"></i>
					<?php endif; ?>
						<!-- <textarea readonly="" placeholder="<?php echo __( 'Join the discussion...', 'depc' ); ?>" class="<?php echo($textarea_customstyle) ?>"><?php echo __( 'Join the discussion...', 'depc' ); ?></textarea> -->
						<button class="comment-toggle"><?php echo __( 'Join the discussion', 'depc' ); ?></button>
					</div>
				<?php endif; ?>
					<div class="dpr-submit-form-wrap" style="display: none;"><!-- deeper submit form wrapper /start -->
						<div class="dpr-submit-form-editor<?php echo($customstyle) ?>">
							<div id="dpr_container">
								<a id="dpr_call_panel" class="dpr_light dpr_right_bottom" data-emoji-panel="{'theme':'light', 'showOnMobile':false, 'close':false, 'position':'right_bottom'}" rel="nofollow noreferrer" title="Insertar Emoji"></a>

								<textarea class="dpr-add-editor" cols="45" rows="8" maxlength="65525" required="required" data-emoji-textarea></textarea>
							</div>
						</div>
						<div class="dpr-submit-form-fields">
							<span class="dpr-submit-form-fields-c dpr-submit-form-fieldswrap-name">
								<i class="sl-user"></i>
								<input class="dpr-submit-form-fields-c-name" require="required" type="text" name="name" placeholder="<?php esc_attr_e('Name','depc') ?>">
							</span>
							<span class="dpr-submit-form-fields-c dpr-submit-form-fieldswrap-email">
								<i class="sl-envelope-open"></i>
								<input class="dpr-submit-form-fields-c-email" require="required" type="email" name="email" placeholder="<?php esc_attr_e('Email','depc') ?>">
							</span>

							<?php if (Depc_Core::get_option( 'dc_use_website_field', 'Comments' , 'on') == 'on'): ?>
								<span class="dpr-submit-form-fields-c dpr-submit-form-fieldswrap-website">
									<i class="sl-compass"></i>
									<input class="dpr-submit-form-fields-c-website" type="text" name="website" placeholder="<?php esc_attr_e('Website','depc') ?>">
								</span>
							<?php endif ?>

						</div>
						<?php if( $recaptcha === 'google' ): ?>
							<div class="dpr-submit-form-captcha">
								<div class="dpr-submit-form-captcha-container">
									<div id="dpr-submit-captcha"></div>
								</div>
							</div>
						<?php endif; ?>

					</div><!-- deeper submit form wrapper /end -->
					<div class="dpr-join-form-inner">
						<div class="dpr-join-form-login-register">
							<?php $login = Depc_Controller_Module_Login::get_instance(); $login->load_login(); ?>
							<?php if ( ! $comment_registration ): ?>
							<a href="#" onclick="return false;"  class="dpr-discu-submit"><i class="sl-cursor"></i> <?php _e( 'Submit', 'depc' ) ?></a>
							<?php endif; ?>
						</div>
						<div class="dpr-join-form-social-login">
							<?php $google = Depc_Controller_Module_Google::get_instance(); $google->load(); ?>
						</div>

					</div>
				</div>

			</div>

			<?php $most_recent_authors->load(); ?>
			<?php $filter->load(); ?>
			<div class="dpr-discu-main-loop-wrap">
				<?php
				$loop = Depc_Controller_Public_Comment_Loop::get_instance();
				$loop->load();
				?>
			</div>
		</div><!-- end dpr-container -->
	</div><!-- end dpr-wrap -->


<!-- End Document
	================================================== -->
