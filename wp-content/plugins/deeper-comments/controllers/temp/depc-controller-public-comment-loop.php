<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Public_Comment_Loop extends Depc_Controller_Public_Comment {

	private static $comment_loop;
	public $settings;
	public $logged_in;
	public $current_user;

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {
		// get instance from comment loop model
		$this->model = Depc_Model_Public_Comment_Loop::get_instance();
		// get delete options
		$this->logged_in = is_user_logged_in();
		// gey current user
		$this->current_user = ( $this->logged_in === true ) ? wp_get_current_user() : false;

		$this->settings['delete'] 		= ( $this->logged_in === true ) ? Depc_Core::get_option( 'dc_delete_member', 'Comments', 'on' ) : 'off' ;

		// get edite options
		$this->settings['edit']  		= ( $this->logged_in === true ) ? Depc_Core::get_option( 'dc_edit_member', 'Comments', 'on' ) :  'off';

		// get user flag options
		$this->settings['inappropriate_member'] = Depc_Core::get_option( 'dc_inappropriate_members', 'Inappropriate_Comments', 'on' );
		$this->settings['inappropriate_guest']  = Depc_Core::get_option( 'dc_inappropriate_guest', 'Inappropriate_Comments', 'on');

		// get link options
		$this->settings['link']  		=  ( $this->logged_in === true ) ? Depc_Core::get_option( 'dc_link_member', 'Comments', 'on' ) : Depc_Core::get_option( 'dc_link_guest', 'Comments' );

		// get link options
		$this->settings['follow']  		=  ( $this->logged_in === true ) ? Depc_Core::get_option( 'dc_follow_member', 'Comments' , 'on' ) : Depc_Core::get_option( 'dc_link_guest', 'Comments' );

		// get collapse options
		$this->settings['collapse']  	= ( $this->logged_in === true ) ? Depc_Core::get_option( 'dc_collapse_member', 'Appearances' ) : Depc_Core::get_option( 'dc_collapse_guest', 'Appearances' );

		//get vote options
		$this->settings['vote_enable'] 	= ( $this->logged_in === true ) ? Depc_Core::get_option( 'dc_vote_user_enable', 'Voting' , 'on' ) : Depc_Core::get_option( 'dc_vote_guest_enable', 'Voting' , 'on') ;

		// get collapse options
		$this->settings['social_enable']   		= Depc_Core::get_option( 'dc_social_enable', 'Social_Share' );
		$this->settings['social_share_fb']   	= Depc_Core::get_option( 'dc_social_share_fb', 'Social_Share' );
		$this->settings['social_share_vk']   	= Depc_Core::get_option( 'dc_social_share_vk', 'Social_Share' );
		$this->settings['social_share_tumblr']  = Depc_Core::get_option( 'dc_social_share_tumblr', 'Social_Share' );
		$this->settings['social_share_pinterest']  = Depc_Core::get_option( 'dc_social_share_pinterest', 'Social_Share' );
		$this->settings['social_share_getpocket']  = Depc_Core::get_option( 'dc_social_share_getpocket', 'Social_Share' );
		$this->settings['social_share_reddit']  = Depc_Core::get_option( 'dc_social_share_reddit', 'Social_Share' );
		$this->settings['social_share_whatsapp']  = Depc_Core::get_option( 'dc_social_share_whatsapp', 'Social_Share' );
		$this->settings['social_share_telegram']  = Depc_Core::get_option( 'dc_social_share_telegram', 'Social_Share' );
		$this->settings['social_share_tw']   	= Depc_Core::get_option( 'dc_social_share_tw', 'Social_Share' );
		$this->settings['social_share_mail']   	= Depc_Core::get_option( 'dc_social_share_mail', 'Social_Share' );

 	}

 	public static function get_comment_loop() {

 		if ( null === self::$comment_loop ) {
 			self::$comment_loop = new self();
 		}
 		return self::$comment_loop;

	}


	public function get_loadmore() {
		$loadmore = Depc_Controller_Public_Comment_Loadmore::get_instance();
		return $loadmore;
	}


 	public function restriction() {

 		// check if show for members
 		if ( $this->logged_in ) {

 			if ( $this->settings['inappropriate_member'] == 'on' ){
 				$render =
 				'
 				<span> | </span>
 				<a href="#" class="dpr-discu-flag dpr-tooltip" data-wntooltip=" '. esc_attr__( "Flag as inappropriate", "depc" ).' ">
 					<i class="sl-flag"></i>
 				</a>';
 				return $render;
 			} else {
 				return false;
 			}

 		} elseif ( !$this->logged_in ) {

 			if ( $this->settings['inappropriate_guest'] == 'on' ) {
 				$render =
	 				'
	 				<span> | </span>
	 				<a href="#" class="dpr-discu-flag dpr-tooltip" data-wntooltip=" '. esc_attr__( "Flag as inappropriate", "depc" ).' ">
		 				<i class="sl-flag"></i>
		 			</a>';
	 			return $render;
 			} else{
 				return false;
 			}
 		}

 	}

 	public function get_validator()
 	{
 		return @static::get_model()->get_validator();
 	}

 	public function get_comment_parent()
 	{
 		return @static::get_model()->get_comment_parent();
 	}



	public function load( $run = false , $post_id = null, $type = false, $count = null ) {

		// get current post id
		$id = new self();
		$id = $id->post_id();

		$comment = static::get_model()->get_comment();
		$comment_parent = static::get_model()->get_comment_parent();
		$validator = static::get_model()->get_validator();

		$nonce = wp_create_nonce( 'dpr-social' );


		if ( $run !== false ) {
			$id = $post_id;
			$comment = $run;
			return static::render_template(
				'tpl/loop.php',
				array(
					'id'       		    => $id,
					'comments'          => $comment,
					'settings'  	    => $this->settings,
					'inappropriate'     => $this->restriction(),
					'comments_parrents' => $comment_parent,
					'validator' 		=> $validator,
					'nonce'  			=> $nonce,
					'type'				=> $type,
					'load_more'  		=> $this->get_loadmore()
				),
				'always'
			);
		}


		echo static::render_template(
			'tpl/loop.php',
			array(
				'id'       		    => $id,
				'comments'          => $comment,
				'settings'  	    => $this->settings,
				'inappropriate'     => $this->restriction(),
				'comments_parrents' => $comment_parent,
				'validator' 		=> $validator,
				'nonce'  			=> $nonce,
				'type'				=> 'default',
				'load_more'  		=> $this->get_loadmore()
				),
			'always'
		);
	}

}