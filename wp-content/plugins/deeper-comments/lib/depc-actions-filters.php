<?php

/**
 * @link       http://webnus.biz
 * @since      1.0.0
 *
 * @package    Deeper Comments
 */


class Depc_Actions_Filters {

	/**
	 * The array of actions registered with WordPress.
	 * @since    1.0.0
	 * @access   protected
	 */
	protected static $actions = array();

	/**
	 * The array of filters registered with WordPress.
	 * @since    1.0.0
	 * @access   protected
	 */
	protected static $filters = array();

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 */
	public static function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {

		self::$actions = self::add( self::$actions, $hook, $component, $callback, $priority, $accepted_args );

	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 */
	public static function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {

		self::$filters = self::add( self::$filters, $hook, $component, $callback, $priority, $accepted_args );

	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 */
	private static function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 */
	public static function init_actions_filters() {

		foreach ( self::$filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( self::$actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

	}

}