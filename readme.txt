=== Display Environment Type ===
Contributors: sdobreff, roytanck, markjaquith, tflight, mrwweb, tekapo
Tags: environment, dtap, production, staging, development
Requires at least: 5.5
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.6.0
License: GPLv3

Displays WordPress 5.5's environment type setting in the admin bar and the "At a Glance" dashboard widget.

== Description ==

WordPress 5.5 introduced a way to differentiate between environment types (development, staging, production). This plugin shows your site's environment type in the admin bar and the dashboard "At a Glance" widget.

[More info about the feature](https://make.wordpress.org/core/2020/07/24/new-wp_get_environment_type-function-in-wordpress-5-5/)

To gain additional control — for example, setting the environment or other values from the WP admin (when `wp-config.php` is writable) — consider installing our other plugin **[0 Day Analytics](https://wordpress.org/plugins/0-day-analytics/)**.

== Installation ==

1. Install and activate the plugin from the 'Plugins' menu in WordPress.

== Recommended Plugins ==
* [0 Day Analytics](https://wordpress.org/plugins/0-day-analytics/) — a powerful plugin for sites that need more insight into errors and runtime behavior. It includes a Cron manager, a Transient manager (database-backed), DB manager, Snippet manager, Mail manager, Plugin Version Switcher available from the Plugins page and many more.

== Frequently Asked Questions ==

= Can I set a custom color for my environment types? =

Colors are intentionally fixed to avoid confusion. If colors were configurable, they'd need to be identical across all related servers to remain consistent.

= What happens if I define custom environment types? =

Custom environment types were briefly supported in WordPress 5.5 but were removed in 5.5.1. This plugin does not support custom types.

= Why don't non-administrators see the environment type on the front end? =

By default, the environment type is shown only to users with administrative capabilities. This avoids exposing a prominent colored indicator to regular subscribers and other non-admin users.

For additional control, use the `det_display_environment_type` filter hook. Example:

    function rt_det_display_filter( $display ) {
        // Disable the environment type display for user ID 2.
        return ( get_current_user_id() !== 2 );
    }
    add_filter( 'det_display_environment_type', 'rt_det_display_filter' );

== Screenshots ==

1. Admin bar display (production).
2. Admin bar display (staging).
3. Admin bar display (development).
4. Admin bar display (custom).
5. The "At a Glance" widget.

== Changelog ==

= 1.6.0 (2025-12-18) =
* Code improvements. Added the Gutenberg menu. 0 Day Analytics introduced.

= 1.5.0 (2024-07-01) =
* Code improvements; shows the constants' values regardless of the `WP_DEBUG` constant. WP Control plugin introduced.

= 1.4.0 (2024-04-07) =
* Code improvements and UI fixes — show the icon on mobile and set colors in the "At a Glance" widget. Added WordPress version in the drop-down menu.

= 1.3.5 (2025-04-04) =
* Added a drop-down submenu with WP constants and their values (enabled / disabled).

= 1.3.4 (2024-12-20) =
* Added a filter hook to modify the environment's display name (thanks @erniecom).

= 1.3.3 (2024-07-05) =
* Internationalization improvements by @tekapo.

= 1.3.2 (2023-11-10) =
* Accessibility improvements by @mrwweb.

= 1.3.1 (2022-03-30) =
* Skip loading the CSS file on the front end if the toolbar is hidden (thanks @tflight).

= 1.3 (2020-08-25) =
* Code refactor (thank you, @markjaquith).
* Environment type now hidden by default for subscribers.

= 1.2.1 (2020-08-23) =
* Removed the distracting hover effect (thank you, @markjaquith).
* Hardening against XSS (props @markjaquith).

= 1.2 (2020-08-21) =
* Adds a filter hook to allow you to determine whether the environment is displayed.

= 1.1 (2020-08-21) =
* Added a conditional front-end display (admins only).
* Improved plugin initialization.

= 1.0.2 (2020-08-21) =
* Removed the (unstyled) display on the front-end admin bar.
* Added a FAQ section to the readme file.

= 1.0.1 (2020-08-21) =
* Fixed the plugin description and plugin URI.

= 1.0 (2020-08-20) =
* Initial release.
