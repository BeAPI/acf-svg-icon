<?php
/*
 Plugin Name: Advanced Custom Fields: SVG Icon
 Version: 1.0.1
 Plugin URI: http://www.beapi.fr
 Description: Add svg icon selector
 Author: BE API Technical team
 Author URI: http://www.beapi.fr
 Text Domain: acf-svg_icon
 Domain Path: /lang

 ----

 Copyright 2016 BE API Technical team (human@beapi.fr)

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

define( 'BEA_ACF_SVG_ICON_VERSION', '1.0.1' );
define( 'BEA_ACF_SVG_ICON_FILE', __FILE__ );
define( 'BEA_ACF_SVG_ICON_URL', plugin_dir_url( BEA_ACF_SVG_ICON_FILE ) );
define( 'BEA_ACF_SVG_ICON_DIR', plugin_dir_path( BEA_ACF_SVG_ICON_FILE ) );

if ( ! class_exists( 'acf_plugin_svg_icon' ) ) {
	class acf_plugin_svg_icon {
		/**
		 *  __construct
		 *
		 *  This function will setup the class functionality
		 *
		 *  @type	function
		 *  @date	17/02/2016
		 *  @since	1.0.0
		 *
		 *  @param	n/a
		 *  @return	n/a
		 */
		function __construct() {
			$this->settings = array(
				'version'	=> BEA_ACF_SVG_ICON_VERSION,
				'url'		=> BEA_ACF_SVG_ICON_URL,
				'path'		=> BEA_ACF_SVG_ICON_DIR
			);

			// set text domain
			load_plugin_textdomain( 'acf-svg_icon', false, plugin_basename( BEA_ACF_SVG_ICON_DIR ) . '/lang' );

			// include field
			add_action( 'acf/include_field_types', array( $this, 'include_field_types' ) ); // v5
			add_action( 'acf/register_fields', array( $this, 'include_field_types' ) ); // v4
		}

		/**
		 *  include_field_types
		 *
		 *  This function will include the field type class
		 *
		 *  @type	function
		 *  @date	17/02/2016
		 *  @since	1.0.0
		 *
		 *  @param	$version (int) major ACF version. Defaults to false
		 *  @return	n/a
		 */
		function include_field_types( $version = false ) {
			// support empty $version
			if ( ! $version ) {
				$version = 4;
			}

			include_once( "fields/acf-svg_icon-v{$version}.php" );
		}
	}

	new acf_plugin_svg_icon();
}