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

## Usage ##

### Chooses a SVG file for a specific field ###

There are 3 possible ways to use this feature.

1. `acf_svg_icon_filepath` - filter for every field
2. `acf_svg_icon_filepath/name={$field_name}` - filter for a specific field based on it's name
3. `acf_svg_icon_filepath/key={$field_key}` - filter for a specific field based on it's key

```php
add_filter( 'acf_svg_icon_filepath', 'bea_svg_icon_filepath' );
function bea_svg_icon_filepath( $filepath ) {
    return get_theme_file_path( 'assets/icons/icons.svg' );
}
```

### Translates the SVG text alternatives ###

There are 3 possible ways to use this feature.

1. `acf_svg_icon_data` - filter for every field
2. `acf_svg_icon_data/name={$field_name}` - filter for a specific field based on it's name
3. `acf_svg_icon_data/key={$field_key}` - filter for a specific field based on it's key

```php
add_filter( 'acf_svg_icon_data/name=icon', 'bea_svg_icon_data' );
function bea_svg_icon_data( $data ) {
    $data__ = array(
        'IconTwitter' => 'Twitter'
    );

    foreach ( $data__ as $id => $value ) {
        if ( array_key_exists( $id, $data ) ) {
            $data[ $id ] = $value;
        }
    }

    return $data;
}
```

By the way, you can also use this filter to reduce the list of SVG symbols ;)

## Tips to display icon ##

```html
<?php $icon = get_field_object( 'icon' ); ?>
<div class="Icon">
    <svg widht="64" height="64">
        <title><?php echo esc_html( $icon['value']['label'] ); ?></title>
        <use xlink:href="<?php echo esc_url( "{$icon['file']['url']}#{$icon['value']['value']}" ); ?>"></use>
    </svg>
</div>
```

## Changelog ##

### 1.0.1
* 11 May 2017
* Initial