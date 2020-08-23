<?php

namespace RoyTanck\DisplayEnvironmentType\Traits;

defined( 'ABSPATH' ) or exit;

trait Utilities {
	/**
	 * Returns the URL to the plugin's base directory, with optional path appended.
	 *
	 * @param string $append_path An additional URL path to append to the base URL.
	 * 
	 * @return string The plugin's base directory URL (with trailing slash), plus any path that was passed.
	 */
	public static function get_url( $append_path = '' ) {
		return plugin_dir_url( dirname( dirname( __DIR__ ) ) . '/display-environment-type.php' ) . $append_path;
	}

	/**
	 * Returns the fully-qualified classname.
	 *
	 * @return string The fully-qualified classname.
	 */
	public static function get_class() {
		return "\\" . static::class;
	}
}
