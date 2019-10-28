# Advanced Custom Fields: SVG Icon #

This enhance [Advanced Custom Field](https://www.advancedcustomfields.com/pro/) plugin by adding a custom field.

This ACF field is a select2 field in order to include your great fonts. It will allow you to select icons and then return the corresponding class icon.

## Compatibility

This ACF field type is compatible with:
* ACF 5.0.0 and up, that means the pro version.
* ACF 4 (not supported).

## Installation

### via Composer

1. Add a line to your repositories array: `{ "type": "git", "url": "https://github.com/BeAPI/acf-svg-icon" }`
2. Add a line to your require block: `"bea/acf-svg-icon": "dev-master"`
3. Run: `composer update`

### Manual

1. Copy the plugin folder into your plugins folder.
2. Activate the plugin via the plugins admin page.
3. Create a new field via ACF and select the SVG Icon selector.

## How to ##

### Upload SVG into library

You can upload media in your library, it must be an <b>SVG</b>, and then it will be displayed into the SVG dropdown.
In this case, consider using [Scalable Vector Graphics (svg)](https://fr.wordpress.org/plugins/scalable-vector-graphics-svg) for security.

### In your own theme ###

To load several SVGs from your theme (development), use the following filter to add the main sprite SVG file :

```php
<?php add_filter( 'acf_svg_icon_filepath', 'bea_svg_icon_filepath' );
function bea_svg_icon_filepath( $filepath ) {
    if ( is_file( get_stylesheet_directory() . '/assets/icons/icons.svg' ) ) {
        $filepath[] = get_stylesheet_directory() . '/assets/icons/icons.svg';
    }
    return $filepath;
}
```

## Contributing ##

If you gonna change some JS or CSS, we use GULP in order to uglify and minify assets. So please do the following for your PR :
1. install node modules : `npm install`
2. install gulp dependencies : `npm install gulp`
3. then minify assets : `gulp dist`

## Changelog ##

### 2.0.4 - 28 Oct 2019
* FEATURE : add filter `acf_svg_icon_parsed_svg` to filter the icons list
* FIX : fix PHP fatal error with SVG inclusion
* FIX : temporary fix an issue with acf_format method
* IMPROVE : respect WP coding standards

### 2.0.3 - 04 Feb 2019
* FIX : Mixing custom and media sources

### 2.0.2 - 04 Feb 2019
* FIX : Return array in get_all_svg_files function (reverted in 2.0.3)

### 2.0.1 - 19 Nov 2018
* FEATURE [#8](https://github.com/BeAPI/acf-svg-icon/issues/8) :  improve performances on parsing svg from library
* FEATURE [#9](https://github.com/BeAPI/acf-svg-icon/issues/9) :  upload custom SVGs

### 1.2.1 - 21 Aug 2017
* fix notice $acf->version property undefined on ACF versions under 5.6
* use built-in wrapper acf_get_setting('version') to retrieve version

### 1.2.0 - 27 July 2017
* Add compatibility for ACF 5.6.0 and more versions
* Still keep compatibility for ACF 5.6.0 and lower versions
* Add some custom CSS for a more beautiful admin UI
* Now displaying the icon name, not anymore like a slug
* Improve readme

### 1.0.1 - 11 May 2017
* Initial
