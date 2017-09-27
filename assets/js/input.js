( function( $ ) {
    function initialize_field( $el ) {
        var $bea_select = $el.find( 'select' );
        var bea_args = $bea_select.data();
        var bea_elem = function( id, text ) {
            return '<svg class="acf_svg__icon" \
                         aria-hidden="true" \
                         role="img" \
                    > \
                        <use xlink:href="' + bea_args.file_url + '#' + id + '"></use> \
                    </svg> \
                   ' + text;
        };

        acf.add_filter( 'select2_args', function( select2_args, $select, args, $f ) {
            if ( $bea_select === $select ) {
                /**
                 * Checks if it's the Select2 v4 or v3 which is used.
                 *
                 * https://stackoverflow.com/questions/26950588/select2-ajax-define-formatresult-formatselection-and-initselection-roles-and-b#answer-37890878
                 * https://select2.org/configuration/options-api
                 */
                if ( acf.select2.version == 4 ) {
                    select2_args.templateResult = function( state ) {
                        // run default templateResult
                        var text = $.fn.select2.defaults.defaults.templateResult( state );

                        return bea_elem( state.id, text );
                    };
                    select2_args.templateSelection = function( state ) {
                        return bea_elem( state.id, state.text );
                    };
                // v3
                } else {
                    select2_args.formatResult = function( result, container, query, escapeMarkup ) {
                        // run default formatResult
                        var text = $.fn.select2.defaults.formatResult( result, container, query, escapeMarkup );

                        return bea_elem( result.id, text );
                    };
                    select2_args.formatSelection = function( object, $div ) {
                        return bea_elem( object.id, object.text );
                    };
                }
            }

            return select2_args;
        } );

        acf.select2.init(
            $bea_select,
            bea_args,
            $el
        );
    }

    if ( typeof acf.add_action !== 'undefined' ) {
        /**
         *  ready append (ACF5)
         *
         *  These are 2 events which are fired during the page load
         *  ready = on page load similar to $(document).ready()
         *  append = on new DOM elements appended via repeater field
         *
         *  @type    event
         *  @date    20/07/13
         *
         *  @param   $el (jQuery selection) the jQuery element which contains the ACF fields
         *  @return  n/a
         */
        acf.add_action( 'ready append', function( $el ) {
            // search $el for fields of type 'svg_icon'
            acf.get_fields( { type : 'svg_icon'}, $el ).each( function() {
                initialize_field( $( this ) );
            } );
        } );
    }
} )( jQuery );