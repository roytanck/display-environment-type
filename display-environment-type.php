<?php
/**
 * Plugin Name:       Display Environment Type
 * Plugin URI:        https://roytanck.com/2020/08/21/new-wordpress-plugin-display-environment-type/
 * Description:       Display the site's environment type in wp-admin.
 * Version:           1.3
 * Requires at least: 5.5
 * Requires PHP:      5.6
 * Author:            Roy Tanck
 * Author URI:        https://roytanck.com/
 * License:           GPLv3
 */

namespace RoyTanck\DisplayEnvironmentType;

defined( 'ABSPATH' ) or exit;

include __DIR__ . '/app/Traits/Utilities.php';
include __DIR__ . '/app/Plugin.php';

Plugin::init();
