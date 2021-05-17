<?php
/**
 * Created by PhpStorm.
 * User: Webnus01
 * Date: 6/25/2017
 * Time: 11:21 AM
 */
if ( !comments_open() ) {
	return;
}

$customstyle = '';
$textarea_customstyle = '';
if( \Depc_Core::get_option( 'dc_generate_avatar', 'Avatar' , 'on' ) != 'on' ){
	$customstyle = ' pl0';
	$textarea_customstyle = 'w-100';
}
if( $url = \Depc_Core::get_option( 'dc_user_profile_url', 'Comments' , '' )  ){
	$user_profile_link = $url;
}
?>
<!-- Primary Page Layout
================================================== -->
<div class="<?php echo $skin; ?>">
	<div class='dpr-preloader-wrap'>
		<div class='dpr-preloader'></div>
	</div>
	<div class="dpr-container dpr-discu-container dpr-discu-container_<?php echo $post; ?>">
		<?php
		$edit->load();
		?>
	    <div class="dpr-join-form-wrap"><!-- deeper switch-tab wrapper /start -->
		    <div class="dpr-join-form">
			    <div class="dpr-join-form-area">
				<?php if( \Depc_Core::get_option( 'dc_generate_avatar', 'Avatar' , 'on' ) == 'on' ):  ?>
					<div class="dpr-discu-user">
			    		<div class="dpr-discu-user-img">
			    			<?php echo $avatar; ?>
			    		</div>
					</div>
				<?php endif; ?>
					<!-- <textarea readonly="" placeholder="<?php echo __( 'Join the discussion...', 'depc' ); ?>"  class="<?php echo($textarea_customstyle) ?>"><?php echo __( 'Join the discussion...', 'depc' ); ?></textarea> -->
					<button class="comment-toggle"><?php echo __( 'Join the discussion', 'depc' ); ?></button>
				</div>

				<div class="dpr-submit-form-wrap" style="display: none;"><!-- deeper submit form wrapper /start -->
					<div class="dpr-submit-form-editor<?php echo($customstyle) ?>">
						<textarea class="dpr-add-editor" placeholder=""></textarea>
					</div>
				</div><!-- deeper submit form wrapper /end -->

			    <div class="dpr-join-form-inner">
				    <div class="dpr-join-form-login-register dpr-is-login">
						<span class="dpr-logged-in-user-message u-r-log"><?php _e( 'You are logged in as', 'depc' ) ?></span>
						<a href="<?php echo $user_profile_link; ?>" class="dpr-discu-user-a"><i class="sl-user"></i><?php echo $username; ?></a>
						<span class="dpr-logged-in-user-message"><a href="<?php echo wp_logout_url( get_permalink() ); ?>" class="dpr-log-out-user-link dpr-logout-a"><?php _e( 'Log Out', 'depc' ) ?></a></span>
					    <a href="#" onclick="return false;" class="dpr-discu-submit"><i class="sl-cursor"></i><?php _e( 'Submit', 'depc' ) ?></a>
				    </div>
			    </div>
		    </div>
	    </div><!-- deeper switch-tab wrapper /end -->
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
