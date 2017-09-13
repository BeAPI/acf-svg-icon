<?php

class acf_field_svg_icon extends acf_field {

	/**
	 * Defaults for the svg.
	 *
	 * @var array
	 */
	public $defaults = array();

	function __construct() {
		// vars
		$this->name     = 'svg_icon';
		$this->label    = __( 'SVG Icon selector', 'acf-svg-icon' );
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
	 * @param    $field - an array holding all the field's data
	 *
	 * @type    action
	 * @since    3.6
	 * @date     23/01/13
	 */
	function render_field( $field ) {
		// create Field HTML
		?>
        <input class="widefat acf-svg-icon-<?php echo esc_attr( $field['type'] ); ?>"
               value="<?php echo esc_attr( $field['value'] ); ?>"
               name="<?php echo esc_attr( $field['name'] ); ?>"
               data-placeholder="<?php _e( 'Select an icon', 'acf-svg-icon' ); ?>"
               data-allow-clear="<?php echo esc_attr( $field['allow_clear'] ) ?>"/>
		<?php
	}

	/**
	 *  render_field_settings()
	 *
	 *  Create extra options for your field. This is rendered when editing a field.
	 *  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	 *
	 * @type    action
	 * @since    3.6
	 * @date     23/01/13
	 *
	 * @param    $field - an array holding all the field's data
	 */
	function render_field_settings( $field ) {

		// allow clear.
		acf_render_field_setting( $field, array(
			'label'        => __( 'Display clear button?', 'acf-svg-icon' ),
			'instructions' => __( 'Whether or not a clear button is displayed when the select box has a selection.', 'acf-svg-icon' ),
			'name'         => 'allow_clear',
			'type'         => 'true_false',
			'ui'           => 1,
		) );
	}

	/**
	 * Get the SVG filepath from theme.
	 *
	 * @return mixed|void
	 * @author Nicolas JUEN
	 */
	private function get_svg_files_path() {
		return apply_filters( 'acf_svg_icon_filepath', array() );
	}

	/**
	 * Merge WP Medias SVG and custom SVG files
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_all_svg_files() {

		// First try to load files from the cache.
		$cache_key = 'acf_svg_icon_files';
		$files     = wp_cache_get( $cache_key );
		if ( ! empty( $files ) ) {
			return $files;
		}

		/**
		 * Get WP Media SVGs
		 *
		 * @since 2.0.0
		 */
		$media_svg_files = $this->get_medias_svg();

		/**
		 * The path to the svg file.
		 *
		 * @since 1.0.0
		 */
		$custom_svg_files = $this->get_svg_files_path();

		$files = array_merge( $media_svg_files, $custom_svg_files );

		// Cache 24 hours.
		wp_cache_set( $cache_key, $files, '', HOUR_IN_SECONDS * 24 );

		return $files;
	}

	/**
	 * Extract icons from svg file.
	 *
	 * @since 1.0.0
	 *
	 * @return array|bool
	 */
	public function parse_svg() {

		$files = $this->get_all_svg_files();
		if ( empty( $files ) ) {
			return false;
		}

		$out = array();

		foreach ( $files as $file ) {
			if ( ! is_file( $file ) ) {
				continue;
			}

			// If not extract them from the CSS file.
			$contents = file_get_contents( $file );
			preg_match_all( '#id="(\S+)"#', $contents, $svg );
			array_shift( $svg );

			foreach ( $svg[0] as $id ) {
				$out[] = array(
					'id'       => $id,
					'text'     => self::get_nice_display_text( $id ),
					'disabled' => false,
				);
			}
		}

		return $out;
	}

	/**
	 * Get WP Medias SVGs
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_medias_svg() {
		$args = array(
			'post_type'      => 'attachment',
			'posts_per_page' => '-1',
			'post_status'    => 'inherit',
			'post_mime_type' => 'image/svg+xml',
		);

		/**
		 * Filter WP Query get attachments args
		 *
		 * @since 2.0.0
		 *
		 * @param array $args
		 */
		$args = apply_filters( 'acf_svg_icon_wp_medias_svg_args', $args );

		$attachments = new WP_Query( $args );
		if ( empty( $attachments->posts ) ) {
			return array();
		}

		$svgs = array();
		foreach ( $attachments->posts as $attachment ) {
		    $file = get_attached_file( $attachment->ID );
		    if ( ! self::check_file_content( $file ) ) {
                continue;
            }
			$svgs[] = $file;
		}

		return $svgs;
	}

	/**
	 * Format the icon id to get his nicename for display purpose
	 *
	 * @param $id
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	public static function get_nice_display_text( $id ) {
		// Split up the string based on the '-' carac
		$ex = explode( '-', $id );
		if ( empty( $ex ) ) {
			return $id;
		}

		// Delete the first value, as it has no real value for the icon name.
		unset( $ex[0] );

		// Remix values into one with spaces
		$text = implode( ' ', $ex );

		// Add uppercase to the first word
		return Ucfirst( $text );
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
		$files = $this->get_all_svg_files();
		if ( empty( $files ) ) {
			return;
		}
		foreach ( $files as $file ) {
			if ( ! is_file( $file ) ) {
				continue;
			}
			include_once( $file );
		}
	}

	/**
	 * Enqueue assets for the SVG icon field in admin
	 *
	 * @since 1.0.0
	 */
	function input_admin_enqueue_scripts() {
		// The suffix
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ? '' : '.min';

		// Localizing the data
		wp_localize_script( 'acf-input-svg-icon', 'svg_icon_format_data', $this->parse_svg() );

		wp_register_style( 'acf-input-svg-icon', ACF_SVG_ICON_URL . 'assets/css/style' . $suffix . '.css', array( 'select2' ), ACF_SVG_ICON_VER );

		// Enqueuing
		wp_enqueue_script( 'acf-input-svg-icon' );
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

	/**
     * Test file content, don't load image svg file
     *
	 * @param $file
     *
     * @since 2.0.0
	 *
	 * @return bool
	 */
	public static function check_file_content( $file ) {
	    $contents = file_get_contents( $file );
	    if ( empty( $contents ) ) {
	        return false;
        }

		if ( false !== strpos( $contents, '<?xml' ) || false !== strpos( $contents, '<!--?xml' ) ) {
			return false;
		}

		return true;
    }
}
