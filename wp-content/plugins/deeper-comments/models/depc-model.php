<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */


abstract class Depc_Model {

	private static $instances = array();
	private static $core;
	private static $current_user;
	private $settings;
	protected $user_setting;

	/**
	 * Provides access to a single instance of a module using the singleton pattern
	 *
	 * @since    1.0.0
	 */
	public static function get_instance() {
		$classname = get_called_class();

		if ( ! isset( self::$instances[ $classname ] ) ) {
			self::$instances[ $classname ] = new $classname();
		}
		return self::$instances[ $classname ];

	}

	public static function get_core() {
		self::$core = new Depc_Core;
		return self::$core;
	}

	public static function get_current_user() {

		self::$current_user = wp_get_current_user();
		return self::$current_user;

	}

	/**
	* Get post id.
	*
	* @since    1.0.0
	*/
	public function post_id() {

		return get_the_ID();

	}

	/**
	 * Get model
	 *
	 * @since    1.0.0
	 */
	protected function get_user_settings() {
		$settings = new Depc_Settings;
		return $settings->get_user_option();
	}

	 /**
     * Get re-captcha verification from Google servers
     * @author Webnus <info@webnus.biz>
     * @param string $remote_ip
     * @param string $response
     * @return boolean
     */
	public function get_recaptcha_response($response, $remote_ip = NULL) {
		// get the IP
		if(is_null($remote_ip)) $remote_ip = (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '');

		// MEC Settings
		$settings = $this->take_settings();

		$data = array('secret'=>( isset( $settings['secretkey'] ) ? $settings['secretkey'] : ''), 'remoteip'=>$remote_ip, 'v'=>'php_1.0', 'response'=>$response);

		$req = "";
		foreach($data as $key=>$value) $req .= $key.'='.urlencode(stripslashes($value)).'&';

		// Validate the re-captcha
		$getResponse = $this->get_web_page("https://www.google.com/recaptcha/api/siteverify?".trim($req, '& '));

		$answers = json_decode($getResponse, true);

		if(isset($answers['success']) and trim($answers['success'])) return true;
		else return false;
	}

	public function take_settings() {

		$this->settings['recaptcha']	= Depc_Core::get_option( 'dc_recaptcha_type', 'Recaptcha' );
		$this->settings['recaptchacm']	= Depc_Core::get_option( 'dc_recaptcha_addcm', 'Recaptcha' );
		$this->settings['secretkey']	= Depc_Core::get_option( 'dc_recptcha_gsecretkey', 'Recaptcha' );

		return $this->settings;

	}

	/**
	 * Render a template
	 *
	 * @since    1.0.0
	 */
	protected static function render_template( $default_template_path = false, $variables = array(), $require = 'once' ){

		if ( ! $template_path = locate_template( basename( $default_template_path ) ) ) {
			$template_path =  Depc_Core::get_depc_path() . '/views/' . $default_template_path;
		}

		if ( is_file( $template_path ) ) {
			extract( $variables );
			ob_start();
			if ( 'always' == $require ) {
				require( $template_path );
			} else {
				require_once( $template_path );
			}
			$template_content = apply_filters( 'depc_template_content', ob_get_clean(), $default_template_path, $template_path, $variables );
		} else {
			$template_content = '';
		}

		return $template_content;
	}

	/**
     * Return a web page
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param array $post
     * @param boolean|string $authentication
     * @return string
     */
	public function get_web_page($url, $post = '', $authentication = false) {
		$result = false;

		if ($post) {
			$args = array(
				'body' => $post,
				'timeout' => '120',
				'redirection' => '10`',
			);
			$response = wp_remote_post( $url, $args );

		} else {
			$response = wp_remote_get( $url );
		}

		$body = wp_remote_retrieve_body( $response );
		return $body;
	}

	/**
	 * Constructor
	 *
	 * @since    1.0.0
	 */
	abstract protected function __construct();

	/**
	 * Register callbacks for actions and filters
	 *
	 * @since    1.0.0
	 */
	abstract public function register_hook_callbacks();

}
