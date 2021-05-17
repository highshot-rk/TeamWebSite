<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Follow_Conversion{

    private static $instances = array();

	/**
	 * Provides access to a single instance of a module using the singleton pattern
	 *
	 * @since    1.0.0
	 */
	public static function get_instance() {
		$classname = get_called_class();

		if ( ! isset( self::$instances[ $classname ] ) ) {
			self::$instances[ $classname ] = new $classname();
		}
		return self::$instances[ $classname ];

	}

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {
		$this->set_actions();
    }

    /**
	 * Set WP Hooks (Actions | Filters)
	 *
	 * @since    1.0.0
	 */
	protected function set_actions() {
		add_action('deeper_comments_render_conversion_following_html', [$this, 'render'], 10, 2);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 10);
		add_action('wp_ajax_dpr_follow_comment', [$this, 'follow']);
		add_action('wp_ajax_dpr_unfollow_comment', [$this, 'unfollow']);
		add_action('deeper_comments_new_comment', [$this, 'send_notification'], 5, 2);
	}



	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script(
			Depc_Core::DEPC_ID . '_follow_conversion',
			Depc_Core::get_depc_url() . 'views/js/package/follow-conversion.js',
			array( 'jquery' ),
			Depc_Core::DEPC_VERSION,
			false
		);
	}

    /**
	 * un-Follow The Conversion
	 *
	 * @since    1.0.0
	 */
    public function unfollow () {
        if( !wp_doing_ajax() || !is_user_logged_in()) {
            return;
		}
		$comment_id = esc_attr($_POST['comment_id']);
		if(!$comment_id) {
			return;
		}

		$user_id = get_current_user_id();
		if(get_user_meta($user_id, $comment_id, true)) {
			delete_user_meta($user_id, $comment_id);
			delete_user_meta($user_id, 'dpr_followed_time'. $comment_id);
			echo '<a href="#" data-comment-id="'. $comment_id .'" class="dpr-discu-follow dpr-tooltip" data-wntooltip="'. esc_attr__( 'Follow this conversion', 'depc' ) .'"><i class="sl-feed"></i></a>';
		} else {
			echo '<a href="#" data-comment-id="'. $comment_id .'" class="dpr-discu-unfollow dpr-tooltip" data-wntooltip="'. esc_attr__( 'Un-follow this conversion', 'depc' ) .'"><i class="sl-feed following"></i></a>';
		}
		wp_die();
	}

    /**
	 * Follow The Conversion
	 *
	 * @since    1.0.0
	 */
    public function follow () {
        if( !wp_doing_ajax() || !is_user_logged_in()) {
            return;
		}
		$comment_id = esc_attr($_POST['comment_id']);
		if(!$comment_id) {
			return;
		}

		$user_id = get_current_user_id();
		if(get_user_meta($user_id, $comment_id, true)) {
			echo '<a href="#" data-comment-id="'. $comment_id .'" class="dpr-discu-follow dpr-tooltip" data-wntooltip="'. esc_attr__( 'Follow this conversion', 'depc' ) .'"><i class="sl-feed"></i></a>';
		} else {
			update_user_meta($user_id, $comment_id, 'dpr_followed');
			update_user_meta($user_id, 'dpr_followed_time'. $comment_id, time());
			echo '<a href="#" data-comment-id="'. $comment_id .'" class="dpr-discu-unfollow dpr-tooltip" data-wntooltip="'. esc_attr__( 'Un-follow this conversion', 'depc' ) .'"><i class="sl-feed following"></i></a>';
		}
		wp_die();
	}

    /**
	 * Render The Follow/Un-follow Content
	 *
	 * @since    1.0.0
	 */
    public function render ($comment_id, $comment) {
        if(!is_user_logged_in()) {
            return;
        }
		$user_id = get_current_user_id();
		if(get_user_meta($user_id, $comment_id, true)) {
			?>
				<a href="#" data-comment-id="<?php echo $comment_id ?>" class="dpr-discu-unfollow dpr-tooltip" data-wntooltip="<?php echo esc_attr__( 'Un-follow this conversion', 'depc' ); ?>">
					<i class="sl-feed following"></i>
				</a>
			<?php
		} else {
			?>
				<a href="#" data-comment-id="<?php echo $comment_id ?>" class="dpr-discu-follow dpr-tooltip" data-wntooltip="<?php echo esc_attr__( 'Follow this conversion', 'depc' ); ?>">
					<i class="sl-feed"></i>
				</a>
			<?php
		}
	}

	/**
	 * Send notification for followed authors
	 *
	 * @since    1.0.0
	 */
    public function send_notification ($comment_id, $comment) {
		if(\Depc_Core::get_option( 'dc_follow_member', 'Comments' , 'on' ) != 'on') {
            return;
		}
		if(\Depc_Core::get_option( 'dc_enable_user_notifications_email', 'Notifications' , 'off' ) != 'on') {
            return;
		}

		$followers = get_users([
			'meta_key' => $comment['comment_parent']
		]);


		$recipients = [];
		foreach ($followers as $follower) {
			$last_seen = get_user_meta($follower->ID, 'dpr_last_seen', true);
			if($last_seen > time() - 90) {
				continue;
			}

			$recipients[] = $follower->user_email;
		}

		if(!$recipients) {
			return;
		}

		$subject = \Depc_Core::get_option( 'dc_follow_notifications_author_subject', 'Notifications' , 'Your comment on %n' );
		$body = \Depc_Core::get_option( 'dc_follow_notifications_author_email_body', 'Notifications' , 'Dear %a,<br>your followed comment %e<br>Read your comment on <a href="%l">%l</a> .<br>Best,<br><a href="%s">%n</a>' );
		$headers = array('Content-Type: text/html; charset=UTF-8');

		$meta = [
			'%comment_url' => get_permalink( sanitize_key( $comment['comment_post_ID'] ) ) . '#comments' . '-' . $comment_id,
			'%site_address' => get_option('siteurl'),
			'%site_name' => get_option('blogname'),
			'%author_name' => isset($comment['comment_author']) && $comment['comment_author'] ? $comment['comment_author'] : get_the_author_meta('display_name', $comment['comment_author']),
			'%author_email' => $comment['comment_author_email'],
			'%author_url' => $comment['comment_author_url'],
			'%post_title' => get_the_title($comment['comment_post_ID']),
			'%comment_content' => $comment['comment_content'],
		];
		$subject = str_replace(['%comment_url', '%site_address', '%site_name', '%author_name', '%author_email', '%author_url', '%post_title', '%comment_content'], $meta, $subject);
		$body = str_replace(['%comment_url', '%site_address', '%site_name', '%author_name', '%author_email', '%author_url', '%post_title', '%comment_content'], $meta, $body);
		wp_mail( $recipients, $subject, $body, $headers );
	}
}