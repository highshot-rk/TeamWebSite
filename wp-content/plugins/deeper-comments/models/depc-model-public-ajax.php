<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Model_Public_Ajax extends Depc_Model_Public {



	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {

		$this->register_hook_callbacks();

	}

	public function register_hook_callbacks() {

		$pcm = Depc_Model_Public_Comment::get_instance();
		$pcme = Depc_Model_Public_Comment_Edit::get_instance();
		$pcml = Depc_Model_Public_Comment_Loop::get_instance();
		$pcmv = Depc_Model_Public_Comment_Vote::get_instance();
		$pcmf = Depc_Model_Public_Comment_Filter::get_instance();
		$pcmlm = Depc_Model_Public_Comment_Loadmore::get_instance();
		$pcmwf = Depc_Model_Public_Comment_Word_Blacklist::get_instance();

		if ( Depc_Request_Validator::is_ajax() ) {

			// pcm
			Depc_Actions_Filters::add_action( "wp_ajax_$pcm->ajax_action", $pcm,  'render' );
			Depc_Actions_Filters::add_action( "wp_ajax_nopriv_$pcm->ajax_action",  $pcm, 'render' );

			//pcme
			Depc_Actions_Filters::add_action( "wp_ajax_$pcme->ajax_edit_action", $pcme, 'render_edit' );
			Depc_Actions_Filters::add_action( "wp_ajax_nopriv_$pcme->ajax_edit_action", $pcme, 'render_edit' );
			Depc_Actions_Filters::add_action( "wp_ajax_$pcme->ajax_delete_action", $pcme, 'render_delete' );
			Depc_Actions_Filters::add_action( "wp_ajax_nopriv_$pcme->ajax_delete_action", $pcme, 'render_delete' );

			//pcml
			Depc_Actions_Filters::add_action( "wp_ajax_$pcml->ajax_flag_action", $pcml, 'render_flag' );
			Depc_Actions_Filters::add_action( "wp_ajax_nopriv_$pcml->ajax_flag_action", $pcml, 'render_flag' );
			Depc_Actions_Filters::add_action( "wp_ajax_$pcml->ajax_social_action", $pcml, 'render_social' );
			Depc_Actions_Filters::add_action( "wp_ajax_nopriv_$pcml->ajax_social_action", $pcml, 'render_social' );

			//pcmv
			Depc_Actions_Filters::add_action( "wp_ajax_$pcmv->ajax_vote_action", $pcmv, 'render' );
			Depc_Actions_Filters::add_action( "wp_ajax_nopriv_$pcmv->ajax_vote_action", $pcmv, 'render' );

			//pcmf
			Depc_Actions_Filters::add_action( "wp_ajax_$pcmf->ajax_action", $pcmf, 'render' );
			Depc_Actions_Filters::add_action( "wp_ajax_nopriv_$pcmf->ajax_action", $pcmf, 'render' );

			//pcmlm
			Depc_Actions_Filters::add_action( "wp_ajax_$pcmlm->ajax_action", $pcmlm, 'render' );
			Depc_Actions_Filters::add_action( "wp_ajax_nopriv_$pcmlm->ajax_action", $pcmlm, 'render' );

		}

		// pcmwf
		Depc_Actions_Filters::add_action( "wp_insert_comment", $pcmwf, 'process',1000,2 );

	}

}