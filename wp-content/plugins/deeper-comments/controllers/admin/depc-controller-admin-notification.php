<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Admin_Notification extends Depc_Controller_Public_Comment{

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
		if(\Depc_Core::get_option( 'dc_enable_dashboard_notifications', 'Notifications' , 'on' ) == 'on' ) {
			add_action('deeper_comments_new_comment', [$this, 'send_notification'], 10, 2);
			add_action('admin_enqueue_scripts', [$this, 'enqueue_assets'], 10 );
			add_action('wp_ajax_dpr_comments_listener', [$this, 'last_notifications'], 10 );
		}
    }

    /**
	 * Enqueue Admin Styles And Scripts
	 *
	 * @since    1.0.0
	 */
    public function enqueue_assets () {

		wp_enqueue_script(
			\Depc_Core::DEPC_ID . '-admin-scripts',
			\Depc_Core::get_depc_url() . 'views/js/package/admin.js',
			array( 'jquery' ),
			Depc_Core::DEPC_VERSION,
			false
		);

	}

    /**
	 * Get Last Notifications
	 *
	 * @since    1.0.0
	 */
    public function last_notifications () {
		if(!current_user_can('administrator') ) {
			wp_send_json([]);
			wp_die();
		}
		$comments = get_comments([
			'date_query' => [
				'after'     => 'today',
				'inclusive' => true,
			],
			'meta_query' => [
				[
					'key' => 'dpr_seen',
					'compare' => 'NOT EXISTS'
				]
			]
		]);

		$result = [];
		if($comments) {
			$counter = 0;
			foreach ($comments as $ID => $comment) {
				update_comment_meta($comment->comment_ID, 'dpr_seen', time());
				$result['comments'][] = [
					'author'  => $comment->comment_author,
					'date'  => $comment->comment_date,
					'url'  => get_permalink( sanitize_key( $comment->comment_post_ID ) ) . '#comments' . '-' . $comment->comment_ID,
					'author_IP'  => $comment->comment_author_IP,
					'excerpt' => esc_html_e( substr( wp_strip_all_tags( html_entity_decode( $comment->comment_content) ), 0, 50 ) ),
				];

				$counter++;
				if($counter == 10) {
					$result['comment_count'] = count($comments) - 10;
					break;
				}
			}
		}
		wp_send_json($result);
		wp_die();
	}

    /**
	 * Send notification when a new comment is posted
	 *
	 * @since    1.0.0
	 */
    public function send_notification ($comment_id, $comment_data) {
		if( current_user_can('editor') || current_user_can('administrator') ) {
			return;
		}

		$recipients = '';
		if(\Depc_Core::get_option( 'dc_enable_notifications', 'Notifications' , 'on' ) == 'on') {
            $recipients = \Depc_Core::get_option( 'dc_notifications_admin_email', 'Notifications' , get_option('admin_email') );
		}

        if(\Depc_Core::get_option( 'dc_enable_notifications_for_author', 'Notifications' , 'on' ) == 'on' ) {
			if( \Depc_Core::get_option( 'dc_enable_notifications_for_author_after_approve', 'Notifications' , 'on' ) == 'on' ) {
				if( wp_get_comment_status( $comment_id ) == 'approved' ) {
					if($recipients) {
						$recipients .= ', ';
					}
					$recipients .= $comment_data['comment_author_email'];
				}
			} else {
				if($recipients) {
					$recipients .= ', ';
				}
				$recipients .= $comment_data['comment_author_email'];
			}
		}

		if($recipients) {
			$subject = \Depc_Core::get_option( 'dc_notifications_author_subject', 'Notifications' , 'Your comment on %site_name' );
			$body = \Depc_Core::get_option( 'dc_notifications_author_email_body', 'Notifications' , 'Dear %author_name,<br>Your comment has been successfully posted on %post_title with the email %author_email<br>Read your comment on <a href="%comment_url">%comment_url</a> .<br>Best,<br><a href="%site_address">%site_name</a>' );
			$headers = array('Content-Type: text/html; charset=UTF-8');

			$meta = [
				'%comment_url' => get_permalink( sanitize_key( $comment_data['comment_post_ID'] ) ) . '#comments' . '-' . $comment_id,
				'%site_address' => get_option('siteurl'),
				'%site_name' => get_option('blogname'),
				'%author_name' => isset($comment_data['comment_author']) && $comment_data['comment_author'] ? $comment_data['comment_author'] : get_the_author_meta('display_name', $comment_data['comment_author']),
				'%author_email' => $comment_data['comment_author_email'],
				'%author_url' => $comment_data['comment_author_url'],
				'%post_title' => get_the_title($comment_data['comment_post_ID']),
				'%comment_content' => $comment_data['comment_content'],
			];
			$subject = str_replace(['%comment_url', '%site_address', '%site_name', '%author_name', '%author_email', '%author_url', '%post_title', '%comment_content'], $meta, $subject);
			$body = str_replace(['%comment_url', '%site_address', '%site_name', '%author_name', '%author_email', '%author_url', '%post_title', '%comment_content'], $meta, $body);
			wp_mail( $recipients, $subject, $body, $headers );
		}
    }
}