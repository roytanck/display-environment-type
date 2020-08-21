<?php
/**
 * Plugin Name:       Display Environment Type
 * Plugin URI:        https://roytanck.com/2020/08/21/new-wordpress-plugin-display-environment-type/
 * Description:       Display the site's environment type in wp-admin.
 * Version:           1.0.2
 * Requires at least: 5.5
 * Requires PHP:      5.6
 * Author:            Roy Tanck
 * Author URI:        https://roytanck.com/
 * License:           GPLv3
 */


// check if called directly
if( !defined( 'ABSPATH' ) ){ exit; }


// create the plugin's class
if( ! class_exists( 'Display_Environment_Type' ) ){

	class Display_Environment_Type {

		/**
		 * Init function that adds functions to hooks
		 */
		public function init(){
			// Add an item to the "at a glance" dashboard widget.
			add_filter( 'dashboard_glance_items', array( $this, 'add_glance_item' ), 10, 1 );
			// Add CSS code for the glace item.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			// Add an admin bar item if in wp-admin.
			if( is_admin() ){
				add_action( 'admin_bar_menu', array( $this, 'add_toolbar_item' ) );
			}
		}


		/**
		 * Adds the "at a glance" item.
		 */
		public function add_glance_item( $items ){
			// Check if the environment type function is available.
			if( function_exists( 'wp_get_environment_type' ) ){
				$env_type = wp_get_environment_type();
				// Check if the environment type is not empty before generating output.
				if( !empty( $env_type ) ){
					// Add the new item to the array.
					$items[] = '<span class="det-env-type det-' . $env_type . '" title="' .  __( 'Environment Type', 'display-environment-type' ) . '">' . ucfirst( $env_type ) . '</span>';
				}
			}
			return $items;
		}


		/**
		 * Adds an admin bar item.
		 */
		public function add_toolbar_item( $admin_bar ){
			// Check if the environment type function is available.
			if( function_exists( 'wp_get_environment_type' ) ){
				$env_type = wp_get_environment_type();
				// Check if the environment type is not empty before generating output.
				if( !empty( $env_type ) ){
					// Add the admin bar item.
					$admin_bar->add_menu( array(
						'id'    => 'det_env_type',
						'parent'=> 'top-secondary',
						'title' => '<span class="ab-icon"></span><span class="ab-label">' . ucfirst( $env_type ) . '</span>',
						'meta'  => array(
							'title' => __( 'Environment Type', 'display-environment-type' ),
							'class' => 'det-' . sanitize_title( $env_type ),
						),
					));
				}
			}
		}


		/**
		 * Enqueues a small admin CSS stylesheet.
		 */
		public function enqueue_styles( $hook ){
			// Enqueue admin CSS.
			wp_enqueue_style( 'det_admin_css', plugin_dir_url( __FILE__ ) . 'css/admin.css' );
		}

	}

	// instantiate the plugin class
	$display_environment_type = new Display_Environment_Type();
	$display_environment_type->init();

}