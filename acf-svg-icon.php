<?php
/*
Plugin Name: Advanced Custom Fields: SVG Icon
Version: 2.2.0
Plugin URI: http://www.beapi.fr
Description: Add an ACF SVG icon selector.
Author: BE API Technical team
Author URI: https://www.beapi.fr
Domain Path: languages
Text Domain: acf-svg-icon

----

Copyright 2017 BE API Technical team (human@beapi.fr)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

define( 'ACF_SVG_ICON_VER', '2.2.0' );
define( 'ACF_SVG_ICON_URL', plugin_dir_url( __FILE__ ) );
define( 'ACF_SVG_ICON_DIR', plugin_dir_path( __FILE__ ) );
define( 'ACF_SVG_ICON_CACHE_KEY', 'acf_svg_icon_files' );

class Acf_Field_Svg_Icon_Plugin {

	/**
	 * Constructor.
	 *
	 * Load plugin's translation and register acf svg fields.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', [ __CLASS__, 'load_translation' ], 1 );

		// Register ACF fields
		add_action( 'acf/include_field_types', [ __CLASS__, 'register_field_v5' ] );

		// Allow to flush the SVG cached data.
		add_action( 'admin_bar_menu', [ __CLASS__, 'add_action_button_in_admin_bar' ], 120 );
		add_action( 'init', [ __CLASS__, 'handle_flush_action' ] );
	}

	/**
	 * Load plugin translation.
	 *
	 * @since 1.0.0
	 */
	public static function load_translation() {
		load_plugin_textdomain( 'acf-svg-icon', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Register SVG icon field for ACF v5 or v5.6 depending on ACF version.
	 *
	 * @since 1.0.0
	 */
	public static function register_field_v5() {
		if ( version_compare( acf_get_setting( 'version' ), '5.6.O', '>=' ) ) {
			// Register field for ACF 5.6 or greater
			include_once sprintf( '%sfields/acf-base.php', ACF_SVG_ICON_DIR );
			include_once sprintf( '%sfields/acf-56.php', ACF_SVG_ICON_DIR );

			acf_register_field_type( 'acf_field_svg_icon_56' );
		} else {
			// Register field for ACF 5
			include_once sprintf( '%sfields/acf-base.php', ACF_SVG_ICON_DIR );
			include_once sprintf( '%sfields/acf-5.php', ACF_SVG_ICON_DIR );

			$klass = 'acf_field_svg_icon_5';
			new $klass();
		}
	}

	/**
	 * Add a button in the WordPress admin bar to flush the cache for the SVG.
	 *
	 * @param \WP_Admin_Bar $admin_bar
	 *
	 * @return void
	 */
	public static function add_action_button_in_admin_bar( $admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$admin_bar->add_node(
			array(
				'id'     => 'acf_svg_icon_flush_cache',
				'parent' => null,
				'group'  => null,
				'title'  => esc_html__( 'Flush ACF SVG Icon cache', 'acf-svg-icon' ),
				'href'   => add_query_arg(
					[
						'action'   => 'acf_svg_icon_flush_cache',
						'_wpnonce' => wp_create_nonce( 'flush_cache' ),
					]
				),
				'meta'   => [
					'title' => esc_html__( 'If some SVG are missing or not up to date this could help resolve the issue.', 'acf-svg-icon' ),
				],
			)
		);
	}

	/**
	 * Handle the cache flush action.
	 *
	 * @return void
	 */
	public static function handle_flush_action() {
		if ( ! isset( $_GET['action'] ) || 'acf_svg_icon_flush_cache' !== $_GET['action'] ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to execute this action.', 'acf-svg-icon' ) );
		}

		$nonce = sanitize_text_field( $_GET['_wpnonce'] );
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'flush_cache' ) ) {
			wp_die( esc_html__( "Security error. Action couldn't be verified.", 'acf-svg-icon' ) );
		}

		delete_transient( ACF_SVG_ICON_CACHE_KEY );

		$referer = wp_get_referer();
		if ( ! $referer ) {
			$referer = home_url( '/' );
		}
		wp_safe_redirect( $referer );
		exit;
	}
}

/**
 * Init plugin.
 *
 * @since 1.0.0
 */
function acf_field_svg_icon() {
	new Acf_Field_Svg_Icon_Plugin();
}

add_action( 'plugins_loaded', 'acf_field_svg_icon' );
