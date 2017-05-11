# Advanced Custom Fields: SVG #

## Description ##

This enhance ACF with a field icons. Add a field type selector to include your great font icon and returns class icon.
One activated you'll be able to use a custom ACF field type which is an icon selector

## Important to know ##

In case you want to include this small plugin to your project running composer you can add this line to your composer.json :

```json
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/BeAPI/acf-svg-icon"
    }
  ]
```

then run the command :

```shell
composer require bea/acf-svg-icon
```

## Tips ##

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

### 1.0.1
* 11 May 2017
* Initial