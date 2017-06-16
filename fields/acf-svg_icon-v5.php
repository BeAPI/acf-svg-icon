<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'acf_field_svg_icon' ) )  {
	class acf_field_svg_icon extends acf_field {
		/**
		 *  __construct
		 *
		 *  This function will setup the field type data
		 *
		 *  @type	function
		 *  @date	5/03/2014
		 *  @since	5.0.0
		 *
		 *  @param	n/a
		 *  @return	n/a
		 */
		function __construct( $settings ) {
			// vars
			$this->name	 = 'svg_icon';
			$this->label	= __( 'SVG Icon selector', 'acf-svg_icon' );
			$this->category = 'choice';
			$this->defaults = array(
				'allow_null' 	=> 0,
				'default_value'	=> '',
				'ui'			=> 0,
				'placeholder'	=> '',
				'return_format'	=> 'value'
			);
			$this->l10n = array(
				'matches_1'				=> _x( 'One result is available, press enter to select it.', 'Select2 JS matches_1',	'acf' ),
				'matches_n'				=> _x( '%d results are available, use up and down arrow keys to navigate.',	'Select2 JS matches_n',	'acf' ),
				'matches_0'				=> _x( 'No matches found', 'Select2 JS matches_0',	'acf' ),
				'input_too_short_1'		=> _x( 'Please enter 1 or more characters', 'Select2 JS input_too_short_1', 'acf'  ),
				'input_too_short_n'		=> _x( 'Please enter %d or more characters', 'Select2 JS input_too_short_n', 'acf'  ),
				'input_too_long_1'		=> _x( 'Please delete 1 character', 'Select2 JS input_too_long_1', 'acf'  ),
				'input_too_long_n'		=> _x( 'Please delete %d characters', 'Select2 JS input_too_long_n', 'acf'  ),
				'selection_too_long_1'	=> _x( 'You can only select 1 item', 'Select2 JS selection_too_long_1', 'acf'  ),
				'selection_too_long_n'	=> _x( 'You can only select %d items', 'Select2 JS selection_too_long_n', 'acf'  ),
				'load_more'				=> _x( 'Loading more results&hellip;', 'Select2 JS load_more', 'acf'  ),
				'searching'				=> _x( 'Searching&hellip;', 'Select2 JS searching', 'acf'  ),
				'load_fail'           	=> _x( 'Loading failed', 'Select2 JS load_fail', 'acf'  )
			);
			$this->settings = $settings;

			// do not delete!
	    	parent::__construct();
		}

		/**
		 *  render_field_settings()
		 *
		 *  Create extra settings for your field. These are visible when editing a field
		 *
		 *  @type	action
		 *  @since	3.6
		 *  @date	23/01/13
		 *
		 *  @param	$field (array) the $field being edited
		 *  @return	n/a
		 */
		function render_field_settings( $field ) {
			$field['default_value'] = acf_encode_choices( $field['default_value'], false );

			// default_value
			acf_render_field_setting( $field, array(
				'label'			=> __( 'Default Value', 'acf' ),
				'instructions'	=> __( 'Enter the default SVG #ID', 'acf-svg_icon' ),
				'name'			=> 'default_value',
				'type'			=> 'text',
			) );

			// allow_null
			acf_render_field_setting( $field, array(
				'label'			=> __( 'Allow Null?', 'acf' ),
				'instructions'	=> '',
				'name'			=> 'allow_null',
				'type'			=> 'true_false',
				'ui'			=> 1,
			) );

			// ui
			acf_render_field_setting( $field, array(
				'label'			=> __( 'Stylised UI', 'acf' ),
				'instructions'	=> '',
				'name'			=> 'ui',
				'type'			=> 'true_false',
				'ui'			=> 1,
			) );

			// return_format
			acf_render_field_setting( $field, array(
				'label'			=> __( 'Return Format', 'acf' ),
				'instructions'	=> __( 'Specify the value returned', 'acf' ),
				'type'			=> 'select',
				'name'			=> 'return_format',
				'choices'		=> array(
					'value'			=> __( 'Value','acf' ),
					'label'			=> __( 'Label','acf' ),
					'array'			=> __( 'Both (Array)','acf' )
				)
			) );
		}

		/**
		 *  update_value()
		 *
		 *  This filter is applied to the $value before it is saved in the db
		 *
		 *  @type	filter
		 *  @since	3.6
		 *  @date	23/01/13
		 *
		 *  @param	$value (mixed) the value found in the database
		 *  @param	$post_id (mixed) the $post_id from which the value was loaded
		 *  @param	$field (array) the field array holding all the field options
		 *  @return	$value
		 */
		function update_value( $value, $post_id, $field ) {
			if ( empty( $value ) ) {
				return $value;
			}

			if ( is_array( $value ) ) {
				// save value as strings, so we can clearly search for them in SQL LIKE statements
				$value = array_map( 'strval', $value );
			}

			return $value;
		}

		/**
		 *  format_value()
		 *
		 *  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
		 *
		 *  @type	filter
		 *  @since	3.6
		 *  @date	23/01/13
		 *
		 *  @param	$value (mixed) the value which was loaded from the database
		 *  @param	$post_id (mixed) the $post_id from which the value was loaded
		 *  @param	$field (array) the field array holding all the field options
		 *
		 *  @return	$value (mixed) the modified value
		 */
		function format_value( $value, $post_id, $field ) {
			if ( empty( $value ) ) {
				return $value;
			}

			$label = acf_maybe_get( $field['choices'], $value, $value );

			// value
			if ( $field['return_format'] == 'value' ) {
				// do nothing

			// label
			} elseif ( $field['return_format'] == 'label' ) {
				$value = $label;

			// array
			} elseif ( $field['return_format'] == 'array' ) {
				$value = array(
					'value'	=> $value,
					'label'	=> $label
				);
			}

			return $value;
		}

		/**
		 *  load_field()
		 *
		 *  This filter is applied to the $field after it is loaded from the database
		 *
		 *  @type	filter
		 *  @date	23/01/2013
		 *  @since	3.6.0
		 *
		 *  @param	$field (array) the field array holding all the field options
		 *  @return	$field
		 */
		function load_field( $field ) {
			// Filters for 3rd party customization
			$field['file'] = array();

			$field['file']['path'] = apply_filters( "acf_svg_icon_filepath", '', $field );
			$field['file']['path'] = apply_filters( "acf_svg_icon_filepath/name={$field['_name']}", $field['file']['path'], $field );
			$field['file']['path'] = apply_filters( "acf_svg_icon_filepath/key={$field['key']}", $field['file']['path'], $field );

			$field['file']['url'] = str_replace( get_stylesheet_directory(), get_stylesheet_directory_uri(), $field['file']['path'] );

			$field['choices'] = $this->parse_svg( $field['file']['path'] );

			$field['choices'] = apply_filters( "acf_svg_icon_data", $field['choices'], $field );
			$field['choices'] = apply_filters( "acf_svg_icon_data/name={$field['_name']}", $field['choices'], $field );
			$field['choices'] = apply_filters( "acf_svg_icon_data/key={$field['key']}", $field['choices'], $field );

			return $field;
		}

		/**
		 *  render_field()
		 *
		 *  Create the HTML interface for your field
		 *
		 *  @param	$field (array) the $field being rendered
		 *
		 *  @type	action
		 *  @since	3.6
		 *  @date	23/01/13
		 *
		 *  @param	$field (array) the $field being edited
		 *  @return	n/a
		 */
		function render_field( $field ) {
			// convert to array
			$field['value'] = acf_get_array( $field['value'], false );
			$field['choices'] = acf_get_array( $field['choices'] );

			// placeholder
			if ( empty( $field['placeholder'] ) ) {
				$field['placeholder'] = __( 'Select an icon', 'acf-svg_icon');
			}

			// add empty value (allows '' to be selected)
			if ( empty( $field['value'] ) ) {
				$field['value'] = array( '' );
			}

			// allow null
			// - have tried array_merge but this causes keys to re-index if is numeric (post ID's)
			if ( $field['allow_null'] ) {
				$prepend = array( '' => '- ' . $field['placeholder'] . ' -' );
				$field['choices'] = $prepend + $field['choices'];
			}

			$atts = array(
				'id'				=> $field['id'],
				'class'				=> $field['class'],
				'name'				=> $field['name'],
				'data-file_url'     => $field['file']['url'],
				'data-ui'			=> $field['ui'],
				'data-placeholder'	=> $field['placeholder'],
				'data-allow_null'	=> $field['allow_null']
			);

			// special atts
			foreach ( array( 'readonly', 'disabled' ) as $k ) {
				if ( ! empty( $field[ $k ] ) ) {
					$atts[ $k ] = $k;
				}
			}

			// hidden input
			if ( $field['ui'] ) {
				$v = $field['value'];
				$v = acf_maybe_get( $v, 0, '' );

				acf_hidden_input( array(
					'id'	=> $field['id'] . '-input',
					'name'	=> $field['name'],
					'value'	=> $v
				) );
			}

			echo '<select ' . acf_esc_attr( $atts ) . '>';
				if ( ! empty( $field['choices'] ) ) {
					foreach( $field['choices'] as $k => $v ) {
						$search = html_entity_decode( $k );
						$pos = array_search( $search, $field['value'] );
						$atts = array( 'value' => $k );

						if( $pos !== false ) {
							$atts['selected'] = 'selected';
							$atts['data-i'] = $pos;
						}

						echo '<option ' . acf_esc_attr( $atts ) . '>' . $v . '</option>';
					}
				}
			echo '</select>';
		}

		/**
		 * Extract icons from svg file.
		 *
		 * @since 1.0.0
		 *
		 * @return array|bool
		 */
		public function parse_svg( $file_path = '' ) {
			if ( ! file_exists( $file_path ) ) {
				return array();
			}

			// First try to load icons from the cache.
			$cache_key = 'acf_svg_icon_' . md5( $file_path );
			$out	   = wp_cache_get( $cache_key );
			if ( ! empty( $out ) ) {
				return $out;
			}

			// If not extract them from the CSS file.
			$contents = file_get_contents( $file_path );
			preg_match_all( '#id="(\S+)"#', $contents, $svg );
			array_shift( $svg );

			foreach ( $svg[0] as $id ) {
				$out[ $id ] = $id;
			}

			// Cache 24 hours.
			wp_cache_set( $cache_key, $out, '', HOUR_IN_SECONDS * 24 );

			return $out;
		}

		/**
		 *  input_admin_enqueue_scripts()
		 *
		 *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
		 *  Use this action to add CSS + JavaScript to assist your render_field() action.
		 *
		 *  @type	action (admin_enqueue_scripts)
		 *  @since	3.6
		 *  @date	23/01/13
		 *
		 *  @param	n/a
		 *  @return	n/a
		 */
		function input_admin_enqueue_scripts() {
			global $wp_scripts;

			// bail ealry if the library can't be no enqueue
		   	if ( ! acf_get_setting( 'enqueue_select2' ) ) {
		   		return;
		   	}

		   	$url = $this->settings['url'];
			$version = $this->settings['version'];
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			$select2_major_version = acf_get_setting( 'select2_version' );
			$select2_version = '';
			$select2_script = '';
			$select2_style = '';

			// attempt to find 3rd party Select2 version
			// - avoid including v3 CSS when v4 JS is already enququed
			if ( isset( $wp_scripts->registered['select2'] ) ) {
				$select2_major_version = (int) $wp_scripts->registered['select2']->ver;
			}

			// v4
			if ( $select2_major_version == 4 ) {
				$select2_version = '4.0';
				$select2_script = acf_get_dir( "assets/inc/select2/4/select2.full{$min}.js" );
				$select2_style = acf_get_dir( "assets/inc/select2/4/select2{$min}.css" );
			// v3
			} else {
				$select2_version = '3.5.2';
				$select2_script = acf_get_dir( "assets/inc/select2/3/select2{$min}.js" );
				$select2_style = acf_get_dir( "assets/inc/select2/3/select2.css" );
			}

			wp_enqueue_script( 'select2', $select2_script, array( 'jquery' ), $select2_version );
			wp_enqueue_style( 'select2', $select2_style, '', $select2_version );

			wp_enqueue_script( 'acf-input-svg_icon', "{$url}assets/js/input{$min}.js", array( 'select2', 'acf-input' ), $version );
			wp_enqueue_style( 'acf-input-svg_icon', "{$url}assets/css/input{$min}.css", array( 'select2', 'acf-input' ), $version );
		}
	}

	new acf_field_svg_icon( $this->settings );
}