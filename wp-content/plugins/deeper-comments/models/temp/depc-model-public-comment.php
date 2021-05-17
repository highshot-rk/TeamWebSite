<?php


/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Model_Public_Comment extends Depc_Model_Public {


	public $ajax_action  = 'dpr_add_comment';
	private static $nounce  = 'dpr_add_comment';
	private $settings;
	protected $deeper_query;


	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {
		$this->register_hook_callbacks();
		$this->auto_delete_unapproved_comments();
	}

	public function register_hook_callbacks() {
		// get settings
		$this->settings = $this->take_settings();
		$this->user_setting = $this->get_user_settings();
	}

 	public function get_query() {
		$this->deeper_query = new WP_Comment_Query;
		return $this->deeper_query;
	}

	/**
	 * Show admin notices if plugin activation had any error
	 *
	 * @since    1.0.0
	 */
	public static function show_comment_template() {

		$array = array(
			'path' => '',
			'params' =>  self::scripts()
		);

		if ( is_user_logged_in() === true ) {
			$array['path'] = Depc_Core::get_depc_path() . '/views/tpl/logged-in-comment.php';
			return $array;

		}else {
			$array['path'] = Depc_Core::get_depc_path() . '/views/tpl/not-log-comment.php';
			return $array;
		}
	}

	public function auto_delete_unapproved_comments(){

		$args = array(
			'status' => 'hold',
		);

		$depc_query = new WP_Comment_Query;
		$comments = $depc_query->query( $args );

		foreach ($comments as $comment) {
			if ( strtotime( $comment->comment_date_gmt ) < ( time() - ( 60 * 60 * 24 * 30 ) ) ) {
				wp_delete_comment($comment->comment_ID , false);
			}
		}

	}
	public function CheckSpam(){

		global $wpdb;
		$ip = $this->get_the_user_ip();
		$last_comment = $wpdb->get_results( "SELECT * FROM $wpdb->comments where comment_author_ip='".$ip."' order by comment_date desc" );

		$last_comment = current($last_comment);

		$sensitivity = \Depc_Core::get_option( 'dc_spam_check_sensitivity', 'Comments' , 3 );
		if(strtotime($last_comment->comment_date) > time() - $sensitivity) {
			return false;
		}

		return true;
	}

	/**
     * Turn all URLs in clickable links.
     *
     * @param string $value
     * @param array  $protocols  http/https, ftp, mail, twitter
     * @param array  $attributes
     * @return string
     */
    private function linkify($value, $protocols = array('http', 'mail'), array $attributes = array())
    {
        // Link attributes
        $attr = '';
        foreach ($attributes as $key => $val) {
            $attr .= ' ' . $key . '="' . htmlentities($val) . '"';
        }

        $links = array();

        // Extract existing links and tags
        $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) { return '<' . array_push($links, $match[1]) . '>'; }, $value);

        // Extract text links for each protocol
        foreach ((array)$protocols as $protocol) {
            switch ($protocol) {
                case 'http':
                case 'https':   $value = preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { if ($match[1]) $protocol = $match[1]; $link = $match[2] ?: $match[3]; return '<' . array_push($links, "<a $attr href=\"$protocol://$link\">$link</a>") . '>'; }, $value); break;
                case 'mail':    $value = preg_replace_callback('~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~', function ($match) use (&$links, $attr) { return '<' . array_push($links, "<a $attr href=\"mailto:{$match[1]}\">{$match[1]}</a>") . '>'; }, $value); break;
                case 'twitter': $value = preg_replace_callback('~(?<!\w)[@#](\w++)~', function ($match) use (&$links, $attr) { return '<' . array_push($links, "<a $attr href=\"https://twitter.com/" . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1]  . "\">{$match[0]}</a>") . '>'; }, $value); break;
                default:        $value = preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { return '<' . array_push($links, "<a $attr href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>'; }, $value); break;
            }
        }

        // Insert all link
        return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) { return $links[$match[1] - 1]; }, $value);
	}

	public function render() {
		// -1 comment is empty
		check_ajax_referer( self::$nounce, 'security' );

		// if recaptcha is on and user is guest
		if ( $this->settings['recaptcha'] === 'google' && $this->settings['recaptchacm'] === 'on' && !is_user_logged_in() ) {
			$g_recaptcha_response = isset( $_POST['recaptcha'] ) ? $_POST['recaptcha'] : NULL;
			if ( $g_recaptcha_response === NULL ) wp_send_json( array( 'status' => -1 , 'data' => __( 'Please set recaptcha.', 'depc') ) );
			if( !$this->get_recaptcha_response( $g_recaptcha_response ) ) wp_send_json( array( 'status' => -1 , 'data' => __('Captcha is invalid! Please try again.', 'depc') ) );
		}

		if (get_option('comment_registration')) {
			if ( !is_user_logged_in() ) {
				wp_send_json( array( 'status' => -1 , 'data' => __( 'To post a comment, please login or register first!', 'depc') ) );
				return;
			}
		}

		if ( !is_user_logged_in() ) {
			$author = sanitize_text_field( $_POST['gname'] ) ;
			$usermail = sanitize_email( $_POST['gemail'] );
			$user_url = esc_url( $_POST['gwebsite'] );
			$user_id = 0 ;
		} else {
			// check for whos try to submit comments
			$current_user = parent::get_current_user();
			$author = $this->get_comment_author();
			$usermail = $current_user->user_email;
			$user_url = $current_user->user_url;
			$user_id = $current_user->ID;
		}

		if( \Depc_Core::get_option( 'dc_spam_check', 'Comments' , 'on' ) == 'on' ) {
			if ($this->CheckSpam() == false) {
				$sensitivity = \Depc_Core::get_option( 'dc_spam_check_sensitivity', 'Comments' , 3 );
				wp_send_json(  array( 'status' => -1 , 'data' => __( 'Posting comments is possible every ' . $sensitivity . ' seconds', 'depc') ) );
				return '';
			}
		}

		$validator = new Depc_Request_Validator;
		$comment_status = 'hold';

		// guest checking
		if ( $validator->is_guest_allowed() == 0 ) {
			_e('Please Log in for posting Comment.', 'depc' );
			wp_die();
		}

		 $_POST['comment_data'] = isset( $_POST['comment_data'] ) ?  trim($_POST['comment_data']) : '';
		// validation checking
		if ( $validator->terms_empty( $_POST['comment_data'] ) == -1 ) wp_send_json( array( 'status' => '-1' , 'data' => esc_attr__( 'Please write your comment.','depc' ) ) );

		// approve
		if ( $validator->may_i_approve() == 1 ) {
			$comment_status = 'approve';
		}

		// Comment Text
		// if( \Depc_Core::get_option( 'dc_convert_autolink', 'Comments' , 'on' ) == 'on' && strpos($_POST['comment_data'], '<a ') === false) {
		if( \Depc_Core::get_option( 'dc_convert_autolink', 'Comments' , 'on' ) == 'on') {
			$_POST['comment_data'] = $this->linkify($_POST['comment_data'], array('http', 'mail', 'https'), ['rel' => 'nofollow']);
		}
		// get time
		$time = current_time('mysql');

		// get comment id parent if exist
		$comment_id = false;
		if ( isset( $_POST['comment_id'] ) ) {
			$comment_id = Depc_Request_Validator::is_numeric( $_POST['comment_id'] , false );
		}

		$comment_id = ( $comment_id != false ) ? $comment_id : 0 ;
		$commentdata = array(
			'comment_post_ID' => sanitize_key( $_POST['post_id'] ),
			'comment_author' => $author,
			'comment_author_email' =>  $usermail,
			'comment_author_url' => $user_url,
			'comment_content' =>  wp_kses(wp_rel_nofollow( $_POST['comment_data'] ), [
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
				'a' => array('href' => [] , 'target' => [], 'title' => [], 'rel' => []),
			]),
			'comment_type' => '',
			'comment_parent' => $comment_id,
			'comment_date' => $time,
			'user_id' => $user_id,
		);

		$comment_id = wp_new_comment( $commentdata );

		// wp_set_comment_status( $comment_id, $comment_status );
		do_action('deeper_comments_new_comment', $comment_id, $commentdata);
		$publish_or_not = $validator::is_numeric( $comment_id , false );

		if( current_user_can('editor') || current_user_can('administrator') ) {
			wp_set_comment_status($comment_id, 'approve');
		}

		if (  $publish_or_not != false && $comment_id != -1 ) {
			$renderd_data = $this->maybe_publish_comment( $comment_id , $commentdata , $comment_status );
			wp_send_json( array( 'status' => 'publish' , 'data' => $renderd_data , 'newcomment_id' => $comment_id ) );
		} else {
			wp_send_json( array( 'status' => 'hold' , 'data' => $comment_id ) );
		}

	}

	private function get_the_user_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		$ip = $_SERVER['REMOTE_ADDR'];
		}
		return apply_filters( 'wpb_get_ip', $ip );
	}

	/**
	 * User script for comment inserting
	 *
	 * @since    1.0.0
	 */
	private static function scripts() {

		$id = new self();
		$id = $id->post_id();
		$logged_in = ( is_user_logged_in() ) ? 1 : 0;
		$settings = new self();
		$settings = $settings->settings;
		$nounce = wp_create_nonce( self::$nounce );

		// Generating javascript code tpl
		$javascript = '
		jQuery(document).ready(function() {
			jQuery(".dpr-discu-container_'.$id.' .dpr-join-form .dpr-discu-submit").depcSubmitComment({
				id: "'.$id.'",
				nounce : "'. $nounce .'",
				reply : false,
				selector : ".dpr-join-form .dpr-discu-submit",
				logged   : "'.$logged_in.'",
				captcha : "' .$settings['recaptchacm']. '"
			});
			jQuery(".dpr-discu-container_'.$id.' .dpr-discu-wrap .dpr-discu-box-footer .dpr-discu-reply-btn").depcSubmitComment({
				id: "'.$id.'",
				nounce : "'. $nounce .'",
				reply : true,
				selector : ".dpr-discu-wrap .dpr-discu-box-footer .dpr-discu-reply-btn",
				logged   : "'.$logged_in.'",
				captcha : "' .$settings['recaptchacm']. '"
			});
			jQuery(".dpr-discu-container_'.$id.' .dpr-discu-wrap .dpr-discu-replies-wrap .dpr-tinymce-button .dpr_add_reply_comment").depcSubmitComment({
				id: "'.$id.'",
				nounce : "'. $nounce .'",
				reply : 1,
				selector : ".dpr-discu-wrap .dpr-discu-replies-wrap .dpr-tinymce-button .dpr_add_reply_comment"
			});
			jQuery(".dpr-discu-container_'.$id.' .dpr-discu-main-loop-wrap .dpr-discu-wrap .dpr-discu-box .dpr-discu-link").depcHandyJs({
				id: "'.$id.'",
				nounce : "'. $nounce .'",
				mode : "copylink",
				selector : ".dpr-discu-wrap .dpr-discu-replies-wrap .dpr-tinymce-button .dpr_add_reply_comment"
			});
			jQuery(".dpr-discu-container_'.$id.' .dpr-discu-main-loop-wrap .dpr-discu-wrap .dpr-discu-box .facebook").depcHandyJs({
				id: "'.$id.'",
				nounce : "'. $nounce .'",
				mode : "facebook",
				selector : ".dpr-discu-main-loop-wrap .dpr-discu-wrap .dpr-discu-box .facebook"
			});
		});
		';

		return $javascript;

	}

	public function avatar( $mail , $type, $comment_id = false ) {

		$core = static::get_core();
		$is_genrate = $core::get_option( 'dc_generate_avatar','Avatar' );
		$out = $this->get_dp_avatar( $type , $comment_id, $mail ) . get_avatar( $mail, 50 , $core::get_option( 'gravatar_type', 'Avatar' , get_option('avatar_default','blank') ) ) ;
		return apply_filters('dpr_user_avatar', $out, $comment_id);

	}

	private function get_dp_avatar( $type, $comment_id = false, $comment_email = false ) {

		$text = $this->get_user_text( $type );
		$text = $this->make_avatar( $text, $comment_id, $comment_email );
		return $text;

	}


	private function make_avatar( $text, $comment_id = false, $comment_email = false ){

		$text = strtoupper( substr( $text , 0,1 ) );
		$color = $this->random_color( $comment_id, $comment_email = false );
		return '<div style="color:'.$color.';" data-dprletters="'.$text.'"></div>';

	}

	public function get_user_text( $type ) {

		if ( $type !== 'current' ) return $type;

		$current_user = parent::get_current_user();
		if ( $current_user->user_firstname && $current_user->user_lastname ) {

			return $current_user->user_firstname . ' ' . $current_user->user_lastname;

		} elseif ( $current_user->user_firstname ) {

			return $current_user->user_firstname;

		} elseif ( $current_user->user_lastname ) {

			return $current_user->user_lastname;

		} elseif ( $current_user->display_name ) {

			return $current_user->display_name;

		} else {

			return $current_user->user_login;

		}

	}

	private function get_comment_author() {

		if ( is_user_logged_in() == 'true' )
			return $this->get_user_text( 'current' );
		else
			return 'hii';

	}



	private static function renderPhpToString($file, $vars=null)
	{
	    if (is_array($vars) && !empty($vars)) {
	        extract($vars);
	    }
	    ob_start();
	    include $file;
	    return ob_get_clean();
	}


	public static function singleCommentView($comment_id){
		$loop = Depc_Controller_Public_Comment_Loop::get_comment_loop();
		$comment = get_comment($comment_id);
		$nonce = wp_create_nonce( 'dpr-social' );

		$data_array = array(
			'id'       		    => $comment_id,
			'comment'           => $comment,
			'settings'  	    => $loop->settings,
			'inappropriate'     => $loop->restriction(),
			'comments_parrents' => $loop->get_comment_parent(),
			'validator'			=> $loop->get_validator(),
			'nonce'  			=> $nonce,
		);

		return static::renderPhpToString(realpath(plugin_dir_path(__FILE__).'/../../views/tpl/newcomment.php'), $data_array);
		$comment_status = ( $comment_status == 'approve' ) ? 'dpr-discu-wrap' : 'dpr-discu-wrap dpr-discu-pending';
	}

	private function maybe_publish_comment( $comment_id, $commentdata , &$comment_status ) {

		return static::singleCommentView($comment_id);

	}

	public function get_user_profile_link() {

		$current_user = parent::get_current_user();
		$link = get_edit_user_link( $current_user->ID );
		return $link;
	}


	protected function random_color_part() {
		return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
	}

	protected function random_color( $comment_id = false , $comment_email = false ) {

		// genrate random color
		$random = '#' . $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
		$author = get_comment_author_email( $comment_id );

		if ($author) {
			$args = array(
				'author_email' => $author,
			);
			$comments = get_comments( $args );

			foreach ($comments as $comment) {
				$comment_meta = get_comment_meta( $comment->comment_ID, 'dpr_comment_color', true );
				if ($comment_meta) {

					return $comment_meta;
				}
			}
		}

		update_comment_meta( $comment_id , 'dpr_comment_color', $random );
		return $random;
	}

	public function remove_authenticate_hooks(){

		global $wp_filter;
		$wp_hooks = $wp_filter['authenticate']->callbacks ? $wp_filter['authenticate']->callbacks : false;

		if(is_array($wp_hooks)){

			foreach($wp_hooks as $priority => $hooks){

				foreach($hooks as $hook_id => $hook_details){

					if(0 !== strpos($hook_id,'wp_')){

						unset($wp_filter['authenticate']->callbacks[$priority][$hook_id]);
					}
				}
			}
		}
	}

	public static function authenticate( $user, $username, $password ) {

		$settings = get_option('Recaptcha',array());
		
		if ( empty( $settings ) || 'google' !== $settings['dc_recaptcha_type'] ) {
			
			return $user;
		}

		$error = new WP_Error( 'authentication failed', __( 'Authentication failed.', 'depc' ) );

		if ( ! isset( $_POST['g-recaptcha-response'] ) || empty( $_POST['g-recaptcha-response'])) {

			return $error;
		}

		$ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP );

		if(!$ip){

			return $error;
		}

		$url = "https://www.google.com/recaptcha/api/siteverify";
		$args = array(
			'body' => array(
				'secret' => $settings['dc_recptcha_gsecretkey'],
				'response' => isset( $_POST["g-recaptcha-response"] ) ? stripslashes( sanitize_text_field( $_POST["g-recaptcha-response"] ) ) : '',
				'remoteip' => $ip
			),
			'sslverify' => false
		);

		$r = wp_remote_post( $url, $args );
		$response = json_decode( wp_remote_retrieve_body( $r ), true );
		$success = isset( $response['success'] ) ? (bool) $response['success'] : false;
		if(!$success){
			
			return $error;
		}

		return $user;
	}

	public function init_before_signon(){

		$this->remove_authenticate_hooks();
		add_filter( 'authenticate', __CLASS__.'::authenticate', 21, 3 );
	}

}