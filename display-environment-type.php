<?php
/**
 * Plugin Name:       Display Environment Type
 * Plugin URI:        https://roytanck.com/2020/08/21/new-wordpress-plugin-display-environment-type/
 * Description:       Display the site's environment type in wp-admin.
 * Version:           1.5.0
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

define( 'DET_VERSION', '1.5.0' );
define( 'DET_TEXTDOMAIN', 'display-environment-type' );
define( 'DET_NAME', 'Display Environment Type' );
define( 'DET_PLUGIN_ROOT', plugin_dir_path( __FILE__ ) );
define( 'DET_PLUGIN_ROOT_URL', plugin_dir_url( __FILE__ ) );
define( 'DET_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'DET_PLUGIN_ABSOLUTE', __FILE__ );
define( 'DET_MIN_PHP_VERSION', '7.4' );
define( 'DET_WP_VERSION', '5.5' );
define( 'DET_SETTINGS_NAME', 'det_options' );

if ( version_compare( PHP_VERSION, DET_MIN_PHP_VERSION, '<=' ) ) {
	\add_action(
		'admin_init',
		static function () {
			deactivate_plugins( DET_PLUGIN_BASENAME );
		}
	);
	\add_action(
		'admin_notices',
		static function () {
			echo \wp_kses_post(
				sprintf(
					'<div class="notice notice-error"><p>%s</p></div>',
					sprintf(
						// translators: the minimum version of the PHP required by the plugin.
						__(
							'"%1$s" requires PHP %2$s or newer. Plugin is automatically deactivated.',
							'display-environment-type'
						),
						DET_NAME,
						DET_MIN_PHP_VERSION
					)
				)
			);
		}
	);

	// Return early to prevent loading the plugin.
	return;
}

require DET_PLUGIN_ROOT . 'vendor/autoload.php';

Display_Environment_Type::init();
