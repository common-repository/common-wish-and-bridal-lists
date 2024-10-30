(function ($) {
    $( function () {
        'use strict';
        var $body =  $( 'body' ),
            timer,
            init = function () {
                $body.on('click', '.wb-link-disabled', onClickDisabledLink)
                    .on( 'click', '.wb-ajax', onClickAjax)
                    .on( 'change', 'input[name="wb-qty"]', onChangeQuantity)
                    .on( 'change', '.wb-item-buy input[name="quantity"]', onChangeQuantityBuy)
                    .on( 'click', '.js-editable', onClickEditable);
            },
            onClickDisabledLink = function ( e ) {
                e.preventDefault();
                e.stopImmediatePropagation();
            },
            onClickAjax = function ( e ) {
                e.preventDefault();
                wb_ajax( $(this) );
            },
            onChangeQuantity = function ( e ) {
                var $this = $( this ),
                    value = $this.val();

                if ( value < 1 ) return;

                if ( wb_params.is_bridal_current_user ) {
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        wb_ajax($this, {'qty': value});

                        $this
                            .parents('.product')
                            .find('.add_to_cart_button')
                            .attr('data-quantity', 1 );

                    }, 1000);
                }
            },
            onChangeQuantityBuy = function () {
                var $this = $( this );

                $this.parents('.product')
                    .find('.add_to_cart_button')
                    .attr('data-quantity', $this.val() );
            },
            wb_ajax = function ( $this, addData ) {

                var sendData = {},
                    $block = $this.is('input') ? $this.parent() : $this;

                $.each($this.data(), function (key, value) {
                    sendData[key] = value;
                });

                if (typeof addData !== 'undefined' ) {
                    $.extend( sendData, addData );
                }

                $block.block({
                    message: null,
                    overlayCSS: { background: '#fff', opacity: 0.6 }
                });


                $.post( wb_params.ajax_url, sendData, function(response) {
                        var data = response.data;

                        console.log(data);


                    $block.unblock();
                        if ( data.reload ) {
                            document.location = location.href;
                        } else if ( response.success == true  ) {
                            if ( data.output ) {
                                $this.replaceWith( data.output );
                            }

                            if ( data.trigger ) {
                                $body.trigger( data.trigger );
                            }

                            if (data.update_fragment ) {
                                $.each(data.update_fragment, function (key, value) {
                                    $( key ).replaceWith( value );
                                });
                            }



                        }
                    }
                );

            },
            onClickEditable = function () {
                var $this = $(this),
                    type = $this.data('editable'),
                    text = $this.text(),
                    width = $this.width(),
                    replaceWith = $('<input name="temp" type="text" value="' +  text + '" />').width( width );

                if ( 'date' == type ) {
                    replaceWith.datepicker({
                        onClose: function ( date ) {
                            displayValue(date, $this, replaceWith);

                        }
                    });
                }


                $this.hide()
                    .after( replaceWith );

                replaceWith.focus();

                if ( 'date' != type ) {

                    replaceWith.blur(function () {
                        var $input = $(this);

                        displayValue($input.val(), $this, $input);

                    })
                        .on('keypress', function ( e ) {
                            var key = e.which,
                                $input = $(this);
                            if(key == 13) {
                                displayValue($input.val(), $this, $input);
                            }
                        });
                }

            },
            displayValue = function (value, $elem, $remove) {
                var previousText = $elem.text();

                if (value != '') {
                    $elem.text( value );
                }

                $remove.remove();
                $elem.show();

                if ( value && (value !== previousText) ) {

                    wb_ajax($elem, {
                        action: 'wb_save_bridal_data',
                        nonce: wb_params.bridal_nonce,
                        value: value
                    });
                }
            };
        init();
    });
})(jQuery);
