<?php

namespace RoyTanck\DisplayEnvironmentType;

defined( 'ABSPATH' ) or exit;

class Plugin {
	use Traits\Utilities;

	const STYLESHEET_HANDLE = 'det-toolbar-styles';

	/**
	 * Private constructor, as this class should not be instantiated.
	 */
	private function __construct() {}

	/**
	 * Tells the plugin to add its hooks on the 'init' action.
	 * 
	 * @return void
	 */
	public static function init() {
		// Wait for the init action to actually do anything.
		add_action( 'init', [ self::get_class(), 'add_hooks' ] );
	}

	/**
	 * Adds all the plugin's hooks.
	 * 
	 * @return void
	 */
	public static function add_hooks() {
		// Bail if we shouldn't display.
		if ( ! self::should_display() ) {
			return;
		}

		$class = self::get_class();

		// Add an item to the "at a glance" dashboard widget.
		add_filter( 'dashboard_glance_items', [ $class, 'add_glance_item' ] );

		// Add an admin bar item if in wp-admin.
		add_action( 'admin_bar_menu', [ $class, 'add_toolbar_item' ], 7 );

		// Add styling.
		add_action( 'admin_enqueue_scripts', [ $class, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $class, 'enqueue_styles' ] );
	}

	/**
	 * Make development type i18n.
	 *
	 * @param string $env_type The environment type.
	 *
	 * @return string The translated environment type.
	 */
	public static function get_env_type_name( $env_type ) {
		$name = '';
		switch ( $env_type ) {
			case 'local':
				$name = __( 'Local', 'display-environment-type' );
				break;
			case 'development':
				$name = __( 'Development', 'display-environment-type' );
				break;
			case 'staging':
				$name = __( 'Staging', 'display-environment-type' );
				break;
			default:
				$name = __( 'Production', 'display-environment-type' );
		}

		/**
		 * Filter the environment type name.
		 *
		 * @param string $name     The translated environment name.
		 * @param string $env_type The environment type key.
		 */
		return apply_filters( 'display_environment_type_name', $name, $env_type );
	}

	/**
	 * Adds the "at a glance" item.
	 * 
	 * @param array $items The current "at a glance" items.
	 * 
	 * @return array The updated "at a glance" items array.
	 */
	public static function add_glance_item( $items ) {
		$env_type = wp_get_environment_type();
		$env_type_name = self::get_env_type_name( $env_type );

		if ( !empty( $env_type ) ) {
			$items[] = '<span class="' . esc_attr( 'det-env-type det-' . $env_type ) . '" title="' .  esc_attr__( 'Environment Type', 'display-environment-type' ) . '">' . esc_html( $env_type_name ) . '</span>';
		}

		return $items;
	}

	/**
	 * Adds an admin bar item.
	 * 
	 * @param \WP_Admin_Bar $admin_bar The WordPress toolbar.
	 * 
	 * @return void
	 */
	public static function add_toolbar_item( $admin_bar ) {
		$env_type = wp_get_environment_type();
		$env_type_name = self::get_env_type_name( $env_type );

		if ( !empty( $env_type ) ) {
			$admin_bar->add_menu( array(
				'id'    => 'det_env_type',
				'parent'=> 'top-secondary',
				'title' => '<span class="ab-icon" aria-hidden="true"></span><span class="ab-label">' . esc_html( $env_type_name ) . '</span>',
				'meta'  => array(
					'class' => 'det-' . sanitize_title( $env_type ),
				),
			));
		}
	}

	/**
	 * Determine whether or not to display the environment type.
	 * 
	 * @return bool Whether the plugin should display anything.
	 */
	protected static function should_display() {
		// By default, we don't display anything.
		$display = false;

		// If the function doesn't exist, the plugin absolutely cannot function.
		if ( ! function_exists( 'wp_get_environment_type' ) ) {
			return false;
		}
		
		// If the admin bar is not showing there is no place to display the environment type.
		if ( ! is_admin_bar_showing() ) {
			return false;
		}

		if ( is_admin() ) {
			// Display in wp-admin for any role above subscriber.
			if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
				$display = true;
			}
		} elseif ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
			// Display on the front-end only if user has the manage_options capability.
			$display = true;
		}

		/**
		 * Filter whether or not the environent type should be displayed.
		 *
		 * Allows you to perform checks like user capabilities or is_admin()
		 * and return true to display the environment type, or false to not.
		 *
		 * @since 1.2
		 *
		 * @param boolean $display Whether the environment type should be displayed.
		 */
		$display = (bool) apply_filters( 'det_display_environment_type', $display );

		return $display;
	}

	/**
	 * Enqueues the CSS styles necessary to display the environment type.
	 */
	public static function enqueue_styles() {
		wp_enqueue_style( self::STYLESHEET_HANDLE, self::get_url( 'css/admin.css' ) );
	}

}
