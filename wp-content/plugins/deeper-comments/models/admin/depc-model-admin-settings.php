<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Model_Admin_Settings extends Depc_Model_Admin {

	protected static $settings;

	const SETTINGS_NAME = Depc_Core::DEPC_ID;



    /**
    * Constructor
    *
    * @since    1.0.0
    */
    protected function __construct() {

        $this->register_hook_callbacks();
    }

    /**
    * Register callbacks for actions and filters
    *
    * @since    1.0.0
    */
    public function register_hook_callbacks() {

        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'wp_ajax_update_options',  array( $this, 'update_options' ) );
        add_action( 'wp_ajax_reset_options',  array( $this, 'reset_options' ) );
        add_action( 'wp_ajax_reset_all_options',  array( $this, 'reset_all_options' ) );
        add_filter( 'get_comment_excerpt',  array( $this, 'get_comment_excerpt' ) , 100 , 1);
        add_filter( 'comment_text',  array( $this, 'comment_text' ) , 100 , 1);
    }

    /**
     * settings sections array
     *
     * @var array
     */
    protected $settings_sections = array();

    /**
     * Settings fields array
     *
     * @var array
     */
    protected $settings_fields = array();

    /**
     * Settings fields array
     *
     * @var array
     */
    protected $child_settings_fields = array();

    /**
     * Enqueue scripts and styles
     */
    function admin_enqueue_scripts() {
     wp_enqueue_style( 'wp-color-picker' );
     wp_enqueue_media();
     wp_enqueue_script( 'wp-color-picker' );
     wp_enqueue_script( 'jquery' );
    }

    /**
     * Set settings sections
     *
     * @param array   $sections setting sections array
     */
    function set_sections( $sections ) {
     $this->settings_sections = $sections;

     return $this;
    }

    /**
     * Get Comment Excerpt
     *
     * @param array   $sections setting sections array
     */
    function get_comment_excerpt( $excerpt ) {
        $excerpt = wp_kses(html_entity_decode( $excerpt ), [
            'pre' => array('class'=>array()),
            'h2' => array(),
            'h3' => array(),
            'h4' => array(),
            'h5' => array(),
            'h6' => array(),
            'ul' => array(),
            'ol' => array(),
            'li' => array(),
            'p' => array('class'=>array()),
            'br' => array(),
            'code' => array(),
            'a' => array('href' => [] , 'target' => [], 'title' => [], 'rel' => []),
        ]);
        return $excerpt;
    }

    /**
     * Comment Text
     *
     * @param array   $sections setting sections array
     */
    function comment_text( $comment_text ) {
        $comment_text = wp_kses(html_entity_decode( $comment_text ), [
            'pre' => array('class'=>array()),
            'h2' => array(),
            'h3' => array(),
            'h4' => array(),
            'h5' => array(),
            'h6' => array(),
            'ul' => array(),
            'ol' => array(),
            'li' => array(),
            'p' => array('class'=>array()),
            'br' => array(),
            'code' => array(),
            'a' => array('href' => [] , 'target' => [], 'title' => [], 'rel' => []),
        ]);
        return $comment_text;
    }

    /**
     * Add a single section
     *
     * @param array   $section
     */
    function add_section( $section ) {
     $this->settings_sections[] = $section;

     return $this;
    }

    /**
     * Set settings fields
     *
     * @param array   $fields settings fields array
     */
    function set_fields( $fields ) {
     $this->settings_fields = $fields;

     return $this;
    }

    /**
     * Set settings fields
     *
     * @param array   $fields settings fields array
     */
    function child_set_fields( $fields ) {
     $this->child_settings_fields = $fields;

     return $this;
    }

    function add_field( $section, $field ) {
     $defaults = array(
      'name'  => '',
      'label' => '',
      'desc'  => '',
      'condition'  => '',
      'type'  => 'text'
      );

     $arg = wp_parse_args( $field, $defaults );
     $this->settings_fields[$section][] = $arg;

     return $this;
    }

    /**
     * Initialize and registers the settings sections and fileds to WordPress
     */
    function admin_init() {

        //register settings sections
    	foreach ( $this->settings_sections as $section => $sec ) {

    		if ( false == get_option( $sec['id'] ) ) {
    			add_option( $sec['id'] );
    		}

    		// should be delete
    		// if ( isset($section['desc']) && !empty($section['desc']) ) {
    		// 	$section['desc'] = '<div class="inside">' . $section['desc'] . '</div>';
    		// 	$callback = create_function('', 'echo "' . str_replace( '"', '\"', $section['desc'] ) . '";');
    		// } else if ( isset( $section['callback'] ) ) {
    		// 	$callback = $section['callback'];
    		// } else {
    		// 	$callback = null;
    		// }

    		if ( isset( $sec['submenu'] ) && !is_null( $sec['submenu'] ) ) {
    			foreach ( $sec['submenu'] as $subsec ) {
    				add_settings_section( $subsec, $subsec, null, $subsec );

    				if ( false == get_option(  $sec['submenu']['id'] ) ) {
    					add_option(  $sec['submenu']['id'] );
    				}

    			}
    		}
    		add_settings_section( $sec['id'], $sec['title'], null, $sec['id'] );
    	}

     	//register settings fields
    	foreach ( $this->settings_fields as $section => $field ) {
    		foreach ( $field as $option ) {

    			$name = $option['name'];
    			$type = isset( $option['type'] ) ? $option['type'] : 'text';
    			$label = isset( $option['label'] ) ? $option['label'] : '';
    			$callback = isset( $option['callback'] ) ? $option['callback'] : array( $this, 'callback_' . $type );
    			$args = array(
    				'id'                => $name,
    				'class'             => isset( $option['class'] ) ? $option['class'] : $name,
    				'label_for'         => "{$section}[{$name}]",
                    'desc'              => isset( $option['desc'] ) ? $option['desc'] : '',
                    'condition'         => isset( $option['condition'] ) ? $option['condition'] : '',
    				'name'              => $label,
    				'section'           => $section,
    				'size'              => isset( $option['size'] ) ? $option['size'] : null,
    				'options'           => isset( $option['options'] ) ? $option['options'] : '',
    				'std'               => isset( $option['default'] ) ? $option['default'] : '',
    				'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
    				'type'              => $type,
    				'placeholder'       => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
    				'min'               => isset( $option['min'] ) ? $option['min'] : '',
    				'max'               => isset( $option['max'] ) ? $option['max'] : '',
    				'step'              => isset( $option['step'] ) ? $option['step'] : '',
    				);

    			add_settings_field( "{$section}[{$name}]", $label, $callback, $section, $section, $args );
    		}
    	}

  		//register child settings fields
    	foreach ( $this->child_settings_fields as $childsection => $childfield ) {

    		foreach ( $childfield as $option ) {

    			$name = $option['name'];
    			$type = isset( $option['type'] ) ? $option['type'] : 'text';
    			$label = isset( $option['label'] ) ? $option['label'] : '';
    			$callback = isset( $option['callback'] ) ? $option['callback'] : array( $this, 'callback_' . $type );
    			$args = array(
    				'id'                => $name,
    				'class'             => isset( $option['class'] ) ? $option['class'] : $name,
    				'label_for'         => "{$childsection}[{$name}]",
    				'desc'              => isset( $option['desc'] ) ? $option['desc'] : '',
    				'condition'         => isset( $option['condition'] ) ? $option['condition'] : '',
    				'name'              => $label,
    				'section'           => $childsection,
    				'size'              => isset( $option['size'] ) ? $option['size'] : null,
    				'options'           => isset( $option['options'] ) ? $option['options'] : '',
    				'std'               => isset( $option['default'] ) ? $option['default'] : '',
    				'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
    				'type'              => $type,
    				'placeholder'       => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
    				'min'               => isset( $option['min'] ) ? $option['min'] : '',
    				'max'               => isset( $option['max'] ) ? $option['max'] : '',
    				'step'              => isset( $option['step'] ) ? $option['step'] : '',
    				);

    			add_settings_field( "{$childsection}[{$name}]", $label, $callback, $childsection, $childsection, $args );
    		}
    	}

  		// creates our settings in the options table
    	foreach ( $this->settings_sections as $section ) {
    		register_setting( $section['id'], $section['id'], array( $this, 'sanitize_options' ) );
    	}
    	// create setting child in table options
		foreach ( $this->settings_sections as $section ) {
			if ( isset( $section['submenu'] ) ) {
				foreach ($section['submenu'] as $submenu) {
					register_setting( $submenu, $submenu, array( $this, 'sanitize_options' ) );
				}
			}
		}

    }

    /**
     * Get field description for display
     *
     * @param array   $args settings field args
     */
    public function get_field_description( $args ) {
     if ( ! empty( $args['desc'] ) ) {
      $desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
     } else {
      $desc = '';
     }

     return $desc;
    }

    /**
     * Get field description for display
     *
     * @param array   $args settings field args
     */
    public function get_condition( $args ) {
     if ( ! empty( $args['condition'] ) ) {

        $condition = '<script>';
        foreach ($args['condition'] as $key => $value) {
            $condition .= "if(jQuery('[name=\"". $args['section'] ."[$key]\"]').last().attr('type') == 'checkbox'){";
                $condition .= "if( jQuery('[name=\"". $args['section'] ."[$key]\"]').last().is(':checked') ){";
                    $condition .= "if( jQuery('[name=\"". $args['section'] ."[$key]\"]').last().val() != '$value' )";
                    $condition .= "{jQuery('tr.".$args['id']."').hide();} else {jQuery('tr.".$args['id']."').show();}";
                $condition .= "} else {";
                    $condition .= "if( jQuery('[name=\"". $args['section'] ."[$key]\"]').first().val() != '$value' )";
                    $condition .= "{jQuery('tr.".$args['id']."').hide();} else {jQuery('tr.".$args['id']."').show();}}";
            $condition .= "} else {";
            $condition .= "if( jQuery('[name=\"". $args['section'] ."[$key]\"]').last().val() != '$value' )";
            $condition .= "{jQuery('tr.".$args['id']."').hide();} else {jQuery('tr.".$args['id']."').show();}}";

            $condition .= "jQuery(document).on('input', '[name=\"". $args['section'] ."[$key]\"]', function() {";
                $condition .= "if(jQuery(this).attr('type') == 'checkbox'){";
                    $condition .= "if( jQuery(this).is(':checked') ){";
                        $condition .= "if( jQuery(this).val() != '$value' )";
                        $condition .= "{jQuery('tr.".$args['id']."').hide();} else {jQuery('tr.".$args['id']."').show();}";
                    $condition .= "} else {";
                        $condition .= "if( jQuery('[name=\"". $args['section'] ."[$key]\"]').first().val() != '$value' )";
                        $condition .= "{jQuery('tr.".$args['id']."').hide();} else {jQuery('tr.".$args['id']."').show();}}";
                $condition .= "} else {";
                $condition .= "if( jQuery(this).val() != '$value' )";
                $condition .= "{jQuery('tr.".$args['id']."').hide();} else {jQuery('tr.".$args['id']."').show();}}";
            $condition .= "});";
        }
        $condition .= '</script>';
     } else {
      $condition = '';
     }
     return $condition;
    }

    /**
     * Displays a text field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_text( $args ) {

     $value       = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
     $size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
     $type        = isset( $args['type'] ) ? $args['type'] : 'text';
     $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';

     $html        = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );
     $html       .= $this->get_field_description( $args );
     $html       .= $this->get_condition($args);

     echo $html;
    }

    /**
     * Displays a url field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_url( $args ) {
     $this->callback_text( $args );
    }

    /**
     * Displays a number field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_number( $args ) {
     $value       = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
     $size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
     $type        = isset( $args['type'] ) ? $args['type'] : 'number';
     $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
     $min         = empty( $args['min'] ) ? '' : ' min="' . $args['min'] . '"';
     $max         = empty( $args['max'] ) ? '' : ' max="' . $args['max'] . '"';
     $step        = empty( $args['max'] ) ? '' : ' step="' . $args['step'] . '"';

     $html        = sprintf( '<input type="%1$s" class="%2$s-number" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s%7$s%8$s%9$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder, $min, $max, $step );
     $html       .= $this->get_field_description( $args );
     $html       .= $this->get_condition($args);

     echo $html;
    }

    /**
     * Displays a checkbox for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_checkbox( $args ) {

     $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

     $html  = '<fieldset>';
     $html  .= sprintf( '<label for="dpr-%1$s[%2$s]">', $args['section'], $args['id'] );
     $html  .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );
     $html  .= sprintf( '<input type="checkbox" class="checkbox" id="dpr-%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', $args['section'], $args['id'], checked( $value, 'on', false ) );
     $html  .= sprintf( '%1$s</label>', $args['desc'] );
     $html  .= '</fieldset>';
     $html       .= $this->get_condition($args);

     echo $html;
    }

    /**
     * Displays a checkbox for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_switch( $args ) {


     $value = $this->get_option( $args['id'], $args['section'], $args['std']);
     $value = esc_attr( (is_array($value) && isset( $value['on'] ) ? $value['on'] : $value));

     $html  = '<fieldset>';
     $html  .= sprintf( '<label for="dpr-%1$s[%2$s]" class="dpr-switch">', $args['section'], $args['id'] );
     $html  .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );
     $html  .= sprintf( '<input type="checkbox" class="checkbox" id="dpr-%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', $args['section'], $args['id'], checked( $value, 'on', false ) );
     $html  .= '<span class="dpr-slider dpr-round"></span></label>';
     $html  .= sprintf( '<p>%1$s</p>', $args['desc'] );
     $html  .= '</fieldset>';
     $html   .= $this->get_condition($args);

     echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @param array   $args settings field args
     */
    function callback_multicheck( $args ) {

     $value = $this->get_option( $args['id'], $args['section'], $args['std'] );
     $html  = '<fieldset>';
     $html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="" />', $args['section'], $args['id'] );
     foreach ( $args['options'] as $key => $label ) {
      $checked = isset( $value[$key] ) ? $value[$key] : '0';
      $html    .= sprintf( '<label for="dpr-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
      $html    .= sprintf( '<input type="checkbox" class="checkbox" id="dpr-%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $checked, $key, false ) );
      $html    .= sprintf( '%1$s</label><br>',  $label );
     }

     $html .= $this->get_field_description( $args );
     $html .= '</fieldset>';
     $html       .= $this->get_condition($args);

     echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @param array   $args settings field args
     */
    function callback_radio( $args ) {

     $value = $this->get_option( $args['id'], $args['section'], $args['std'] );
     $html  = '<fieldset>';

     foreach ( $args['options'] as $key => $label ) {
      $html .= sprintf( '<label for="dpr-%1$s[%2$s][%3$s]">',  $args['section'], $args['id'], $key );
      $html .= sprintf( '<input type="radio" class="radio" id="dpr-%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
      $html .= sprintf( '%1$s</label><br>', $label );
     }

     $html .= $this->get_field_description( $args );
     $html .= '</fieldset>';
     $html       .= $this->get_condition($args);

     echo $html;
    }

    /**
     * Displays a selectbox for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_select( $args ) {

     $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
     $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
     $html  = sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );

     foreach ( $args['options'] as $key => $label ) {
      $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
     }

     $html .= sprintf( '</select>' );
     $html .= $this->get_field_description( $args );
     $html       .= $this->get_condition($args);

     echo $html;
    }

    /**
     * Displays a textarea for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_textarea( $args ) {

     $value       = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
     $size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
     $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="'.$args['placeholder'].'"';

     $html        = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]"%4$s>%5$s</textarea>', $size, $args['section'], $args['id'], $placeholder, $value );
     $html        .= $this->get_field_description( $args );
     $html       .= $this->get_condition($args);

     echo $html;
    }

    /**
     * Displays a textarea for a settings field
     *
     * @param array   $args settings field args
     * @return string
     */
    function callback_html( $args ) {
     echo $this->get_field_description( $args );
    }

    /**
     * Displays a rich text textarea for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_wysiwyg( $args ) {

     $value = $this->get_option( $args['id'], $args['section'], $args['std'] );
     $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : '500px';

     echo '<div style="max-width: ' . $size . ';">';

     $editor_settings = array(
      'teeny'         => true,
      'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
      'textarea_rows' => 10
      );

     if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
      $editor_settings = array_merge( $editor_settings, $args['options'] );
     }

     wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );

     echo '</div>';

     echo $this->get_field_description( $args );
    }

    /**
     * Displays a file upload field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_file( $args ) {

     $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
     $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
     $id    = $args['section']  . '[' . $args['id'] . ']';
     $label = isset( $args['options']['button_label'] ) ? $args['options']['button_label'] : __( 'Choose File' );

     $html  = sprintf( '<input type="text" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
     $html  .= '<input type="button" class="button wpsa-browse" value="' . $label . '" />';
     $html  .= $this->get_field_description( $args );
     $html       .= $this->get_condition($args);

     echo $html;
    }

    /**
     * Displays a password field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_password( $args ) {

     $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
     $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

     $html  = sprintf( '<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
     $html  .= $this->get_field_description( $args );
     $html       .= $this->get_condition($args);

     echo $html;
    }

    /**
     * Displays a color picker field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_color( $args ) {

     $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
     $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

     $html  = sprintf( '<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, $args['section'], $args['id'], $value, $args['std'] );
     $html  .= $this->get_field_description( $args );
     $html       .= $this->get_condition($args);

     echo $html;
    }

    /**
     * Sanitize callback for Settings API
     *
     * @return mixed
     */
    function sanitize_options( $options ) {

     if ( !$options ) {
      return $options;
     }

     foreach( $options as $option_slug => $option_value ) {
      $sanitize_callback = $this->get_sanitize_callback( $option_slug );

            // If callback is set, call it
      if ( $sanitize_callback ) {
       $options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
       continue;
      }
     }

     return $options;
    }

    /**
     * Get sanitization callback for given option slug
     *
     * @param string $slug option slug
     *
     * @return mixed string or bool false
     */
    function get_sanitize_callback( $slug = '' ) {
     if ( empty( $slug ) ) {
      return false;
     }

        // Iterate over registered fields and see if we can find proper callback
     foreach( $this->settings_fields as $section => $options ) {
      foreach ( $options as $option ) {
       if ( $option['name'] != $slug ) {
        continue;
       }

                // Return the callback name
       return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
      }
     }

     return false;
    }

    /**
     * Get the value of a settings field
     *
     * @param string  $option  settings field name
     * @param string  $section the section name this field belongs to
     * @param string  $default default text if it's not found
     * @return string
     */
    function get_option( $option, $section, $default = '' ) {

     $options = get_option( $section );

     if ( isset( $options[$option] ) ) {
      return $options[$option];
     }

     return $default;
    }

    /**
     * Show navigations as tab
     *
     * Shows all the settings section labels as tab
     */
    function show_navigation() {
    ?>
     <div class="dpr-be-sidebar">
            <ul class="dpr-be-group-menu">
    <?php foreach ( $this->settings_sections as $tab ): ?>
       <li class="dpr-be-group-menu-li dpr-be-group-menu-parent-li <?php echo isset( $tab['submenu'] ) && !is_null( $tab['submenu'] ) ? 'has-sub' : ''; ?>">

                    <a href="#<?php echo $tab['id']; ?>" id="<?php echo $tab['id']; ?>" class="dpr-be-group-tab-link-a">
                     <?php if ( isset( $tab['submenu'] ) && !is_null( $tab['submenu'] ) ) : ?>
                     <span class="extra-icon">
                         <i class="sl-arrow-down"></i>
                     </span>
                  <?php endif; ?>
                     <i class="<?php echo isset( $tab['icon'] ) ? $tab['icon'] : '' ; ?>"></i>
                     <span class="dpr-be-group-menu-title"> <?php echo $tab['title']; ?> </span>
                    </a>
                    <?php if( isset( $tab['submenu'] ) && !is_null( $tab['submenu'] ) ): ?>
                    <ul id="" class="subsection" style="display: block;">
                     <?php foreach( $tab['submenu'] as $subitem ): ?>
                     <li id="" class="dpr-be-group-menu-li dpr-be-group-menu-child-li">
                      <a href="" id="<?php echo $subitem; ?>" class="dpr-be-group-tab-link-a">
                       <span class="pr-be-group-menu-title"><?php echo str_replace( '_' , ' ' , $subitem); ?></span>
                      </a>
                     </li>
                    <?php endforeach; ?>
                    </ul>
                 <?php endif; ?>
                </li>
             <?php endforeach; ?>
            </ul>
        </div>
 <?php
    }

    /**
     * Show the section settings forms
     *
     * This function displays every sections in a different form
     */
    function show_forms() {
     ?>


        <div class="dpr-be-main">
            <div id="dpr-be-infobar">
                <a href="" id="" class="dpr-btn dpr-save-btn"><?php esc_html_e('Save Changes','depc') ?></a>
                <a href="" id="" class="dpr-btn dpr-reset-btn"><?php esc_html_e('Reset Section','depc') ?></a>
                <!-- <a href="" id="" class="dpr-btn dpr-reset-all-btn">Reset All</a> -->
            </div>
            <div class="dpr-be-notification"></div>

            <?php foreach ( $this->settings_sections as $form ) : ?>

             <div class="dpr-be-content" data-mode="ncurrent">
              <div class="dpr-be-group-tab">
               <div id="<?php echo $form['id']; ?>" class="group" >
                <form method="post" action="options.php" class="depc_save_ajax">
                 <?php
                 do_action( 'wsa_form_top_' . $form['id'], $form );
                 settings_fields( $form['id'] );
                 do_settings_sections( $form['id'] );
                 do_action( 'wsa_form_bottom_' . $form['id'], $form );
                 if ( isset( $this->settings_fields[ $form['id'] ] ) ):
                  ?>
                 <?php submit_button(); ?>
                <?php endif; ?>
                </form>
               </div>
              </div>
             </div>
        <?php endforeach; ?>

        <?php foreach ( $this->settings_sections as $form ) : ?>

            <?php if ( isset( $form['submenu'] ) && $form['submenu'] ) : ?>
              <?php foreach ( $form['submenu'] as $subkey => $submenu ) : ?>

              <div class="dpr-be-content" data-mode="ncurrent">
               <div class="dpr-be-group-tab">
                <div id="<?php echo $submenu; ?>" class="group" >
                 <form method="post" action="options.php" class="depc_save_ajax">
                  <?php
                  do_action( 'wsa_form_top_' . $form['id'], $form );
                  settings_fields( $submenu );
                  do_settings_sections( $submenu );
                  do_action( 'wsa_form_bottom_' . $submenu, $form );
                  if ( isset( $this->child_settings_fields[ $submenu ] ) ): ?>
                   <?php submit_button(); ?>
                  <?php endif; ?>
                 </form>
                </div>
               </div>
              </div>
          <?php endforeach; ?>

         <?php endif; ?>

        <?php endforeach; ?>

            <div id="dpr-be-footer">
                <a href="" id="" class="dpr-btn dpr-save-btn"><?php esc_html_e('Save Changes','depc') ?></a>
                <a href="" id="" class="dpr-btn"><?php esc_html_e('Reset Section','depc') ?></a><a href="" id="dpr-reset-all-settings" class="dpr-btn"><?php esc_html_e('Reset All','depc') ?></a>
            </div>
        </div>

    <?php
    $this->script();
}

    /**
     * Tabbable JavaScript codes & Initiate Color Picker
     *
     * This code uses localstorage for displaying active tabs
     */
    function script() {
     ?>
     <script>
      jQuery(document).ready(function($) {
                //Initiate Color Picker
                $('.wp-color-picker-field').wpColorPicker();

                // show active tab
                $('.dpr-be-content').hide();
                $('.dpr-be-content:nth-child(6)').show();
                $('.dpr-be-content:nth-child(6)').attr('data-mode', 'current');
                // $('.dpr-be-group-menu-parent-li .dpr-be-group-menu-child-li').first().addClass('active');
                $('#disscustion_settings').parent().trigger('click');
                $( '.dpr-be-content #Comments' ).find('h2').text( findAndReplace( 'Comments' , '_' , ' ' ) );
                // @need
                $('.dpr-be-group-menu-parent-li a').on('click', function(event) {
                    event.preventDefault();
                });

                // Switches option sections
                $('.dpr-be-group-menu-child-li a').click(function(e) {
                	e.preventDefault();

                    var $thisid = $(this).attr('id');
                    var $this = $(this).parent();

					// remove active and hide
					$('.dpr-be-content').hide();
                    $('.dpr-be-content' ).attr('data-mode', 'ncurrent');
					$('.dpr-be-group-menu-parent-li , .dpr-be-group-menu-child-li').removeClass('active');

					// show and fade in
					$('.dpr-be-content #' + $thisid ).parent().parent().show();
                    $('.dpr-be-content #' + $thisid ).parent().parent().attr('data-mode', 'current');
					$this.addClass('active');

                    var name = $( '.dpr-be-content #' + $thisid ).find('h2').text();
                    $( '.dpr-be-content #' + $thisid ).find('h2').text( findAndReplace( name , '_' , ' ' ) );


					// check if child add class to parent too
					if ( $this.parent().parent().hasClass('dpr-be-group-menu-parent-li') ) {
						$this.parent().parent().addClass('active');
					}
//s
                 // set active tab
                 // localStorage.setItem( 'dpactivetab', JSON.stringify($(this)) );

                });


                $('.wpsa-browse').on('click', function (event) {
                 event.preventDefault();

                 var self = $(this);

                    // Create the media frame.
                    var file_frame = wp.media.frames.file_frame = wp.media({
                     title: self.data('uploader_title'),
                     button: {
                      text: self.data('uploader_button_text'),
                     },
                     multiple: false
                    });

                    file_frame.on('select', function () {
                     attachment = file_frame.state().get('selection').first().toJSON();
                     self.prev('.wpsa-url').val(attachment.url).change();
                    });

                    // Finally, open the modal
                    file_frame.open();
                });

            });
            function findAndReplace(string, target, replacement) {

                   var i = 0, length = string.length;

                   for (i; i < length; i++) {

                     string = string.replace(target, replacement);

                 }

                 return string;

            }

        </script>
        <?php
        $this->_style_fix();
    }
    /**
     * Find String
     */
    public function find_string($string, $start, $end = '' ){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function update_options() {


        // check nonce
        check_ajax_referer( 'dpr_admin_nonce', 'security' );
        $old_opts = get_option( $_POST['form'][0]['value'] );
        $new_opts = array();
        // get options after save action
        for ( $i=0; $i < 1 ; $i++ ) {
            for ( $j=0; $j < sizeof( $_POST['form'] ); $j++ ) {

                // get opts names
                if ( $j > 3 ) {

                    $key = $this->find_string( $_POST['form'][$j]['name'], '[', ']' );
                    $val = $_POST['form'][$j]['value'];
                    if(isset( $new_opts[$key] )) {
                        if(!is_array($new_opts[$key])) {
                            $_val = $new_opts[$key];
                            $new_opts[$key] = [];
                            $new_opts[$key][$val] = $_val;
                        }
                        $new_opts[$key][$val] = $val;
                    } else {
                        $new_opts[$key] = $val;
                    }

                }

            }

        }

        // replace new value with old value
        if ( is_array( $old_opts ) ) {

            $basket = array_merge( $old_opts, $new_opts );

        } else {

            $basket = $new_opts;

        }

        // update dpr options
        update_option( $_POST['form'][0]['value'], $basket );

        wp_send_json_success();
        wp_die();

    }

    /**
     * ALl Settings Reset Action
     */
    public function reset_all_options() {

        // check nonce
        check_ajax_referer( 'dpr_admin_nonce', 'security' );

        if(!user_can( get_current_user_id(), 'administrator' )) {
            wp_send_json_error();
            wp_die();
        }

        delete_option('Comments');
        delete_option('Appearances');
        delete_option('Inappropriate_Comments');
        delete_option('Social_Share');
        delete_option('Word_Blacklist');
        delete_option('Voting');
        delete_option('Avatar');
        delete_option('Skin');
        delete_option('Limitation');
        delete_option('Notifications');
        delete_option('Load_More');
        delete_option('Comment_Sorting_Bar');
        delete_option('Comment_Form');
        delete_option('Comment_Box');
        delete_option('Replay');
        delete_option('Load_More_Button');
        delete_option('Author_Avatar');
        delete_option('Elements');
        delete_option('Sorting_Bar');
        delete_option('Custom_CSS');
        delete_option('Login_Register');
        delete_option('Recaptcha');

        wp_send_json_success();
        wp_die();
    }

    /**
     * Section Settings Reset Action
     */
    public function reset_options() {

        // check nonce
        check_ajax_referer( 'dpr_admin_nonce', 'security' );

        if(!user_can( get_current_user_id(), 'administrator' )) {
            wp_send_json_error();
            wp_die();
        }

        if(!isset($_POST['section']) || empty($_POST['section'])) {
            wp_send_json_error();
            wp_die();
        }

        $section = esc_attr( $_POST['section'] );
        delete_option($section);

        wp_send_json_success();
        wp_die();
    }

    public function _style_fix() {
     global $wp_version;

     if ( version_compare( $wp_version, '3.8', '<=' ) ):
      ?>
     <style type="text/css">
      /** WordPress 3.8 Fix **/
      .form-table th { padding: 20px 10px; }
      #wpbody-content .metabox-holder { padding-top: 5px; }
     </style>
     <?php
     endif;
    }

 /**
  * Retrieves all of the settings from the database
  *
  * @since    1.0.0
  * @return array
  */
 public static function get_settings( $setting_name = false ) {

  if ( ! isset( static::$settings ) ) {
   static::$settings = get_option( static::SETTINGS_NAME, array() );
  }

  if ( $setting_name ) {
   return isset( static::$settings[$setting_name] ) ? static::$settings[$setting_name] : array();
  }

  return static::$settings;

 }

 /**
  * Delete all plugin setings
  *
  * @since    1.0.0
  * @return boolean
  */
 public static function delete_settings( $setting_name = false ) {

  if ( $setting_name ) {
   static::get_settings();

   unset( static::$settings[$setting_name] );

   return static::update_settings( static::$settings );
  }

  return delete_option( static::SETTINGS_NAME );

 }

}