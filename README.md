# Advanced Custom Fields: SVG #

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

### Using this with your own svg file in your own theme ###

```php
add_filter( 'acf_svg_icon_filepath', 'bea_svg_icon_filepath' );
function bea_svg_icon_filepath( $filepath ) {
    if ( is_file( get_stylesheet_directory() . '/assets/icons/icons.svg' ) ) {
        $filepath = get_stylesheet_directory() . '/assets/icons/icons.svg';
    }
    return $filepath;
}
```

## Changelog ##

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
