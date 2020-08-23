=== Display Environment Type ===
Contributors: roytanck, markjaquith
Tags: environment type, dtap, production, staging, development
Requires at least: 5.5
Tested up to: 5.5
Requires PHP: 5.6
Stable tag: 1.2
License: GPLv3

Displays WordPress 5.5's new environment type setting in the admin bar and the 'at a glance' dashboard widget.
 
== Description ==

WordPress 5.5 introduces a way to differentiate between environment types (development, staging, production). This plugin displays your site's environment type on the admin bar.

[More info about the new feature](https://make.wordpress.org/core/2020/07/24/new-wp_get_environment_type-function-in-wordpress-5-5/)
 
== Installation ==

1. Install and activate using the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==
 
= Can I set custom color for my environment types? =
 
The colors are currently fixed. This was done to avoid possible confusion. If the colors were user-configurable, they would need to be set up exactly the same on all related servers.
 
= What will happen when I define custom environment types? =
 
Custom types will be displayed in blue, with a lightbulb icon. There's currently no way to set different colors for your custom types if you have more than one.

= Why is there no display on the front-end of the site, for logged-in users with the admin bar enabled? =

There's no display for non-admin users. The reasoning behind this is that in most cases, you'd probably not want to bother logged-in subscribers with a bright-colored box on their admin bar.

For additional control, you can use the 'det_display_environment_type' filter hook:

    function rt_det_display_filter( $display ){
        // Disable the environment type display for user ID 2.
        return ( get_current_user_id() !== 2 );
    }
    add_filter( 'det_display_environment_type', 'rt_det_display_filter' );

== Screenshots ==
 
1. Admin bar display (production).
2. Admin bar display (staging).
3. Admin bar display (development).
4. Admin bar display (custom).
5. The 'at a glance' widget.
 
== Changelog ==

= 1.2 (2020-08-21) =
* Adds a filter hook to allow you to determine whether the environmment is displayed.

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
