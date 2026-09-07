<?php

class Acf_Field_Svg_Icon extends acf_field {

	/**
	 * Defaults for the svg.
	 *
	 * @var array
	 */
	public $defaults = [];

	/**
	 * Name of the cache key used to store SVG data after processing.
	 *
	 * @deprecated This as been replaced by the constant {@see ACF_SVG_ICON_CACHE_KEY} and
	 *             will be removed in the next version.
	 *
	 * @var string
	 */
	public $cache_key = 'acf_svg_icon_files';

	/**
	 * Symbol id => option id, built on demand by get_icon_options_by_symbol().
	 *
	 * @var array|null
	 */
	private $icon_options_by_symbol = null;

	public function __construct() {
		// vars
		$this->name     = 'svg_icon';
		$this->label    = __( 'SVG Icon selector', 'acf-svg-icon' );
		$this->category = __( 'Basic', 'acf' ); //phpcs:ignore WordPress.WP.I18n.TextDomainMismatch -- default ACF category name
		$this->defaults = [
			'allow_clear' => 0,
		];

		// do not delete!
		parent::__construct();

		// Hooks !
		add_action( 'add_attachment', [ $this, 'flush_cache_for_attachments' ] );
		add_action( 'edit_attachment', [ $this, 'flush_cache_for_attachments' ] );
		add_action( 'delete_attachment', [ $this, 'flush_cache_for_attachments' ] );
	}

	/**
	 *  Create the HTML interface for your field
	 *
	 * @param    $field - an array holding all the field's data
	 *
	 * @type    action
	 * @since    3.6
	 * @date     23/01/13
	 */
	public function render_field( $field ) {
		?>
		<input class="widefat acf-svg-icon-<?php echo esc_attr( $field['type'] ); ?>"
				value="<?php echo esc_attr( $field['value'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>"
				data-placeholder="<?php esc_attr_e( 'Select an icon', 'acf-svg-icon' ); ?>"
				data-allow-clear="<?php echo esc_attr( $field['allow_clear'] ); ?>"/>
		<?php
	}

	/**
	 *  render_field_settings()
	 *
	 *  Create extra options for your field. This is rendered when editing a field.
	 *  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	 *
	 * @type    action
	 *
	 * @param    $field - an array holding all the field's data
	 *
	 * @since    3.6
	 * @date     23/01/13
	 *
	 */
	public function render_field_settings( $field ) {
		// allow clear.
		acf_render_field_setting(
			$field,
			[
				'label'        => __( 'Display clear button?', 'acf-svg-icon' ),
				'instructions' => __( 'Whether or not a clear button is displayed when the select box has a selection.', 'acf-svg-icon' ),
				'name'         => 'allow_clear',
				'type'         => 'true_false',
				'ui'           => 1,
			]
		);
	}

	/**
	 * Icon ids are always stored as `sprite.svg#icon-id` since 2.3.0.
	 *
	 * Values saved by earlier versions in single sprite mode are bare ids
	 * (`icon-alert`) and no longer match any option built by parse_svg(), which
	 * leaves the Select2 field empty. Rewrite them on the fly so the admin can
	 * match the option again. Media library values stay numeric.
	 *
	 * @param mixed $value   Raw field value from the database.
	 * @param mixed $post_id Post ID (unused, required by ACF signature).
	 * @param array $field   Field settings (unused, required by ACF signature).
	 *
	 * @return mixed
	 */
	public function load_value( $value, $post_id, $field ) {
		return $this->normalize_icon_value( $value );
	}

	/**
	 * Persist the canonical `sprite.svg#icon-id` format when the field is saved.
	 *
	 * @param mixed $value   Submitted field value.
	 * @param mixed $post_id Post ID (unused, required by ACF signature).
	 * @param array $field   Field settings (unused, required by ACF signature).
	 *
	 * @return mixed
	 */
	public function update_value( $value, $post_id, $field ) {
		return $this->normalize_icon_value( $value );
	}

	/**
	 * Resolve a legacy bare icon id to the sprite that actually contains it.
	 *
	 * @param mixed $value Raw or submitted field value.
	 *
	 * @return mixed Unchanged when the id belongs to no registered sprite.
	 */
	private function normalize_icon_value( $value ) {
		// Media library SVG: the option id is the attachment ID.
		if ( ! is_string( $value ) || '' === $value || is_numeric( $value ) ) {
			return $value;
		}

		// Already stored in the canonical format.
		if ( false !== strpos( $value, '#' ) ) {
			return $value;
		}

		$options = $this->get_icon_options_by_symbol();

		return isset( $options[ $value ] ) ? $options[ $value ] : $value;
	}

	/**
	 * Map each symbol id to the option id exposed to Select2.
	 *
	 * Example: `icon-alert` => `sprite.svg#icon-alert`. When the same symbol
	 * exists in several sprites, the first registered sprite wins.
	 *
	 * Memoized because parse_svg() reads every sprite from disk, while this map
	 * is only needed for values still using the legacy format.
	 *
	 * @return array
	 */
	private function get_icon_options_by_symbol() {
		if ( null !== $this->icon_options_by_symbol ) {
			return $this->icon_options_by_symbol;
		}

		$options = [];
		foreach ( (array) $this->parse_svg() as $option ) {
			if ( ! isset( $option['id'] ) || ! is_string( $option['id'] ) ) {
				continue;
			}

			$separator = strpos( $option['id'], '#' );
			if ( false === $separator ) {
				continue;
			}

			$symbol = substr( $option['id'], $separator + 1 );
			if ( ! isset( $options[ $symbol ] ) ) {
				$options[ $symbol ] = $option['id'];
			}
		}

		$this->icon_options_by_symbol = $options;

		return $this->icon_options_by_symbol;
	}

	/**
	 * Get the SVG filepath from theme.
	 *
	 * @return array
	 * @author Nicolas JUEN
	 */
	private function get_svg_files_path() {
		$custom_svg_path_icons = apply_filters( 'acf_svg_icon_filepath', [] );

		return array_map(
			function ( $val ) {
				return [
					'type' => 'custom',
					'file' => $val,
				];
			},
			(array) $custom_svg_path_icons
		);
	}

	/**
	 * Merge WP Medias SVG and custom SVG files
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function get_all_svg_files() {
		// First try to load files list from the cache.
		$files = get_transient( ACF_SVG_ICON_CACHE_KEY );
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
		set_transient( ACF_SVG_ICON_CACHE_KEY, $files, HOUR_IN_SECONDS * 24 );

		return $files;
	}

	/**
	 * Extract icons from svg file.
	 *
	 * @return array|bool
	 * @since 1.0.0
	 *
	 */
	public function parse_svg() {
		$files = $this->get_all_svg_files();
		if ( empty( $files ) ) {
			return false;
		}

		/**
		 * Get the allowed tags to parse icon's ids
		 *
		 * @param string $allowed_tags : Passed directly to strip_tags
		 *
		 * @return string
		 * @author david-treblig
		 * @since 2.0.1
		 *
		 */
		$allowed_tags = apply_filters( 'acf_svg_icon_svg_parse_tags', '<symbol><g>' );

		$out = [];

		foreach ( $files as $file ) {
			if ( ! is_file( $file['file'] ) ) {
				continue;
			}

			if ( 'media' === $file['type'] ) {
				$pathinfo = pathinfo( $file['file'] );
				$out[]    = [
					'id'       => $file['id'],
					'text'     => self::get_nice_display_text( $pathinfo['filename'], false ),
					'url'      => $file['file_url'],
					'disabled' => false,
				];
			} else {
				// If not extract them from the CSS file.
				$contents = file_get_contents( $file['file'] ); //phpcs:ignore WordPress.WP.AlternativeFunctions -- use to load local file
				preg_match_all( '/id="(\S+)"/m', strip_tags( $contents, $allowed_tags ), $svg );

				foreach ( $svg[1] as $id ) {
					$id = sanitize_title( $id );
					// Return sprite name and icon name
					$value = basename( $file['file'] ) . '#' . $id;
					$out[] = [
						'id'       => $value,
						'text'     => self::get_nice_display_text( $id ),
						'disabled' => false,
					];
				}
			}
		}

		return apply_filters( 'acf_svg_icon_parsed_svg', $out, $files );
	}

	/**
	 * Get WP Medias SVGs
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function get_medias_svg() {
		$args = [
			'post_type'      => 'attachment',
			'posts_per_page' => '-1',
			'post_status'    => 'inherit',
			'post_mime_type' => 'image/svg+xml',
		];

		/**
		 * Filter WP Query get attachments args
		 *
		 * @param array $args
		 *
		 * @since 2.0.0
		 */
		$args = apply_filters( 'acf_svg_icon_wp_medias_svg_args', $args );

		$attachments = new WP_Query( $args );
		if ( empty( $attachments->posts ) ) {
			return [];
		}

		$svg = [];
		foreach ( $attachments->posts as $attachment ) {
			$svg[] = [
				'type'     => 'media',
				'id'       => $attachment->ID,
				'file'     => get_attached_file( $attachment->ID ),
				'file_url' => wp_get_attachment_url( $attachment->ID ),
			];
		}

		return $svg;
	}

	/**
	 * Format the icon id to get his nicename for display purpose
	 *
	 * @param $id
	 * @param bool $delete_suffix
	 *
	 * @return string
	 * @since 1.2.0
	 */
	public static function get_nice_display_text( $id, $delete_suffix = true ) {
		// Split up the string based on the '-' character
		$ex = explode( '-', $id );
		if ( empty( $ex ) ) {
			return $id;
		}

		// Delete the first value, as it has no real value for the icon name.
		if ( $delete_suffix ) {
			unset( $ex[0] );
		}

		// Remix values into one with spaces
		$text = implode( ' ', $ex );

		// Add uppercase to the first word
		return ucfirst( $text );
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
		 * @param array $font_urls the default svg file url
		 *
		 * @since 1.0.0
		 *
		 */
		$files = $this->get_all_svg_files();
		if ( empty( $files ) ) {
			return;
		}

		foreach ( $files as $file ) {
			// Ignore file type "media" because we use the URL and not the svg embeded.
			if ( 'media' === $file['type'] || ! is_file( $file['file'] ) ) {
				continue;
			}

			$svg = file_get_contents( $file['file'] ); //phpcs:ignore WordPress.WP.AlternativeFunctions -- use to load local file

			if ( true === strpos( $svg, 'style="' ) ) {
				$svg = str_replace( 'style="', 'style="display:none; ', $svg );
			} else {
				$svg = str_replace( '<svg ', '<svg style="display:none;" ', $svg );
			}

			echo $svg; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Enqueue assets for the SVG icon field in admin
	 *
	 * @since 1.0.0
	 */
	public function input_admin_enqueue_scripts() {
		// Min version ?
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ? '' : '.min';

		wp_localize_script( 'acf-input-svg-icon', 'svg_icon_format_data', $this->parse_svg() );
		wp_register_style( 'acf-input-svg-icon', ACF_SVG_ICON_URL . 'assets/css/style' . $suffix . '.css', [ 'select2' ], ACF_SVG_ICON_VER );

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
	 * Flush cache when an SVG is added, update or removed from the medias
	 *
	 * @param $post_ID
	 *
	 * @since 2.0.0
	 *
	 */
	public function flush_cache_for_attachments( $post_ID ) {
		$mime_type = get_post_mime_type( $post_ID );
		if ( 'image/svg+xml' !== $mime_type ) {
			return;
		}

		delete_transient( ACF_SVG_ICON_CACHE_KEY );
	}

	/**
	 * TODO: Pas compris l'intérêt de ce filtre ici
	 *
	 * @param $value
	 * @param $post_id
	 * @param $field
	 *
	 * @return mixed
	 */
	public function format_value( $value, $post_id, $field ) {
		if ( ! is_int( $value ) ) {
			return $value;
		}

		//$file = get_attached_file( $value );
		return $value;
	}
}
