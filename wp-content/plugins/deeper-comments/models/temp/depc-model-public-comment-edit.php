<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Model_Public_Comment_Edit extends Depc_Model_Public_Comment {



	private $query = '';
	public $ajax_edit_action  = 'dpr_edit_comment';
	public $ajax_delete_action  = 'dpr_delete_comment';
	private static $nounce  = 'dpr_edit_comment';

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	public function __construct() {}

	public function render_edit() {

		check_ajax_referer( self::$nounce, 'security' );
		Depc_Request_Validator::is_numeric( $_POST['comment_id'] , true );

		$commentarr = array();
		$commentarr['comment_ID'] = $_POST['comment_id'];
		$commentarr['comment_content'] =  wp_rel_nofollow( $_POST['content'] );
		wp_update_comment( $commentarr );
		wp_die();

	}

	public function render_delete() {

		check_ajax_referer( self::$nounce, 'security' );
		Depc_Request_Validator::is_numeric( $_POST['comment_id'] , true );

		wp_trash_comment( trim(  $_POST['comment_id'] ) );
		clean_comment_cache( trim(  $_POST['comment_id'] ) );

		wp_die();

	}

	public static function get_script() {
		return self::scripts();
	}

	private static function scripts() {

		$id = new self();
		$id = $id->post_id();

		$nounce = wp_create_nonce( self::$nounce );
		// Generating javascript code tpl
		$javascript = '<script type="text/javascript">
		jQuery(document).ready(function() {
			if(typeof jQuery(".dpr-discu-container_' . $id . ' .dpr-discu-wrap .dpr-discu-edit").depcEditComment == "undefined") {
				return;
			}
			jQuery(".dpr-discu-container_'.$id.' .dpr-discu-wrap .dpr-discu-edit").depcEditComment({
				id: "'.$id.'",
				save: false
			});

			jQuery(".dpr-discu-container_'.$id.' .dpr-discu-wrap .dpr-discu-edit .dpr_edit_comment").depcEditComment({
				id: "'.$id.'",
				save: true,
				nounce : "'. $nounce .'"
			});

			jQuery(".dpr-discu-container_'.$id.' .dpr-discu-wrap .dpr-discu-delete").depcEditComment({
				id: "'.$id.'",
				save: null,
				nounce : "'. $nounce .'"
			});

			jQuery(".dpr-discu-container_'.$id.' .dpr-discu-wrap .dpr-discu-flag").depcEditComment({
				id: "'.$id.'",
				save: -1,
				nounce : "'. $nounce .'"
			});

			jQuery(".dpr-discu-container_'.$id.' .dpr-discu-metadata-share-wrap .dpr-discu-sharing .dpr-discu-social-icon a").depcEditComment({
				id: "'.$id.'",
				save: -2,
				nounce : "'. $nounce .'"
			});

		});

		</script>';

		return $javascript;

	}

}