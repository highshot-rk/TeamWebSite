<?php
class Depc_Model_Admin_Profile extends Depc_Model_Admin {

	
	
	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {
		$this->register_hook_callbacks();
	}

	public function register_hook_callbacks() {

		Depc_Actions_Filters::add_action( 'show_user_profile', $this, 'render_callback' );
		Depc_Actions_Filters::add_action( 'edit_user_profile', $this, 'render_callback' );
		Depc_Actions_Filters::add_action( 'profile_update', $this, 'update' );

	}

	/**
	 * Show custom user profile fields
	 * @param  obj $user The user object.
	 * @return void
	 */
	public function render_callback( $user ) {
		?>
			<script>
				jQuery(document).ready(function($) {
                //Initiate Color Picker
                $('.wp-color-picker-field').wpColorPicker();
            });
			</script>
			<table class="form-table">
				<tr>
					<th>
						<label for="dpr_avatar_color"><?php _e( 'Deeper Comment Avatar Color', 'depc' ); ?></label>
					</th>
					<td>
						<input type="text" class="dpr-avatar-color wp-color-picker-field" id="dpr_avatar_color" name="dpr_avatar_color" value="<?php echo esc_attr( get_the_author_meta( 'dpr_comment_color', $user->ID ) ); ?>" data-default-color="" />

						<br><span class="description"><?php _e('This color will be used when the admin is enabled to get the avatar color from user settings.', 'depc'); ?></span>
					</td>
				</tr>
			</table>

		<?php
	}

	public function update ($user_id) {
		if ( isset( $_POST['dpr_avatar_color'] ) )
			update_user_meta( $user_id, 'dpr_comment_color', $_POST['dpr_avatar_color'] );

	}

}