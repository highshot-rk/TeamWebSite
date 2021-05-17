<?php

/* get settings */
$sizes = filter_input( INPUT_GET, 'size', FILTER_SANITIZE_STRING );
if ( ! empty( $sizes ) ) {
	$s = explode( 'x', $sizes );
	if ( is_numeric($s[0]) && (int) $s[0] > 0  ) $w = (int) $s[0];
	if ( isset($s[1]) && is_numeric($s[1]) && (int) $s[1] > 0  ) $h = (int) $s[1];
}

// background color
$bcolor = (string) filter_input( INPUT_GET, 'bcolor', FILTER_SANITIZE_STRING );
if ( strlen($bcolor) === 6 ) $bcolor = "0x#{$bcolor}";
$int = hexdec( $bcolor );
$bcolor = array(
	"r" => 0xFF & ($int >> 0x10),
	"g" => 0xFF & ($int >> 0x8),
	"b" => 0xFF & $int
);

// text color
$txtcolor = (string) filter_input( INPUT_GET, 'txtxcolor', FILTER_SANITIZE_STRING );
if ( strlen($txtcolor) === 6 ) $txtcolor = "0x#{$txtcolor}";

$int = hexdec( $txtcolor );
$txtcolor = array(
	"r" => 0xFF & ($int >> 0x10),
	"g" => 0xFF & ($int >> 0x8),
	"b" => 0xFF & $int
);

// get text
$text = (string) filter_input( INPUT_GET, 'text', FILTER_SANITIZE_STRING );
$fontSize = "22";
$font = 'Lato-Lig.ttf';

session_start();
$code = $text;
$_SESSION["code"]=$code;
$im = imagecreatetruecolor( $w,$h);
$bg = imagecolorallocate($im, $bcolor['r'],$bcolor['g'],$bcolor['b']); //background color black
$fg = imagecolorallocate($im, $txtcolor['r'],$txtcolor['g'],$txtcolor['b']); //text color white
imagefill($im, 0, 0, $bg);
list( $x, $y ) = ImageTTFCenter( $im, $text, $font, $fontSize , 8 );
imagettftext( $im, $fontSize, 0, $x, $y, $fg, $font, $text );
header("Cache-Control: no-cache, must-revalidate");
header('Content-type: image/png');
imagepng($im);
imagedestroy($im);

//function to get center position on image*/
function ImageTTFCenter($image, $text, $font, $size, $angle = 8) {
	$xi = imagesx($image);
	$yi = imagesy($image);
	$box = imagettfbbox($size, $angle, $font, $text);
	$xr = abs(max($box[2], $box[4]))+5;
	$yr = abs(max($box[5], $box[7]));
	$x = intval(($xi - $xr) / 2);
	$y = intval(($yi + $yr) / 2);
	return array($x, $y);
}