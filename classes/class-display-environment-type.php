<?php
/**
 * Responsible for plugin initialization.
 *
 * @package    det
 * @license    GPL v3
 * @copyright  %%YEAR%%
 * @since      latest
 */

declare(strict_types=1);

namespace DET;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( '\DET\Display_Environment_Type' ) ) {

	/**
	 * Responsible for all the plugin behavior
	 *
	 * @since latest
	 */
	class Display_Environment_Type {

		const STYLESHEET_HANDLE = 'det-toolbar-styles';

		/**
		 * Tells the plugin to add its hooks on the 'init' action.
		 *
		 * @return void
		 *
		 * @since latest
		 */
		public static function init() {
			// Wait for the init action to actually do anything.
			\add_action( 'init', array( __CLASS__, 'add_hooks' ) );
		}

		/**
		 * Adds all the plugin's hooks.
		 *
		 * @return void
		 *
		 * @since latest
		 */
		public static function add_hooks() {
			// AJAX endpoint to dismiss the recommendation (always registered).
			\add_action( 'wp_ajax_det_dismiss_recommendation', array( __CLASS__, 'ajax_dismiss_recommendation' ) );

			// Admin notice recommendation (dismissible per user).
			\add_action( 'admin_notices', array( __CLASS__, 'admin_notice_recommendation' ) );

			// Add admin assets for the dismissible notice.
			\add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );

			// Bail if we shouldn't display.
			if ( ! self::should_display() ) {
				return;
			}

			// Add an item to the "at a glance" dashboard widget.
			\add_filter( 'dashboard_glance_items', array( __CLASS__, 'add_glance_item' ) );

			// Add an admin bar item if in wp-admin.
			\add_action( 'admin_bar_menu', array( __CLASS__, 'add_toolbar_item' ), 7 );

			// Add styling.
			\add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
			\add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );

			// Add Gutenberg editor support.
			\add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_block_editor_assets' ) );
		}

		/**
		 * Get the translated name for an environment type.
		 *
		 * @param string $env_type The environment type.
		 *
		 * @return string The translated environment type.
		 *
		 * @since latest
		 */
		public static function get_env_type_name( $env_type ) {
			$name = '';
			switch ( $env_type ) {
				case 'local':
					$name = \__( 'Local', 'display-environment-type' );
					break;
				case 'development':
					$name = \__( 'Development', 'display-environment-type' );
					break;
				case 'staging':
					$name = \__( 'Staging', 'display-environment-type' );
					break;
				default:
					$name = \__( 'Production', 'display-environment-type' );
			}

			/**
			 * Filter the environment type name.
			 *
			 * @param string $name     The translated environment name.
			 * @param string $env_type The environment type key.
			 */
			return \apply_filters( 'display_environment_type_name', $name, $env_type );
		}

		/**
		 * Adds the "at a glance" item.
		 *
		 * @param array $items The current "at a glance" items.
		 *
		 * @return array The updated "at a glance" items array.
		 *
		 * @since latest
		 */
		public static function add_glance_item( $items ) {
			$env_type      = \wp_get_environment_type();
			$env_type_name = self::get_env_type_name( $env_type );

			if ( ! empty( $env_type ) ) {
				$css_class = 'det-env-type det-' . \sanitize_html_class( (string) $env_type );
				$items[]   = '<span class="' . \esc_attr( $css_class ) . '" title="' . \esc_attr__( 'Environment Type', 'display-environment-type' ) . '">' . \esc_html( $env_type_name ) . '</span>';
			}

			return $items;
		}

		/**
		 * Adds an admin bar item.
		 *
		 * @param \admin_bar $admin_bar The WordPress toolbar.
		 *
		 * @return void
		 *
		 * @since latest
		 */
		public static function add_toolbar_item( $admin_bar ) {
			$env_type      = \wp_get_environment_type();
			$env_type_name = self::get_env_type_name( $env_type );

			if ( ! empty( $env_type ) ) {
				$admin_bar->add_menu(
					array(
						'id'     => 'det_env_type',
						'parent' => 'top-secondary',
						'title'  => '<span class="ab-icon" aria-hidden="true"></span><span class="ab-label">' . \esc_html( $env_type_name ) . '</span>',
						'meta'   => array(
							'class' => 'det-' . \sanitize_html_class( (string) $env_type ),
						),
					)
				);

				$admin_bar->add_node(
					array(
						'id'     => 'det-wp-debug',
						'title'  => self::show_label_value( 'WP_DEBUG', ( WP_DEBUG ? 'true' : 'false' ) ),
						'parent' => 'det_env_type',
					)
				);

				$admin_bar->add_node(
					array(
						'id'     => 'det-wp-debug-log',
						'title'  => self::show_label_value( 'WP_DEBUG_LOG', ( WP_DEBUG_LOG ? 'true' : 'false' ) ),
						'parent' => 'det_env_type',
						'meta'   => array(
							'title' => \esc_attr( (string) WP_DEBUG_LOG ),
						),
					)
				);

				$admin_bar->add_node(
					array(
						'id'     => 'det-wp-debug-display',
						'title'  => self::show_label_value( 'WP_DEBUG_DISPLAY', ( WP_DEBUG_DISPLAY ? 'true' : 'false' ) ),
						'parent' => 'det_env_type',
					)
				);

				$wp_development_mode = ( \function_exists( 'wp_get_development_mode' ) ? \wp_get_development_mode() : null );

				if ( null !== $wp_development_mode ) {
					if ( empty( $wp_development_mode ) ) {
						$wp_development_mode = 'false';
					}
					$admin_bar->add_node(
						array(
							'id'     => 'det-wp-development-mode',
							'title'  => self::show_label_value( 'WP_DEVELOPMENT_MODE', $wp_development_mode ),
							'parent' => 'det_env_type',
						)
					);
				}

				$admin_bar->add_node(
					array(
						'id'     => 'det-script-display',
						'title'  => self::show_label_value( 'SCRIPT_DEBUG', ( SCRIPT_DEBUG ? 'true' : 'false' ) ),
						'parent' => 'det_env_type',
					)
				);

				$admin_bar->add_node(
					array(
						'id'     => 'det-savequeries',
						'title'  => self::show_label_value( 'SAVEQUERIES', ( \defined( 'SAVEQUERIES' ) && \SAVEQUERIES ? 'true' : 'false' ) ),
						'parent' => 'det_env_type',
					)
				);

				$admin_bar->add_node(
					array(
						'id'     => 'det-wp',
						'title'  => self::show_label_value( 'WP', \get_bloginfo( 'version', 'display' ) ),
						'parent' => 'det_env_type',
					)
				);

				$admin_bar->add_node(
					array(
						'id'     => 'det-php',
						'title'  => self::show_label_value( 'PHP', \phpversion() ),
						'parent' => 'det_env_type',
					)
				);
			}
		}

		/**
		 * Html_label_value
		 *
		 * @param  string $label Text to display as label.
		 * @param  string $value Text to display as value.
		 *
		 * @return string        HTML to display label and value.
		 *
		 * @since latest
		 */
		private static function show_label_value( $label, $value ): string {
			$html  = '';
			$html .= '<span class="ei-label">' . \esc_html( $label ) . '</span>';
			$html .= '<span class="ei-value">' . \esc_html( $value ) . '</span>';

			return $html;
		}

		/**
		 * Determine whether or not to display the environment type.
		 *
		 * @return bool Whether the plugin should display anything.
		 *
		 * @since latest
		 */
		protected static function should_display() {
			// By default, we don't display anything.
			$display = false;

			// If the function doesn't exist, the plugin absolutely cannot function.
			if ( ! \function_exists( 'wp_get_environment_type' ) ) {
				return false;
			}

			// If the admin bar is not showing there is no place to display the environment type.
			if ( ! \is_admin_bar_showing() ) {
				return false;
			}

			if ( \is_admin() ) {
				// Display in wp-admin for any role above subscriber.
				if ( \is_user_logged_in() && \current_user_can( 'edit_posts' ) ) {
					$display = true;
				}
			} elseif ( \is_user_logged_in() && \current_user_can( 'manage_options' ) ) {
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
			$display = (bool) \apply_filters( 'det_display_environment_type', $display );

			return $display;
		}

		/**
		 * Enqueues the CSS styles necessary to display the environment type.
		 *
		 * @return void
		 *
		 * @since latest
		 */
		public static function enqueue_styles() {
			\wp_enqueue_style(
				self::STYLESHEET_HANDLE,
				\DET_PLUGIN_ROOT_URL . 'css/admin.css',
				array(),
				\DET_VERSION
			);
		}

		/**
		 * Enqueue small admin assets (inline JS) for the dismissible recommendation.
		 *
		 * @return void
		 *
		 * @since latest
		 */
		public static function enqueue_admin_assets() {
			if ( ! \is_admin() ) {
				return;
			}

			// Register a small plugin-specific handle and pass safe data via localization.
			\wp_register_script( 'det-admin', '', array( 'jquery' ), \DET_VERSION );
			\wp_enqueue_script( 'det-admin' );

			// Pass Ajax URL and nonce securely to JS.
			\wp_localize_script(
				'det-admin',
				'detAdmin',
				array(
					'ajax_url' => \admin_url( 'admin-ajax.php' ),
					'nonce'    => \wp_create_nonce( 'det_dismiss_recommendation' ),
				)
			);

			$script = <<<'JS'
			(function($){
			$(document).on('click', '.det-recommendation .notice-dismiss', function(e){
				e.preventDefault();
				var container = $(this).closest('.det-recommendation');
				if (!container.length) return false;
				$.post(detAdmin.ajax_url, { action: 'det_dismiss_recommendation', _ajax_nonce: detAdmin.nonce }, function(response){
				if (response.success) {
					container.fadeOut(300, function(){ $(this).remove(); });
				}
				});
				return false;
			});
			})(jQuery);
			JS;

			\wp_add_inline_script( 'det-admin', $script );
		}

		/**
		 * Render a dismissible admin notice recommending the other plugin.
		 *
		 * @return void
		 *
		 * @since latest
		 */
		public static function admin_notice_recommendation() {
			$user_id = \get_current_user_id();
			if ( ! $user_id ) {
				return;
			}

			// Only show to administrators.
			if ( ! \current_user_can( 'manage_options' ) ) {
				return;
			}

			$hidden = \get_user_meta( $user_id, 'det_dismiss_recommendation', true );
			if ( $hidden ) {
				return;
			}

			$plugin_url    = 'https://wordpress.org/plugins/0-day-analytics/';
			$download_url  = 'https://downloads.wordpress.org/plugin/0-day-analytics.zip';
			$plugin_anchor  = '<a href="' . \esc_url( $plugin_url ) . '" target="_blank" rel="noopener noreferrer">' . \esc_html__( '0 Day Analytics', 'display-environment-type' ) . '</a>';
			$download_anchor = '<a href="' . \esc_url( $download_url ) . '" target="_blank" rel="noopener noreferrer">' . \esc_html__( 'download', 'display-environment-type' ) . '</a>';

			/* translators: %1$s and %2$s are HTML links. */
			$message = \sprintf(
				\__( '<strong>Display Environment Type</strong>: If you like this plugin, you will love our other one - %1$s - try it out now - %2$s', 'display-environment-type' ),
				$plugin_anchor,
				$download_anchor
			);

			$allowed = array(
				'a'      => array(
					'href'   => true,
					'target' => true,
					'rel'    => true,
				),
				'strong' => array(),
			);

			echo '<div class="notice notice-info is-dismissible det-recommendation"><p>' . \wp_kses( $message, $allowed ) . '</p></div>';
		}

		/**
		 * AJAX handler to dismiss the recommendation for the current user.
		 *
		 * @return void
		 *
		 * @since latest
		 */
		public static function ajax_dismiss_recommendation() {
			\check_ajax_referer( 'det_dismiss_recommendation' );
			$user_id = \get_current_user_id();
			if ( $user_id ) {
				\update_user_meta( $user_id, 'det_dismiss_recommendation', 1 );
			}
			\wp_send_json_success();
		}

		/**
		 * Enqueues assets for the block editor.
		 *
		 * @return void
		 *
		 * @since latest
		 */
		public static function enqueue_block_editor_assets() {
			$env_type      = \wp_get_environment_type();
			$env_type_name = self::get_env_type_name( $env_type );

			// Enqueue the JavaScript file.
			\wp_enqueue_script(
				'det-block-editor',
				\DET_PLUGIN_ROOT_URL . 'js/block-editor.js',
				array(),
				\DET_VERSION,
				true
			);

			// Enqueue the CSS file for block editor.
			\wp_enqueue_style(
				'det-block-editor-styles',
				\DET_PLUGIN_ROOT_URL . 'css/block-editor.css',
				array(),
				\DET_VERSION
			);

			// Pass environment data to JavaScript.
			\wp_localize_script(
				'det-block-editor',
				'detEnvData',
				array(
					'envType'           => $env_type,
					'envTypeName'       => $env_type_name,
					'wpDebug'           => WP_DEBUG ? 'true' : 'false',
					'wpDebugLog'        => WP_DEBUG_LOG ? 'true' : 'false',
					'wpDebugDisplay'    => WP_DEBUG_DISPLAY ? 'true' : 'false',
					'wpDevelopmentMode' => ( \function_exists( 'wp_get_development_mode' ) ? \wp_get_development_mode() : '' ),
					'scriptDebug'       => SCRIPT_DEBUG ? 'true' : 'false',
					'saveQueries'       => ( \defined( 'SAVEQUERIES' ) && \SAVEQUERIES ? 'true' : 'false' ),
					'wpVersion'         => \get_bloginfo( 'version', 'display' ),
					'phpVersion'        => \phpversion(),
				)
			);
		}
	}
}
