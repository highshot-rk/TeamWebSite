<?php
/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */
 $authors = [];
?>
<?php if(\Depc_Core::get_option( 'dc_show_most_recent_authors', 'Appearances' , 'on') == 'on'): ?>
<?php $comments = get_comments( array( 'post_id' => get_the_ID() , 'orderby' => 'date' , 'order' => "DESC" , 'status' => 'approve', 'number' => 5) ); ?>
	<?php if( $comments ): ?>
		<div class="dpr-most-recent-authors">
			<?php
			foreach ( $comments as $comment ) : ?>
				<?php if( ! in_array( $comment->comment_author_email , $authors ) ): ?>
					<?php $avatar = Depc_Model_Public_Comment::get_instance(); ?>
					<div class="dpr-most-recent-user dpr-tooltip" title="<?php echo $comment->comment_author ?>" data-wntooltip="<?php echo $comment->comment_author ?>">
						<div class="dpr-most-recent-user-img">
							<?php
								echo $avatar->avatar( $comment->comment_author_email , $comment->comment_author, $comment->comment_ID  );
							?>
						</div>
					</div>
					<?php $authors[] = $comment->comment_author_email; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<div class="clearfix"></div>
	<?php endif; ?>
<?php endif; ?>

<?php if(\Depc_Core::get_option( 'dc_show_comments_count', 'Comment_Sorting_Bar' ,'on') == 'on'): ?>
	<div class="dpr-comments-count">
		<?php echo wp_count_comments(get_the_ID())->approved; ?>
		<?php echo \Depc_Core::get_option( 'dc_show_comments_count_text', 'Comment_Sorting_Bar' ,'Comments') ?>
	</div>
<?php endif; ?>