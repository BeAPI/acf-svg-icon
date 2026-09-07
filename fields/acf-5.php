<?php

class Acf_Field_Svg_Icon_5 extends Acf_Field_Svg_Icon {

	public function __construct() { //phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		// do not delete!
		parent::__construct();
	}

	/**
	 * Enqueue assets for the SVG icon field in admin
	 *
	 * @since 1.0.0
	 */
	public function input_admin_enqueue_scripts() {
		// Min version ?
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ? '' : '.min';

		wp_register_script(
			'acf-input-svg-icon',
			ACF_SVG_ICON_URL . 'assets/js/input-5' . $suffix . '.js',
			[
				'jquery',
				'select2',
				'acf-input',
			],
			ACF_SVG_ICON_VER,
			true
		);
		wp_enqueue_script( 'acf-input-svg-icon' );

		parent::input_admin_enqueue_scripts();
	}
}
