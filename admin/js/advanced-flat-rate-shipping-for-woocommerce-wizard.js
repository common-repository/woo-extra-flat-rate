(function ($) {
    'use strict';
    $(document).ready(function(){
        /** Plugin Setup Wizard Script START */
        // Hide & show wizard steps based on the url params 
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('require_license')) {
            $('.ds-plugin-setup-wizard-main .tab-panel').hide();
            $( '.ds-plugin-setup-wizard-main #step5' ).show();
        } else {
            $( '.ds-plugin-setup-wizard-main #step1' ).show();
        }
        
        // Plugin setup wizard steps script
        $(document).on('click', '.ds-plugin-setup-wizard-main .tab-panel .btn-primary:not(.ds-wizard-complete)', function () {
            var curruntStep = jQuery(this).closest('.tab-panel').attr('id');
            var nextStep = 'step' + ( parseInt( curruntStep.slice(4,5) ) + 1 ); // Masteringjs.io

            if( 'step5' !== curruntStep ) {
                // Youtube videos stop on next step
                $('iframe[src*="https://www.youtube.com/embed/"]').each(function(){
                   $(this).attr('src', $(this).attr('src'));
                   return false;
                });
                
                jQuery( '#' + curruntStep ).hide();
                jQuery( '#' + nextStep ).show();   
            }
        });

        // Get allow for marketing or not
        if ( $( '.ds-plugin-setup-wizard-main .ds_count_me_in' ).is( ':checked' ) ) {
            $('#fs_marketing_optin input[name="allow-marketing"][value="true"]').prop('checked', true);
        } else {
            $('#fs_marketing_optin input[name="allow-marketing"][value="false"]').prop('checked', true);
        }

        // Get allow for marketing or not on change	    
        $(document).on( 'change', '.ds-plugin-setup-wizard-main .ds_count_me_in', function() {
            if ( this.checked ) {
                $('#fs_marketing_optin input[name="allow-marketing"][value="true"]').prop('checked', true);
            } else {
                $('#fs_marketing_optin input[name="allow-marketing"][value="false"]').prop('checked', true);
            }
        });

        // Complete setup wizard
        $(document).on( 'click', '.ds-plugin-setup-wizard-main .tab-panel .ds-wizard-complete', function() {
            if ( $( '.ds-plugin-setup-wizard-main .ds_count_me_in' ).is( ':checked' ) ) {
                $( '.fs-actions button'  ).trigger('click');
            } else {
                $('.fs-actions #skip_activation')[0].click();
            }
        });

        // Send setup wizard data on Ajax callback
        $(document).on( 'click', '.ds-plugin-setup-wizard-main .fs-actions button', function() {
            var wizardData = {
                'action': 'afrsm_plugin_setup_wizard_submit',
                'survey_list': $('.ds-plugin-setup-wizard-main .ds-wizard-where-hear-select').val(),
                'nonce': afrsfw_wizard_conditional_vars.setup_wizard_ajax_nonce
            };

            $.ajax({
                url: afrsfw_wizard_conditional_vars.ajaxurl,
                data: wizardData,
                success: function ( success ) {
                    console.log(success);
                }
            });
        });
        /** Plugin Setup Wizard Script End */
    });
})(jQuery);