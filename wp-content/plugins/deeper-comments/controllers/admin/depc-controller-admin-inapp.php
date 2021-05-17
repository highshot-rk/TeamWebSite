<?php
/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Controller_Admin_Inapp extends Depc_Controller_Admin {

	// class instance
	static $instance;

	// inappropriate table class
	public $inapp_obj;

	// class constructor
	public function __construct() {
		$this->register_hook_callbacks();
	}

	protected function register_hook_callbacks() {

		Depc_Actions_Filters::add_filter( 'set-screen-option', __CLASS__, 'set_screen' , 10, 3 );
		Depc_Actions_Filters::add_action( 'admin_menu', $this, 'plugin_menu' , 11 );
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function plugin_menu() {
		global $submenu, $menu;

		$hook = add_submenu_page( 'deeper_intro', 'Inappropriate Comments', 'Inappropriate C.M ', 'moderate_comments', 'deeper_inapp_cm', array ( $this, 'render_callback' ) );

		add_action( "load-$hook", array( $this, 'screen_option' ) );

		// get count of inappropriate comment
		$inapp_count = self::comment_count();

		if ( $inapp_count ) {
			// inappropriate count in submenu
			foreach ( $submenu as $keys => $values ) {

				if (  isset( $submenu[$keys][1][2] ) && $submenu[$keys][1][2] == 'deeper_inapp_cm' ) {

					$submenu[$keys][1][0] .= ' <span class="dpr-bubble"><span>' . $inapp_count . '</span></span>';

					return;

				}

			}

		}

	}

	/**
	* Screen options
	*/
	public function screen_option() {

		$option = 'per_page';
		$args   = array(
		'label'   => 'Comments',
		'default' => 10,
		'option'  => 'inapp_comments_per_page'
		);

		add_screen_option( $option, $args );

		$this->inapp_obj = new Depc_Model_Admin_Inapp();
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function comment_count() {
		$args = array(
			'meta_key' => 'dpr_inapporpriate_user',
			'count' => true
			);
		$count = get_comments($args);
		return $count;
	}

	/**
	* list of inapp cm
	*/
	public function render_callback() {
		?>
		<div class="wrap">
			<h2><?php esc_attr_e( 'Inappropriate Comments', 'depc' ); ?></h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->inapp_obj->prepare_items();
								$this->inapp_obj->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
		<?php
	}

}
