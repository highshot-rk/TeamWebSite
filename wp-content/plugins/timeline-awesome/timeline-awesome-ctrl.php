<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Block;

Container::make( 'post_meta', 'side_shortcode', esc_html__( 'Shortcode', 'timeline-awesome' ) )
	->where( 'post_type', '=', 'timeline-awesome' )
	->set_context( 'side' )
	->set_priority( 'default' )
	->add_fields( array(

	Field::make( 'html', 'timeline_style', esc_html__( 'Section Description', 'timeline-awesome' ) )
		->set_html( sprintf( '<div class="shortcode-wrap-ta"><code id="shortcode_to_copy"></code></div>', esc_html__( 'Here, you can add some useful description for the fields below / above this text.', 'timeline-awesome' ) ) ),
));
