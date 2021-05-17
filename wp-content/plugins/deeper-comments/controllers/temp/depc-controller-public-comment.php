<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Public_Comment extends Depc_Controller_Public {

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {

		$this->model = Depc_Model_Public_Comment::get_instance();
		// get user setting
		$this->user_setting = $this->get_user_settings();

 	}

	/**
	 * Register callbacks for actions and filters
	 *
	 * @since    1.0.0
	 */
	public function register_hook_callbacks(){}

 	public static function get_comment() {

 		if ( null === self::$comment_loop ) {
 			self::$comment_loop = new self();
 		}
 		return self::$comment_loop;

	}

	public function scripts() {

		$getpath = static::get_model()->show_comment_template();
		return $getpath['params'];
	}

	/**
	 * Return avatar
	 *
	 * @since    1.0.0
	 */
	public function avatar(){

		// get user email
		$current_user = wp_get_current_user();

		$getpath = static::get_model()->avatar( $current_user->user_email , 'current' );
		return $getpath;

	}

	/**
	 * Return user name
	 *
	 * @since    1.0.0
	 */
	public function username(){

		$getpath = static::get_model()->get_user_text( 'current' );
		return $getpath;

	}

	public function get_edit() {
		$edit = Depc_Controller_Public_Comment_Edit::get_instance();
		return $edit;
	}

	public function get_filter() {
		$filter = Depc_Controller_Public_Comment_Filter::get_instance();
		return $filter;
	}

	public function get_most_recent_authors() {
		$Most_Recent_Authors = Depc_Controller_Public_Comment_MRA::get_instance();
		return $Most_Recent_Authors;
	}


	/**
	 * [get_skins get skins in frontend]
	 * @param  [string] $skin [admin option]
	 * @return [string]       [css class]
	 */
	public function get_skins( $skin ) {

		switch ( $skin ) {
			case 'default':
				$skin = 'dpr-wrap';
				break;
			case 'template1':
				$skin = 'dpr-wrap dpr-template-n2';
				break;
			case 'template2':
				$skin = 'dpr-wrap dpr-template-n2 dpr-template-n3';
				break;
			case 'template3':
				$skin = 'dpr-wrap dpr-wrap dpr-template-n2 dpr-template-n3 dpr-template-n4';
				break;
			default:
				$skin = 'dpr-wrap';
				break;
		}

		return $skin;
	}

	/**
	 * Load Comment Templates
	 *
	 * @since    1.0.0
	 */
	public function load(){
		// get current post id
		$id = new self();
		$id = $id->post_id();
		// get user name
		$username = self::username();
		//get avatar with admin settings
		$avatar = self::avatar();
		// get comment template according to users
		$getpath = static::get_model()->show_comment_template();
		$getpath['path'] = basename( $getpath['path'] );
		// get user profile link
		$user_profile_link = static::get_model()->get_user_profile_link();
		//get skin
		$skin = $this->get_skins( $this->user_setting['skins'] );

		echo static::render_template(
			'tpl/' . $getpath['path'],
			array(
				'post'       	 		=> $id,
				'username'       		=> $username,
				'avatar'       	 		=> $avatar,
				'user_profile_link'		=> $user_profile_link,
				'edit'  				=> $this->get_edit(),
				'most_recent_authors'	=> $this->get_most_recent_authors(),
				'filter'  				=> $this->get_filter(),
				'recaptcha'  			=> $this->user_setting['recaptcha'],
				'recptcha_gsitekey'  	=> $this->user_setting['recptcha_gsitekey'],
				'captcha_theme'  		=> $this->user_setting['captcha_theme'],
				'captcha_size'  		=> $this->user_setting['captcha_size'],
				'skin'  				=> $skin
			),
			'always'
			);
	}
}