<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

?>
<!-- Start comment Template
	================================================== -->
	<?php

	// Show comments for posts in specific categories
	if($categories = \Depc_Core::get_option( 'dc_categories_limitation', 'Limitation' , false )) {
		$return = true;
		foreach ( get_the_category() as $category) {
			if(array_key_exists($category->term_id, $categories)) {
				$return = false;
			}
		}

		if($return) {
			return;
		}
	}

	// Show comments for posts in specific posts types
	if($postTypes = \Depc_Core::get_option( 'dc_post_type_limitation', 'Limitation' , false )) {
		if(!array_key_exists(get_post_type(get_the_ID()), $postTypes)) {
			return;
		}
	}
	// Styling
	$styles = '';

	# Theme Color
	if($def_color = Depc_Core::get_option( 'dc_dpr_discu_default_color', 'Skin' )) {
		$styles .= '.dpr-join-form-login-register a.dpr-form-active,.dpr-join-form-login-register a:hover,.dpr-user-nas a:hover,.dpr-wrap input[type=submit],.dpr_add_reply_comment,.dpr_cancel_comment,.dpr_cancel_reply_comment,.dpr_edit_comment,.dpr-wrap a,.jconfirm.jconfirm-supervan .jconfirm-box .jconfirm-buttons button,.jconfirm .jconfirm-box .jconfirm-buttons button,.dpr-topic-author-box .dpr-author-name a,.dpr-profile-box-likebox i,.dpr-profile-box-linkbox,.dpr-loadmore-wrap a.dpr-loadmore-btn:hover,.dpr-profile-box-content-blogposts a.dpr-blog-category,.dpr-forum-box-header-title a:hover,.dpr-profile-box-header-title a:hover,.dpr-profile-box .dpr-profile-location i,.dpr-switch-tab a.dpr-active-tab,.dpr-switch-tab a:hover,.dpr-discu-box a:hover i,.dpr-discu-inreplyto a,.dpr-discu-user-name a,.dpr-topic-box a:hover i,.dpr-discu-inreplyto a,.dpr-discu-user-name,.dpr-discu-reply-btn-wrap a.dpr-discu-reply-btn:hover,.dpr-discu-metadata-share-wrap .dpr-discu-sharing li:hover a,.dpr-discu-timeline-toggle i:hover,.dpr-modal-footer a,.dpr-topic-box h3.dpr-topic-title a:hover,.dpr-topic-tags a:hover,.dpr-widget-tags-in a:hover,.dpr-widget-topics h6.dpr-topic-title a:hover,.dpr-profile-box1-socials a:hover i,.dpr-influence-level-a:hover{color:'. $def_color .';}';
		$styles .= '.dpr-join-form-login-register a.dpr-discu-submit,.dpr-discu-metadata-share-wrap:hover>li,.dpr-influence-level-a:hover span{background-color:'. $def_color .';}';
		$styles .= '.dpr-join-form-login-register a.dpr-discu-submit{box-shadow: 0 3px 14px -4px '. $def_color .';}';
		$styles .= '.dpr-discu-timeline-point{box-shadow: 0 0 0 0 '. $def_color .';}';
		$styles .= '.dpr-discu-timeline-point:hover{box-shadow: 0 0 7px 0 '. $def_color .';}';
		$styles .= '.dpr-loadmore-wrap a.dpr-loadmore-btn:hover,.dpr-influence-level-a:hover,.dpr-topic-tags a:hover,.dpr-widget-tags-in a:hover,.dpr-discu-timeline-point{border-color:'. $def_color .';}';
	}

	// .dpr-join-form-wrap
	$styles .= '.dpr-join-form-wrap {';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_join_form_wrap', 'Comment_Form' , 0 ) ?  'background-color:' . \Depc_Core::get_option( 'dpr_join_form_wrap', 'Comment_Form' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_join_form_wrap_border_color', 'Comment_Form' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_join_form_wrap_border_color', 'Comment_Form' ) . ';' : '';
	$styles .= '}';

	// .dpr-join-form-inner
	$styles .= '.dpr-join-form-inner{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_join_form_inner', 'Comment_Form' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_join_form_inner', 'Comment_Form' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_join_form_inner_border_color', 'Comment_Form' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_join_form_inner_border_color', 'Comment_Form' ) . ';' : '';
	$styles .= '}';

	// .dpr-join-form-area textarea
	$styles .= '.dpr-join-form-area textarea{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_join_form_area_textarea', 'Comment_Form' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_join_form_area_textarea', 'Comment_Form' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_join_form_area_textarea_border_color', 'Comment_Form' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_join_form_area_textarea_border_color', 'Comment_Form' ) . ';' : '';
		// Text Color
		$styles .= \Depc_Core::get_option( 'dpr_join_form_area_textarea_text_color', 'Comment_Form' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_join_form_area_textarea_text_color', 'Comment_Form' ) . ';' : '';
	$styles .= '}';

	// .dpr-join-form-login-register a, .dpr-user-nas a
	$styles .= '.dpr-join-form-login-register a, .dpr-user-nas a{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_join_form_wrap_buttons', 'Comment_Form' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_join_form_wrap_buttons', 'Comment_Form' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_join_form_wrap_buttons_border_color', 'Comment_Form' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_join_form_wrap_buttons_border_color', 'Comment_Form' ) . ';' : '';
		// Text Color
		$styles .= \Depc_Core::get_option( 'dpr_join_form_wrap_buttons_text_color', 'Comment_Form' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_join_form_wrap_buttons_text_color', 'Comment_Form' ) . ';' : '';
	$styles .= '}';

	// .dpr-join-form-login-register a.dpr-discu-submit
	$styles .= '.dpr-join-form-login-register a.dpr-discu-submit{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_join_form_wrap_submit', 'Comment_Form' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_join_form_wrap_submit', 'Comment_Form' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_join_form_wrap_submit_border_color', 'Comment_Form' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_join_form_wrap_submit_border_color', 'Comment_Form' ) . ';' : '';
		// Text Color
		$styles .= \Depc_Core::get_option( 'dpr_join_form_wrap_submit_text_color', 'Comment_Form' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_join_form_wrap_submit_text_color', 'Comment_Form' ) . ';' : '';
	$styles .= '}';

	// .dpr-comments-count
	$styles .= '.dpr-comments-count{';
		// Text Color
		$styles .= \Depc_Core::get_option( 'dpr_comments_count_text_color', 'Comment_Form' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_comments_count_text_color', 'Comment_Form' ) . ';' : '';
	$styles .= '}';


	// Comment Box
	// .dpr-discu-box
	$styles .= '.dpr-discu-metadata-share-wrap .dpr-discu-sharing,.dpr-discu-box{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_comment_box_background_color', 'Comment_Box' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_comment_box_background_color', 'Comment_Box' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_comment_box_border_color', 'Comment_Box' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_comment_box_border_color', 'Comment_Box' ) . ';' : '';
	$styles .= '}';
	// .dpr-discu-metadata-share-wrap .dpr-discu-sharing:before, .dpr-msgbox:before
	$styles .= '.dpr-discu-metadata-share-wrap .dpr-discu-sharing:before, .dpr-msgbox:before{';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_comment_box_border_color', 'Comment_Box' , 0 ) ?  'border-top-color:' . \Depc_Core::get_option( 'dpr_comment_box_border_color', 'Comment_Box' ) . ';' : '';
	$styles .= '}';
	// .dpr-discu-metadata-share-wrap .dpr-discu-sharing:after, .dpr-msgbox:after
	$styles .= '.dpr-discu-metadata-share-wrap .dpr-discu-sharing:after, .dpr-msgbox:after{';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_comment_box_background_color', 'Comment_Box' , 0 ) ?  'border-top-color:' . \Depc_Core::get_option( 'dpr_comment_box_background_color', 'Comment_Box' ) . ';' : '';
	$styles .= '}';
	// .dpr-discu-box:before
	$styles .= '.dpr-discu-box:before{';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_comment_box_border_color', 'Comment_Box' , 0 ) ?  'border-right-color:' . \Depc_Core::get_option( 'dpr_comment_box_border_color', 'Comment_Box' ) . ';' : '';
	$styles .= '}';
	// .dpr-discu-box:after
	$styles .= '.dpr-discu-box:after{';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_comment_box_background_color', 'Comment_Box' , 0 ) ?  'border-right-color:' . \Depc_Core::get_option( 'dpr_comment_box_background_color', 'Comment_Box' ) . ';' : '';
	$styles .= '}';

	// .dpr-discu-dislike-count, .dpr-discu-like-count, .dpr-discu-replies-count, .dpr-discu-share-count, .dpr-topic-last-reply-time, .dpr-topic-views-count,.dpr-discu-metadata
	$styles .= '.dpr-discu-dislike-count, .dpr-discu-like-count, .dpr-discu-replies-count, .dpr-discu-share-count, .dpr-topic-last-reply-time, .dpr-topic-views-count,.dpr-discu-metadata{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_comment_box_metadata_background_color', 'Comment_Box' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_comment_box_metadata_background_color', 'Comment_Box' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_comment_box_metadata_border_color', 'Comment_Box' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_comment_box_metadata_border_color', 'Comment_Box' ) . ';' : '';
	$styles .= '}';

	// .dpr-discu-box-footer
	$styles .= '.dpr-discu-box-footer{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_comment_box_footer_background_color', 'Comment_Box' , 0 ) ?  'background-color:' . \Depc_Core::get_option( 'dpr_comment_box_footer_background_color', 'Comment_Box' ) . ';' : '';
	$styles .= '}';

	// .dpr-discu-reply-btn-wrap a.dpr-discu-reply-btn i, .dpr-discu-reply-btn-wrap a.dpr-discu-reply-btn
	$styles .= '.dpr-discu-reply-btn-wrap a.dpr-discu-reply-btn i, .dpr-discu-reply-btn-wrap a.dpr-discu-reply-btn{';
		// Text Color
		$styles .= \Depc_Core::get_option( 'dpr_comment_box_reply_text_color', 'Comment_Box' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_comment_box_reply_text_color', 'Comment_Box' ) . ';' : '';
	$styles .= '}';

	// .dpr-discu-inreplyto a, .dpr-discu-user-name
	$styles .= '.dpr-discu-inreplyto a, .dpr-discu-user-name{';
		// Text Color
		$styles .= \Depc_Core::get_option( 'dpr_comment_box_author_name_text_color', 'Comment_Box' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_comment_box_author_name_text_color', 'Comment_Box' ) . ';' : '';
	$styles .= '}';

	// .dpr-discu-box i, .dpr-topic-box i
	$styles .= '.dpr-discu-box i, .dpr-topic-box i{';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_comment_box_icons_color', 'Comment_Box' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_comment_box_icons_color', 'Comment_Box' ) . ';' : '';
	$styles .= '}';

	// .dpr-discu-text p
	$styles .= '.dpr-discu-text p{';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_comment_box_text_color', 'Comment_Box' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_comment_box_text_color', 'Comment_Box' ) . ';' : '';
	$styles .= '}';


	// Replay
	// .dpr-discu-box
	$styles .= '.dpr-discu-replies-wrap .dpr-discu-sharing,.dpr-discu-replies-wrap .dpr-discu-box{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_replay_comment_box_background_color', 'Replay' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_replay_comment_box_background_color', 'Replay' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_replay_comment_box_border_color', 'Replay' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_replay_comment_box_border_color', 'Replay' ) . ';' : '';
	$styles .= '}';
	// .dpr-discu-sharing:before, .dpr-msgbox:before
	$styles .= '.dpr-discu-replies-wrap .dpr-discu-sharing:before, .dpr-discu-replies-wrap .dpr-msgbox:before{';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_replay_comment_box_border_color', 'Replay' , 0 ) ?  'border-top-color:' . \Depc_Core::get_option( 'dpr_replay_comment_box_border_color', 'Replay' ) . ';' : '';
	$styles .= '}';
	// .dpr-discu-sharing:after, .dpr-msgbox:after
	$styles .= '.dpr-discu-replies-wrap .dpr-discu-sharing:after, .dpr-discu-replies-wrap .dpr-msgbox:after{';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_replay_comment_box_background_color', 'Replay' , 0 ) ?  'border-top-color:' . \Depc_Core::get_option( 'dpr_replay_comment_box_background_color', 'Replay' ) . ';' : '';
	$styles .= '}';
	// .dpr-discu-box:before
	$styles .= '.dpr-discu-replies-wrap .dpr-discu-box:before{';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_replay_comment_box_border_color', 'Replay' , 0 ) ?  'border-right-color:' . \Depc_Core::get_option( 'dpr_replay_comment_box_border_color', 'Replay' ) . ';' : '';
	$styles .= '}';
	// .dpr-discu-box:after
	$styles .= '.dpr-discu-replies-wrap .dpr-discu-box:after{';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_replay_comment_box_background_color', 'Replay' , 0 ) ?  'border-right-color:' . \Depc_Core::get_option( 'dpr_replay_comment_box_background_color', 'Replay' ) . ';' : '';
	$styles .= '}';

	// .dpr-discu-dislike-count, .dpr-discu-like-count, .dpr-discu-replies-count, .dpr-discu-share-count, .dpr-topic-last-reply-time, .dpr-topic-views-count,.dpr-discu-metadata
	$styles .= '.dpr-discu-replies-wrap .dpr-discu-dislike-count, .dpr-discu-replies-wrap .dpr-discu-like-count, .dpr-discu-replies-wrap .dpr-discu-replies-count, .dpr-discu-replies-wrap .dpr-discu-share-count, .dpr-discu-replies-wrap .dpr-topic-last-reply-time, .dpr-discu-replies-wrap .dpr-topic-views-count, .dpr-discu-replies-wrap .dpr-discu-metadata{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_replay_comment_box_metadata_background_color', 'Replay' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_replay_comment_box_metadata_background_color', 'Replay' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_replay_comment_box_metadata_border_color', 'Replay' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_replay_comment_box_metadata_border_color', 'Replay' ) . ';' : '';
	$styles .= '}';

	// .dpr-discu-box-footer
	$styles .= '.dpr-discu-replies-wrap .dpr-discu-box-footer{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_replay_comment_box_footer_background_color', 'Replay' , 0 ) ?  'background-color:' . \Depc_Core::get_option( 'dpr_replay_comment_box_footer_background_color', 'Replay' ) . ';' : '';
	$styles .= '}';

	// .dpr-discu-reply-btn-wrap a.dpr-discu-reply-btn i, .dpr-discu-reply-btn-wrap a.dpr-discu-reply-btn
	$styles .= '.dpr-discu-replies-wrap .dpr-discu-reply-btn-wrap a.dpr-discu-reply-btn i, .dpr-discu-replies-wrap .dpr-discu-reply-btn-wrap a.dpr-discu-reply-btn{';
		// Text Color
		$styles .= \Depc_Core::get_option( 'dpr_replay_comment_box_reply_text_color', 'Replay' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_replay_comment_box_reply_text_color', 'Replay' ) . ';' : '';
	$styles .= '}';

	// .dpr-discu-inreplyto a, .dpr-discu-user-name
	$styles .= '.dpr-discu-replies-wrap .dpr-discu-inreplyto a, .dpr-discu-replies-wrap .dpr-discu-user-name{';
		// Text Color
		$styles .= \Depc_Core::get_option( 'dpr_replay_comment_box_author_name_text_color', 'Replay' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_replay_comment_box_author_name_text_color', 'Replay' ) . ';' : '';
	$styles .= '}';

	// .dpr-discu-box i, .dpr-topic-box i
	$styles .= '.dpr-discu-replies-wrap .dpr-discu-box i, .dpr-discu-replies-wrap .dpr-topic-box i{';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_replay_comment_box_icons_color', 'Replay' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_replay_comment_box_icons_color', 'Replay' ) . ';' : '';
	$styles .= '}';

	// .dpr-discu-text p
	$styles .= '.dpr-discu-replies-wrap .dpr-discu-text p{';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_replay_comment_box_text_color', 'Replay' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_replay_comment_box_text_color', 'Replay' ) . ';' : '';
	$styles .= '}';

	// Load More Button
	// .dpr-loadmore-wrap a.dpr-loadmore-btn
	$styles .= '.dpr-loadmore-wrap a.dpr-loadmore-btn{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_load_more_button_background_color', 'Load_More_Button' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_load_more_button_background_color', 'Load_More_Button' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_load_more_button_border_color', 'Load_More_Button' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_load_more_button_border_color', 'Load_More_Button' ) . ';' : '';
		// Text Color
		$styles .= \Depc_Core::get_option( 'dpr_load_more_button_text_color', 'Load_More_Button' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_load_more_button_text_color', 'Load_More_Button' ) . ';' : '';
		// Border Radius
		$styles .= \Depc_Core::get_option( 'dpr_load_more_button_border_radius', 'Load_More_Button' , 0 ) ?  'border-radius:' . \Depc_Core::get_option( 'dpr_load_more_button_border_radius', 'Load_More_Button' ) . 'px;' : '';
		// Padding
		$styles .= \Depc_Core::get_option( 'dpr_load_more_button_padding', 'Load_More_Button' , 0 ) ?  'padding:' . \Depc_Core::get_option( 'dpr_load_more_button_padding', 'Load_More_Button' ) . 'px;' : '';
		// Font Size
		$styles .= \Depc_Core::get_option( 'dpr_load_more_button_font_size', 'Load_More_Button' , 0 ) ?  'font-size:' . \Depc_Core::get_option( 'dpr_load_more_button_font_size', 'Load_More_Button' ) . 'px;' : '';
	$styles .= '}';

	// Author Avatar
	// .dpr-discu-user-img img
	$styles .= '.dpr-discu-user-img img{';
		// Border Style
		$styles .= \Depc_Core::get_option( 'dpr_author_avatar_border_style', 'Author_Avatar' , 0 ) ?  'border-style:' . \Depc_Core::get_option( 'dpr_author_avatar_border_style', 'Author_Avatar' ) . ';' : '';
		// Border Width
		$styles .= \Depc_Core::get_option( 'dpr_author_avatar_border_width', 'Author_Avatar' , 0 ) ?  'border-width:' . \Depc_Core::get_option( 'dpr_author_avatar_border_width', 'Author_Avatar' ) . 'px;' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_author_avatar_border_color', 'Author_Avatar' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_author_avatar_border_color', 'Author_Avatar' ) . ';' : '';
		// Border Radius
		$styles .= \Depc_Core::get_option( 'dpr_author_avatar_border_radius', 'Author_Avatar' , 0 ) ?  'border-radius:' . \Depc_Core::get_option( 'dpr_author_avatar_border_radius', 'Author_Avatar' ) . 'px;' : '';
		// Width
		$styles .= \Depc_Core::get_option( 'dpr_author_avatar_width', 'Author_Avatar' , 0 ) ?  'width:' . \Depc_Core::get_option( 'dpr_author_avatar_width', 'Author_Avatar' ) . 'px;' : '';
		// Height
		$styles .= \Depc_Core::get_option( 'dpr_author_avatar_height', 'Author_Avatar' , 0 ) ?  'height:' . \Depc_Core::get_option( 'dpr_author_avatar_height', 'Author_Avatar' ) . 'px;' : '';
		// Top
		$styles .= \Depc_Core::get_option( 'dpr_author_avatar_top', 'Author_Avatar' , 0 ) ?  'top:' . \Depc_Core::get_option( 'dpr_author_avatar_top', 'Author_Avatar' ) . 'px;' : '';
		// Right
		$styles .= \Depc_Core::get_option( 'dpr_author_avatar_right', 'Author_Avatar' , 0 ) ?  'right:' . \Depc_Core::get_option( 'dpr_author_avatar_right', 'Author_Avatar' ) . 'px;' : '';
		// Left
		$styles .= \Depc_Core::get_option( 'dpr_author_avatar_left', 'Author_Avatar' , 0 ) ?  'left:' . \Depc_Core::get_option( 'dpr_author_avatar_left', 'Author_Avatar' ) . 'px;' : '';
		// Bottom
		$styles .= \Depc_Core::get_option( 'dpr_author_avatar_bottom', 'Author_Avatar' , 0 ) ?  'bottom:' . \Depc_Core::get_option( 'dpr_author_avatar_bottom', 'Author_Avatar' ) . 'px;' : '';
	$styles .= '}';

	// Elements
	// span.dpr-user-status.online
	$styles .= 'span.dpr-user-status.online{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_online_status_background_color', 'Elements' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_online_status_background_color', 'Elements' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_online_status_border_color', 'Elements' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_online_status_border_color', 'Elements' ) . ';' : '';
		// Border Radius
		$styles .= \Depc_Core::get_option( 'dpr_online_status_border_radius', 'Elements' , 0 ) ?  'border-radius:' . \Depc_Core::get_option( 'dpr_online_status_border_radius', 'Elements' ) . 'px;' : '';
		// Width
		$styles .= \Depc_Core::get_option( 'dpr_online_status_width', 'Elements' , 0 ) ?  'width:' . \Depc_Core::get_option( 'dpr_online_status_width', 'Elements' ) . 'px;' : '';
		// Height
		$styles .= \Depc_Core::get_option( 'dpr_online_status_height', 'Elements' , 0 ) ?  'height:' . \Depc_Core::get_option( 'dpr_online_status_height', 'Elements' ) . 'px;' : '';
	$styles .= '}';
	// span.dpr-user-status.offline
	$styles .= 'span.dpr-user-status.offline{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_offline_status_background_color', 'Elements' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_offline_status_background_color', 'Elements' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_offline_status_border_color', 'Elements' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_offline_status_border_color', 'Elements' ) . ';' : '';
		// Border Radius
		$styles .= \Depc_Core::get_option( 'dpr_offline_status_border_radius', 'Elements' , 0 ) ?  'border-radius:' . \Depc_Core::get_option( 'dpr_offline_status_border_radius', 'Elements' ) . 'px;' : '';
		// Width
		$styles .= \Depc_Core::get_option( 'dpr_offline_status_width', 'Elements' , 0 ) ?  'width:' . \Depc_Core::get_option( 'dpr_offline_status_width', 'Elements' ) . 'px;' : '';
		// Height
		$styles .= \Depc_Core::get_option( 'dpr_offline_status_height', 'Elements' , 0 ) ?  'height:' . \Depc_Core::get_option( 'dpr_offline_status_height', 'Elements' ) . 'px;' : '';
	$styles .= '}';

	// Filter Bar
	// .dpr-switch-tab-wrap
	$styles .= '.dpr-switch-tab-wrap{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_background_color', 'Sorting_Bar' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_filter_bar_background_color', 'Sorting_Bar' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_border_color', 'Sorting_Bar' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_filter_bar_border_color', 'Sorting_Bar' ) . ';' : '';
		// Border Radius
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_border_radius', 'Sorting_Bar' , 0 ) ?  'border-radius:' . \Depc_Core::get_option( 'dpr_filter_bar_border_radius', 'Sorting_Bar' ) . 'px;' : '';
		// Width
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_width', 'Sorting_Bar' , 0 ) ?  'width:' . \Depc_Core::get_option( 'dpr_filter_bar_width', 'Sorting_Bar' ) . 'px;' : '';
		// Height
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_height', 'Sorting_Bar' , 0 ) ?  'height:' . \Depc_Core::get_option( 'dpr_filter_bar_height', 'Sorting_Bar' ) . 'px;' : '';
		// Margin Right
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_margin_right', 'Sorting_Bar' , 0 ) ?  'margin-right:' . \Depc_Core::get_option( 'dpr_filter_bar_margin_right', 'Sorting_Bar' ) . 'px;' : '';
		// Margin Left
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_margin_left', 'Sorting_Bar' , 0 ) ?  'margin-left:' . \Depc_Core::get_option( 'dpr_filter_bar_margin_left', 'Sorting_Bar' ) . 'px;' : '';
		// Margin Top
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_margin_top', 'Sorting_Bar' , 0 ) ?  'margin-top:' . \Depc_Core::get_option( 'dpr_filter_bar_margin_top', 'Sorting_Bar' ) . 'px;' : '';
		// Margin Bottom
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_margin_bottom', 'Sorting_Bar' , 0 ) ?  'margin-bottom:' . \Depc_Core::get_option( 'dpr_filter_bar_margin_bottom', 'Sorting_Bar' ) . 'px;' : '';
	$styles .= '}';
	// .dpr-switch-tab a
	$styles .= '.dpr-switch-tab a{';
		// Text Color
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_text_color', 'Sorting_Bar' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_filter_bar_text_color', 'Sorting_Bar' ) . ';' : '';
	$styles .= '}';
	// .dpr-switch-tab a.dpr-active-tab
	$styles .= '.dpr-switch-tab a.dpr-active-tab{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_active_tab_background_color', 'Sorting_Bar' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_filter_bar_active_tab_background_color', 'Sorting_Bar' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_active_tab_border_color', 'Sorting_Bar' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_filter_bar_active_tab_border_color', 'Sorting_Bar' ) . ';' : '';
		// Text Color
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_active_tab_text_color', 'Sorting_Bar' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_filter_bar_active_tab_text_color', 'Sorting_Bar' ) . ';' : '';
		// Border Radius
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_active_tab_border_radius', 'Sorting_Bar' , 0 ) ?  'border-radius:' . \Depc_Core::get_option( 'dpr_filter_bar_active_tab_border_radius', 'Sorting_Bar' ) . 'px;' : '';
		// Width
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_active_tab_width', 'Sorting_Bar' , 0 ) ?  'width:' . \Depc_Core::get_option( 'dpr_filter_bar_active_tab_width', 'Sorting_Bar' ) . 'px;' : '';
		// Height
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_active_tab_height', 'Sorting_Bar' , 0 ) ?  'height:' . \Depc_Core::get_option( 'dpr_filter_bar_active_tab_height', 'Sorting_Bar' ) . 'px;' : '';
	$styles .= '}';
	// .dpr-switch-search-wrap input[type=text]
	$styles .= '.dpr-switch-search-wrap input[type=text]{';
		// Background Color
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_search_background_color', 'Sorting_Bar' , 0 ) ?  'background:' . \Depc_Core::get_option( 'dpr_filter_bar_search_background_color', 'Sorting_Bar' ) . ';' : '';
		// Border Color
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_search_border_color', 'Sorting_Bar' , 0 ) ?  'border-color:' . \Depc_Core::get_option( 'dpr_filter_bar_search_border_color', 'Sorting_Bar' ) . ';' : '';
		// Text Color
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_search_text_color', 'Sorting_Bar' , 0 ) ?  'color:' . \Depc_Core::get_option( 'dpr_filter_bar_search_text_color', 'Sorting_Bar' ) . ';' : '';
		// Border Radius
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_search_border_radius', 'Sorting_Bar' , 0 ) ?  'border-radius:' . \Depc_Core::get_option( 'dpr_filter_bar_search_border_radius', 'Sorting_Bar' ) . 'px;' : '';
		// Width
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_search_width', 'Sorting_Bar' , 0 ) ?  'width:' . \Depc_Core::get_option( 'dpr_filter_bar_search_width', 'Sorting_Bar' ) . 'px;' : '';
		// Height
		$styles .= \Depc_Core::get_option( 'dpr_filter_bar_search_height', 'Sorting_Bar' , 0 ) ?  'height:' . \Depc_Core::get_option( 'dpr_filter_bar_search_height', 'Sorting_Bar' ) . 'px;' : '';
	$styles .= '}';

	$styles .= \Depc_Core::get_option( 'dc_custom_css', 'Custom_CSS' , '' );
	$styles = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $styles); // negative look ahead
	$styles = preg_replace('/\s{2,}/', ' ', $styles);
	$styles = preg_replace('/\s*([:;{}])\s*/', '$1', $styles);
	$styles = preg_replace('/;}/', '}', $styles);
	$styles = preg_replace('/(?:[^\r\n,{}]+)(?:,(?=[^}]*{)|\s*{[\s]*})/', '', $styles);

	if($styles) {
		echo '<style id="deeper-comments-custom-css">' . $styles . '</style>';
	}

	$container_style = '<style>.dpr-discu-container{width:%s%s}</style>';
	$custom_css = \Depc_Core::get_option( 'dc_dpr_discu_container_width', 'Skin' , 0 ) == 0 ? sprintf( $container_style , '100' , '%' ) : sprintf( $container_style , \Depc_Core::get_option( 'dc_dpr_discu_container_width', 'Skin' , 0 ), 'px');
	echo($custom_css);
	$comment = Depc_Controller_Public_Comment::get_instance();
	$comment->load();
	?>

<!-- End Start comment Template
	================================================== -->
