<?php
/**
 * Plugin Name:       Display Environment Type
 * Plugin URI:        https://roytanck.com/2020/08/21/new-wordpress-plugin-display-environment-type/
 * Description:       Display the site's environment type in wp-admin.
 * Version:           1.6.0
 * Requires at least: 5.5
 * Requires PHP:      7.4
 * Author:            Stoil Dobreff
 * Author URI:        https://roytanck.com/
 * License:           GPLv3
 *
 * @package           display-environment-type
 */

use DET\Display_Environment_Type;

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'DET_VERSION' ) ) {
	define( 'DET_VERSION', '1.6.0' );
}
if ( ! defined( 'DET_TEXTDOMAIN' ) ) {
	define( 'DET_TEXTDOMAIN', 'display-environment-type' );
}
if ( ! defined( 'DET_NAME' ) ) {
	define( 'DET_NAME', 'Display Environment Type' );
}
if ( ! defined( 'DET_PLUGIN_ROOT' ) ) {
	define( 'DET_PLUGIN_ROOT', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'DET_PLUGIN_ROOT_URL' ) ) {
	define( 'DET_PLUGIN_ROOT_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'DET_PLUGIN_BASENAME' ) ) {
	define( 'DET_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'DET_PLUGIN_ABSOLUTE' ) ) {
	define( 'DET_PLUGIN_ABSOLUTE', __FILE__ );
}
if ( ! defined( 'DET_MIN_PHP_VERSION' ) ) {
	define( 'DET_MIN_PHP_VERSION', '7.4' );
}
if ( ! defined( 'DET_WP_VERSION' ) ) {
	define( 'DET_WP_VERSION', '5.5' );
}
if ( ! defined( 'DET_SETTINGS_NAME' ) ) {
	define( 'DET_SETTINGS_NAME', 'det_options' );
}

if ( version_compare( PHP_VERSION, DET_MIN_PHP_VERSION, '<' ) ) {
	\add_action(
		'admin_init',
		static function () {
			if ( ! \is_admin() || ! \current_user_can( 'activate_plugins' ) ) {
				return;
			}
			\deactivate_plugins( DET_PLUGIN_BASENAME );
		}
	);

	\add_action(
		'admin_notices',
		static function () {
			if ( ! \is_admin() || ! \current_user_can( 'activate_plugins' ) ) {
				return;
			}
			$message = \sprintf(
				/* translators: the minimum version of PHP required by the plugin. */
				\esc_html__( '"%1$s" requires PHP %2$s or newer. Plugin is automatically deactivated.', 'display-environment-type' ),
				\esc_html( DET_NAME ),
				\esc_html( DET_MIN_PHP_VERSION )
			);

			echo '<div class="notice notice-error"><p>' . $message . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	);

	// Return early to prevent loading the plugin.
	return;
}

$autoload = DET_PLUGIN_ROOT . 'vendor/autoload.php';
if ( \file_exists( $autoload ) ) {
	require_once $autoload;

	if ( \class_exists( 'DET\\Display_Environment_Type' ) ) {
		Display_Environment_Type::init();
	} else {
		\add_action(
			'admin_notices',
			static function () {
				if ( ! \is_admin() || ! \current_user_can( 'activate_plugins' ) ) {
					return;
				}
				$msg = \esc_html__( 'Display Environment Type failed to initialize: class not found.', 'display-environment-type' );
				echo '<div class="notice notice-error"><p>' . $msg . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		);
	}
} else {
	\add_action(
		'admin_notices',
		static function () use ( $autoload ) {
			if ( ! \is_admin() || ! \current_user_can( 'activate_plugins' ) ) {
				return;
			}
			$msg = \sprintf(
				/* translators: the path to the missing autoloader file. */
				\esc_html__( 'Missing autoloader: %s', 'display-environment-type' ),
				\esc_html( $autoload )
			);
			echo '<div class="notice notice-error"><p>' . $msg . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	);
}
