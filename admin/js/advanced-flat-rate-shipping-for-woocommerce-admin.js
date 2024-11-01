(function ($) {
    'use strict';
    jQuery('.multiselect2').select2();

    function allowSpeicalCharacter(str) {
        return str.replace('&#8211;', '–').replace('&gt;', '>').replace('&lt;', '<').replace('&#197;', 'Å');
    }

    function productFilter() {
        jQuery('.product_fees_conditions_values_product').each(function () {
            $('.product_fees_conditions_values_product').select2({
                ajax: {
                    url: coditional_vars.ajaxurl,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            value: params.term,
                            action: 'afrsm_pro_product_fees_conditions_values_product_ajax',
                            nonce: coditional_vars.select_list_ajax_nonce,
                        };
                    },
                    processResults: function (data) {
                        var options = [];
                        if (data) {
                            $.each(data, function (index, text) {
                                options.push({id: text[0], text: allowSpeicalCharacter(text[1])});
                            });

                        }
                        return {
                            results: options
                        };
                    },
                    cache: true
                },
                minimumInputLength: 3
            });
        });
    }

    function setAllAttributes(element, attributes) {
        Object.keys(attributes).forEach(function (key) {
            element.setAttribute(key, attributes[key]);
            // use val
        });
        return element;
    }
    function numberValidateForAdvanceRules() {
        $('.number-field').keypress(function (e) {
            var regex = new RegExp('^[0-9-%.]+$');
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('.qty-class').keypress(function (e) {
            var regex = new RegExp('^[0-9]+$');
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        $('.weight-class, .price-class, .measure-class').keypress(function (e) {
            var regex = new RegExp('^[0-9.]+$');
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
    }

    // Set cookies
    function setCookie(name, value, minutes) {
        var expires = '';
        if (minutes) {
            var date = new Date();
            date.setTime(date.getTime() + (minutes * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + (value || '') + expires + '; path=/';
    }

    // Get cookies
    function getCookie(name) {
        let nameEQ = name + '=';
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    }

    if ($('#is_allow_free_shipping').is(':checked')) {
        $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_section').show();

        var free_shipping_based_on = $('#sm_free_shipping_based_on').val();
        if( free_shipping_based_on === 'min_coupan_amt'){
            $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_coupon').show();
            $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_amt').hide();
        }else{
            $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_coupon').hide();
            $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_amt').show();
        }
    } else {
        $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_section').hide();
    }
    $(document).on('change', '#is_allow_free_shipping', function () {
        if (this.checked) {
            $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_section').show();
            var free_shipping_based_on = $('#sm_free_shipping_based_on').val();
            if( free_shipping_based_on === 'min_coupan_amt'){
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_coupon').show();
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_amt').hide();
            }else{
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_coupon').hide();
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_amt').show();
            }
        } else {
            $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_section').hide();
        }
    });

    $(window).on('load', function () {
        jQuery('.multiselect2').select2();

        $('a[href="admin.php?page=afrsm-pro-list"]').parent().addClass('current');
        $('a[href="admin.php?page=afrsm-pro-list"]').addClass('current');

        // if (jQuery('.afrsm-main-table .wp-list-table tbody tr').length <= 1) {
        //     jQuery('.shipping-methods-order').hide();
        // }

        if ($('#is_allow_free_shipping').is(':checked')) {
            $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_section').show();

            var free_shipping_based_on = $('#sm_free_shipping_based_on').val();
            if( free_shipping_based_on === 'min_coupan_amt'){
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_coupon').show();
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_amt').hide();
            }else{
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_coupon').hide();
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_amt').show();
            }
        } else {
            $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_section').hide();
        }

        /*Start: Get last url parameters*/
        function getUrlVars() {
            var vars = [], hash, get_current_url;
            get_current_url = coditional_vars.current_url;
            var hashes = get_current_url.slice(get_current_url.indexOf('?') + 1).split('&');
            for (var i = 0; i < hashes.length; i++) {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        }

        var ele = $('#total_row').val();
        var count;
        if (ele > 2) {
            count = ele;
        } else {
            count = 2;
        }
        $('body').on('click', '#shipping-add-field', function () {
            var fee_add_field = $('#tbl-shipping-method tbody').get(0);

            var tr = document.createElement('tr');
            tr = setAllAttributes(tr, {'id': 'row_' + count});
            fee_add_field.appendChild(tr);

            // generate td of condition
            var td = document.createElement('td');
            td = setAllAttributes(td, {});
            tr.appendChild(td);
            var conditions = document.createElement('select');
            conditions = setAllAttributes(conditions, {
                'rel-id': count,
                'id': 'product_fees_conditions_condition_' + count,
                'name': 'fees[product_fees_conditions_condition][]',
                'class': 'product_fees_conditions_condition'
            });
            conditions = insertOptions(conditions, get_all_condition());
            td.appendChild(conditions);
            // td ends

            // generate td for equal or no equal to
            td = document.createElement('td');
            td = setAllAttributes(td, {});
            tr.appendChild(td);
            var conditions_is = document.createElement('select');
            conditions_is = setAllAttributes(conditions_is, {
                'name': 'fees[product_fees_conditions_is][]',
                'class': 'product_fees_conditions_is product_fees_conditions_is_' + count
            });
            conditions_is = insertOptions(conditions_is, condition_types(false));
            td.appendChild(conditions_is);
            // td ends

            // td for condition values
            td = document.createElement('td');
            td = setAllAttributes(td, {
                'id': 'column_' + count,
                'class': 'condition-value'
            });
            tr.appendChild(td);
            condition_values(jQuery('#product_fees_conditions_condition_' + count));

            var condition_key = document.createElement('input');
            condition_key = setAllAttributes(condition_key, {
                'type': 'hidden',
                'name': 'condition_key[value_' + count + '][]',
                'value': '',
            });
            td.appendChild(condition_key);
            // var conditions_values_index = jQuery('.product_fees_conditions_values_' + count).get(0);
            jQuery('.product_fees_conditions_values_' + count).trigger('change');
            jQuery('.multiselect2').select2();
            // td ends

            // td for delete button
            td = document.createElement('td');
            tr.appendChild(td);
            var delete_button = document.createElement('a');
            delete_button = setAllAttributes(delete_button, {
                'id': 'fee-delete-field',
                'rel-id': count,
                'title': coditional_vars.delete,
                'class': 'delete-row',
                'href': 'javascript:;'
            });
            var deleteicon = document.createElement('i');
            deleteicon = setAllAttributes(deleteicon, {
                'class': 'dashicons dashicons-trash'
            });
            delete_button.appendChild(deleteicon);
            td.appendChild(delete_button);
            // td ends

            numberValidateForAdvanceRules();
            count++;
        });

        // Script for Display upgrade pro popup on select premium option
        function premiumSelectShowPopup( option, value ) {
            // Script for hide row on change
            $(document).on('change', option, function(){
                if( value === $(this).val() ) {
                    $(this).find(':selected').prop('selected', false);
                    $('body').addClass('afrsm-modal-visible');
                }
            });
        }

        // Script for Display upgrade pro popup
        function afrsm_openPopup() {
            $('body').addClass('afrsm-modal-visible');
            $('.upgrade-to-pro-modal-main .pro-feature-trial-btn').removeClass('upgrade-now');
            $('.upgrade-to-pro-modal-main .pro-feature-trial-btn').addClass('upgrade-pro-afrsm');
        }

        /** Script for Freemius upgrade popup */
        function upgradeToProFreemius( couponCode, isTrackOption ) {
            let handler;
            handler = FS.Checkout.configure({
                plugin_id: '3379',
                plan_id: '5472',
                public_key:'pk_9edf804dccd14eabfd00ff503acaf',
                image: 'https://www.thedotstore.com/wp-content/uploads/sites/1417/2023/09/Advanced-Flat-rate-shipping-Banner-3-new.png',
                coupon: couponCode,
            });

            handler.open({
                name: 'Flat Rate Shipping Plugin',
                subtitle: 'Ad. Flat Rate Shipping For WooCommerce Pro',
                licenses: jQuery('input[name="licence"]:checked').val(),
                purchaseCompleted: function( response ) {
                    if ( isTrackOption ) {
                        var purchaseId = response.purchase.id.toString();
                        var emailId    = coditional_vars.admin_email;
                        var pDate      = coditional_vars.pdate;
                        var pName      = 'Advanced Flat Rate';
                        var EndpointUrl = 'https://pluginsdemo.thedotstore.com/wp-json/wp/v2/dotspluginpurchase';

                        var settings = {
                            'url': EndpointUrl,
                            'method': 'POST',
                            'data': {
                                'purchaseId': purchaseId,
                                'emailId': emailId,
                                'pDate': pDate,
                                'pName': pName
                            },
                            'timeout': 0,
                        };
                            
                        $.ajax(settings).done(function (response) {
                            console.log(response);
                        });
                    } else {
                        console.log (response);
                    }
                },
                success: function (response) {
                    console.log (response);
                }
            });
        }

        $(document).on('click', '.upgrade-to-pro-modal-main .upgrade-pro-afrsm', function(e){
            e.preventDefault();
            track_premium_popup_steps('upgrade_to_pro');

            $('body').removeClass('afrsm-modal-visible');
            let couponCode = $('.upgrade-to-pro-discount-code').val();
            upgradeToProFreemius( couponCode, true );
        });

        $(document).on('click', '.upgrade-to-pro-modal-main .upgrade-now', function(e){
            e.preventDefault();
            $('body').removeClass('afrsm-modal-visible');
            let couponCode = $('.upgrade-to-pro-discount-code').val();
            upgradeToProFreemius( couponCode, false );
        });

        $(document).on('click', '.dotstore-upgrade-dashboard .upgrade-now, .dots-header .dots-upgrade-btn', function(e){
            e.preventDefault();
            upgradeToProFreemius( '', false );
        });

        // script for upgrade to pro modal
        $(document).on('click', '.afrsm-section-left .afrsm-pro-label, .afrsm-section-left .upgrade-now', function(){
            $('body').addClass('afrsm-modal-visible');
        });
        $(document).on('click', '.afrsm-section-left .afrsm-pro-tab', function(){
            $('body').addClass('afrsm-modal-visible');
            $(this).removeClass('current');
            $('ul .tab-link').first().addClass('current');
            $('#tab-11').addClass('current');
        });

        $(document).on('click', '#dotsstoremain .modal-close-btn', function(){
            $('body').removeClass('afrsm-modal-visible');
        });

        $('body').on('change', '.product_fees_conditions_condition', function (e) {
            var selectedOption = $(this).find(':selected');
            if( selectedOption.hasClass('afrsm-pro') ){
                track_premium_popup_steps('option_selected');
                $(this).find(':selected').prop('selected', false);

                afrsm_openPopup();
                condition_values(this);
                e.preventDefault();
            } else {
                condition_values(this);
            }
        });
        /** Upgrade to popup code over*/

        // Premium option selected
        premiumSelectShowPopup( '#what_to_do_method', 'in_pro' );

        /* description toggle */
        $('span.advanced_flat_rate_shipping_for_woocommerce_tab_description').click(function (event) {
            event.preventDefault();
            $(this).next('p.description').toggle();
        });

        let count_product; 
        $('body').on('click', '#ap-add-field', function () {
            var filedTitle = $(this).attr('data-filedtitle');
            var filedType = $(this).attr('data-filedtype');
            var qow = $(this).attr('data-qow');
            var filedTitle2 = $(this).attr('data-filedtitle2');
            
            var row_product_ele = $('#total_row_' + filedTitle).val();
            if (row_product_ele > 2) {
                count_product = row_product_ele;
            } else {
                count_product = 2;
            }
            createAdvancePricingRulesField(filedType, qow, filedTitle, count_product, filedTitle2);
            numberValidateForAdvanceRules();
            count_product++;
            $('#total_row_' + filedTitle).val(count_product);
        });
        $('ul.tabs li').click(function () {
            var tab_id = $(this).attr('data-tab');
            
            $('ul.tabs li').removeClass('current');
            $('.tab-content').removeClass('current');
            
            $(this).addClass('current');
            $('#' + tab_id).addClass('current');
        });

        function createAdvancePricingRulesField (field_type, qty_or_weight, field_title, field_count, field_title2) {
            var label_text, min_input_placeholder, max_input_placeholder, inpt_class, inpt_type;
            if (qty_or_weight === 'qty') {
                label_text = coditional_vars.cart_qty;
            } else if (qty_or_weight === 'weight') {
                label_text = coditional_vars.cart_weight;
            } else if (qty_or_weight === 'subtotal') {
                label_text = coditional_vars.cart_subtotal;
            }
            
            if (qty_or_weight === 'qty') {
                min_input_placeholder = coditional_vars.min_quantity;
            } else if (qty_or_weight === 'weight') {
                min_input_placeholder = coditional_vars.min_weight;
            } else if (qty_or_weight === 'subtotal') {
                min_input_placeholder = coditional_vars.min_subtotal;
            }
            
            if (qty_or_weight === 'qty') {
                max_input_placeholder = coditional_vars.max_quantity;
            } else if (qty_or_weight === 'weight') {
                max_input_placeholder = coditional_vars.max_weight;
            } else if (qty_or_weight === 'subtotal') {
                max_input_placeholder = coditional_vars.max_subtotal;
            }
            
            if (qty_or_weight === 'qty') {
                inpt_class = 'qty-class';
                inpt_type = 'number';
            } else if (qty_or_weight === 'weight') {
                inpt_class = 'weight-class';
                inpt_type = 'text';
            } else if (qty_or_weight === 'subtotal') {
                inpt_class = 'price-class subtotal-class';
                inpt_type = 'text';
            }
            var tr = document.createElement('tr');
            tr = setAllAttributes(tr, {
                'class': 'ap_' + field_title + '_row_tr',
                'id': 'ap_' + field_title + '_row_' + field_count,
            });
            
            var product_td = document.createElement('td');
            if (field_type === 'label') {
                var product_label = document.createElement('label');
                var product_label_text = document.createTextNode(label_text);

                product_label = setAllAttributes(product_label, {
                    'for': label_text.toLowerCase(),
                });
                product_label.appendChild(product_label_text);
                product_td.appendChild(product_label);
                
                var input_hidden = document.createElement('input');
                input_hidden = setAllAttributes(input_hidden, {
                    'id': 'ap_' + field_title + '_fees_conditions_condition_' + field_count,
                    'type': 'hidden',
                    'name': 'fees[ap_' + field_title + '_fees_conditions_condition][' + field_count + '][]',
                });
                product_td.appendChild(input_hidden);
            }
            tr.appendChild(product_td);


            var min_qty_td = document.createElement('td');
            min_qty_td = setAllAttributes(min_qty_td, {
                'class': 'column_' + field_count + ' condition-value',
            });
            var min_qty_input = document.createElement('input');
            if (qty_or_weight === 'qty') {
                min_qty_input = setAllAttributes(min_qty_input, {
                    'type': inpt_type,
                    'id': 'ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '[]',
                    'name': 'fees[ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '][]',
                    'class': 'text-class min-val-class ' + inpt_class,
                    'placeholder': min_input_placeholder,
                    'value': '',
                    'min': '1',
                    'required': '1',
                });
            } else {
                min_qty_input = setAllAttributes(min_qty_input, {
                    'type': inpt_type,
                    'id': 'ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '[]',
                    'name': 'fees[ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '][]',
                    'class': 'text-class min-val-class ' + inpt_class,
                    'placeholder': min_input_placeholder,
                    'value': '',
                    'required': '1',
                });
            }
            
            min_qty_td.appendChild(min_qty_input);
            tr.appendChild(min_qty_td);
            
            var max_qty_td = document.createElement('td');
            max_qty_td = setAllAttributes(max_qty_td, {
                'class': 'column_' + field_count + ' condition-value',
            });
            var max_qty_input = document.createElement('input');
            if (qty_or_weight === 'qty') {
                max_qty_input = setAllAttributes(max_qty_input, {
                    'type': inpt_type,
                    'id': 'ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '[]',
                    'name': 'fees[ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '][]',
                    'class': 'text-class max-val-class ' + inpt_class,
                    'placeholder': max_input_placeholder,
                    'value': '',
                    'min': '1',
                });
            } else {
                max_qty_input = setAllAttributes(max_qty_input, {
                    'type': inpt_type,
                    'id': 'ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '[]',
                    'name': 'fees[ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '][]',
                    'class': 'text-class max-val-class ' + inpt_class,
                    'placeholder': max_input_placeholder,
                    'value': '',
                });
            }
            
            max_qty_td.appendChild(max_qty_input);
            tr.appendChild(max_qty_td);
            
            var price_td = document.createElement('td');
            var price_input = document.createElement('input');
            price_input = setAllAttributes(price_input, {
                'type': 'text',
                'id': 'ap_fees_ap_price_' + field_title + '[]',
                'name': 'fees[ap_fees_ap_price_' + field_title + '][]',
                'class': 'price-val-class text-class number-field',
                'placeholder': coditional_vars.amount,
                'value': '',
                'required': '1',
            });
            price_td.appendChild(price_input);
            tr.appendChild(price_td);
            
            var delete_td = document.createElement('td');
            var delete_a = document.createElement('a');
            delete_a = setAllAttributes(delete_a, {
                'id': 'ap-' + field_title + '-delete-field',
                'rel-id': field_count,
                'title': coditional_vars.delete,
                'class': 'delete-row',
                'href': 'javascript:;'
            });
            var delete_i = document.createElement('i');
            delete_i = setAllAttributes(delete_i, {
                'class': 'dashicons dashicons-trash'
            });
            delete_a.appendChild(delete_i);

            delete_td.appendChild(delete_a);
            
            tr.appendChild(delete_td);
            
            var tBodyTrLast = document.getElementById('tbl_ap_' + field_title + '_method').getElementsByTagName('tbody')[0];
            tBodyTrLast.appendChild(tr);
        }
        
        $('.afrsm-main-table input[name="submitFee"]').on('click', function (e) {
            validation(e);
        });
        
        function validation (e) {
            // fees_pricing_rules
            var getAfrsmSectionLeft = document.getElementsByClassName('afrsm-section-left')[0];
            var validation_color_code = '#dc3232';
            var default_color_code = '#0085BA';
            var fees_pricing_rules_validation = true;
            var product_based_validation = true;
            var apply_per_qty_validation = true;
            var div;
            
            if ($('input[name="ap_rule_status"]').prop('checked') === true) {
                if ($('.pricing_rules:visible').length !== 0) {
                    //set flag default to n
                    
                    var submit_total_cart_weight_form_flag = true;
                    var submit_total_cart_weight_flag = false;

                    var submit_total_cart_subtotal_form_flag = true;
                    var submit_total_cart_subtotal_flag = false;

                    var total_cart_weight_val_arr = [];
                    var total_cart_subtotal_val_arr = [];

                    var no_one_total_cart_weight_row_flag;
                    var no_one_total_cart_subtotal_row_flag;

                    //Start loop each row of AP Product rules
                    no_one_total_cart_weight_row_flag = $('#tbl_ap_total_cart_weight_method tr.ap_total_cart_weight_row_tr').length;
                    no_one_total_cart_subtotal_row_flag = $('#tbl_ap_total_cart_subtotal_method tr.ap_total_cart_subtotal_row_tr').length;
                    
                    var count_total_tr = no_one_total_cart_weight_row_flag +
                            no_one_total_cart_subtotal_row_flag;

                    var current_tab_id;

                    //Start loop each row of AP Total Cart Weight rules
                    if ($('#tbl_ap_total_cart_weight_method tr.ap_total_cart_weight_row_tr').length) {
                        $('#tbl_ap_total_cart_weight_method tr.ap_total_cart_weight_row_tr').each(function () {
                            //initialize variables
                            var total_cart_weight_product_price = '';
                            var min_weight = '',
                                    max_weight = '';
                            var total_cart_weight_tr_id = jQuery(this).attr('id');
                            var total_cart_weight_tr_int_id = total_cart_weight_tr_id.substr(total_cart_weight_tr_id.lastIndexOf('_') + 1);
                            var max_weight_flag = true;

                            //check product empty or not
                            //check product price empty or not
                            if ($(this).find('[name="fees[ap_fees_ap_price_total_cart_weight][]"]').length) {
                                total_cart_weight_product_price = $(this).find('[name="fees[ap_fees_ap_price_total_cart_weight][]"]').val();
                                if (total_cart_weight_product_price === '') {
                                    jQuery($(this).find('[name="fees[ap_fees_ap_price_total_cart_weight][]"]')).css('border', '1px solid ' + validation_color_code);
                                } else {
                                    jQuery($(this).find('[name="fees[ap_fees_ap_price_total_cart_weight][]"]')).css('border', '');
                                }
                            }
                            //check if min quantity empty or not
                            if ($(this).find('[name="fees[ap_fees_ap_total_cart_weight_min_weight][]"]').length) {
                                min_weight = $(this).find('[name="fees[ap_fees_ap_total_cart_weight_min_weight][]"]').val();
                                if (min_weight === '') {
                                    jQuery($(this).find('[name="fees[ap_fees_ap_total_cart_weight_min_weight][]"]')).css('border', '1px solid ' + validation_color_code);
                                } else {
                                    min_weight = parseFloat(min_weight);
                                    jQuery($(this).find('[name="fees[ap_fees_ap_total_cart_weight_min_weight][]"]')).css('border', '');
                                }
                            }

                            //check if max quantity empty or not
                            if ($(this).find('[name="fees[ap_fees_ap_total_cart_weight_max_weight][]"]').length) {
                                max_weight = $(this).find('[name="fees[ap_fees_ap_total_cart_weight_max_weight][]"]').val();
                                if (max_weight !== '' && min_weight !== '') {
                                    max_weight = parseFloat(max_weight);
                                    if (min_weight > max_weight) {
                                        jQuery($(this).find('[name="fees[ap_fees_ap_total_cart_weight_max_weight][]"]')).css('border', '1px solid ' + validation_color_code);
                                        max_weight_flag = false;
                                    } else {
                                        jQuery($(this).find('[name="fees[ap_fees_ap_total_cart_weight_max_weight][]"]')).css('border', '');
                                    }
                                }
                            }

                            //check if both min and max quantity empty than error focus and set prevent submit flag
                            if (min_weight === '' && total_cart_weight_product_price === '') {
                                submit_total_cart_weight_flag = false;
                            } else if (max_weight_flag === false) {
                                submit_total_cart_weight_flag = false;
                                displayMsg('message_cart_weight', coditional_vars.min_max_weight_error);
                            } else if (total_cart_weight_product_price === '') {
                                submit_total_cart_weight_flag = false;
                            } else if (min_weight === '') {
                                submit_total_cart_weight_flag = false;
                            } else {
                                submit_total_cart_weight_flag = true;
                            }

                            total_cart_weight_val_arr[total_cart_weight_tr_int_id] = submit_total_cart_weight_flag;
                        });

                        if (total_cart_weight_val_arr !== '') {
                            current_tab_id = jQuery($('#tbl_ap_total_cart_weight_method tr.ap_total_cart_weight_row_tr').parent().parent().parent().parent()).attr('id');
                            if (jQuery.inArray(false, total_cart_weight_val_arr) !== -1) {
                                submit_total_cart_weight_form_flag = false;
                                changeColorValidation(current_tab_id, false, validation_color_code);
                            } else {
                                submit_total_cart_weight_form_flag = true;
                                changeColorValidation(current_tab_id, true, default_color_code);
                            }
                        }
                    }
                    //End loop each row of AP Total Cart Weight rules

                    //Start loop each row of AP Total Subcart rules
                    if ($('#tbl_ap_total_cart_subtotal_method tr.ap_total_cart_subtotal_row_tr').length) {
                        $('#tbl_ap_total_cart_subtotal_method tr.ap_total_cart_subtotal_row_tr').each(function () {
                            //initialize variables
                            var total_cart_subtotal_product_price = '';
                            var min_subtotal = '',
                                    max_subtotal = '';
                            var total_cart_subtotal_tr_id = jQuery(this).attr('id');
                            var total_cart_subtotal_tr_int_id = total_cart_subtotal_tr_id.substr(total_cart_subtotal_tr_id.lastIndexOf('_') + 1);
                            // var current_total_cart_subtotal_tab_id = jQuery($(this).parent().parent().parent().parent()).attr('id');
                            var max_subtotal_flag = true;

                            //check product empty or not
                            //check product price empty or not
                            if ($(this).find('[name="fees[ap_fees_ap_price_total_cart_subtotal][]"]').length) {
                                total_cart_subtotal_product_price = $(this).find('[name="fees[ap_fees_ap_price_total_cart_subtotal][]"]').val();
                                if (total_cart_subtotal_product_price === '') {
                                    jQuery($(this).find('[name="fees[ap_fees_ap_price_total_cart_subtotal][]"]')).css('border', '1px solid ' + validation_color_code);
                                } else {
                                    jQuery($(this).find('[name="fees[ap_fees_ap_price_total_cart_subtotal][]"]')).css('border', '');
                                }
                            }
                            //check if min quantity empty or not
                            if ($(this).find('[name="fees[ap_fees_ap_total_cart_subtotal_min_subtotal][]"]').length) {
                                min_subtotal = $(this).find('[name="fees[ap_fees_ap_total_cart_subtotal_min_subtotal][]"]').val();
                                if (min_subtotal === '') {
                                    jQuery($(this).find('[name="fees[ap_fees_ap_total_cart_subtotal_min_subtotal][]"]')).css('border', '1px solid ' + validation_color_code);
                                } else {
                                    min_subtotal = parseFloat(min_subtotal);
                                    jQuery($(this).find('[name="fees[ap_fees_ap_total_cart_subtotal_min_subtotal][]"]')).css('border', '');
                                }
                            }

                            //check if max quantity empty or not
                            if ($(this).find('[name="fees[ap_fees_ap_total_cart_subtotal_max_subtotal][]"]').length) {
                                max_subtotal = $(this).find('[name="fees[ap_fees_ap_total_cart_subtotal_max_subtotal][]"]').val();
                                if (max_subtotal !== '' && max_subtotal !== '') {
                                    max_subtotal = parseFloat(max_subtotal);
                                    if (min_subtotal > max_subtotal) {
                                        jQuery($(this).find('[name="fees[ap_fees_ap_total_cart_subtotal_max_subtotal][]"]')).css('border', '1px solid ' + validation_color_code);
                                        max_subtotal_flag = false;
                                    } else {
                                        jQuery($(this).find('[name="fees[ap_fees_ap_total_cart_subtotal_max_subtotal][]"]')).css('border', '');
                                    }
                                }
                            }

                            if (min_subtotal === '' && total_cart_subtotal_product_price === '') {
                                submit_total_cart_subtotal_flag = false;
                            } else if (max_subtotal_flag === false) {
                                submit_total_cart_subtotal_flag = false;
                                displayMsg('message_cart_weight', coditional_vars.min_max_subtotal_error);
                            } else if (total_cart_subtotal_product_price === '') {
                                submit_total_cart_subtotal_flag = false;
                            } else if (min_subtotal === '') {
                                submit_total_cart_subtotal_flag = false;
                            } else {
                                submit_total_cart_subtotal_flag = true;
                            }
                            total_cart_subtotal_val_arr[total_cart_subtotal_tr_int_id] = submit_total_cart_subtotal_flag;
                        });

                        if (total_cart_subtotal_val_arr !== '') {
                            current_tab_id = jQuery($('#tbl_ap_total_cart_subtotal_method tr.ap_total_cart_subtotal_row_tr').parent().parent().parent().parent()).attr('id');
                            if (jQuery.inArray(false, total_cart_subtotal_val_arr) !== -1) {
                                submit_total_cart_subtotal_form_flag = false;
                                changeColorValidation(current_tab_id, false, validation_color_code);
                            } else {
                                submit_total_cart_subtotal_form_flag = true;
                                changeColorValidation(current_tab_id, true, default_color_code);
                            }
                        }
                    }
                    //End loop each row of AP Total Subcart rules


                    //if error in validation than prevent form submit.
                    if ( submit_total_cart_weight_form_flag === false || submit_total_cart_subtotal_form_flag === false ) {//if validate error found
                        fees_pricing_rules_validation = false;
                    } else {
                        if (count_total_tr > 0) {
                            fees_pricing_rules_validation = true;
                        } else {
                            div = document.createElement('div');
                            div = setAllAttributes(div, {
                                'class': 'warning_msg',
                                'id': 'warning_msg_1'
                            });
                            div.textContent = coditional_vars.warning_msg2;
                            $(div).insertBefore('.afrsm-section-left .afrsm-main-table');
                            if ($('#warning_msg_1').length) {
                                $('html, body').animate({scrollTop: 0}, 'slow');
                                setTimeout(function () {
                                    $('#warning_msg_1').remove();
                                }, 7000);
                            }
                            fees_pricing_rules_validation = false;
                        }
                    }
                }
            }

            if ($('input[name="sm_fee_chk_qty_price"]').prop('checked') === true) {
                if ($('#price_cartqty_based').length) {
                    var price_cartqty_based = $('#price_cartqty_based').val();
                    if (price_cartqty_based === 'qty_product_based') {
                        var product_fees_conditions_conditions = $('select[name=\'fees[product_fees_conditions_condition][]\']')
                            .map(function () {
                                return $(this).val();
                            }).get();
                        switch (price_cartqty_based) {
                            case 'qty_product_based':
                                if (product_fees_conditions_conditions.indexOf('product') === -1 && product_fees_conditions_conditions.indexOf('variableproduct') === -1 &&
                                    product_fees_conditions_conditions.indexOf('category') === -1 && product_fees_conditions_conditions.indexOf('tag') === -1
                                    && product_fees_conditions_conditions.indexOf('sku') === -1) {
                                    e.preventDefault();
                                    product_based_validation = false;
                                    if ($('#warning_msg_3').length < 1) {
                                        div = document.createElement('div');
                                        div = setAllAttributes(div, {
                                            'class': 'warning_msg',
                                            'id': 'warning_msg_3'
                                        });
                                        div.textContent = coditional_vars.warning_msg3;
                                        getAfrsmSectionLeft.prepend(div);
                                    }
                                    if ($('#warning_msg_3').length) {
                                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                                        setTimeout(function () {
                                            $('#warning_msg_3').remove();
                                        }, 7000);
                                    }
                                } else {
                                    product_based_validation = true;
                                }
                                break;
                        }
                    }
                }
            }

            /*Apply per qty*/
            if ($('#fee_chk_qty_price').length) {
                if ($('input[name="sm_fee_chk_qty_price"]').prop('checked') === true) {
                    if ($('input[name="ap_rule_status"]').prop('checked') === true) {
                        apply_per_qty_validation = false;
                        if ($('#warning_msg_4').length < 1) {
                            div = document.createElement('div');
                            div = setAllAttributes(div, {
                                'class': 'warning_msg',
                                'id': 'warning_msg_4'
                            });
                            div.textContent = coditional_vars.warning_msg4;
                            getAfrsmSectionLeft.prepend(div);
                        }
                        if ($('#warning_msg_4').length) {
                            $('html, body').animate({ scrollTop: 0 }, 'slow');
                            setTimeout(function () {
                                $('#warning_msg_4').remove();
                            }, 7000);
                        }
                        advancePricingRulesStatus('true');
                    }
                } else {
                    apply_per_qty_validation = true;
                }
            }
            
            /** Tooltip character limit validation */
            if( jQuery('#sm_tooltip_desc').val().length > coditional_vars.tooltip_char_limit ){
                jQuery('.tooltip_error').show();

				$('html, body').animate({
					scrollTop: $('#tooltip_section').offset().top
				}, 'slow');

				e.preventDefault();
				return false;
            } else {
                jQuery('.tooltip_error').hide();
            }

            if (fees_pricing_rules_validation === false ||
                product_based_validation === false ||
                apply_per_qty_validation === false) {
                if ($('#warning_msg_5').length <= 0) {
                    div = document.createElement('div');
                    div = setAllAttributes(div, {
                        'class': 'warning_msg',
                        'id': 'warning_msg_5'
                    });
                    div.textContent = coditional_vars.warning_msg5;
                    getAfrsmSectionLeft.prepend(div);
                }
                if ($('#warning_msg_5').length) {
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                    setTimeout(function () {
                        $('#warning_msg_5').remove();
                    }, 7000);
                }
                e.preventDefault();
                return false;
            } else {
                if (jQuery('.adv-pricing-rules .advance-shipping-method-table').is(':hidden')) {
                    jQuery('.adv-pricing-rules .advance-shipping-method-table tr td input').each(function () {
                        $(this).removeAttr('required');
                    });
                }
                return true;
            }
        }
        
        function changeColorValidation (current_tab, required, validation_color_code) {
            if (required === false) {
                jQuery('.pricing_rules_tab ul li[data-tab=' + current_tab + ']').css('border-top-color', validation_color_code);
                jQuery('.pricing_rules_tab ul li[data-tab=' + current_tab + ']').css('box-shadow', 'inset 0 3px 0 ' + validation_color_code);
            } else {
                jQuery('.pricing_rules_tab ul li[data-tab=' + current_tab + ']').css('border-top-color', '');
                jQuery('.pricing_rules_tab ul li[data-tab=' + current_tab + ']').css('box-shadow', '');
            }
            
        }
        
        function displayMsg (msg_id, msg_content) {
            var getAfrsmSectionLeft = document.getElementsByClassName('afrsm-section-left')[0];
            if ($('#' + msg_id).length <= 0) {
                var msg_div = document.createElement('div');
                msg_div = setAllAttributes(msg_div, {
                    'class': 'warning_msg',
                    'id': msg_id
                });
                
                msg_div.textContent = msg_content;
                getAfrsmSectionLeft.prepend(msg_div);
                
                $('html, body').animate({ scrollTop: 0 }, 'slow');
                setTimeout(function () {
                    $('#' + msg_id).remove();
                }, 7000);
            }
        }

        
        /*Extra Validation*/
        numberValidateForAdvanceRules();

        //remove tr on delete icon click
        $('body').on('click', '.delete-row', function () {
            $(this).parent().parent().remove();
        });

        function insertOptions(parentElement, options) {
            var option;
            for (var i = 0; i < options.length; i++) {
                if (options[i].type === 'optgroup') {
                    var optgroup = document.createElement('optgroup');
                    optgroup = setAllAttributes(optgroup, options[i].attributes);
                    for (var j = 0; j < options[i].options.length; j++) {
                        option = document.createElement('option');
                        option = setAllAttributes(option, options[i].options[j].attributes);
                        option.textContent = options[i].options[j].name;
                        optgroup.appendChild(option);
                    }
                    parentElement.appendChild(optgroup);
                } else {
                    option = document.createElement('option');
                    option = setAllAttributes(option, options[i].attributes);
                    option.textContent = allowSpeicalCharacter(options[i].name);
                    parentElement.appendChild(option);
                }

            }
            return parentElement;

        }

        function allowSpeicalCharacter(str) {
            return str.replace('&#8211;', '–').replace('&gt;', '>').replace('&lt;', '<').replace('&#197;', 'Å');
        }
 
        function get_all_condition() {
            return [
                {
                    'type': 'optgroup',
                    'attributes': {'label': coditional_vars.location_specific},
                    'options': [
                        {'name': coditional_vars.country, 'attributes': {'value': 'country'}},
                        {'name': coditional_vars.state, 'attributes': { 'value': 'state' }},
                        {'name': coditional_vars.city, 'attributes': { 'value': 'city_in_pro', 'class':'afrsm-pro'  } },
                        {'name': coditional_vars.postcode, 'attributes': { 'value': 'postcode' }},
                        {'name': coditional_vars.zone, 'attributes': { 'value': 'zone', 'class':'afrsm-pro' }},
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes': {'label': coditional_vars.product_specific},
                    'options': [
                        {'name': coditional_vars.cart_contains_product, 'attributes': {'value': 'product'}},
                        {'name': coditional_vars.cart_contains_category_product, 'attributes': {'value': 'category'}},
                        {'name': coditional_vars.cart_contains_tag_product, 'attributes': {'value': 'tag'}},
                        {'name': coditional_vars.cart_contains_variable_product, 'attributes': { 'value': 'variableproduct_in_pro', 'class':'afrsm-pro' }},
                        {'name': coditional_vars.cart_contains_sku_product, 'attributes': { 'value': 'sku_in_pro', 'class':'afrsm-pro' }},
						{'name': coditional_vars.cart_contains_product_qty, 'attributes': { 'value': 'product_qty_in_pro', 'class':'afrsm-pro' }},
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes': {'label': coditional_vars.user_specific},
                    'options': [
                        {'name': coditional_vars.user, 'attributes': {'value': 'user'}},
                        {'name': coditional_vars.user_role, 'attributes': { 'value': 'user_role_in_pro', 'class':'afrsm-pro' }}
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes': {'label': coditional_vars.cart_specific},
                    'options': [
                        {'name': coditional_vars.cart_subtotal_before_discount, 'attributes': {'value': 'cart_total'}},
                        {'name': coditional_vars.quantity, 'attributes': {'value': 'quantity'}},
                        {'name': coditional_vars.width, 'attributes': {'value': 'width'}},
                        {'name': coditional_vars.height, 'attributes': {'value': 'height'}},
                        {'name': coditional_vars.length, 'attributes': {'value': 'length'}},
                        {'name': coditional_vars.volume, 'attributes': {'value': 'volume'}},
                        {'name': coditional_vars.cart_subtotal_after_discount, 'attributes': {'value': '', 'class':'afrsm-pro' }},
						{'name': coditional_vars.cart_subtotal_productspecific,	'attributes': {'value': '', 'class':'afrsm-pro' }},
						{'name': coditional_vars.weight, 'attributes': {'value': '', 'class':'afrsm-pro' }},
						{'name': coditional_vars.coupon, 'attributes': {'value': '', 'class':'afrsm-pro' }},
						{'name': coditional_vars.shipping_class, 'attributes': {'value': '', 'class':'afrsm-pro' }}
                    ]
                },
                {
					'type': 'optgroup',
					'attributes': { 'label': coditional_vars.checkout_specific },
					'options': [
						{'name': coditional_vars.payment_method, 'attributes': {'value': 'payment_method_in_pro', 'class':'afrsm-pro' }},
					]
				},
            ];
        }

        function condition_values(element) {
            var condition = $(element).val();
            var count = $(element).attr('rel-id');
            var column = jQuery('#column_' + count).get(0);
            jQuery(column).empty();
            var loader = document.createElement('img');
            loader = setAllAttributes(loader, {'src': coditional_vars.plugin_url + 'images/ajax-loader.gif'});
            column.appendChild(loader);

            $.ajax({
                type: 'GET',
                url: coditional_vars.ajaxurl,
                data: {
                    'action': 'afrsm_pro_product_fees_conditions_values_ajax',
                    'nonce': coditional_vars.afrsm_ajax_nonce,
                    'condition': condition,
                    'count': count
                },
                contentType: 'application/json',
                success: function (response) {
                    var condition_values;
                    jQuery('.product_fees_conditions_is_' + count).empty();
                    var column = jQuery('#column_' + count).get(0);
                    var condition_is = jQuery('.product_fees_conditions_is_' + count).get(0);
                    if (condition === 'cart_total'
                        || condition === 'quantity'
                        || condition === 'width'
                        || condition === 'height'
                        || condition === 'length'
                        || condition === 'volume' ) {
                        condition_is = insertOptions(condition_is, condition_types(true));
                    } else {
                        condition_is = insertOptions(condition_is, condition_types(false));
                    }
                    jQuery('.product_fees_conditions_is_' + count).trigger('chosen:updated');
                    jQuery(column).empty();

                    var condition_values_id = '';
                    var extra_class = '';
                    if (condition === 'product') {
                        condition_values_id = 'product-filter-' + count;
                        extra_class = 'product_fees_conditions_values_product';
                    }

                    if (isJson(response)) {
                        condition_values = document.createElement('select');
                        condition_values = setAllAttributes(condition_values, {
                            'name': 'fees[product_fees_conditions_values][value_' + count + '][]',
                            'class': 'afrsm_select product_fees_conditions_values product_fees_conditions_values_' + count + ' multiselect2 ' + extra_class,
                            'multiple': 'multiple',
                            'id': condition_values_id
                        });
                        if( condition === 'category' ){
                            condition_values.setAttribute('data-placeholder', coditional_vars.select_category);
                        } else if (condition === 'tag') {
                            condition_values.setAttribute('data-placeholder', coditional_vars.select_tag);
                        } else if (condition === 'country') {
                            condition_values.setAttribute('data-placeholder', coditional_vars.select_country);
                        } else if (condition === 'state') {
                            condition_values.setAttribute('data-placeholder', coditional_vars.select_state);
                        } else if (condition === 'zone') {
                            condition_values.setAttribute('data-placeholder', coditional_vars.select_zone);
                        } else if (condition === 'user') {
                            condition_values.setAttribute('data-placeholder', coditional_vars.select_user);
                        } else {
                            condition_values.setAttribute('data-placeholder', coditional_vars.validation_length1);
                        } 
                        column.appendChild(condition_values);
                        var data = JSON.parse(response);
                        condition_values = insertOptions(condition_values, data);
                    } else {
                        var input_extra_class;
                        if (condition === 'quantity') {
                            input_extra_class = ' qty-class';
                        }
                        if ( condition === 'width' || condition === 'height' || condition === 'length' || condition === 'volume' ){
                            input_extra_class = ' measure-class';
                        }
                        if (condition === 'weight') {
                            input_extra_class = ' weight-class';
                        }
                        if (condition === 'cart_total' || condition === 'cart_totalafter' || condition === 'cart_productspecific' || condition === 'last_spent_order') {
                            input_extra_class = ' price-class';
                        }

                        let fieldPlaceholder;
						if ( condition === 'postcode' ) {
							fieldPlaceholder = coditional_vars.select_postcode;
						} else if ( condition === 'quantity' ) {
							fieldPlaceholder = coditional_vars.select_integer_number;
						} else {
							fieldPlaceholder = coditional_vars.select_float_number;
						}

                        condition_values = document.createElement(jQuery.trim(response));
                        condition_values = setAllAttributes(condition_values, {
                            'name': 'fees[product_fees_conditions_values][value_' + count + ']',
                            'class': 'product_fees_conditions_values' + input_extra_class,
                            'type': 'text',

                        });
                        column.appendChild(condition_values);
                    }
                    column = $('#column_' + count).get(0);
                    var input_node = document.createElement('input');
                    input_node = setAllAttributes(input_node, {
                        'type': 'hidden',
                        'name': 'condition_key[value_' + count + '][]',
                        'value': ''
                    });
                    column.appendChild(input_node);

                    jQuery('.multiselect2').select2();
                    productFilter();
                    numberValidateForAdvanceRules();
                }
            });
        }

        function condition_types(text) {
            if (text === true) {
                return [
                    {'name': coditional_vars.equal_to, 'attributes': {'value': 'is_equal_to'}},
                    {'name': coditional_vars.less_or_equal_to, 'attributes': {'value': 'less_equal_to'}},
                    {'name': coditional_vars.less_than, 'attributes': {'value': 'less_then'}},
                    {'name': coditional_vars.greater_or_equal_to, 'attributes': {'value': 'greater_equal_to'}},
                    {'name': coditional_vars.greater_than, 'attributes': {'value': 'greater_then'}},
                    {'name': coditional_vars.not_equal_to, 'attributes': {'value': 'not_in'}},
                ];
            } else {
                return [
                    {'name': coditional_vars.equal_to, 'attributes': {'value': 'is_equal_to'}},
                    {'name': coditional_vars.not_equal_to, 'attributes': {'value': 'not_in'}},
                ];

            }
        }

        productFilter();

        function isJson(str) {
            try {
                JSON.parse(str);
            } catch (err) {
                return false;
            }
            return true;
        }

        var default_placeholder = jQuery('#fee_settings_product_cost').attr('placeholder');
        $('#fee_settings_select_fee_type').change(function () {
            if (jQuery(this).val() === 'fixed') {
                jQuery('#fee_settings_product_cost').attr('placeholder', default_placeholder);
            } else if (jQuery(this).val() === 'percentage') {
                jQuery('#fee_settings_product_cost').attr('placeholder', '%');
            }

        });

        $('body').on('click', '.condition-check-all', function () {
            $('input.multiple_delete_fee:checkbox').not(this).prop('checked', this.checked);
        });

        // $('#delete-shipping-method').click(function () {
        //     if (0 == $('.multiple_delete_fee:checkbox:checked').length) {
        //         alert('Please select at least one shipping method');
        //         return false;
        //     }
        //     if (confirm('Are You Sure You Want to Delete?')) {
        //         var allVals = [];
        //         $('.multiple_delete_fee:checked').each(function () {
        //             allVals.push($(this).val());
        //         });
        //         $.ajax({
        //             type: 'GET',
        //             url: coditional_vars.ajaxurl,
        //             data: {
        //                 'action': 'afrsm_pro_wc_multiple_delete_shipping_method',
        //                 'nonce': coditional_vars.dsm_ajax_nonce,
        //                 'allVals': allVals
        //             },
        //             success: function (response) {
        //                 if (1 == response) {
        //                     alert('Delete Successfully');
        //                     $(".multiple_delete_fee").prop("checked", false);
        //                     location.reload();
        //                 }
        //             }
        //         });
        //     }
        // });

        saveAllIdOrderWise('on_load');

        /*Start code for save all method as per sequence in list*/
        function saveAllIdOrderWise(position) {
            var smOrderArray = [];
            // $('.afrsm_list tbody tr').each(function () {
            //     smOrderArray.push(this.id);
            // });
            jQuery('#the-list tr').each(function(){
				smOrderArray.push(jQuery(this).find('input').val());
			});
            var paged = $('.current-page').val();
            $.ajax({
                type: 'GET',
                url: coditional_vars.ajaxurl,
                data: {
                    'action': 'afrsm_pro_sm_sort_order',
                    'nonce': coditional_vars.afrsm_ajax_nonce,
                    'smOrderArray': smOrderArray,
                    'paged': paged
                },
                success: function () {
                    if ('on_click' === jQuery.trim(position)) {
                        jQuery('.afrsm-main-table .loader-overlay').remove();
                        // alert(coditional_vars.success_msg1);
                        // location.reload();
                    }
                }
            });
        }

        /*End code for save all method as per sequence in list*/

        $('.tablesorter').tablesorter({
            headers: {
                0: {
                    sorter: false
                },
                4: {
                    sorter: false
                }
            }
        });
        var fixHelperModified = function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width());
            });
            return $helper;
        };
        //Make diagnosis table sortable
        if( $('.afrsm_list tbody tr').length > 1 ) {
            $('.afrsm_list tbody').sortable({
                placeholder: {
                    element: function(currentItem) {
                        var cols    =   jQuery(currentItem).children('td').not('.hidden').length + 1;
                        return jQuery('<tr class="ui-sortable-placeholder"><td colspan="' + cols + '">&nbsp;</td></tr>')[0];
                    },
                    update: function() {
                        return;
                    }
                },
                'axis': 'y',
                helper: fixHelperModified,
                stop: function() {
                    saveAllIdOrderWise('on_click');
                }
            });
            $('.afrsm_list tbody').disableSelection();
        }

        // $(document).on('click', '.shipping-methods-order', function () {
        //     var div = document.createElement('div');
		// 	div = setAllAttributes(div, {
		// 		'class': 'loader-overlay',
		// 	});
			
		// 	var img = document.createElement('img');
		// 	img = setAllAttributes(img, {
		// 		'id': 'before_ajax_id',
		// 		'src': coditional_vars.ajax_icon
		// 	});
			
		// 	div.appendChild(img);
		// 	var tBodyTrLast = document.querySelector('.afrsm-main-table');
		// 	tBodyTrLast.appendChild(div);
        //     saveAllIdOrderWise('on_click');
        // });

        //Save Master Settings
        $(document).on('click', '#save_master_settings', function () {
            var shipping_display_mode = $('#shipping_display_mode').val();
            var afrsm_force_customer_to_select_sm = $('#afrsm_force_customer_to_select_sm:checked').val();
            
            var chk_enable_logging;
            if ($('#chk_enable_logging').prop('checked') === true) {
                chk_enable_logging = 'on';
            } else {
                chk_enable_logging = 'off';
            }
            $('.afrsm-main-table').block({
                message: null,
                overlayCSS: {
                    background: 'rgb(255, 255, 255)',
                    opacity: 0.6,
                },
            });
            $.ajax({
                type: 'GET',
                url: coditional_vars.ajaxurl,
                data: {
                    'action': 'afrsm_pro_save_master_settings',
                    'nonce': coditional_vars.genaral_setting_ajax_nonce,
                    'shipping_display_mode': shipping_display_mode,
                    'chk_enable_logging': chk_enable_logging,
                    'afrsm_force_customer_to_select_sm': afrsm_force_customer_to_select_sm,
                },
                success: function () {
                    $('.afrsm-main-table').unblock();
                    var div_wrap = $('<div></div>').addClass('notice notice-success');
                    var p_text = $('<p></p>').text(coditional_vars.success_msg2);
                    div_wrap.append(p_text);
                    $(div_wrap).insertAfter($('.wp-header-end'));
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                    
                    setTimeout( function(){
                        div_wrap.remove();
                    }, 5000 );
                }
            });
        });
 
        $(document).on('change', '#sm_free_shipping_based_on', function () {
            if ( this.value === 'min_coupan_amt'){
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_coupon').show();
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_amt').hide();
            }else{
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_coupon').hide();
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .free_shipping_amt').show();
            }
        });

        if ($('#is_allow_custom_weight_base').is(':checked')) {
            $('.afrsm-section-left .afrsm-main-table .shipping-method-table .depend_of_custom_weight_base').show();
        } else {
            $('.afrsm-section-left .afrsm-main-table .shipping-method-table .depend_of_custom_weight_base').hide();
        }
        if ($('#is_allow_custom_qty_base').is(':checked')) {
            $('.afrsm-section-left .afrsm-main-table .shipping-method-table .depend_of_custom_qty_base').show();
        } else {
            $('.afrsm-section-left .afrsm-main-table .shipping-method-table .depend_of_custom_qty_base').hide();
        }

        $(document).on('change', '#is_allow_custom_weight_base', function () {
            if (this.checked) {
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .depend_of_custom_weight_base').show();
            } else {
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .depend_of_custom_weight_base').hide();
            }
        });
        $(document).on('change', '#is_allow_custom_qty_base', function () {
            if (this.checked) {
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .depend_of_custom_qty_base').show();
            } else {
                $('.afrsm-section-left .afrsm-main-table .shipping-method-table .depend_of_custom_qty_base').hide();
            }
        });

        /* Add AP Category functionality end here */

        $(document).on('click', '#clone_shipping_method', function () {
            var current_shipping_id = $(this).attr('data-attr');
            $.ajax({
                type: 'GET',
                url: coditional_vars.ajaxurl,
                data: {
                    'action': 'afrsm_pro_clone_shipping_method',
                    'nonce': coditional_vars.afrsm_ajax_nonce,
                    'current_shipping_id': current_shipping_id
                }, beforeSend: function () {
                    var div = document.createElement('div');
                    div = setAllAttributes(div, {
                        'class': 'loader-overlay',
                    });

                    var img = document.createElement('img');
                    img = setAllAttributes(img, {
                        'id': 'before_ajax_id',
                        'src': coditional_vars.ajax_icon
                    });

                    div.appendChild(img);
                    var tBodyTrLast = document.getElementById('shipping-methods-listing');
                    tBodyTrLast.appendChild(div);
                }, complete: function () {
                    jQuery('.afrsm-main-table img#before_ajax_id').remove();
                },
                success: function (response) {
                    var response_data = JSON.parse(response);
                    if ('true' === jQuery.trim(response_data['0'])) {
                        location.href = response_data['1'];
                    }
                }
            });
        });

        /*Start: hide show pricing rules status*/
        function advancePricingRulesStatus (args) {
            var url_parameters = getUrlVars();
            if (url_parameters !== '') {
                var current_shipping_id = url_parameters.id;
                var current_value = args;
                $.ajax({
                    type: 'GET',
                    url: coditional_vars.ajaxurl,
                    data: {
                        'action': 'afrsm_pro_change_status_of_advance_pricing_rules',
                        'nonce': coditional_vars.afrsm_ajax_nonce,
                        'current_shipping_id': current_shipping_id,
                        'current_value': current_value
                    },
                    success: function (response) {
                        if ('true' === jQuery.trim(response)) {
                            $('input[name="ap_rule_status"]').prop('checked', false);
                            hideShowPricingRulesBasedOnPricingRuleStatus();
                        }
                    }
                });
            }
        }
        
        /* Hide and show pricing rules based on status */
        hideShowPricingRulesBasedOnPricingRuleStatus();
        
        /* Hide and show pricing rules based on runtime status */
        function hideShowPricingRulesBasedOnPricingRuleStatus () {
            if (true === $('input[name="ap_rule_status"]').prop('checked')) {
                jQuery('.pricing_rules').css('display', 'inline-block');
            } else if (false === $('input[name="ap_rule_status"]').prop('checked')) {
                jQuery('.pricing_rules').css('display', 'none');
            }
        }
         
        /* Hide and show pricing rules based on click on status*/
        $('body').on('click', 'input[name="ap_rule_status"]', function () {
            if (true === $(this).prop('checked')) {
                jQuery('.pricing_rules').css('display', 'inline-block');
            } else if (false === $(this).prop('checked')) {
                jQuery('.pricing_rules').css('display', 'none');
            }
        });

        /* Shipping Zone Section */
        $(document).on('click', '#fetch_old_shipping_zone', function () {
            $.ajax({
                type: 'GET',
                url: coditional_vars.ajaxurl,
                data: {
                    'action': 'afrsm_pro_fetch_shipping_zone',
                }, beforeSend: function () {
                    var div = document.createElement('div');
                    div = setAllAttributes(div, {
                        'class': 'loader-overlay',
                    });
                    
                    var img = document.createElement('img');
                    img = setAllAttributes(img, {
                        'id': 'before_ajax_id',
                        'src': coditional_vars.ajax_icon
                    });
                    
                    div.appendChild(img);
                    var tBodyTrLast = document.getElementsByClassName('advance_zone_listing').getElementsByClassName('wc-col-wrap')[0];
                    tBodyTrLast.appendChild(div);
                }, complete: function () {
                    jQuery('.advance_zone_listing .wc-col-wrap img#before_ajax_id').remove();
                },
                success: function (response) {
                    var response_data = JSON.parse(response);
                    if ('true' === jQuery.trim(response_data[0])) {
                        location.href = response_data[1];
                    } else {
                        location.href = response_data[1];
                    }
                }
            });
        });
        
        $('body').on('click', 'a.shipping-zone-delete', function () {
            var answer = confirm($(this).data('message'));
            if (answer) {
                return true;
            } else {
                return false;
            }
        });
        
        $('body').on('change', 'input[name=zone_type]', function () {
            if ($(this).is(':checked')) {
                setTimeout(function(){
                    $('.chosen-select').select2();
                },5);
                var value = $(this).val();
                $('#add-zone input[type="radio"]').each(function () {
                    var tmp_nm = $(this).val();
                    $('.zone_type_' + tmp_nm).removeClass('active_zone');
                    // if (tmp_nm !== value) {
                    //     $(this).parent().parent().next().attr('style', 'pointer-events: none');
                    // } else {
                    //     $(this).parent().parent().next().attr('style', 'pointer-events: all');
                    // }
                });
                $('.zone_type_' + value).addClass('active_zone');
            }
        });
        $('body').on('click', '.select_us_states', function () {
            $(this).closest('div').find('option[value="US:AK"], option[value="US:AL"], option[value="US:AZ"], option[value="US:AR"], option[value="US:CA"], ' +
                                        'option[value="US:CO"], option[value="US:CT"], option[value="US:DE"], option[value="US:DC"], option[value="US:FL"], option[value="US:GA"], ' +
                                        'option[value="US:HI"], option[value="US:ID"], option[value="US:IL"], option[value="US:IN"], option[value="US:IA"], option[value="US:KS"], ' +
                                        'option[value="US:KY"], option[value="US:LA"], option[value="US:ME"], option[value="US:MD"], option[value="US:MA"], option[value="US:MI"], ' +
                                        'option[value="US:MN"], option[value="US:MS"], option[value="US:MO"], option[value="US:MT"], option[value="US:NE"], option[value="US:NV"], ' +
                                        'option[value="US:NH"], option[value="US:NJ"], option[value="US:NM"], option[value="US:NY"], option[value="US:NC"], option[value="US:ND"], ' +
                                        'option[value="US:OH"], option[value="US:OK"], option[value="US:OR"], option[value="US:PA"], option[value="US:RI"], option[value="US:SC"], ' +
                                        'option[value="US:SD"], option[value="US:TN"], option[value="US:TX"], option[value="US:UT"], option[value="US:VT"], option[value="US:VA"], ' +
                                        'option[value="US:WA"], option[value="US:WV"], option[value="US:WI"], option[value="US:WY"]').attr('selected', 'selected');
            $(this).closest('div').find('select').trigger('change');
            return false;
        });
        $('body').on('click', '.select_europe', function () {
            $(this).closest('div').find('option[value="AL"], option[value="AD"], option[value="AM"], option[value="AT"], option[value="BY"], option[value="BE"], ' +
                                        'option[value="BA"], option[value="BG"], option[value="CH"], option[value="CY"], option[value="CZ"], option[value="DE"], option[value="DK"], ' +
                                        'option[value="EE"], option[value="ES"], option[value="FO"], option[value="FI"], option[value="FR"], option[value="GB"], option[value="GE"], ' +
                                        'option[value="GI"], option[value="GR"], option[value="HU"], option[value="HR"], option[value="IE"], option[value="IS"], option[value="IT"], ' +
                                        'option[value="LT"], option[value="LU"], option[value="LV"], option[value="MC"], option[value="MK"], option[value="MT"], option[value="NO"], ' +
                                        'option[value="NL"], option[value="PO"], option[value="PT"], option[value="RO"], option[value="RU"], option[value="SE"], option[value="SI"], ' +
                                        'option[value="SK"], option[value="SM"], option[value="TR"], option[value="UA"], option[value="VA"]').attr('selected', 'selected');
            $(this).closest('div').find('select').trigger('change');
            return false;
        });
        $('body').on('click', '.select_asia', function () {
            $(this).closest('div').find('option[value="AE"], option[value="AF"], option[value="AM"], option[value="AZ"], option[value="BD"], option[value="BH"], ' +
                                        'option[value="BN"], option[value="BT"], option[value="CC"], option[value="CN"], option[value="CX"], option[value="CY"], option[value="GE"], ' +
                                        'option[value="HK"], option[value="ID"], option[value="IL"], option[value="IN"], option[value="IO"], option[value="IQ"], option[value="IR"], ' +
                                        'option[value="JO"], option[value="JP"], option[value="KG"], option[value="KH"], option[value="KP"], option[value="KR"], option[value="KW"], ' +
                                        'option[value="KZ"], option[value="LA"], option[value="LB"], option[value="LK"], option[value="MM"], option[value="MN"], option[value="MO"], ' +
                                        'option[value="MV"], option[value="MY"], option[value="NP"], option[value="OM"], option[value="PH"], option[value="PK"], option[value="PS"], ' +
                                        'option[value="QA"], option[value="SA"], option[value="SG"], option[value="SY"], option[value="TH"], option[value="TJ"], option[value="TL"], ' +
                                        'option[value="TM"], option[value="TW"], option[value="UZ"], option[value="VN"], option[value="YE"]').attr('selected', 'selected');
            $(this).closest('div').find('select').trigger('change');
            return false;
        });
        $('body').on('click', '.select_africa', function () {
            $(this).closest('div').find(' option[value="AO"], option[value="BF"], option[value="BI"], option[value="BJ"], option[value="BW"], option[value="CD"], ' +
                                        'option[value="CF"], option[value="CG"], option[value="CI"], option[value="CM"], option[value="CV"], option[value="DJ"], option[value="DZ"], ' +
                                        'option[value="EG"], option[value="EH"], option[value="ER"], option[value="ET"], option[value="GA"], option[value="GH"], option[value="GM"], ' +
                                        'option[value="GN"], option[value="GQ"], option[value="GW"], option[value="KE"], option[value="KM"], option[value="LR"], option[value="LS"], ' +
                                        'option[value="LY"], option[value="MA"], option[value="MG"], option[value="ML"], option[value="MR"], option[value="MU"], option[value="MW"], ' +
                                        'option[value="MZ"], option[value="NA"], option[value="NE"], option[value="NG"], option[value="RE"], option[value="RW"], option[value="SC"], ' +
                                        'option[value="SD"], option[value="SS"], option[value="SH"], option[value="SL"], option[value="SN"], option[value="SO"], option[value="ST"], ' +
                                        'option[value="SZ"], option[value="TD"], option[value="TG"], option[value="TN"], option[value="TZ"], option[value="UG"], option[value="YT"], ' +
                                        'option[value="ZA"], option[value="ZM"], option[value="ZW"]')
                .attr('selected', 'selected');
            $(this).closest('div').find('select').trigger('change');
            return false;
        });
        $('body').on('click', '.select_antarctica', function () {
            $(this).closest('div').find('option[value="AQ"], option[value="BV"], option[value="GS"], option[value="HM"], option[value="TF"]').attr('selected', 'selected');
            $(this).closest('div').find('select').trigger('change');
            return false;
        });
        $('body').on('click', '.select_northamerica', function () {
            $(this).closest('div').find('option[value="AG"], option[value="AI"], option[value="AN"], option[value="AW"], option[value="BB"], option[value="BL"], ' +
                                        'option[value="BM"], option[value="BS"], option[value="BZ"], option[value="CA"], option[value="CR"], option[value="CU"], option[value="DM"], ' +
                                        'option[value="DO"], option[value="GD"], option[value="GL"], option[value="GP"], option[value="GT"], option[value="HN"], option[value="HT"], ' +
                                        'option[value="JM"], option[value="KN"], option[value="KY"], option[value="LC"], option[value="MF"], option[value="MQ"], option[value="MS"], ' +
                                        'option[value="MX"], option[value="NI"], option[value="PA"], option[value="PM"], option[value="PR"], option[value="SV"], option[value="TC"], ' +
                                        'option[value="TT"], option[value="US"], option[value="VC"], option[value="VG"], option[value="VI"]').attr('selected', 'selected');
            $(this).closest('div').find('select').trigger('change');
            return false;
        });
        $('body').on('click', '.select_oceania', function () {
            $(this).closest('div').find(' option[value="AS"], option[value="AU"], option[value="CK"], option[value="FJ"], option[value="FM"], option[value="GU"], ' +
                                        'option[value="KI"], option[value="MH"], option[value="MP"], option[value="NC"], option[value="NF"], option[value="NR"], option[value="NU"], ' +
                                        'option[value="NZ"], option[value="PF"], option[value="PG"], option[value="PN"], option[value="PW"], option[value="SB"], option[value="TK"], ' +
                                        'option[value="TO"], option[value="TV"], option[value="UM"], option[value="VU"], option[value="WF"], option[value="WS"]').attr('selected', 'selected');
            $(this).closest('div').find('select').trigger('change');
            return false;
        });
        $('body').on('click', '.select_southamerica', function () {
            $(this).closest('div').find(' option[value="AR"], option[value="BO"], option[value="BR"], option[value="CL"], option[value="CO"], option[value="EC"], ' +
                                        'option[value="FK"], option[value="GF"], option[value="GY"], option[value="PE"], option[value="PY"], option[value="SR"], option[value="UY"], ' +
                                        'option[value="VE"]').attr('selected', 'selected');
            $(this).closest('div').find('select').trigger('change');
            return false;
        });
        $('body').on('click', '.select_africa_states', function () {
            $(this).closest('div').find('option[value="ZA:EC"], option[value="ZA:FS"], option[value="ZA:GP"], option[value="ZA:KZN"], option[value="ZA:LP"], ' +
                                        'option[value="ZA:MP"], option[value="ZA:NC"], option[value="ZA:NW"], option[value="ZA:WC"]').attr('selected', 'selected');
            $(this).closest('div').find('select').trigger('change');
            return false;
        });
        $('body').on('click', '.select_asia_states', function () {
            $(this).closest('div').find('option[value="BD"],option[value="BD:BAG"], option[value="BD:BAN"], option[value="BD:BAR"], option[value="BD:BARI"], ' +
                                        'option[value="BD:BHO"], option[value="BD:BOG"], option[value="BD:BRA"], option[value="BD:CHA"], option[value="BD:CHI"], option[value="BD:CHU"], ' +
                                        'option[value="BD:COM"], option[value="BD:COX"], option[value="BD:DHA"], option[value="BD:DIN"], option[value="BD:FAR"], option[value="BD:FEN"], ' +
                                        'option[value="BD:GAI"], option[value="BD:GAZI"], option[value="BD:GOP"], option[value="BD:HAB"], option[value="BD:JAM"], option[value="BD:JES"], ' +
                                        'option[value="BD:JHA"], option[value="BD:JHE"], option[value="BD:JOY"], option[value="BD:KHA"], option[value="BD:KHU"], option[value="BD:KIS"], ' +
                                        'option[value="BD:KUR"], option[value="BD:KUS"], option[value="BD:LAK"], option[value="BD:LAL"], option[value="BD:MAD"], option[value="BD:MAG"], ' +
                                        'option[value="BD:MAN"], option[value="BD:MEH"], option[value="BD:MOU"], option[value="BD:MUN"], option[value="BD:MYM"], option[value="BD:NAO"], ' +
                                        'option[value="BD:NAR"], option[value="BD:NARG"], option[value="BD:NARD"], option[value="BD:NAT"], option[value="BD:NAW"], option[value="BD:NET"], ' +
                                        'option[value="BD:NIL"], option[value="BD:NOA"], option[value="BD:PAB"], option[value="BD:PAN"], option[value="BD:PAT"], option[value="BD:PIR"], ' +
                                        'option[value="BD:RAJB"], option[value="BD:RAJ"], option[value="BD:RAN"], option[value="BD:RANP"], option[value="BD:SAT"], option[value="BD:SHA"], ' +
                                        'option[value="BD:SHE"], option[value="BD:SIR"], option[value="BD:SUN"], option[value="BD:SYL"], option[value="BD:TAN"], option[value="BD:THA"],' +
                                        'option[value="CN:CN1"], option[value="CN:CN2"],option[value="CN"], option[value="CN:CN3"], option[value="CN:CN4"], option[value="CN:CN5"], ' +
                                        'option[value="CN:CN6"], option[value="CN:CN7"], option[value="CN:CN8"], option[value="CN:CN9"], option[value="CN:CN10"], option[value="CN:CN11"], ' +
                                        'option[value="CN:CN12"], option[value="CN:CN13"], option[value="CN:CN14"], option[value="CN:CN15"], option[value="CN:CN16"], option[value="CN:CN17"], ' +
                                        'option[value="CN:CN18"], option[value="CN:CN19"], option[value="CN:CN20"], option[value="CN:CN21"], option[value="CN:CN22"], option[value="CN:CN23"], ' +
                                        'option[value="CN:CN24"], option[value="CN:CN25"], option[value="CN:CN26"], option[value="CN:CN27"], option[value="CN:CN28"], option[value="CN:CN29"], ' +
                                        'option[value="CN:CN30"], option[value="CN:CN31"], option[value="CN:CN32"],option[value="HK:HONG KONG"], option[value="HK:KOWLOON"], ' +
                                        'option[value="HK:NEW TERRITORIES"], option[value="HK:KOWLOON"], option[value="HK:NEW TERRITORIES"],option[value="HK"], option[value="ID"], ' +
                                        'option[value="ID:AC"], option[value="ID:SU"], option[value="ID:SB"], option[value="ID:RI"], option[value="ID:KR"], option[value="ID:JA"], ' +
                                        'option[value="ID:SS"], option[value="ID:BB"], option[value="ID:BE"], option[value="ID:LA"], option[value="ID:JK"], option[value="ID:JB"], ' +
                                        'option[value="ID:BT"], option[value="ID:JT"], option[value="ID:JI"], option[value="ID:YO"], option[value="ID:BA"], option[value="ID:NB"], ' +
                                        'option[value="ID:NT"], option[value="ID:KB"], option[value="ID:KT"], option[value="ID:KI"], option[value="ID:KS"], option[value="ID:KU"], ' +
                                        'option[value="ID:SA"], option[value="ID:ST"], option[value="ID:SG"], option[value="ID:SR"], option[value="ID:SN"], option[value="ID:GO"], ' +
                                        'option[value="ID:MA"], option[value="ID:MU"], option[value="ID:PA"], option[value="ID:PB"],option[value="IN"], option[value="IN:AP"], ' +
                                        'option[value="IN:AR"], option[value="IN:AS"], option[value="IN:BR"], option[value="IN:CT"], option[value="IN:GA"], option[value="IN:GJ"], ' +
                                        'option[value="IN:HR"], option[value="IN:HP"], option[value="IN:JK"], option[value="IN:JH"], option[value="IN:KA"], option[value="IN:KL"], ' +
                                        'option[value="IN:MP"], option[value="IN:MH"], option[value="IN:MN"], option[value="IN:ML"], option[value="IN:MZ"], option[value="IN:NL"], ' +
                                        'option[value="IN:OR"], option[value="IN:PB"], option[value="IN:RJ"], option[value="IN:SK"], option[value="IN:TN"], option[value="IN:TS"], ' +
                                        'option[value="IN:TR"], option[value="IN:UK"], option[value="IN:UP"], option[value="IN:WB"], option[value="IN:AN"], option[value="IN:CH"], ' +
                                        'option[value="IN:DN"], option[value="IN:DD"], option[value="IN:DL"], option[value="IN:LD"], option[value="IN:PY"],option[value="IR"], ' +
                                        'option[value="IR:KHZ"], option[value="IR:THR"], option[value="IR:ILM"], option[value="IR:BHR"], option[value="IR:ADL"], option[value="IR:ESF"], ' +
                                        'option[value="IR:YZD"], option[value="IR:KRH"], option[value="IR:KRN"], option[value="IR:HDN"], option[value="IR:GZN"], option[value="IR:ZJN"], ' +
                                        'option[value="IR:LRS"], option[value="IR:ABZ"], option[value="IR:EAZ"], option[value="IR:WAZ"], option[value="IR:CHB"], option[value="IR:SKH"], ' +
                                        'option[value="IR:RKH"], option[value="IR:NKH"], option[value="IR:SMN"], option[value="IR:FRS"], option[value="IR:QHM"], option[value="IR:KRD"], ' +
                                        'option[value="IR:KBD"], option[value="IR:GLS"], option[value="IR:GIL"], option[value="IR:MZN"], option[value="IR:MKZ"], option[value="IR:HRZ"], ' +
                                        'option[value="IR:SBN"],option[value="JP"], option[value="JP:JP01"], option[value="JP:JP02"], option[value="JP:JP03"], option[value="JP:JP04"], ' +
                                        'option[value="JP:JP05"], option[value="JP:JP06"], option[value="JP:JP07"], option[value="JP:JP08"], option[value="JP:JP09"], option[value="JP:JP10"], ' +
                                        'option[value="JP:JP11"], option[value="JP:JP12"], option[value="JP:JP13"], option[value="JP:JP14"], option[value="JP:JP15"], option[value="JP:JP16"], ' +
                                        'option[value="JP:JP17"], option[value="JP:JP18"], option[value="JP:JP19"], option[value="JP:JP20"], option[value="JP:JP21"], option[value="JP:JP22"], ' +
                                        'option[value="JP:JP23"], option[value="JP:JP24"], option[value="JP:JP25"], option[value="JP:JP26"], option[value="JP:JP27"], option[value="JP:JP28"], ' +
                                        'option[value="JP:JP29"], option[value="JP:JP30"], option[value="JP:JP31"], option[value="JP:JP32"], option[value="JP:JP33"], option[value="JP:JP34"], ' +
                                        'option[value="JP:JP35"], option[value="JP:JP36"], option[value="JP:JP37"], option[value="JP:JP38"], option[value="JP:JP39"], option[value="JP:JP40"], ' +
                                        'option[value="JP:JP41"], option[value="JP:JP42"], option[value="JP:JP43"], option[value="JP:JP44"], option[value="JP:JP45"], option[value="JP:JP46"], ' +
                                        'option[value="JP:JP47"],option[value="MY"], option[value="MY:JHR"], option[value="MY:KDH"], option[value="MY:KTN"], option[value="MY:LBN"], ' +
                                        'option[value="MY:MLK"], option[value="MY:NSN"], option[value="MY:PHG"], option[value="MY:PNG"], option[value="MY:PRK"], option[value="MY:PLS"], ' +
                                        'option[value="MY:SBH"], option[value="MY:SWK"], option[value="MY:SGR"], option[value="MY:TRG"], option[value="MY:PJY"], option[value="MY:KUL"],' +
                                        'option[value="NP"], option[value="NP:BAG"], option[value="NP:BHE"], option[value="NP:DHA"], option[value="NP:GAN"], option[value="NP:JAN"], ' +
                                        'option[value="NP:KAR"], option[value="NP:KOS"], option[value="NP:LUM"], option[value="NP:MAH"], option[value="NP:MEC"], option[value="NP:NAR"], ' +
                                        'option[value="NP:RAP"], option[value="NP:SAG"], option[value="NP:SET"],option[value="PH"], option[value="PH:ABR"], option[value="PH:AGN"], ' +
                                        'option[value="PH:AGS"], option[value="PH:AKL"], option[value="PH:ALB"], option[value="PH:ANT"], option[value="PH:APA"], option[value="PH:AUR"], ' +
                                        'option[value="PH:BAS"], option[value="PH:BAN"], option[value="PH:BTN"], option[value="PH:BTG"], option[value="PH:BEN"], option[value="PH:BIL"], ' +
                                        'option[value="PH:BOH"], option[value="PH:BUK"], option[value="PH:BUL"], option[value="PH:CAG"], option[value="PH:CAN"], option[value="PH:CAS"], ' +
                                        'option[value="PH:CAM"], option[value="PH:CAP"], option[value="PH:CAT"], option[value="PH:CAV"], option[value="PH:CEB"], option[value="PH:COM"], ' +
                                        'option[value="PH:NCO"], option[value="PH:DAV"], option[value="PH:DAS"], option[value="PH:DAC"], option[value="PH:DAO"], option[value="PH:DIN"],' +
                                        'option[value="PH:EAS"], option[value="PH:GUI"], option[value="PH:IFU"], option[value="PH:ILN"], option[value="PH:ILS"], option[value="PH:ILI"], ' +
                                        'option[value="PH:ISA"], option[value="PH:KAL"], option[value="PH:LUN"], option[value="PH:LAG"], option[value="PH:LAN"], option[value="PH:LAS"], ' +
                                        'option[value="PH:LEY"], option[value="PH:MAG"], option[value="PH:MAD"], option[value="PH:MAS"], option[value="PH:MSC"], option[value="PH:MSR"], ' +
                                        'option[value="PH:MOU"], option[value="PH:NEC"], option[value="PH:NER"], option[value="PH:NSA"], option[value="PH:NUE"], option[value="PH:NUV"], ' +
                                        'option[value="PH:MDC"], option[value="PH:MDR"], option[value="PH:PLW"], option[value="PH:PAM"], option[value="PH:PAN"], option[value="PH:QUE"], ' +
                                        'option[value="PH:QUI"], option[value="PH:RIZ"], option[value="PH:ROM"], option[value="PH:WSA"], option[value="PH:SAR"], option[value="PH:SIQ"], ' +
                                        'option[value="PH:SOR"], option[value="PH:SCO"], option[value="PH:SLE"], option[value="PH:SUK"], option[value="PH:SLU"], option[value="PH:SUN"], ' +
                                        'option[value="PH:SUR"], option[value="PH:TAR"], option[value="PH:TAW"], option[value="PH:ZMB"], option[value="PH:ZAN"], option[value="PH:ZAS"], ' +
                                        'option[value="PH:ZSI"], option[value="PH:00"],option[value="TH"], option[value="TH:TH-37"], option[value="TH:TH-15"], option[value="TH:TH-14"], ' +
                                        'option[value="TH:TH-10"], option[value="TH:TH-38"], option[value="TH:TH-31"], option[value="TH:TH-24"], option[value="TH:TH-18"], ' +
                                        'option[value="TH:TH-36"], option[value="TH:TH-22"], option[value="TH:TH-50"], option[value="TH:TH-57"], option[value="TH:TH-20"], ' +
                                        'option[value="TH:TH-86"], option[value="TH:TH-46"], option[value="TH:TH-62"], option[value="TH:TH-71"], option[value="TH:TH-40"],' +
                                        ' option[value="TH:TH-81"], option[value="TH:TH-52"], option[value="TH:TH-51"], option[value="TH:TH-42"], option[value="TH:TH-16"], ' +
                                        'option[value="TH:TH-58"], option[value="TH:TH-44"], option[value="TH:TH-49"], option[value="TH:TH-26"], option[value="TH:TH-73"], ' +
                                        'option[value="TH:TH-48"], option[value="TH:TH-30"], option[value="TH:TH-60"], option[value="TH:TH-80"], option[value="TH:TH-55"], ' +
                                        'option[value="TH:TH-96"], option[value="TH:TH-39"], option[value="TH:TH-43"], option[value="TH:TH-12"], option[value="TH:TH-13"], ' +
                                        'option[value="TH:TH-94"], option[value="TH:TH-82"], option[value="TH:TH-93"], option[value="TH:TH-56"], option[value="TH:TH-67"], ' +
                                        'option[value="TH:TH-76"], option[value="TH:TH-66"], option[value="TH:TH-65"], option[value="TH:TH-54"], option[value="TH:TH-83"], ' +
                                        'option[value="TH:TH-25"], option[value="TH:TH-77"], option[value="TH:TH-85"], option[value="TH:TH-70"], option[value="TH:TH-21"], ' +
                                        'option[value="TH:TH-45"], option[value="TH:TH-27"], option[value="TH:TH-47"], option[value="TH:TH-11"], option[value="TH:TH-74"], ' +
                                        'option[value="TH:TH-75"], option[value="TH:TH-19"], option[value="TH:TH-91"], option[value="TH:TH-17"], option[value="TH:TH-33"], ' +
                                        'option[value="TH:TH-90"], option[value="TH:TH-64"], option[value="TH:TH-72"], option[value="TH:TH-84"], option[value="TH:TH-32"], ' +
                                        'option[value="TH:TH-63"], option[value="TH:TH-92"], option[value="TH:TH-23"], option[value="TH:TH-34"], option[value="TH:TH-41"], ' +
                                        'option[value="TH:TH-61"], option[value="TH:TH-53"], option[value="TH:TH-95"], option[value="TH:TH-35"]').attr('selected', 'selected');
            $(this).closest('div').find('select').trigger('change');
            return false;
        });
        $('body').on('click', '.select_oceania_states', function () {
            $(this).closest('div').find('option[value="AU"], option[value="AU:ACT"], option[value="AU:NSW"], option[value="AU:NT"], option[value="AU:QLD"], ' +
                                        'option[value="AU:SA"], option[value="AU:TAS"], option[value="AU:VIC"], option[value="AU:WA"],option[value="NZ"], option[value="NZ:NL"], ' +
                                        'option[value="NZ:AK"], option[value="NZ:WA"], option[value="NZ:BP"], option[value="NZ:TK"], option[value="NZ:GI"], option[value="NZ:HB"], ' +
                                        'option[value="NZ:MW"], option[value="NZ:WE"], option[value="NZ:NS"], option[value="NZ:MB"], option[value="NZ:TM"], option[value="NZ:WC"], ' +
                                        'option[value="NZ:CT"], option[value="NZ:OT"], option[value="NZ:SL"]').attr('selected', 'selected');
            $(this).closest('div').find('select').trigger('change');
            return false;
        });
        $('body').on('click', '.select_none', function () {
            $(this).closest('div').find('select option').removeAttr('selected');
            $(this).closest('div').find('select').trigger('change');
            return false;
        });
        $('body').on('click', '.select_all', function () {
            $(this).closest('div').find('select option').attr('selected', 'selected');
            $(this).closest('div').find('select').trigger('change');
            return false;
        });
        /**
         * Hide zone type
        //  */
        // $('.zone_type_options').hide();
        $('input[name=zone_type]').change();
        
        /**
         * Select availability
         */
        $('select.availability').change(function () {
            if ($(this).val() === 'all') {
                $(this).closest('tr').next('tr').hide();
                $(this).closest('tr').next('tr').next('tr').hide();
                $(this).closest('tr').next('tr').next('tr').next('tr').hide();
                $(this).closest('tr').next('tr').next('tr').next('tr').next('tr').hide();
            } else if ($(this).val() === 'specific') {
                $(this).closest('tr').next('tr').show();
                $(this).closest('tr').next('tr').next('tr').hide();
                $(this).closest('tr').next('tr').next('tr').next('tr').hide();
                $(this).closest('tr').next('tr').next('tr').next('tr').next('tr').hide();
            } else if ($(this).val() === 'Countrybase') {
                $(this).closest('tr').next('tr').hide();
                $(this).closest('tr').next('tr').next('tr').show();
                $(this).closest('tr').next('tr').next('tr').next('tr').show();
                $(this).closest('tr').next('tr').next('tr').next('tr').next('tr').show();
            } else {
                $(this).closest('tr').next('tr').hide();
                $(this).closest('tr').next('tr').next('tr').hide();
                $(this).closest('tr').next('tr').next('tr').next('tr').hide();
                $(this).closest('tr').next('tr').next('tr').next('tr').next('tr').hide();
            }
        }).change();
        /* Shipping Zone Section */
    });
    jQuery(window).on('load', function () {
        jQuery('.multiselect2').select2();
        jQuery( '.product_fees_conditions_values_country' ).select2({
			placeholder: coditional_vars.select_country
		});
        jQuery('#tbl-shipping-method tr').each(function() {
            var val = jQuery(this).find('.th_product_fees_conditions_condition select').val();
            var get_placehoder = coditional_vars['select_'+val];
            if( jQuery(this).find('.condition-value textarea').length ){
                jQuery(this).find('.condition-value textarea').attr('placeholder', get_placehoder);
            } else {
                $( '.product_fees_conditions_values_'+val ).select2({
                    placeholder: get_placehoder
                }); 
            }
        });

        function allowSpeicalCharacter(str) {
            return str.replace('&#8211;', '–').replace('&gt;', '>').replace('&lt;', '<').replace('&#197;', 'Å');
        }

        jQuery('.product_fees_conditions_values_product').each(function () {
            jQuery('.product_fees_conditions_values_product').select2();
            jQuery('.product_fees_conditions_values_product').select2({
                ajax: {
                    url: coditional_vars.ajaxurl,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            value: params.term,
                            action: 'afrsm_pro_product_fees_conditions_values_product_ajax',
                            nonce: coditional_vars.select_list_ajax_nonce,
                        };
                    },
                    processResults: function (data) {
                        var options = [];
                        if (data) {
                            jQuery.each(data, function (index, text) {
                                options.push({id: text[0], text: allowSpeicalCharacter(text[1])});
                            });

                        }
                        return {
                            results: options
                        };
                    },
                    cache: true
                },
                minimumInputLength: 3
            });
        });

        /*Start: Change shipping status form list section*/
        $(document).on('click', '#shipping_status_id', function () {
            var current_shipping_id = $(this).attr('data-smid');
            var current_value = $(this).prop('checked');
            var search = $('#shipping-method-search-input').val();
            $.ajax({
                type: 'GET',
                url: coditional_vars.ajaxurl,
                data: {
                    'action': 'afrsm_pro_change_status_from_list_section',
                    'nonce': coditional_vars.afrsm_ajax_nonce,
                    'current_shipping_id': current_shipping_id,
                    'current_value': current_value,
                    's':search
                }, beforeSend: function () {
                    var div = document.createElement('div');
                    div = setAllAttributes(div, {
                        'class': 'loader-overlay',
                    });

                    var img = document.createElement('img');
                    img = setAllAttributes(img, {
                        'id': 'before_ajax_id',
                        'src': coditional_vars.ajax_icon
                    });

                    div.appendChild(img);
                    var tBodyTrLast = document.querySelector('.afrsm-main-table');
					tBodyTrLast.appendChild(div);
                }, complete: function () {
                    jQuery('.afrsm-main-table .loader-overlay').remove();
                }, success: function (response) {
                    jQuery('.active_list').text(response.active_count);
                    // alert(jQuery.trim(response.message));
                }
            });
        });
        /*End: Change shipping status form list section*/

        $( 'a[href="admin.php?page=afrsm-pro-list"]' ).parents().addClass( 'current wp-has-current-submenu' );
		$( 'a[href="admin.php?page=afrsm-pro-list"]' ).addClass( 'current' );
    });

    jQuery(document).ready(function() {
        
        /** tiptip js implementation */
		$( '.woocommerce-help-tip' ).tipTip( {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200,
			'keepAlive': true
		} );

        // Toggle dynamic rules visibility script start
	    var show_dynamic_rules = localStorage.getItem('afrsm-charges-rules-display');
	    if( ( null !== show_dynamic_rules || undefined !== show_dynamic_rules ) && ( 'hide' === show_dynamic_rules ) ) {
	        $('.afrsm_dynamic_rules_tooltips p').addClass('afrsm-dynamic-rules-hide');
	        $('.afrsm_dynamic_rules_tooltips p + .afrsm_dynamic_rules_content').css('display', 'none');
	    } else {
	        $('.afrsm_dynamic_rules_tooltips p').removeClass('afrsm-dynamic-rules-hide');
	        $('.afrsm_dynamic_rules_tooltips p + .afrsm_dynamic_rules_content').css('display', 'block');
	    }

	    $(document).on( 'click', '.afrsm_dynamic_rules_tooltips p', function(){
	        $(this).toggleClass('afrsm-dynamic-rules-hide');
	        $(this).next('.afrsm_dynamic_rules_content').slideToggle(300);
	        if( $(this).hasClass('afrsm-dynamic-rules-hide') ){
	            localStorage.setItem('afrsm-charges-rules-display', 'hide');
	        } else {
	            localStorage.setItem('afrsm-charges-rules-display', 'show');
	        }
	    });
	    // Toggle dynamic rules visibility script end

        $('#afrsm_chk_advanced_settings').click(function(){
            $('.afrsm_advanced_setting_section').toggle();
            if( !jQuery('#sm_select_day_of_week').data('select2') ) {
                var placeholder = jQuery('#sm_select_day_of_week').attr('placeholder');
                jQuery('#sm_select_day_of_week').select2({
                    placeholder: placeholder
                });
            }
        });

        // Script for Beacon configuration
        var helpBeaconCookie = getCookie( 'afrsm-help-beacon-hide' );
        if ( ! helpBeaconCookie ) {
            Beacon('init', 'afe1c188-3c3b-4c5f-9dbd-87329301c920');
            Beacon('config', {
                display: {
                    style: 'icon',
                    iconImage: 'message',
                    zIndex: '99999'
                }
            });

            // Add plugin articles IDs to display in beacon
            Beacon('suggest', ['5d52af180428631e94f95049', '5d52b4280428631e94f9509b', '5d52b5b72c7d3a68825e95b2', '609385cb8af76a714bfde74f', '5d67bb732c7d3a7a4d77d609']);

            // Add custom close icon form beacon
            setTimeout(function() {
                if ( $( '.hsds-beacon .BeaconFabButtonFrame' ).length > 0 ) {
                    let newElement = document.createElement('span');
                    newElement.classList.add('dashicons', 'dashicons-no-alt', 'dots-beacon-close');
                    let container = document.getElementsByClassName('BeaconFabButtonFrame');
                    container[0].appendChild( newElement );
                }
            }, 3000);

            // Hide beacon
            $(document).on('click', '.dots-beacon-close', function(){
                Beacon('destroy');
                setCookie( 'afrsm-help-beacon-hide' , 'true', 24 * 60 );
            });
        }
	});

    /** Dynamic Promotional Bar START */
    $(document).on('click', '.dpbpop-close', function () {
        var popupName 		= $(this).attr('data-popup-name');
        setCookie( 'banner_' + popupName, 'yes', 60 * 24 * 7);
        $('.' + popupName).hide();
    });

    $(document).on('click', '.dpb-popup .dpb-popup-meta a', function () {
        var promotional_id = $(this).parents().find('.dpbpop-close').attr('data-bar-id');

        //Create a new Student object using the values from the textfields
        var apiData = {
            'bar_id' : promotional_id
        };

        $.ajax({
            type: 'POST',
            url: coditional_vars.dpb_api_url + 'wp-content/plugins/dots-dynamic-promotional-banner/bar-response.php',
            data: JSON.stringify(apiData),// now data come in this function
            dataType: 'json',
            cors: true,
            contentType:'application/json',
            
            success: function (data) {
                console.log(data);
            },
            error: function () {
            }
         });
    });
    /** Dynamic Promotional Bar END */

    // Dashboard features popup script
    $(document).on('click', '.dotstore-upgrade-dashboard .premium-key-fetures .premium-feature-popup', function (event) {
        let $trigger = $('.feature-explanation-popup, .feature-explanation-popup *');
        if(!$trigger.is(event.target) && $trigger.has(event.target).length === 0){
            $('.feature-explanation-popup-main').not($(this).find('.feature-explanation-popup-main')).hide();
            $(this).parents('li').find('.feature-explanation-popup-main').show();
            $('body').addClass('feature-explanation-popup-visible');
        }
    });
    $(document).on('click', '.dotstore-upgrade-dashboard .popup-close-btn', function () {
        $(this).parents('.feature-explanation-popup-main').hide();
        $('body').removeClass('feature-explanation-popup-visible');
    });
    /** Upgrade Dashboard Script End */

    $( '.sm_reset_time' ).click(function(){
        $( '#sm_time_from' ).val('');
        $( '#sm_time_to' ).val('');
    });

    /**
     * Track the popup steps
     */
    function track_premium_popup_steps(step){
        var EndpointUrl = 'https://pluginsdemo.thedotstore.com/wp-json/wp/v2/dotspluginpurchase';
        var pDate       = coditional_vars.pdate;
        var settings = {
            'url': EndpointUrl,
            'method': 'POST',
            'data': {
                'pDate': pDate,
                'step': step
            },
            'timeout': 0,
        };
            
        $.ajax(settings).done(function (response) {
            console.log(response);
        });
    }

})(jQuery);