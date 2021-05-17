<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Notification{

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
		add_action('deeper_comments_new_comment', [$this, 'send_notification'], 10, 2);
		if ( \Depc_Request_Validator::is_ajax() ) {
			add_action('wp_ajax_dpr_user_comments_listener', [$this, 'notification_handler'], 10);
		}
    }

    /**
	 * Notification Handler
	 *
	 * @since    1.0.0
	 */
    public function notification_handler () {
		if(\Depc_Core::get_option( 'dc_enable_user_notifications', 'Notifications' , 'on' ) != 'on') {
			return;
		}

		$user_id = get_current_user_id();

		if(!$user_id) {
			wp_die();
		}

		$user_meta_data = get_user_meta ( $user_id );
		$comments = $results = [];
		foreach ($user_meta_data as $meta => $data) {
			if($data[0] == 'dpr_followed') {
				$comments[] = $meta;
			}
		}

		foreach ($comments as $comment_id) {

			$child_comments = get_comments(array(
				'status'    => 'approve',
				'parent'    => $comment_id,
			));

			foreach ($child_comments as $child) {

				if(!get_comment_meta($child->comment_ID, 'dpr_seen_' . $user_id , true) &&
					get_user_meta($user_id, 'dpr_followed_time' . $child->comment_ID , true) < strtotime( $child->comment_date) &&
					$child->user_id != $user_id){

					update_comment_meta($child->comment_ID, 'dpr_seen_' . $user_id , time());
					$results['comments'][] = [
						'author'  => $child->comment_author,
						'date'  => $child->comment_date,
						'url'  => get_permalink( sanitize_key( $child->comment_post_ID ) ) . '#comments' . '-' . $child->comment_ID,
						'excerpt' => substr(trim(strip_tags($child->comment_content)), 0, 120),
					];
				}

			}

		}
		wp_send_json($results);
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