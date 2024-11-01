(function($) {
    'use strict';

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).on('load', function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.

     */

    $(document).ready(function () {
        $('body').on('change', 'input[name="payment_method"]', function () {
            $('body').trigger('update_checkout');
        });

        /** Checkout WC compatibility Start */
        $( document.body ).on( 'updated_checkout', function(){
            var move_element;
            $('.cfw-shipping-methods-list').addClass('afrsm_shipping');
            $('.cfw-shipping-methods-list li').each( function(){ 
                if( $(this).find('.extra-flate-tool-tip').length > 0){
                    move_element = $(this).find('.extra-flate-tool-tip');
                    $(move_element).appendTo( $(this).find('.cfw-shipping-method-inner') );
                }
                if( $(this).find('.forceall-tooltip').length > 0){
                    move_element = $(this).find('.forceall-tooltip');
                    $(move_element).appendTo( $(this).find('.cfw-shipping-method-inner') );
                }
            });
        });
        /** Checkout WC compatibility End */
    });

    $(window).on('load', function () {
        if ($('.forceall_shipping_method').length) {
            if ($('.forceall_shipping_method').is(':hidden')) {
                updateCartButton();
            }
        }

        function updateCartButton() {
            $('.forceall_shipping_method').attr('checked', true).trigger('change');
            var checked = $('.forceall_shipping_method').is(':checked');
            if (checked === 'true') {
                $('[name="update_cart"]').trigger('click');
            }
        }
    });

})(jQuery);