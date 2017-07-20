<?php

class acf_field_svg_icon extends acf_field {

	/**
	 * Defaults for the svg.
	 * @var array
	 */
	public $defaults = array();

	function __construct() {
		// vars
		$this->name	 = 'svg_icon';
		$this->label	= __( 'SVG Icon selector', 'acf-svg-icon' );
		$this->category = __( 'Basic', 'acf' );
		$this->defaults = array(
			'allow_clear' => 0,
		);

		// do not delete!
		parent::__construct();
	}

	/**
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	function render_field( $field ) {
		// create Field HTML
		?>
		<select class="widefat acf-svg-icon-<?php echo esc_attr( $field['type'] ); ?>"
			   data-initialvalue="<?php echo esc_attr( $field['value'] ); ?>"
			   name="<?php echo esc_attr( $field['name'] ); ?>"
			   data-placeholder="<?php _e( 'Select an icon', 'acf-svg-icon' ); ?>"
			   data-allow-clear="<?php echo esc_attr( $field['allow_clear'] ) ?>" style="width: 100%;"></select>
		<?php
	}

	/**
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	function render_field_settings( $field ) {

		// allow clear.
		acf_render_field_setting( $field,
			array(
				'label'		=> __( 'Display clear button?', 'acf-svg-icon' ),
				'instructions' => __( 'Whether or not a clear button is displayed when the select box has a selection.', 'acf-svg-icon' ),
				'name'		 => 'allow_clear',
				'type'		 => 'true_false',
				'ui'		   => 1,
			)
		);
	}

	/**
	 * Get the SVG filepath from theme.
	 *
	 * @return mixed|void
	 * @author Nicolas JUEN
	 */
	private function get_svg_file_path() {
		return apply_filters( 'acf_svg_icon_filepath', false );
	}


	/**
	 * Extract icons from svg file.
	 *
	 * @since 1.0.0
	 *
	 * @return array|bool
	 */
	public function parse_svg() {

		/**
		 * The path to the svg file.
		 *
		 * @since 1.0.0
		 *
		 * @param string $filepath default path
		 */
		$file_path = $this->get_svg_file_path();

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
			$out[] = array( 'id' => $id, 'text' => $id, 'disabled' => false );
		}

		// Cache 24 hours.
		wp_cache_set( $cache_key, $out, '', HOUR_IN_SECONDS * 24 );

		return $out;
	}

	/**
	 * Display the css based on the vars given for dynamic fonts url.
	 *
	 * @since 1.0.0
	 */
	public function display_svg() {

		/**
		 * The svg's files URLs
		 *
		 * @since 1.0.0
		 *
		 * @param array $font_urls the default svg file url
		 */
		$file_path = $this->get_svg_file_path();
		if ( empty( $file_path ) ) {
			return;
		}
		include_once( $file_path );
	}

	/**
	 * Enqueue assets for the SVG icon field in admin
	 *
	 * @since 1.0.0
	 */
	function input_admin_enqueue_scripts() {

		// The suffix.
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ? '' : '.min';

		// Scripts.
		wp_register_script(
			'acf-input-svg-icon',
			ACF_SVG_ICON_URL . 'assets/js/input' . $suffix . '.js',
			array( 'jquery', 'select2', 'acf-input' ),
			ACF_SVG_ICON_VER
		);

		// Localizing the script.
		wp_localize_script( 'acf-input-svg-icon', 'svg_icon_format_data', $this->parse_svg() );

		wp_register_style(
			'acf-input-svg-icon',
			ACF_SVG_ICON_URL . 'assets/css/style' . $suffix . '.css',
			array( 'select2' ),
			ACF_SVG_ICON_VER
		);

		// Enqueuing.
		wp_enqueue_script( 'acf-input-svg-icon' );
		wp_enqueue_style( 'select2' );
		wp_enqueue_style( 'acf-input-svg-icon' );
	}

	/**
	 * Display SVG style in head.
	 *
	 * @since 1.0.0
	 */
	public function input_admin_footer() {
		$this->display_svg();
	}
}
