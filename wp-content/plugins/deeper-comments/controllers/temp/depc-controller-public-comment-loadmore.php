<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */


/**
 * Load more front genrator
 */
class Depc_Controller_Public_Comment_Loadmore extends Depc_Controller_Public {


	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {
		// get validator class
		$this->validator = new Depc_Request_Validator;
		// get load more class
		$this->model = Depc_Model_Public_Comment_Loadmore::get_instance();
		// get user setting from admin panel
		$this->settings = $this->get_user_settings();

	}


	/**
	 * [scripts load triger script from model]
	 * @return [markup] [js]
	 */
	public function scripts() {

		$script = static::get_model()->scripts();
		return $script;
	}



	/**
	 * [load loadmore button markup and info about offset]
	 * @param  [int] $id [postid]
	 * @param  [int] $type [requested loadmore in which comment type]
	 * @return [html]     [markup]
	 */
	public function load( $id, $type ) {

		// get type of load more request from filter bar or first hit?
		$loaded_comment = ( $type === 'default' ) ? $this->settings['dc_defultcnt_comment'] : $this->settings['filter_count'] ;

		if ( $id == 0 ) {
			// get post id in ajax
			$url = wp_get_referer();
			$id = url_to_postid( $url );
		}

		// if load more is off return
		if ( $this->settings['loadmore']  == 'off' ) return;

		// get current post id
		if ( $type === 'default' ) {
			$comment_toload = (int) static::get_model()->parent_comments( $id ) - (int) $loaded_comment;
			$this->settings['default_sorting'] = $this->validator->get_comment_order( $this->settings['default_sorting'] );
		} else {
			$comment_toload = (int) static::get_model()->get_filter_count( $id ) - (int) $loaded_comment;
			$this->settings['default_sorting'] = $this->validator->get_comment_order( $type );
		}

		// if comment is not avalible to load return
		if ( 0 >= $comment_toload || wp_doing_ajax() ) return;
		$out = '
		<div class="dpr-loadmore-wrap">
			<a href="#" class="dpr-loadmore-btn" data-comments="'.$comment_toload.'" data-type="'.$this->settings['default_sorting'].'" data-loaded="'.$loaded_comment.'" >'.esc_attr__( 'Load More', 'depc' ).'</a>
		</div>
		<div class="dpr-preloadmore-wrap">
			<div class="dpr-preloadmore"></div>
		</div>
		';

		return $out;
	}

}