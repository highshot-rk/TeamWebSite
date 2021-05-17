<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Online_Users{

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
        if(\Depc_Core::get_option( 'dc_show_online_status', 'Appearances' , 'off' ) == 'on' ) {
            add_action('wp_loaded', [$this, 'handle'], 10);
            add_action('dpr-discu-user-name', [$this, 'get_online_status'], 10, 1);
        }
    }

    /**
	 * Handle Online Users
	 *
	 * @since    1.0.0
	 */
    public function handle () {
        $user = wp_get_current_user();
        if($user->ID === 0) {
            return;
        }
        update_user_meta($user->ID, 'dpr_last_seen', time());
    }

    /**
	 * Show Users Online Status
	 *
	 * @since    1.0.0
	 */
    public function get_online_status ($comment_ID) {
        $comment = get_comment( $comment_ID );
        if(!$comment->user_id) {
            return;
        }

        $last_seen = get_user_meta($comment->user_id, 'dpr_last_seen', true);
        if(!$last_seen) {
            $last_seen = 0;
        }

        if($last_seen > time() - 90) {
            echo '<span class="dpr-user-status online dpr-tooltip" data-wntooltip="'. __('Online', 'depc') .'"></span>';
        } else {
            echo '<span class="dpr-user-status offline dpr-tooltip" data-wntooltip="'. __('Offline', 'depc') .'"></span>';
        }
    }
}