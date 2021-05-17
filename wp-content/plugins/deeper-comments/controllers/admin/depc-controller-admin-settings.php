<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Admin_Settings extends Depc_Controller_Admin {

	private $settings_api;

	const SETTINGS_PAGE_URL = Depc_Core::DEPC_ID;
	const REQUIRED_CAPABILITY = 'moderate_comments';


	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {

		$this->settings_api = Depc_Model_Admin_Settings::get_instance();
		add_action( 'admin_init', array($this, 'register_hook_callbacks') );
		add_action( 'admin_menu', array($this, 'admin_menu') );
		add_action( 'admin_menu', array($this, 'admin_menu') );
		if ( Depc_Core::get_option( 'dc_show_in_admin_bar', 'Appearances' , 'off' ) == 'on' ) {
			add_action( 'wp_before_admin_bar_render', array($this, 'admin_bar') );
		}
        add_action( 'admin_menu', array($this, 'admin_submenu_menu') , 12 );
	}

	/**
	 * Register callbacks for actions and filters
	 *
	 * @since    1.0.0
	 */
	public function register_hook_callbacks() {
        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );
        $this->settings_api->child_set_fields( $this->get_child_settings_fields() );
        //initialize settings
        $this->settings_api->admin_init();

	}

    public function admin_menu() {
        add_menu_page( 'Deeper Comments', 'Deeper Comments', 'moderate_comments', 'deeper_intro' , array($this, 'hi') , Depc_Core::get_depc_url() . 'views/img/webnus-deeper-comments-icon.svg' , 26 );
	}

	public function admin_bar() {
		global $wp_admin_bar;
		if(!current_user_can( 'administrator' )) {
			return;
		}
		echo ("<style>#wpadminbar .dpr-bubble {padding: 0px 8px !important;display: inline-block;position: relative !important;margin-left: 5px !important;background-color: rgba(0, 0, 0, 0.23);}.dpr-admin-bar-item,.dpr-admin-bar-item a:focus,.dpr-admin-bar-item:focus{background-color: #ffffff26!important;padding-left:10px!important;transition:.5s!important}.dpr-admin-bar-item:hover{box-shadow:0 0 15px -5px #00bdf2}.dpr-admin-bar-item a:focus,.dpr-admin-bar-item a:hover,.dpr-admin-bar-item:hover a{background:unset!important;color:#fff!important;font-weight:700}.dpr-admin-bar-item img{display:inline-block;right:5px;position:relative!important;top:5px}</style>");
		$icon = '<span class="wp-menu-image dashicons-before"><img src="'.Depc_Core::get_depc_url() . 'views/img/webnus-deeper-comments-icon-white.svg'.'" alt="Deeper Comments" title="Deeper Comments"></span>';


		$inapp_count = Depc_Controller_Admin_Inapp::comment_count();
		$count = '';
		if ( $inapp_count ) {
			$count = '<span class="dpr-bubble"><span>' . $inapp_count . '</span></span>';
		}

		$wp_admin_bar->add_menu( array(
			'id'     => 'deeper-comments',
			'parent' => 'top-secondary',
			'title'  => $icon . __( 'Deeper Comments', 'depc' ) . $count,
			'href'   => get_admin_url( NULL, 'admin.php?page=deeper_inapp_cm' ),
			'meta'   => array(
				'class'	=> 'dpr-admin-bar-item'
			),

		) );
	}

    public function admin_submenu_menu() {

        add_submenu_page( 'deeper_intro', 'Deeper Comment', 'Settings', 'moderate_comments', 'deeper_settings', array($this, 'plugin_page') );
        remove_submenu_page( 'deeper_intro', 'deeper_intro' );
    }

	public function get_settings_sections() {
		$sections = array(
            array(
				'id'    => 'disscustion_settings',
				'title' => __( 'Discussion Settings', 'depc' ),
				'icon'  => 'sl-speech',
				'submenu'   => array(
					'id'  => __( 'Comments', 'depc' ),
					'id1'  => __( 'Appearances', 'depc' ),
				    'id2' => __( 'Inappropriate_Comments', 'depc' ),
				    'id3' => __( 'Social_Share', 'depc' ),
					'id4' => __( 'Voting', 'depc' ),
					'id5' => __( 'Word_Blacklist', 'depc'),
				    'id6' => __( 'Avatar', 'depc' ),
				    'id7' => __( 'Skin', 'depc' ),
				    'id8' => __( 'Limitation', 'depc' ),
				    'id9' => __( 'Notifications', 'depc' ),
					'id10' => __( 'Load_More', 'depc' ),
					'id11' => __( 'Comment_Sorting_Bar', 'depc' ),
				)
			),

			array(
				'id'    => 'styling',
				'title' => __('Custom Styling', 'depc'),
				'icon'  => 'sl-magic-wand',
				'submenu'   => array(
					'id'    => __('Comment_Form', 'depc'),
					'id1'   => __('Comment_Box', 'depc'),
					'id2'   => __('Replay', 'depc'),
					'id3'   => __('Load_More_Button', 'depc'),
					'id4'   => __('Author_Avatar', 'depc'),
					'id5'   => __('Elements', 'depc'),
					'id6'   => __('Sorting_Bar', 'depc'),
					'id7'   => __('Custom_CSS', 'depc')
				)
			),

            array(
				'id'    => 'social_login',
				'title' => __( 'Login Register', 'depc' ),
				'icon'  => 'sl-login',
				'submenu'   => array(
				    'id'    => __( 'Login_Register', 'depc' ),
				    'id1'   => __( 'Recaptcha', 'depc' )
					)
			),
		);
		return $sections;
	}

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public function get_settings_fields() {
    	$settings_fields = array(
    		'disscustion_settings' => array(
		    ),
    		'styling' => array(
		    ),
            'social_login' => array(
            ),
	    );

    	return $settings_fields;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public function get_child_settings_fields() {
        $settings_fields = array(
			__( 'Comments', 'depc' ) => array(
				array(
					'name'    => 'dc_delete_member',
					'label'   => __( 'Delete Comments By Users', 'depc' ),
					'desc'    => __('Permission to delete comments in front-end for logged-in users.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_edit_member',
					'label'   => __('Edit Comments By Users', 'depc'),
					'desc'    => __('Permission to edit comments in front-end for logged-in users.', 'depc'),
					'type'    => 'switch',
					'default' => 'on',
				),
				array(
					'name'    => 'dc_spam_check',
					'label'   => __( 'Spam Check', 'depc' ),
					'desc'    => __( 'Posting comments are possible based on spam check sensitivity. ', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_user_profile_url',
					'label'   => __( 'User Profile Url', 'depc' ),
					'desc'    => __( 'Set author profile url, leave empty for default profile url', 'depc' ),
                    'type'    => 'text',
                    'default' => '',
				),
				array(
					'name'    => 'dc_spam_check_sensitivity',
					'label'   => __( 'Spam Check Sensitivity Time', 'depc' ),
					'desc'    => __( 'Add an interval between posting comments, for example 3/second.', 'depc' ),
					'min'               => 0,
                    'step'              => '1',
                    'type'              => 'number',
                    'default'           => '3',
				),
				array(
					'name'    => 'dc_convert_autolink',
					'label'   => __( 'AutoLink', 'depc' ),
					'desc'    => __( 'Convert plain text URLs into HTML hyperlinks.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_use_tinymce',
					'label'   => __( 'TinyMCE Editor for Writing Comments', 'depc' ),
					'desc'    => __( 'Using TinyMCE (visual) editor when writing comments.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_use_image_tinymce',
					'label'   => __('Image Insertion Button<br/><small>TinyMCE Editor Toolbar</small>', 'depc' ),
					'desc'    => __( 'Show image insertion button on TinyMCE editor toolbar when writing a comment.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_use_emoji_tinymce',
					'label'   => __('Emoji List Button<br/><small>TinyMCE Editor Toolbar</small>', 'depc' ),
					'desc'    => __('Show emoji list button on TinyMCE editor toolbar when writing a comment.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_link_member',
					'label'   => __('Link Insertion Button for Users<br/><small>TinyMCE Editor Toolbar</small>', 'depc'),
					'desc'    => __('Show link insertion button for logged-in users on TinyMCE editor toolbar when writing a comment.', 'depc'),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_link_guest',
					'label'   => __('Link Insertion Button for Guests<br/><small>TinyMCE Editor Toolbar</small>', 'depc'),
					'desc'    => __('Show link insertion button for guests on TinyMCE editor toolbar when writing a comment.', 'depc'),
					'type'    => 'switch',
					'default' => 'on'
				),

                array(
                    'name'              => 'dc_edit_expiration',
                    'label'             => __( 'Access to Edit Comments (Days)', 'depc' ),
                    'desc'              => __( 'Set an end date for comment author to edit their comments, leave empty for unlimited access time.', 'depc' ),
                    'placeholder'       => __( '1 to unlimited', 'depc' ),
                    'min'               => 0,
                    'step'              => '1',
                    'type'              => 'number',
                    'default'           => '10',
                    'sanitize_callback' => 'floatval'
                ),

				array(
					'name'    => 'dc_follow_member',
					'label'   => __( 'Notification on New Replies', 'depc' ),
					'desc'    => __( 'Users receive notifications about new replies to a comment they\'ve followed.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_allow_guest_comment',
					'label'   => __( 'Enable/Disable Guest Comments', 'depc' ),
					'desc'    => __( 'Enable/disable permission for guests to write and post comments.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_use_website_field',
					'label'   => __('Show/Hide Website Field', 'depc'),
					'desc'    => __('Showing/Hiding website field when posting comments <b>(only for guests)</strong>.', 'depc'),
					'type'    => 'switch',
					'default' => 'on'
				),
			),
			__( 'Appearances', 'depc' ) => array(

				array(
                    'name'    => 'dc_show_most_recent_authors',
                    'label'   => __( 'Show/Hide Most Recent Authors', 'depc' ),
                    'desc'    => __( 'Show/Hide latest authors who have left a comment.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
                    'name'    => 'dc_show_in_admin_bar',
                    'label'   => __( 'Show/Hide DC Menu in Admin Bar', 'depc' ),
                    'desc'    => __( 'Show Deeper Comments menu in Admin Bar.', 'depc' ),
					'type'    => 'switch',
					'default' => 'off'
				),
				array(
                    'name'    => 'dc_show_comment_date',
                    'label'   => __( 'Comment date', 'depc' ),
                    'desc'    => __( 'Show comment date in comments', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_show_online_status',
					'label'   => __('User Online Status', 'depc'),
					'desc'    => __('Show online status of comment author beside the author name.', 'depc'),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_collapse_member',
					'label'   => __('Collapse Button for Users', 'depc'),
					'desc'    => __('Show/Hide the “Collapse” button for the comments(logged-in users). This button can be added separately for each comment and each comment can be collapsed separately.', 'depc'),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_collapse_guest',
					'label'   => __('Collapse Button for Guests', 'depc'),
					'desc'    => __('Show/Hide the “Collapse” button for the comments(Guests). This button can be added separately for each comment so each comment can be collapsed separately.', 'depc'),
					'type'    => 'switch',
					'default' => 'on'
				),


			),
			__( 'Inappropriate_Comments', 'depc' ) => array(
				array(
				    'name'    => 'dc_inappropriate_members',
				    'label'   => __( 'Show/Hide Report Button for Users<br/><small>Inappropriate Flag Button</small>', 'depc' ),
				    'desc'    => __( 'Show/Hide inappropriate comments flag button on top of comments for logged-in users.', 'depc' ),
				    'type'    => 'switch',
				    'default' => 'on'
				),
				array(
					'name'    => 'dc_inappropriate_guest',
					'label'   => __('Show/Hide Report Button for Guests<br/><small>Inappropriate Flag Button</small>', 'depc' ),
					'desc'    => __('Show/Hide inappropriate comments flag button on top of comments for guests.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on',
				),
				array(
					'name'    => 'dc_inapp_auto_ban',
					'label'   => __('Automatic Pending Comments<br/><small>Based on Given Reports</small>', 'depc' ),
					'desc'    => __('Maximum report times before comment status changes to <strong>"pending"</strong>.', 'depc' ),
					'type'    => 'number',
					'default' => '5',
				),
			),
			__( 'Social_Share', 'depc' ) => array(
				array(
					'name'    => 'dc_social_enable',
					'label'   => __( 'Show/Hide Social Share Toggle<br/><small>Comment\'s Footer Metadata Box</small>', 'depc' ),
					'desc'    => __( 'Show/Hide social share toggle menu on comments footer.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_social_share_fb',
					'label'   => __( 'Facebook Share Button', 'depc' ),
					'desc'    => __( 'Show/Hide facebook share button on share toggle menu.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				// array(
				// 	'name'    => 'dc_social_share_fb_id',
				// 	'label'   => __( 'Facebook App ID', 'depc' ),
				// 	'desc'    => __('Create Facebook App ID via <a href="https://developers.facebook.com/docs/apps/register" target="_blank">this</a> link.', 'depc' ),
				// 	'type'    => 'text',
				// 	'default' => ''
				// ),
				array(
					'name'    => 'dc_social_share_vk',
					'label'   => __( 'VK Share Button', 'depc' ),
					'desc'    => __( 'Show/Hide VK share button on share toggle menu.', 'depc' ),
					'type'    => 'switch',
					'default' => 'off'
				),
				array(
					'name'    => 'dc_social_share_pinterest',
					'label'   => __( 'Pinterest Share Button', 'depc' ),
					'desc'    => __( 'Show/Hide Pinterest share button on share toggle menu.', 'depc' ),
					'type'    => 'switch',
					'default' => 'off'
				),
				array(
					'name'    => 'dc_social_share_getpocket',
					'label'   => __( 'Getpocket Share Button', 'depc' ),
					'desc'    => __( 'Show/Hide Getpocket share button on share toggle menu.', 'depc' ),
					'type'    => 'switch',
					'default' => 'off'
				),
				array(
					'name'    => 'dc_social_share_tumblr',
					'label'   => __( 'Tumblr Share Button', 'depc' ),
					'desc'    => __( 'Show/Hide Tumblr share button on share toggle menu.', 'depc' ),
					'type'    => 'switch',
					'default' => 'off'
				),
				array(
					'name'    => 'dc_social_share_reddit',
					'label'   => __( 'Reddit Share Button', 'depc' ),
					'desc'    => __( 'Show/Hide Reddit share button on share toggle menu.', 'depc' ),
					'type'    => 'switch',
					'default' => 'off'
				),
				array(
					'name'    => 'dc_social_share_whatsapp',
					'label'   => __( 'WhatsApp Share Button', 'depc' ),
					'desc'    => __( 'Show/Hide WhatsApp share button on share toggle menu.', 'depc' ),
					'type'    => 'switch',
					'default' => 'off'
				),
				array(
					'name'    => 'dc_social_share_telegram',
					'label'   => __( 'Telegram Share Button', 'depc' ),
					'desc'    => __( 'Show/Hide Telegram share button on share toggle menu.', 'depc' ),
					'type'    => 'switch',
					'default' => 'off'
				),
				array(
					'name'    => 'dc_social_share_tw',
					'label'   => __('Twitter Share Button', 'depc' ),
					'desc'    => __('Show/Hide Twitter share button on share toggle menu.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_social_share_mail',
					'label'   => __( 'Email Share Button', 'depc' ),
					'desc'    => __('Show/Hide email share button on share toggle menu.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
			),
			__( 'Word_Blacklist', 'depc' ) => array(
				array(
                    'name'    => 'dc_word_filter',
                    'label'   => __( 'Word Blacklist', 'depc' ),
                    'desc'    => __('If these words are used in comments texts, They will be flagged as inappropriate comments, <strong>Separate them with ","</strong>.', 'depc' ),
                    'type'    => 'textarea'
                ),
			),
            __( 'Voting', 'depc' ) => array(
                array(
                    'name'    => 'dc_vote_user_enable',
                    'label'   => __( 'Comment Like/Dislike Buttons<br/><small>For Logged-in Users</small>', 'depc' ),
                    'desc'    => __( 'Enable comment like/dislike buttons for logged-in users.', 'depc' ),
                    'type'    => 'switch',
                    'default' => 'on'
                ),
                array(
                    'name'              => 'dc_vote_user_date',
                    'label'             => __( 'Re-vote Minimum Limitation Time<br/><small>For Logged-in Users</small>', 'depc' ),
                    'desc'              => __( 'Minimum time that logged-in users have to wait before re-voting a comment.(day)', 'depc' ),
                    'placeholder'       => __('0 for unlimited', 'depc' ),
                    'min'               => 0,
                    'step'              => '1',
                    'type'              => 'number',
                    'default'           => '10',
                    'sanitize_callback' => 'floatval'
                ),
                array(
                    'name'    => 'dc_vote_guest_enable',
                    'label'   => __('Comment Like/Dislike Buttons<br/><small>For Guests</small>', 'depc' ),
                    'desc'    => __('Enable comment like/dislike buttons for logged-in guests.', 'depc' ),
                    'type'    => 'switch',
                    'default' => 'on'
                ),
                array(
                    'name'              => 'dc_vote_guest_date',
                    'label'             => __('Re-vote Minimum Limitation Time<br/><small>For Guests</small>', 'depc' ),
                    'desc'              => __('Minimum time that guests have to wait before re-voting a comment.(day)', 'depc' ),
                    'placeholder'       => __( '0 for unlimited', 'depc' ),
                    'min'               => 0,
                    'step'              => '1',
                    'type'              => 'number',
                    'default'           => '10',
                    'sanitize_callback' => 'floatval'
                ),
			),
			__( 'Avatar', 'depc' ) => array(
				array(
					'name'    => 'dc_generate_avatar',
					'label'   => __( 'Show/Hide Comment\'s Author Avatar', 'depc' ),
					'desc'    => __('Show/Hide avatar if there is no Gravatar associated with comment\'s author email.', 'depc' ),
					'type'    => 'switch',
					'default'  => 'on'
				),
				array(
					'name'    => 'gravatar_type',
					'label'   => __( 'Avatar Type', 'depc' ),
					'type'    => 'select',
					'default' => get_option('avatar_default','blank'),
					'desc'    => __('Use "Blank" type to show the first character of comment\'s author name as avatar.', 'depc'),
					'options' => array(
						'mp'			=> 'Mp',
						'identicon'		=> 'Identicon',
						'monsterid'		=> 'Monsterid',
						'wavatar'		=> 'Wavatar',
						'retro'			=> 'Retro',
						'robohash'		=> 'Robohash',
						'blank'			=> 'Blank'
					)
				),

			),
			__( 'Skin', 'depc' ) => array(
				array(
					'name'    => 'dc_skins',
					'label'   => __( 'Deeper Comments Skin', 'depc' ),
					'type'    => 'select',
					'default' => 'normal',
					'options' => array(
						'default'   => 'Default',
						'template1'   => 'Template 1',
						'template2'   => 'Template 2',
						'template3'   => 'Template 3',
					)
				),
				array(
					'name'    => 'dc_dpr_discu_theme_mode',
					'label'   => __('Theme', 'depc' ),
					'type'    => 'select',
					'default' => 'light',
					'options' => array(
						// 'dark'     => 'Dark',
						'light'    => 'Light'
					)
				),
				array(
					'name'    => 'dc_dpr_discu_container_width',
					'label'   => __( 'Container width (px)', 'depc' ),
					'desc'    => __( 'Enter the width of Deeper Comments container in <strong>px</strong>, Put 0 to have <strong>100%</strong> width.', 'depc' ),
					'default' => '0',
					'type'    => 'text'
				),
				array(
					'name'    => 'dc_dpr_discu_default_color',
					'label'   => __( 'Default Color', 'depc' ),
					'desc'    => __( 'Enter the default color for elements.(Almost for Background-Colors)', 'depc' ),
					'default' => '#437df9',
					'type'    => 'color'
				),
			),
			__( 'Limitation', 'depc' ) => array(
				array(
					'name'    => 'dc_post_type_limitation',
					'label'   => __( 'Post Type', 'depc' ),
					'desc'   => __( 'Show/Hide comments based on WordPress post types.<br/><small>Enable "Post" and "Page" to show Deeper Comments in your posts & pages.</small>', 'depc' ),
					'type'    => 'multicheck',
					'options' => get_post_types()
				),
				array(
					'name'    => 'dc_categories_limitation',
					'label'   => __( 'Post Categories', 'depc' ),
					'desc'   => __('Show/Hide comments based on WordPress posts categories.', 'depc'),
					'type'    => 'multicheck',
					'options' => static::get_categories()
				),
			),
			__( 'Notifications', 'depc' ) => array(
				array(
					'name'    => 'dc_enable_dashboard_notifications',
					'label'   => __('Dashboard Notifications', 'depc' ),
					'desc'   => __( 'By enabling this option you will receive a notification in dashboard every time a new comment is posted.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_enable_notifications',
					'label'   => __( 'Email Notifications<br/><small>For WP Admin</small>', 'depc' ),
					'desc'   => __( 'By enabling this option you will receive a notification email every time a new comment is posted.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_notifications_admin_email',
					'label'   => __( 'Admin Email address', 'depc' ),
					'desc'   => __('Enter custom email only if you want to receive email notifications on a different email than the one you\'ve defined on WP general settings page.', 'depc'),
					'type'    => 'text',
					'default' => get_option('admin_email')
				),
				array(
					'name'    => 'dc_enable_notifications_for_author',
					'label'   => __('Email Notifications<br/><small>For Comment\'s Author</small>', 'depc' ),
					'desc'   => __('By enabling this option comment\'s author will receive a notification email after posting a comment.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_enable_notifications_for_author_after_approve',
					'label'   => __('Email Notifications when Approved<br/><small>For Comment\'s Author</small>', 'depc' ),
					'desc'   => __('By enabling this option comment\'s author will receive a notification email after approval of comment.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_notifications_author_subject',
					'label'   => __( 'Email\'s Subject<br/><small>For Comment\'s Author</small>', 'depc' ),
					'desc'   => __( 'Comment URL: <b>"%comment_url"</b>, Site address: <b>"%site_address"</b>, Site name: <b>"%site_name"</b>, Author name: <b>"%author_name"</b>, Author Email: <b>"%author_email"</b>, Author URL: <b>"%author_url"</b>, Post title: <b>"%post_title"</b>, Comment content: <b>"%comment_content"</b>', 'depc' ),
					'type'    => 'text',
					'default' => __('Your comment on %site_name','depc')
				),
				array(
					'name'    => 'dc_notifications_author_email_body',
					'label'   => __('Email\'s Body<br/><small>For Comment\'s Author</small>', 'depc' ),
					'desc'   => __('Comment URL: <b>"%comment_url"</b>, Site address: <b>"%site_address"</b>, Site name: <b>"%site_name"</b>, Author name: <b>"%author_name"</b>, Author Email: <b>"%author_email"</b>, Author URL: <b>"%author_url"</b>, Post title: <b>"%post_title"</b>, Comment content: <b>"%comment_content"</b>', 'depc'),
					'type'    => 'wysiwyg',
					'default' => __('Dear %author_name,<br>
						Your comment has been successfully posted on %post_title with the email %author_email<br>
						Read your comment on <a href="%comment_url">%comment_url</a> .<br>
						Best,<br>
						<a href="%site_address">%site_name</a>'
					,'depc')
				),
				array(
					'name'    => 'dc_enable_user_notifications',
					'label'   => __( 'Website Notifications<br/><small>For Online Users</small>', 'depc' ),
					'desc'   => __( 'By enabling this option, a notification will be shown to online users who have followed a comment.  <strong>Make sure to enable "Online Status" in "Appearances".</strong>', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_enable_user_notifications_email',
					'label'   => __('Email Notifications<br/><small>For Users & Guests</small>', 'depc'),
					'desc'   => __('By enabling this option notifications will be sent to comment\'s followers.', 'depc' ),
					'type'    => 'switch',
					'default' => 'off'
				),
				array(
					'name'    => 'dc_follow_notifications_author_subject',
					'label'   => __( 'Email’s Subject for Followed Comments', 'depc' ),
					'desc'   => __('Comment URL: <b>"%comment_url"</b>, Site address: <b>"%site_address"</b>, Site name: <b>"%site_name"</b>, Author name: <b>"%author_name"</b>, Author Email: <b>"%author_email"</b>, Author URL: <b>"%author_url"</b>, Post title: <b>"%post_title"</b>, Comment content: <b>"%comment_content"</b>', 'depc'),
					'type'    => 'text',
					'default' => __('New Notification From a Comment on %site_name','depc')
				),
				array(
					'name'    => 'dc_follow_notifications_author_email_body',
					'label'   => __( 'Email’s Body for Followed Comments', 'depc' ),
					'desc'   => __('Comment URL: <b>"%comment_url"</b>, Site address: <b>"%site_address"</b>, Site name: <b>"%site_name"</b>, Author name: <b>"%author_name"</b>, Author Email: <b>"%author_email"</b>, Author URL: <b>"%author_url"</b>, Post title: <b>"%post_title"</b>, Comment content: <b>"%comment_content"</b>', 'depc'),
					'type'    => 'wysiwyg',
					'default' => __('Dear %author_name,<br/>
						The comment you`ve followed on %site_name has a new notification.<br/>
						Check it out on <a href="%comment_url">%comment_url</a> .<br/>
						Best Wishes,<br/>
						<a href="%site_address">%site_name</a>'
					,'depc')
				),
			),
			__( 'Load_More', 'depc' ) => array(
				array(
					'name'	  => 'dc_enable_loadmore',
					'label'	  => __( 'Show/Hide Load More Buttons', 'depc' ),
					'desc' 	  => __( 'Show/Hide load more button under comments list.', 'depc' ),
					'type' 	  => 'switch',
					'default' => 'off',
				),
				array(
					'name'    => 'dc_defultcnt_comment',
					'label'   => __('Number of Comments In Page', 'depc' ),
					'desc'    => __('Number of comments to loads in page.', 'depc' ),
					'type'    => 'number',
					'placeholder'       => __( '10', 'depc' ),
					'min'               => 1,
					'max'               => 15,
					'step'              => '1',
					'default'           => '10',
					'sanitize_callback' => 'number',
					'condition' => [
						'dc_enable_loadmore' => 'on',
					]
				),
				array(
					'name'    => 'dc_enable_loadmore_count',
					'label'   => __( 'Number of Comments To Load', 'depc' ),
					'desc'    => __( 'Number of loaded comments when load more button is clicked.', 'depc' ),
					'type'    => 'number',
					'placeholder'       => __( '5', 'depc' ),
					'min'               => 1,
					'max'               => 15,
					'step'              => '1',
					'default'           => '5',
					'sanitize_callback' => 'number',
					'condition' => [
						'dc_enable_loadmore' => 'on',
					]
                ),
			),
			__( 'Comment_Sorting_Bar', 'depc' ) => array(
                array(
                    'name'    => 'dc_enable_filter_member',
                    'label'   => __( 'Show/Hide Sorting<br/><small>For Logged-in Users</small>', 'depc' ),
                    'desc'    => __( 'If enabled sorting bar will be shown at the comment\'s header bar for logged-in users.', 'depc' ),
                    'type'    => 'switch'
                ),
                array(
                    'name'    => 'dc_enable_filter_guest',
                    'label'   => __('Show/Hide Sorting<br/><small>For Guests</small>', 'depc' ),
                    'desc'    => __('If enabled sorting bar will be shown at the comment\'s header bar for guests.', 'depc' ),
                    'type'    => 'switch'
                ),
                array(
                    'name'    => 'dc_default_sorting',
                    'label'   => __( 'Default Comments Sorting Option', 'depc' ),
                    'type'    => 'select',
                    'default' => 'newest',
                    'options' => array(
                        'trending'  => 'Trending',
                        'popular'   => 'Popular',
                        'oldest'   	=> 'Oldest',
                        'newest'   	=> 'Newest'
                    )
				),
				array(
					'name'    => 'dc_enable_filter_count',
					'label'   => __( 'Number of Comments to Show', 'depc' ),
					'desc'    => __( 'Number of comments to loads when a new sorting option is selected.', 'depc' ),
					'type'    => 'number',
					'placeholder'       => __( '10', 'depc' ),
					'min'               => 1,
					'max'               => 15,
					'step'              => '1',
					'default'           => '10',
					'sanitize_callback' => 'number',
					'condition' => [
						'dc_enable_filter_member' => 'on',
						'dc_enable_filter_guest' => 'on'
					]
				),
				array(
					'name'    => 'dc_show_comments_count',
					'label'   => __( 'Show Comments Count', 'depc' ),
					'desc'    => __('Show comments count on top of the comment\'s header bar.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_show_comments_count_text',
					'label'   => __( 'Comments Count Suffix Word', 'depc' ),
					'desc'    => __( 'Comments count suffix word to be shown after it\'s count.', 'depc' ),
					'type'    => 'text',
					'default' => 'Comment'
				),
            ),
			__( 'Comment_Form', 'depc' ) => array(
				array(
					'name'    => 'dpr_join_form_wrap',
					'label'   => __( 'Wrap Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_join_form_wrap_border_color',
					'label'   => __( 'Wrap Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_join_form_inner',
					'label'   => __( 'Footer Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_join_form_inner_border_color',
					'label'   => __( 'Footer Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_join_form_area_textarea',
					'label'   => __( 'Join the discussion Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_join_form_area_textarea_border_color',
					'label'   => __( 'Join the discussion Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_join_form_area_textarea_text_color',
					'label'   => __( 'Join the discussion Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_join_form_wrap_buttons',
					'label'   => __( 'Buttons Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_join_form_wrap_buttons_border_color',
					'label'   => __( 'Buttons Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_join_form_wrap_buttons_text_color',
					'label'   => __( 'Buttons Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_join_form_wrap_submit',
					'label'   => __( 'Submit Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_join_form_wrap_submit_border_color',
					'label'   => __( 'Submit Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_join_form_wrap_submit_text_color',
					'label'   => __( 'Submit Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_comments_count_text_color',
					'label'   => __( 'Comments Count Color', 'depc' ),
					'type'    => 'color',
				),
			),
			__( 'Comment_Box', 'depc' ) => array(
				array(
					'name'    => 'dpr_comment_box_background_color',
					'label'   => __( 'Wrap Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_comment_box_border_color',
					'label'   => __( 'Wrap Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_comment_box_text_color',
					'label'   => __( 'Comment Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_comment_box_metadata_background_color',
					'label'   => __( 'Metadata Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_comment_box_metadata_border_color',
					'label'   => __( 'Metadata Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_comment_box_footer_background_color',
					'label'   => __( 'Footer Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_comment_box_reply_text_color',
					'label'   => __( 'Reply Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_comment_box_author_name_text_color',
					'label'   => __( 'Author Name Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_comment_box_date_text_color',
					'label'   => __( 'Date Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_comment_box_icons_color',
					'label'   => __( 'Icons Color', 'depc' ),
					'type'    => 'color',
				),
			),
			__( 'Replay', 'depc' ) => array(
				array(
					'name'    => 'dpr_replay_comment_box_background_color',
					'label'   => __( 'Wrap Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_replay_comment_box_border_color',
					'label'   => __( 'Wrap Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_replay_comment_box_text_color',
					'label'   => __( 'Comment Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_replay_comment_box_metadata_background_color',
					'label'   => __( 'Metadata Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_replay_comment_box_metadata_border_color',
					'label'   => __( 'Metadata Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_replay_comment_box_footer_background_color',
					'label'   => __( 'Footer Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_replay_comment_box_reply_text_color',
					'label'   => __( 'Reply Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_replay_comment_box_author_name_text_color',
					'label'   => __( 'Author Name Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_replay_comment_box_date_text_color',
					'label'   => __( 'Date Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_replay_comment_box_icons_color',
					'label'   => __( 'Icons Color', 'depc' ),
					'type'    => 'color',
				),
			),
			__( 'Load_More_Button', 'depc' ) => array(
				array(
					'name'    => 'dpr_load_more_button_background_color',
					'label'   => __( 'Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_load_more_button_border_color',
					'label'   => __( 'Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_load_more_button_text_color',
					'label'   => __( 'Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_load_more_button_border_radius',
					'label'   => __( 'Border Radius', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_load_more_button_padding',
					'label'   => __( 'Padding', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_load_more_button_font_size',
					'label'   => __( 'Font Size', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
			),
			__( 'Author_Avatar', 'depc' ) => array(
				array(
					'name'    => 'dpr_author_avatar_border_width',
					'label'   => __( 'Border Width', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_author_avatar_border_style',
					'label'   => __( 'Border Style', 'depc' ),
					'type'    => 'select',
					'options' => [
						'solid' => 'solid',
						'dashed' => 'dashed',
						'dotted' => 'dotted',
						'double' => 'double',
						'groove' => 'groove',
						'ridge' => 'ridge',
					]
				),
				array(
					'name'    => 'dpr_author_avatar_border_color',
					'label'   => __( 'Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_author_avatar_border_radius',
					'label'   => __( 'Border Radius', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_author_avatar_width',
					'label'   => __( 'Width', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_author_avatar_height',
					'label'   => __( 'Height', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_author_avatar_top',
					'label'   => __( 'Top', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_author_avatar_left',
					'label'   => __( 'Left', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_author_avatar_right',
					'label'   => __( 'Right', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_author_avatar_bottom',
					'label'   => __( 'Bottom', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
			),
			__( 'Elements', 'depc' ) => array(
				array(
					'name'    => 'dpr_online_status_background_color',
					'label'   => __( 'Online Status Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_online_status_border_color',
					'label'   => __( 'Online Status Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_online_status_border_radius',
					'label'   => __( 'Online Status Border Radius', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_online_status_width',
					'label'   => __( 'Online Status Width', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_online_status_height',
					'label'   => __( 'Online Status Height', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_offline_status_background_color',
					'label'   => __( 'Offline Status Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_offline_status_border_color',
					'label'   => __( 'Offline Status Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_offline_status_border_radius',
					'label'   => __( 'Offline Status Border Radius', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_offline_status_width',
					'label'   => __( 'Offline Status Width', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_offline_status_height',
					'label'   => __( 'Offline Status Height', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
			),
			__( 'Sorting_Bar', 'depc' ) => array(
				array(
					'name'    => 'dpr_filter_bar_background_color',
					'label'   => __( 'Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_filter_bar_border_color',
					'label'   => __( 'Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_filter_bar_text_color',
					'label'   => __( 'Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_filter_bar_border_radius',
					'label'   => __( 'Border Radius', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_filter_bar_width',
					'label'   => __( 'Width', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_filter_bar_height',
					'label'   => __( 'Height', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_filter_bar_margin_right',
					'label'   => __( 'Margin Right', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_filter_bar_margin_left',
					'label'   => __( 'Margin Left', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_filter_bar_margin_top',
					'label'   => __( 'Margin Top', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_filter_bar_margin_bottom',
					'label'   => __( 'Margin Bottom', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_filter_bar_active_tab_background_color',
					'label'   => __( 'Active Tab Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_filter_bar_active_tab_border_color',
					'label'   => __( 'Active Tab Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_filter_bar_active_tab_text_color',
					'label'   => __( 'Active Tab Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_filter_bar_active_tab_border_radius',
					'label'   => __( 'Active Tab Border Radius', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_filter_bar_active_tab_width',
					'label'   => __( 'Active Tab Width', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_filter_bar_active_tab_height',
					'label'   => __( 'Active Tab Height', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_filter_bar_search_background_color',
					'label'   => __( 'Search Box Background Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_filter_bar_search_border_color',
					'label'   => __( 'Search Box Border Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_filter_bar_search_text_color',
					'label'   => __( 'Search Box Text Color', 'depc' ),
					'type'    => 'color',
				),
				array(
					'name'    => 'dpr_filter_bar_search_border_radius',
					'label'   => __( 'Search Box Border Radius', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_filter_bar_search_width',
					'label'   => __( 'Search Box Width', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
				array(
					'name'    => 'dpr_filter_bar_search_height',
					'label'   => __( 'Search Box Height', 'depc' ),
					'desc'   => __( '(px)', 'depc' ),
					'type'    => 'number',
				),
			),
			__( 'Custom_CSS', 'depc' )     => array(
				array(
					'name'    => 'dc_custom_css',
					'label'   => __( 'Custom CSS', 'depc' ),
					'type'    => 'textarea',
					'default' => ''
				),
			),
			__( 'Login_Register', 'depc' ) => array(
				array(
					'name'    => 'dc_quick_login',
					'label'   => __( 'Quick Login', 'depc' ),
					'desc'    => __( 'Quick login modal popup for guest users.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_quick_register',
					'label'   => __( 'Quick Register', 'depc' ),
					'desc'    => __('Quick register modal popup for guest users.', 'depc' ),
					'type'    => 'switch',
					'default' => 'on'
				),
				array(
					'name'    => 'dc_term_onoff',
					'label'   => __( 'Show/Hide Accept Terms Checkbox', 'depc' ),
					'desc'    => __( 'Show/Hide accept terms & conditions checkbox before quick register.', 'depc' ),
					'type'    => 'switch',
					'default' => 'off'
				),
				array(
					'name'    => 'dc_term_pages',
					'label'   => __( 'Terms and Conditions Page', 'depc' ),
					'desc'    => __( 'Select terms and conditions page for quick register.', 'depc' ),
					'type'    => 'select',
					'options' => $this->get_wp_pages()
				),
				array(
					'name'    => 'dc_social_login',
					'label'   => __( 'Enable/Disable Social Login', 'depc' ),
					'type'    => 'switch',
					'default' => 'off'
				),
				array(
					'name'      => 'dc_social_login_google',
					'label'     => __( 'Google', 'depc' ),
					'type'      => 'switch',
					'default'   => 'off',
					'condition' => [
						'dc_social_login' => 'on'
					]
				),
				array(
					'name'      => 'dc_social_login_google_client_id',
					'label'     => __( 'Client ID', 'depc' ),
					'desc'      => __('You need to create your own credentials in order to use google API.', 'depc'),
					'type'      => 'text',
					'default'   => '',
					'condition' => [
						'dc_social_login_google' => 'on'
					]
				),
				array(
					'name'      => 'dc_social_login_google_client_secret',
					'label'     => __( 'Client Secret', 'depc' ),
					'type'      => 'text',
					'default'   => '',
					'condition' => [
						'dc_social_login_google' => 'on'
					]
				),
				array(
					'name'      => 'dc_social_login_google_button_style',
					'label'     => __( 'Button Style', 'depc' ),
					'type'    => 'select',
					'options' => array(
						'dark'			=> 'Dark',
						'light'			=> 'Light'
					),
					'condition' => [
						'dc_social_login_google' => 'on'
					]
				),

				array(
					'name'      => 'dc_social_login_facebook',
					'label'     => __( 'Facebook', 'depc' ),
					'type'      => 'switch',
					'default'   => 'off',
					'condition' => [
						'dc_social_login' => 'on'
					]
				),
				array(
					'name'      => 'dc_social_login_facebook_app_id',
					'label'     => __( 'App ID', 'depc' ),
					'desc'      => __('You need to create your own credentials in order to use Facebook API.', 'depc'),
					'type'      => 'text',
					'default'   => '',
					'condition' => [
						'dc_social_login_facebook' => 'on'
					]
				),
				array(
					'name'      => 'dc_social_login_facebook_app_secret',
					'label'     => __( 'App Secret', 'depc' ),
					'type'      => 'text',
					'default'   => '',
					'condition' => [
						'dc_social_login_facebook' => 'on'
					]
				),
				array(
					'name'      => 'dc_social_login_twitter',
					'label'     => __( 'Twitter', 'depc' ),
					'type'      => 'switch',
					'default'   => 'off',
					'condition' => [
						'dc_social_login' => 'on'
					]
				),
				array(
					'name'      => 'dc_social_login_twitter_consumer_key',
					'label'     => __( 'Consumer Key (API Key)', 'depc' ),
					'desc'      => __('You need to create your own credentials in order to use twitter API.', 'depc'),
					'type'      => 'text',
					'default'   => '',
					'condition' => [
						'dc_social_login_twitter' => 'on'
					]
				),
				array(
					'name'      => 'dc_social_login_twitter_consumer_secret',
					'label'     => __( 'Consumer Secret (API Secret)', 'depc' ),
					'type'      => 'text',
					'default'   => '',
					'condition' => [
						'dc_social_login_twitter' => 'on'
					]
				),
				array(
					'name'      => 'dc_social_login_linkedin',
					'label'     => __( 'LinkedIn', 'depc' ),
					'type'      => 'switch',
					'default'   => 'off',
					'condition' => [
						'dc_social_login' => 'on'
					]
				),
				array(
					'name'      => 'dc_social_login_linkedin_client_id',
					'label'     => __( 'Client ID', 'depc' ),
					'desc'      => __('You need to create your own credentials in order to use linkedin API.', 'depc'),
					'type'      => 'text',
					'default'   => '',
					'condition' => [
						'dc_social_login_linkedin' => 'on'
					]
				),
				array(
					'name'      => 'dc_social_login_linkedin_client_secret',
					'label'     => __( 'Client Secret', 'depc' ),
					'type'      => 'text',
					'default'   => '',
					'condition' => [
						'dc_social_login_linkedin' => 'on'
					]
				),
			),
            __( 'Recaptcha', 'depc' ) => array(
				array(
					'name'    => 'dc_recaptcha_type',
					'label'   => __( 'Google reCAPTCHA<br/><small>Get Your Keys From <a href="https://www.google.com/recaptcha/admin/create" target="_blank">Here</a>.</small>', 'depc' ),
					'desc'    => __( 'Enable/disable Google reCAPTCHA for preventing robots activities.', 'depc' ),
					'type'    => 'select',
					'options' => array(
						'none'     => 'Disabled',
						'google'   => 'Enabled'
					)
				),
				array(
					'name'    => 'dc_recptcha_gsitekey',
					'label'   => __( 'Site Key', 'depc' ),
					'type'    => 'text',
					'condition' => [
						'dc_recaptcha_type' => 'google',
					]
				),
				array(
					'name'    => 'dc_recptcha_gsecretkey',
					'label'   => __( 'Secret Key', 'depc' ),
					'type'    => 'text',
					'condition' => [
						'dc_recaptcha_type' => 'google',
					]
				),
				array(
					'name'    => 'dc_recaptcha_addcm',
					'label'   => __( 'Enable for Guest Users', 'depc' ),
					'desc'    => __( 'If enabled, recaptcha whould be shown in comments form for guests.', 'depc' ),
					'type'    => 'switch',
					'condition' => [
						'dc_recaptcha_type' => 'google',
					]
				),
				array(
					'name'    => 'dc_recaptcha_theme',
					'label'   => __('Google reCAPTCHA Theme', 'depc' ),
					'type'    => 'select',
					'default' => 'light',
					'options' => array(
						'dark'     => 'Dark',
						'light'    => 'Light'
					),
					'condition' => [
						'dc_recaptcha_type' => 'google',
					]
				),
				array(
					'name'    => 'dc_recaptcha_size',
					'label'   => __('Google reCAPTCHA Size', 'depc' ),
					'type'    => 'select',
					'default' => 'normal',
					'options' => array(
						'compact'  => 'Compact',
						'normal'   => 'Normal'
					),
					'condition' => [
						'dc_recaptcha_type' => 'google',
					]
				),
			),
        );

        return $settings_fields;
	}
	/**
	* Get Categories
	*
	* @since     1.0.0
	*/
	public function get_categories() {
		$categories = [];
		foreach (get_categories() as $category) {
			$categories[$category->term_id] = $category->name;
		}

		return $categories;
	}


    function plugin_page() {
    	echo '
            <div id="wrap">
                <div class="container">
                <hr class="vertical-space5">
                    <div class="dpr-be-container">';

                	$this->settings_api->show_navigation();
                	$this->settings_api->show_forms();

    	echo '       </div>
                </div>
            </div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
    	$pages = get_pages();
    	$pages_options = array();
    	if ( $pages ) {
    		foreach ($pages as $page) {
    			$pages_options[$page->ID] = $page->post_title;
    		}
    	}

    	return $pages_options;
    }

	/**
	 * Creates the markup for the Settings page
	 *
	 * @since    1.0.0
	 */
	public function markup_settings_page() {

		if ( current_user_can( static::REQUIRED_CAPABILITY ) ) {

			echo static::render_template(
				'page-settings/page-settings.php',
				array(
					'page_title' 	=> Depc_Core::DEPC_NAME,
					'settings_name' => Depc_Model_Admin_Settings::SETTINGS_NAME
					)
				);

		} else {

			wp_die( __( 'Access denied.' ) );

		}

	}

	/**
	 * Adds the section introduction text to the Settings page
	 *
	 * @param array $section
	 *
	 * @since    1.0.0
	 */
	public function markup_section_headers( $section ) {

		echo static::render_template(
			'page-settings/page-settings-section-headers.php',
			array(
				'section'      => $section,
				'text_example' => __( 'This is a text example for section header',Depc_Core::DEPC_ID )
				)
			);

	}

	/**
	 * Delivers the markup for settings fields
	 *
	 * @param array $args
	 *
	 * @since    1.0.0
	 */
	public function markup_fields( $field_args ) {

		$field_id = $field_args['id'];
		$settings_value = static::get_model()->get_settings( $field_id );

		echo static::render_template(
			'page-settings/page-settings-fields.php',
			array(
				'field_id'       => esc_attr( $field_id ),
				'settings_name'  => Depc_Model_Admin_Settings::SETTINGS_NAME,
				'settings_value' => ! empty( $settings_value ) ? esc_attr( $settings_value ) : ''
				),
			'always'
			);

	}


	/**
	 * Delivers wordpress pages
	 *
	 * @param array $args
	 *
	 * @since    1.0.0
	 */
	public function get_wp_pages() {

		$pages = get_pages();
		$page_array = array();

		foreach ( $pages as $page ) {

			$page_array[$page->ID] = $page->post_title;
		}

		return $page_array ;

	}

}
