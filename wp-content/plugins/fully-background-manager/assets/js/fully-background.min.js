jQuery( document ).ready( function( $ ) {

	/* === Begin color picker JS. === */

	/* Add the WordPress color picker to our custom color input. */
	$( '.fbm-wp-color-picker' ).wpColorPicker();

	/* Hide the "Color" label. */
	$( 'label[for="fbm-background-color"]' ).hide();

	/* === End color picker JS. === */

	/* === Begin background image JS. === */

	/* If the background <img> source has a value, show it.  Otherwise, hide. */
	if ( $( '.fbm-background-image-url' ).attr( 'src' ) ) {
		$( '.fbm-background-image-url' ).show();
	} else {
		$( '.fbm-background-image-url' ).hide();
	}

	/* If there's a value for the background image input. */
	if ( $( 'input#fbm-background-image' ).val() ) {

		/* Hide the 'set background image' link. */
		$( '.fbm-add-media-text' ).hide();

		/* Show the 'remove background image' link, the image, and extra options. */
		$( '.fbm-remove-media, .fbm-background-image-options, .fbm-background-image-url' ).show();
	}

	/* Else, if there's not a value for the background image input. */
	else {

		/* Show the 'set background image' link. */
		$( '.fbm-add-media-text' ).show();

		/* Hide the 'remove background image' link, the image, and extra options. */
		$( '.fbm-remove-media, .fbm-background-image-options, .fbm-background-image-url' ).hide();
	}

	/* When the 'remove background image' link is clicked. */
	$( '.fbm-remove-media' ).click(
		function( j ) {

			/* Prevent the default link behavior. */
			j.preventDefault();

			/* Set the background image input value to nothing. */
			$( '#fbm-background-image' ).val( '' );

			/* Show the 'set background image' link. */
			$( '.fbm-add-media-text' ).show();

			/* Hide the 'remove background image' link, the image, and extra options. */
			$( '.fbm-remove-media, .fbm-background-image-url, .fbm-background-image-options' ).hide();
		}
	);

	/**
	 * The following code deals with the custom media modal frame.  It is a modified version 
	 * of Thomas Griffin's New Media Image Uploader example plugin.
	 *
	 * @link      https://github.com/thomasgriffin/New-Media-Image-Uploader
	 * @license   http://www.opensource.org/licenses/gpl-license.php
	 * @author    Thomas Griffin <thomas@thomasgriffinmedia.com>
	 * @copyright Copyright 2013 Thomas Griffin
	 */

	// Prepare the variable that holds our custom media manager.
	var fbm_custom_backgrounds_frame;

	/* When the 'set background image' link is clicked. */
	$( '.fbm-add-media' ).click( 

		function( j ) {

			/* Prevent the default link behavior. */
			j.preventDefault();

			// If the frame already exists, open it.
			if ( fbm_custom_backgrounds_frame ) {
				fbm_custom_backgrounds_frame.open();
				return;
			}

			// Create the media frame.
			fbm_custom_backgrounds_frame = wp.media.frames.fbm_custom_backgrounds_frame = wp.media( 
				{

					// We can pass in a custom class name to our frame.
					className: 'media-frame fbm-custom-background-extended-frame',

					// Frame type ('select' or 'post').
					frame: 'select',

					// Whether to allow multiple images
					multiple: false,

					// Custom frame title.
					title: fbm_custom_backgrounds.title,

					// Media type allowed.
					library: {
						type: 'image'
					},

					// Custom "insert" button.
					button: {
						text:  fbm_custom_backgrounds.button
					}
				}
			);

			// Do stuff with the data when an image has been selected.
			fbm_custom_backgrounds_frame.on( 'select', 

				function() {

					// Construct a JSON representation of the model.
					var media_attachment = fbm_custom_backgrounds_frame.state().get( 'selection' ).first().toJSON();

					// Send the attachment ID to our custom input field via jQuery.
					$( '#fbm-background-image').val( media_attachment.id );
					$( '.fbm-background-image-url' ).attr( 'src', media_attachment.url );
					$( '.fbm-add-media-text' ).hide();

					$( '.fbm-background-image-url, .fbm-remove-media, .fbm-background-image-options' ).show();
				}
			);

			// Open up the frame.
			fbm_custom_backgrounds_frame.open();
		}
	);

	/* === End background image JS. === */

});
