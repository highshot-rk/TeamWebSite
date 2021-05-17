<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */
$child_customstyle = '';
if( \Depc_Core::get_option( 'dc_generate_avatar', 'Avatar' , 'on' ) != 'on' ){
	$child_customstyle = ' ml20 w-100 pl20 pr20 no-avatar';
}
?>
<?php if ( $comments ) : ?>
	<?php function depc_comment_loop($comments, $child_customstyle, $settings , $inappropriate,$id, $validator, $comments_parrents, $child_item = 0, $depth = 5) { ?>
		<?php foreach ( $comments as $comment ) : ?>
		<?php $avatar = Depc_Model_Public_Comment::get_instance(); ?>
<!-- Comment loop Page layout
	================================================== -->
		<div class="dpr-discu-wrap dpr-discu-wrap_<?php echo $comment->comment_ID; ?><?php echo $child_customstyle ?>">
		<?php if( \Depc_Core::get_option( 'dc_generate_avatar', 'Avatar' , 'on' ) == 'on' ):  ?>
			<div class="dpr-discu-user">
				<div class="dpr-discu-user-img">
					<?php
						echo $avatar->avatar( $comment->comment_author_email , $comment->comment_author, $comment->comment_ID  );
					?>
				</div>
			</div>
		<?php endif; ?>
		<div id="<?php echo 'comments-'. $comment->comment_ID; ?>" class="dpr-discu-box" >
			<div class='dpr-preloader-wrap'>
				<div class='dpr-preloader'></div>
			</div>
			<div class="dpr-discu-box-header" data-id="<?php echo $comment->comment_ID; ?>">

				<span class="dpr-discu-user-name">
					<?php do_action('dpr-discu-user-name', $comment->comment_ID) ?>
					<?php if( $comment->comment_author_url ): ?>
						<a href="<?php echo esc_attr( $comment->comment_author_url ); ?>" rel="no-follow">
							<span><?php esc_attr_e( $comment->comment_author ); ?></span>
						</a>
					<?php else: ?>
						<span><?php esc_attr_e( $comment->comment_author ); ?></span>
					<?php endif; ?>
				</span>
				<?php if( \Depc_Core::get_option( 'dc_show_comment_date', 'Appearances' ,'on') == 'on' ): ?>
				<span class="dpr-discu-date">
					<span><?php echo date_i18n( get_option( 'date_format' ), strtotime( $comment->comment_date_gmt ) ); ?></span>
				</span>
				<?php endif; ?>
				<div class="dpr-discu-box-header-icons">
					<?php if( $settings['collapse'] == 'on' ): ?>
						<a href="#" class="dpr-discu-collapse dpr-tooltip" data-wntooltip="<?php esc_attr_e( 'Collapse comment', 'depc' ); ?>"  data-id="<?php echo $comment->comment_ID; ?>" >
							<i class="sl-arrow-down"></i>
						</a>
					<?php endif; ?>
					<?php if( $settings['link'] == 'on' ): ?>
						<span> | </span>
						<a href="#" data-clipboard-text="<?php echo get_comments_link( $id ) . '-' . $comment->comment_ID; ?>" class="dpr-discu-link dpr-tooltip" data-wntooltip="<?php esc_attr_e( 'Click to copy comment link', 'depc' ); ?>">
							<i class="sl-link"></i>
						</a>
					<?php endif; ?>
					<?php if( $settings['follow'] == 'on' ): ?>
						<span> | </span>
						<?php do_action('deeper_comments_render_conversion_following_html', $comment->comment_ID, $comment); ?>
					<?php endif; ?>
					<?php
					if ( $inappropriate != false ) {
						$dom = new DOMDocument();
						$dom->preserveWhiteSpace = false;
						$dom->loadHTML( $inappropriate );
						$atag = $dom->getElementsByTagName('a')->item(0);
						$atag->setAttribute('data-id' , $comment->comment_ID );
						$html=$inappropriate;
						echo $html;
					}
					?>
					<?php if( $settings['edit'] == 'on' && $validator->allow_edit_comment( $comment->user_id , $comment->comment_date_gmt ) != false ): ?>
						<span> | </span>
						<a href="#" class="dpr-discu-edit dpr-tooltip" data-wntooltip="<?php esc_attr_e( 'Edit', 'depc' ); ?>" data-id="<?php echo $comment->comment_ID; ?>">
							<i class="sl-pencil"></i>
						</a>
					<?php endif; ?>
					<?php $parent_result = in_array( $comment->comment_ID , $comments_parrents ) ? true : false ;?>
					<?php if( $settings['delete'] == 'on' && $validator->allow_delete_comment( $comment->user_id, $parent_result  ) != false ): ?>
						<span> | </span>
						<a href="#" class="dpr-discu-delete dpr-tooltip" data-wntooltip="<?php esc_attr_e( 'Delete', 'depc' ); ?>"  data-id="<?php echo $comment->comment_ID; ?>" >
							<i class="sl-trash"></i>
						</a>
					<?php endif; ?>
				</div>

			</div>

			<div class="clearfix"></div>

			<div class="dpr-c-contents">
				<?php if( $comment->comment_parent ): ?>
					<div class="dpr-discu-inreplyto dpr-tooltip" data-wntooltip="in Reply to">
						<?php $cm_author = get_comment_author( $comment->comment_parent ); ?>
						<i class="sl-action-redo"></i><?php do_action('dpr-discu-user-name', $comment->comment_parent) ?><span class="dpr-discu-user-name"><?php esc_attr_e( $cm_author ); ?></span>
						<?php $cm_content = get_comment_text( $comment->comment_parent ); ?>
						<span class="dpr-discu-replyto-text"><?php esc_html_e( substr( wp_strip_all_tags( html_entity_decode( $cm_content) ), 0, 50 ) ); ?></span>
					</div>
				<?php endif; ?>

				<div class="dpr-discu-text">
					<div class="dpr-discu-comment-content">
							<?php
							// die();
								echo wp_kses(html_entity_decode( trim($comment->comment_content) ), [
									'a' => array('href' => [] , 'target' => [], 'title' => [], 'rel' => []),
									'pre' => array('class'=>array()),
									'h2' => array(),
									'h3' => array(),
									'h4' => array(),
									'h5' => array(),
									'h6' => array(),
									'ul' => array(),
									'ol' => array(),
									'li' => array(),
									'p' => array('class'=>array()),
									'br' => array(),
									'code' => array(),
								]);
							?>
					</div>
				</div>
				<?php if( $child_item != $depth ): ?>
					<div class="dpr-discu-box-footer">
				<?php else: ?>
					<div class="dpr-discu-box-footer last-item">
				<?php endif; ?>
					<div class="dpr-discu-metadata">
						<?php if( $settings['vote_enable'] == 'on' ):
							$voted['like'] = get_comment_meta( $comment->comment_ID, 'like_count', true );
							$voted['dislike'] = get_comment_meta( $comment->comment_ID, 'dislike_count', true );

							$voted['like'] = ( $voted['like'] != '' ) ? $voted['like'] : 0 ;
							$voted['dislike'] = ( $voted['dislike'] != '' ) ? $voted['dislike'] : 0 ;
						?>
							<div class="dpr-discu-box-footer-metadata-like" data-id="<?php esc_attr_e( $comment->comment_ID ); ?>">
								<span class="dpr-cont-discu-like">
									<a href="#" class="dpr-discu-like dpr-tooltip" data-wntooltip="<?php esc_attr_e( "Like", "depc" ); ?>">
										<i class="sl-like"></i>
										<span id="dpr-discu-like-count" class="dpr-discu-like-count"><?php echo esc_attr_e( $voted['like'] ); ?></span>
									</a>
								</span>
								<span class="dpr-cont-discu-dislike">
									<a href="#" class="dpr-discu-dislike dpr-tooltip" data-wntooltip="<?php echo esc_attr_e( "DisLike", "depc" ); ?>">
										<i class="sl-dislike"></i>
										<span id="dpr-discu-dislike-count" class="dpr-discu-dislike-count"><?php esc_attr_e( $voted['dislike'] ); ?></span>
									</a>
								</span>
							</div>
						<?php endif; ?>

						<div class="dpr-discu-metadata-share-wrap">
						<?php $scoial_cnt = get_comment_meta( $comment->comment_ID , 'dpr_social', true ); ?>
							<span class="dpr-discu-metadata-share">
								<span href="#" class="dpr-discu-share">
									<i class="sl-share"></i>
									<span class="dpr-discu-share-count dpr-discu-share-count-<?php esc_attr_e( $comment->comment_ID ); ?>" data-id="<?php esc_attr_e( $comment->comment_ID ); ?>"><?php  isset( $scoial_cnt['shared_count'] ) ? esc_attr_e( $scoial_cnt['shared_count'] ) : esc_attr_e( '0' ) ; ?></span>
								</span>
							</span>
							<?php if( $settings['social_enable'] == 'on' ): ?>
								<ul class="dpr-discu-sharing">

									<?php if( $settings['social_share_fb'] == 'on' ): ?>
										<li class="dpr-discu-social-icon">
											<!-- <a class="facebook dpr-tooltip" href="" onclick="javascript:window.open(this.href, 'MsgWindow' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600'); return false;" data-wntooltip="<?php esc_html_e("Share on Facebook", "depc"); ?>" data-id="<?php esc_attr_e( $comment->comment_ID );?>" >
												<i class="sl-social-facebook"></i>
											</a> -->
											<a class="facebook dpr-tooltip" data-link="<?php echo get_comments_link( $id ) . '-' . $comment->comment_ID; ?>"  href="" onclick="" data-wntooltip="<?php esc_html_e("Share on Facebook", "depc"); ?>" data-id="<?php esc_attr_e( $comment->comment_ID );?>" >
												<i class="sl-social-facebook"></i>
											</a>
										</li>
									<?php endif; ?>
									<?php if( $settings['social_share_vk'] == 'on' ): ?>
										<li class="dpr-discu-social-icon">
											<a class="vk dpr-tooltip" data-title="<?php echo get_the_title() ?>" data-link="<?php echo get_comments_link( $id ) . '-' . $comment->comment_ID; ?>"  href="" onclick="" data-wntooltip="<?php esc_html_e("Share on VK", "depc"); ?>" data-id="<?php esc_attr_e( $comment->comment_ID );?>" >
												<i class="sl-social-vk"></i>
											</a>
										</li>
									<?php endif; ?>
									<?php if( $settings['social_share_tumblr'] == 'on' ): ?>
										<li class="dpr-discu-social-icon">
											<a class="tumblr dpr-tooltip" data-title="<?php echo get_the_title() ?>" data-link="<?php echo get_comments_link( $id ) . '-' . $comment->comment_ID; ?>"  href="" onclick="" data-wntooltip="<?php esc_html_e("Share on Tumblr", "depc"); ?>" data-id="<?php esc_attr_e( $comment->comment_ID );?>" >
												<i class="sl-social-tumblr"></i>
											</a>
										</li>
									<?php endif; ?>
									<?php if( $settings['social_share_pinterest'] == 'on' ): ?>
										<li class="dpr-discu-social-icon">
											<a class="pinterest dpr-tooltip" data-title="<?php echo get_the_title() ?>" data-link="<?php echo get_comments_link( $id ) . '-' . $comment->comment_ID; ?>"  href="" onclick="" data-wntooltip="<?php esc_html_e("Share on Pinterest", "depc"); ?>" data-id="<?php esc_attr_e( $comment->comment_ID );?>" >
												<i class="sl-social-pinterest"></i>
											</a>
										</li>
									<?php endif; ?>
									<?php if( $settings['social_share_getpocket'] == 'on' ): ?>
										<li class="dpr-discu-social-icon">
											<a class="getpocket dpr-tooltip" data-title="<?php echo get_the_title() ?>" data-link="<?php echo get_comments_link( $id ) . '-' . $comment->comment_ID; ?>"  href="" onclick="" data-wntooltip="<?php esc_html_e("Share on Getpocket", "depc"); ?>" data-id="<?php esc_attr_e( $comment->comment_ID );?>" >
												<i class="sl-social-getpocket"></i>
											</a>
										</li>
									<?php endif; ?>
									<?php if( $settings['social_share_reddit'] == 'on' ): ?>
										<li class="dpr-discu-social-icon">
											<a class="reddit dpr-tooltip" data-title="<?php echo get_the_title() ?>" data-link="<?php echo get_comments_link( $id ) . '-' . $comment->comment_ID; ?>"  href="" onclick="" data-wntooltip="<?php esc_html_e("Share on Reddit", "depc"); ?>" data-id="<?php esc_attr_e( $comment->comment_ID );?>" >
												<i class="sl-social-reddit"></i>
											</a>
										</li>
									<?php endif; ?>
									<?php if( $settings['social_share_telegram'] == 'on' ): ?>
										<li class="dpr-discu-social-icon">
											<a class="telegram dpr-tooltip" data-title="<?php echo get_the_title() ?>" data-link="<?php echo get_comments_link( $id ) . '-' . $comment->comment_ID; ?>"  href="" onclick="" data-wntooltip="<?php esc_html_e("Share on Telegram", "depc"); ?>" data-id="<?php esc_attr_e( $comment->comment_ID );?>" >
												<i class="sl-social-telegram"></i>
											</a>
										</li>
									<?php endif; ?>

									<?php if( $settings['social_share_tw'] == 'on' ): ?>
										<li class="dpr-discu-social-icon">
											<a class="twitter dpr-tooltip" href="https://twitter.com/share?url=<?php echo urlencode( get_comments_link( $id ) . '-' . $comment->comment_ID ); ?>" onclick="javascript:window.open(this.href, 'MsgWindow' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600'); return false;" t data-wntooltip="<?php esc_html_e("Twitter", "depc"); ?>"  data-id="<?php esc_attr_e( $comment->comment_ID );?>">
												<i class="sl-social-twitter"></i>
											</a>
										</li>
									<?php endif; ?>

									<?php if( $settings['social_share_mail'] == 'on' ): ?>
										<li class="dpr-discu-social-icon">
											<a class="email dpr-tooltip" href="mailto:?subject=<?php echo urlencode(get_the_title()); ?>&body=<?php echo urlencode( get_comments_link( $id ) . '-' . $comment->comment_ID ); ?>"  data-wntooltip="<?php esc_html_e("Email", "depc"); ?>"  data-id="<?php esc_attr_e( $comment->comment_ID );?>">
												<i class="sl-envelope"></i>
											</a>
										</li>
									<?php endif; ?>

									<?php if( $settings['social_share_whatsapp'] == 'on' ): ?>
										<li class="dpr-discu-social-icon">
											<a class="whatsapp dpr-tooltip" href="whatsapp://send?text=<?php echo urlencode( get_comments_link( $id ) . '-' . $comment->comment_ID ); ?>"  data-wntooltip="<?php esc_html_e("WhatsApp", "depc"); ?>"  data-id="<?php esc_attr_e( $comment->comment_ID );?>">
												<i class="sl-whatsapp"></i>
											</a>
										</li>
									<?php endif; ?>

								</ul>
							<?php endif; ?>

						</div>

					</div>
					<div class="dpr-discu-replies-box">
						<div class="dpr-discu-reply-btn-wrap dpr-discu-reply-btn-main">
							<?php if( $child_item != $depth ): ?>
								<a href="#" class="dpr-discu-reply-btn" data-clicked="not" data-id="<?php esc_attr_e( $comment->comment_ID );?>"data-parent="<?php esc_attr_e( $comment->comment_ID );?>" ><i class="sl-action-redo"></i><?php esc_attr_e( 'Reply', 'depc' ); ?></a>
							<?php endif; ?>
							</div>
							<div class="dpr-discu-replies-wrap">
								<?php if( $child_item != $depth ): ?>
								<span class="dpr-tinymce-replies"></span>
								<div class="dpr-tinymce-button"></div>
								<?php endif; ?>

								<!-- child loop start from here -->
								<?php
								$parent = $comment->comment_ID;
								$child_comments = get_comments( array( 'number'=> 10,'order' => 'DESC','status' => 'approve','parent' => $parent ) );
								if ( $child_comments && $child_item < $depth){
									depc_comment_loop($child_comments, $child_customstyle, $settings, $inappropriate, $id, $validator, $comments_parrents, $child_item+1, $depth);
								}
								?>
							</div>

						</div>
					</div>
				</div>
			</div>

		</div>
	<?php endforeach; ?>
	<?php } ?>
	<?php depc_comment_loop($comments, $child_customstyle, $settings, $inappropriate, $id, $validator, $comments_parrents, 0, get_option('thread_comments_depth')); ?>
	<div class="dpr-insertafter"></div>
	<?php echo $load_more->load( $id, $type ); ?>
<?php else: ?>
	<h3><?php _e('No Comment.','depc'); ?></h3>
<?php endif; ?>

<script type="text/javascript">
	var dc_use_tinymce = '<?php echo \Depc_Core::get_option( 'dc_use_tinymce', 'Comments' ,'on');  ?>';
	var dc_use_emoji_tinymce = '<?php echo \Depc_Core::get_option( 'dc_use_emoji_tinymce', 'Comments' ,'on');  ?>';
	var dc_use_images = '<?php echo \Depc_Core::get_option( 'dc_use_image_tinymce', 'Comments' ,'on');  ?>';
</script>

<!-- End Comment loop Page layout
	================================================== -->
