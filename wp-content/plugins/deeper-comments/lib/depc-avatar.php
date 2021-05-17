<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */

class Depc_Avatar {

	protected $text;
	protected $bcolor;
	protected $txtcolor;
	protected $width;
	protected $height;
	/**
	 * Allow to configure some options, generate a random code and print the fields
	 */
	function __construct( $args = array() ) {
		$defaults = array(
			'text' => 'Guest',
			'width' => 50,
			'height' => 50,
			'bcolor' => '2b2b2b',
			'txtxcolor' => 'ffffff'
		);
		$args = wp_parse_args( $args, $defaults );
		foreach ( $args as $k => $v ) {
			$this->$k = $v;
		}
	}
	/**
	 * Return args with avatar
	 */
	public function getImg() {

		$f = '<img src="%s" width="%d" height="%d" alt="" />';
		$data = array(
			'size'      => "{$this->width}x{$this->height}",
			'text'      => "{$this->text}",
			'bcolor'    => "{$this->bcolor}",
			'txtxcolor' => "{$this->txtxcolor}"
		);
		$url = add_query_arg( $data, Depc_Core::get_depc_url() . 'lib/app/image.php' );
		return sprintf( $f, $url, $this->width, $this->height );
	}

	/**
	 * Convert an hexadecimal color to an rgb array color
	 */
	static function hexrgb( $hex ) {
		$int = hexdec( $hex );
		return array(
			"r" => 0xFF & ($int >> 0x10),
			"g" => 0xFF & ($int >> 0x8),
			"b" => 0xFF & $int
		);
	}

}
