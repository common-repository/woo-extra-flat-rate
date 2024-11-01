<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Class AFRSM_Shipping_Method.
 *
 * WooCommerce Advanced flat rate shipping method class.
 */
if ( class_exists( 'AFRSM_Shipping_Method' ) ) {
    return;
    // Stop if the class already exists
}
class AFRSM_Shipping_Method extends WC_Shipping_Method {
    private static $admin_object = null;

    private $fee_cost = 0;

    /**
     * Constructor
     *
     * @since 3.0.0
     */
    public function __construct() {
        $get_id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
        $post_title = ( isset( $get_id ) ? get_the_title( $get_id ) : '' );
        $shipping_method_id = ( isset( $get_id ) && !empty( $get_id ) ? $get_id : 'advanced_flat_rate_shipping' );
        $shipping_method_title = ( !empty( $post_title ) ? $post_title : esc_html__( 'Advanced Flat Rate Shipping', 'advanced-flat-rate-shipping-for-woocommerce' ) );
        $this->id = $shipping_method_id;
        $this->title = __( 'Advanced Flat Rate Shipping', 'advanced-flat-rate-shipping-for-woocommerce' );
        $this->method_title = __( $shipping_method_title, 'advanced-flat-rate-shipping-for-woocommerce' );
        $this->afrsm_shipping_init();
        // Save settings
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options') );
        self::$admin_object = new Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin('', '');
    }

    /**
     * Init
     *
     * @since 3.0.0
     */
    function afrsm_shipping_init() {
        $this->afrsm_shipping_init_form_fields();
        $this->init_settings();
    }

    /**
     * Init form fields.
     *
     * @since 3.0.0
     */
    public function afrsm_shipping_init_form_fields() {
        $this->form_fields = array(
            'advanced_flat_rate_shipping_table' => array(
                'type' => 'advanced_flat_rate_shipping_table',
            ),
        );
    }

    /**
     * List all shipping method
     *
     * @since 3.0.0
     */
    public function afrsm_shipping_generate_advanced_flat_rate_shipping_table_html() {
        ob_start();
        require plugin_dir_path( __FILE__ ) . 'afrsm-pro-list-page.php';
        return ob_get_clean();
    }

    /**
     * Calculate shipping.
     *
     * @param array $package List containing all products for this method.
     *
     * @return bool false if $matched_shipping_methods is false then it will return false
     * @since 3.0.0
     *
     * @uses  get_default_language()
     * @uses  afrsm_match_methods()
     * @uses  WC_Cart::get_cart()
     * @uses  afrsm_allow_customer()
     * @uses  afrsm_forceall()
     * @uses  afrsm_fees_per_qty_on_ap_rules_off()
     * @uses  afrsm_cart_subtotal_before_discount_cost()
     * @uses  afrsm_cart_subtotal_after_discount_cost()
     * @uses  afrsm_evaluate_cost()
     * @uses  afrsm_get_package_item_qty()
     * @uses  afrsm_find_shipping_classes()
     * @uses  get_term_by()
     * @uses  WC_Shipping_Method::add_rate()
     *
     */
    public function calculate_shipping( $package = array() ) {
        global $sitepress;
        $default_lang = self::$admin_object->afrsm_pro_get_default_langugae_with_sitpress();
        $matched_shipping_methods = $this->afrsm_shipping_match_methods( $package, $sitepress, $default_lang );
        if ( false === $matched_shipping_methods || !is_array( $matched_shipping_methods ) || empty( $matched_shipping_methods ) ) {
            $Custom_shipping = apply_filters( 'no_afrsm_availble', array() );
            if ( !empty( $Custom_shipping ) && is_array( $Custom_shipping ) ) {
                $this->add_rate( $Custom_shipping );
                return false;
            } else {
                return false;
            }
        }
        $cart_array = self::$admin_object->afrsm_pro_get_cart();
        // Compatibility with Autoship Cloud powered by QPilot (#45167)
        if ( function_exists( 'autoship_cart_has_valid_autoship_items' ) ) {
            if ( autoship_cart_has_valid_autoship_items() ) {
                return;
            }
        }
        $getSortOrder = get_option( 'sm_sortable_order_' . $default_lang );
        $sort_order = array();
        if ( !empty( $getSortOrder ) ) {
            foreach ( $getSortOrder as $getSortOrder_id ) {
                settype( $getSortOrder_id, 'integer' );
                if ( in_array( $getSortOrder_id, $matched_shipping_methods, true ) ) {
                    $sort_order[] = $getSortOrder_id;
                }
            }
            unset($matched_shipping_methods);
            $matched_shipping_methods = $sort_order;
        }
        /**
         * match shipping methods
         */
        if ( !empty( $matched_shipping_methods ) ) {
            // ordering issue and highest, smallest, forceall shipping issue code
            foreach ( $matched_shipping_methods as $main_shipping_method_id_val ) {
                if ( !empty( $main_shipping_method_id_val ) || $main_shipping_method_id_val !== 0 ) {
                    if ( !empty( $sitepress ) ) {
                        $shipping_method_id_val = apply_filters(
                            'wpml_object_id',
                            $main_shipping_method_id_val,
                            'wc_afrsm',
                            true,
                            $default_lang
                        );
                    } else {
                        $shipping_method_id_val = $main_shipping_method_id_val;
                    }
                    $shipping_title = get_the_title( $shipping_method_id_val );
                    $shipping_rate = array(
                        'id'    => 'advanced_flat_rate_shipping' . ':' . $shipping_method_id_val,
                        'label' => __( $shipping_title, 'advanced-flat-rate-shipping-for-woocommerce' ),
                        'cost'  => 0,
                    );
                    $cart_based_qty = '0';
                    if ( !empty( $cart_array ) ) {
                        $cart_product_ids_arr = array();
                        foreach ( $cart_array as $value ) {
                            if ( !empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
                                $product_id_lan = $value['variation_id'];
                            } else {
                                $product_id_lan = $value['product_id'];
                            }
                            $cart_product_ids_arr[] = $product_id_lan;
                            $_product = wc_get_product( $product_id_lan );
                            $check_virtual = self::$admin_object->afrsm_check_product_type_for_front( $_product, $value );
                            if ( true === $check_virtual ) {
                                $cart_based_qty += $value['quantity'];
                            }
                        }
                    }
                    // Calculate the costs
                    $has_costs = false;
                    // True when a cost is set. False if all costs are blank strings.
                    $costs = get_post_meta( $shipping_method_id_val, 'sm_product_cost', true );
                    $cost_args = array(
                        'qty'  => $this->afrsm_shipping_get_package_item_qty( $package ),
                        'cost' => $package['contents_cost'],
                    );
                    $costs = $this->afrsm_shipping_evaluate_cost( $costs, $cost_args );
                    $ap_rule_status = get_post_meta( $shipping_method_id_val, 'ap_rule_status', true );
                    $cost_on_total_cart_weight_status = get_post_meta( $shipping_method_id_val, 'cost_on_total_cart_weight_status', true );
                    $cost_on_total_cart_subtotal_status = get_post_meta( $shipping_method_id_val, 'cost_on_total_cart_subtotal_status', true );
                    if ( 'on' === $ap_rule_status ) {
                        $cost_rule_match = get_post_meta( $shipping_method_id_val, 'cost_rule_match', true );
                        if ( !empty( $cost_rule_match ) ) {
                            if ( is_serialized( $cost_rule_match ) ) {
                                $cost_rule_match = maybe_unserialize( $cost_rule_match );
                            } else {
                                $cost_rule_match = $cost_rule_match;
                            }
                            if ( array_key_exists( 'cost_on_total_cart_weight_rule_match', $cost_rule_match ) ) {
                                $cost_on_total_cart_weight_rule_match = $cost_rule_match['cost_on_total_cart_weight_rule_match'];
                            } else {
                                $cost_on_total_cart_weight_rule_match = 'any';
                            }
                            if ( array_key_exists( 'cost_on_total_cart_subtotal_rule_match', $cost_rule_match ) ) {
                                $cost_on_total_cart_subtotal_rule_match = $cost_rule_match['cost_on_total_cart_subtotal_rule_match'];
                            } else {
                                $cost_on_total_cart_subtotal_rule_match = 'any';
                            }
                        } else {
                            $cost_on_total_cart_weight_rule_match = 'any';
                            $cost_on_total_cart_subtotal_rule_match = 'any';
                        }
                        $get_condition_array_ap_total_cart_weight = get_post_meta( $shipping_method_id_val, 'sm_metabox_ap_total_cart_weight', true );
                        $get_condition_array_ap_total_cart_subtotal = get_post_meta( $shipping_method_id_val, 'sm_metabox_ap_total_cart_subtotal', true );
                    } else {
                        $get_condition_array_ap_total_cart_weight = '';
                        $get_condition_array_ap_total_cart_subtotal = '';
                        $cost_on_total_cart_weight_rule_match = 'any';
                        $cost_on_total_cart_subtotal_rule_match = 'any';
                    }
                    $cost = $costs;
                    $sm_taxable = get_post_meta( $shipping_method_id_val, 'sm_select_taxable', true );
                    $sm_extra_cost_calculation_type = get_post_meta( $shipping_method_id_val, 'sm_extra_cost_calculation_type', true );
                    if ( '' !== $cost ) {
                        $has_costs = true;
                        $cost_args = array(
                            'qty'  => $this->afrsm_shipping_get_package_item_qty( $package ),
                            'cost' => $package['contents_cost'],
                        );
                        $shipping_rate['cost'] = $this->afrsm_shipping_evaluate_cost( $cost, $cost_args );
                    }
                    // Add shipping class costs
                    $found_shipping_classes = $this->afrsm_shipping_find_shipping_classes( $package );
                    $highest_class_cost = 0;
                    if ( !empty( $found_shipping_classes ) ) {
                        foreach ( $found_shipping_classes as $shipping_class => $products ) {
                            $shipping_class_term = get_term_by( 'slug', $shipping_class, 'product_shipping_class' );
                            $shipping_extra_id = '';
                            if ( false !== $shipping_class_term ) {
                                if ( !empty( $sitepress ) ) {
                                    $shipping_extra_id = apply_filters(
                                        'wpml_object_id',
                                        $shipping_class_term->term_id,
                                        'product_shipping_class',
                                        true,
                                        $default_lang
                                    );
                                } else {
                                    $shipping_extra_id = $shipping_class_term->term_id;
                                }
                            }
                            $sm_extra_cost = get_post_meta( $shipping_method_id_val, 'sm_extra_cost', true );
                            $class_cost_string = ( isset( $sm_extra_cost[$shipping_extra_id] ) && !empty( $sm_extra_cost[$shipping_extra_id] ) ? $sm_extra_cost[$shipping_extra_id] : '' );
                            if ( '' === $class_cost_string ) {
                                continue;
                            }
                            $has_costs = true;
                            $class_cost = $this->afrsm_shipping_evaluate_cost( $class_cost_string, array(
                                'qty'  => array_sum( wp_list_pluck( $products, 'quantity' ) ),
                                'cost' => array_sum( wp_list_pluck( $products, 'line_total' ) ),
                            ) );
                            if ( 'per_class' === $sm_extra_cost_calculation_type ) {
                                $shipping_rate['cost'] += $class_cost;
                            } else {
                                $highest_class_cost = ( $class_cost > $highest_class_cost ? $class_cost : $highest_class_cost );
                            }
                        }
                        if ( 'per_order' === $sm_extra_cost_calculation_type && $highest_class_cost ) {
                            $shipping_rate['cost'] += $highest_class_cost;
                        }
                    }
                    // apply for tax
                    if ( 'no' === $sm_taxable ) {
                        $shipping_rate['taxes'] = false;
                    } else {
                        $shipping_rate['taxes'] = '';
                    }
                    $match_advance_rule = array();
                    if ( 'on' === $cost_on_total_cart_weight_status ) {
                        $match_advance_rule['hfbotcw'] = $this->afrsm_shipping_advance_pricing_rules_total_cart_weight( $get_condition_array_ap_total_cart_weight, $cart_array, $cost_on_total_cart_weight_rule_match );
                    }
                    if ( 'on' === $cost_on_total_cart_subtotal_status ) {
                        $match_advance_rule['hfbotcs'] = $this->afrsm_shipping_advance_pricing_rules_total_cart_subtotal( $get_condition_array_ap_total_cart_subtotal, $cart_array, $cost_on_total_cart_subtotal_rule_match );
                    }
                    /**
                     * Filter for advanced matching URL.
                     *
                     * @since  3.8
                     *
                     * @author jb
                     */
                    $match_advance_rule[] = apply_filters(
                        'afrsm_pro_match_advance_pricing_rules',
                        $shipping_method_id_val,
                        $cart_array,
                        $sitepress,
                        $default_lang
                    );
                    $advance_shipping_rate = 0;
                    if ( isset( $match_advance_rule ) && !empty( $match_advance_rule ) && is_array( $match_advance_rule ) ) {
                        foreach ( $match_advance_rule as $val ) {
                            if ( is_array( $val ) || is_object( $val ) ) {
                                if ( '' !== $val['flag'] && 'yes' === $val['flag'] ) {
                                    $advance_shipping_rate += $val['total_amount'];
                                }
                            }
                        }
                    }
                    $advance_shipping_rate = $this->afrsm_price_format( $advance_shipping_rate );
                    $shipping_rate['cost'] += $advance_shipping_rate;
                    $shipping_rate['cost'] = $this->afrsm_price_format( $shipping_rate['cost'] );
                    /*** allow free shipping Start ***/
                    $free_shipping_status = get_post_meta( $shipping_method_id_val, 'is_allow_free_shipping', true );
                    if ( "on" === $free_shipping_status ) {
                        $admin_object = new Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin('', '');
                        $free_shipping_based_on = get_post_meta( $shipping_method_id_val, 'sm_free_shipping_based_on', true );
                        $free_shipping_costs = $admin_object->afrsm_woocs_convert_price( get_post_meta( $shipping_method_id_val, 'sm_free_shipping_cost', true ) );
                        $free_shipping_coupan_cost = $admin_object->afrsm_woocs_convert_price( get_post_meta( $shipping_method_id_val, 'sm_free_shipping_coupan_cost', true ) );
                        $free_shipping_label = get_post_meta( $shipping_method_id_val, 'sm_free_shipping_label', true );
                        $free_shipping_based_on_product = get_post_meta( $shipping_method_id_val, 'sm_free_shipping_based_on_product', true );
                        $sm_free_shipping_exclude_product = get_post_meta( $shipping_method_id_val, 'sm_free_shipping_exclude_product', true );
                        $is_free_shipping_exclude_prod = get_post_meta( $shipping_method_id_val, 'is_free_shipping_exclude_prod', true );
                        $total_cart_value = WC()->cart->subtotal;
                        $total_discount_value = $admin_object->afrsm_pro_remove_currency_symbol( WC()->cart->get_total_discount() );
                        if ( "min_order_amt" === $free_shipping_based_on ) {
                            $final_total_cart_value = $total_cart_value - $total_discount_value;
                            if ( "on" === $is_free_shipping_exclude_prod ) {
                                $exlude_product_subtotal = $this->afrsm_cart_exclude_product_subtotal( $sm_free_shipping_exclude_product );
                                if ( $exlude_product_subtotal <= $final_total_cart_value ) {
                                    $final_total_cart_value = $final_total_cart_value - $exlude_product_subtotal;
                                }
                            }
                            if ( "" !== $free_shipping_costs && $final_total_cart_value >= $free_shipping_costs ) {
                                $has_costs = true;
                                $shipping_rate['cost'] = "";
                                $shipping_rate['label'] = ( !empty( $free_shipping_label ) ? $free_shipping_label : $shipping_rate['label'] );
                                $this->add_rate( $shipping_rate );
                            }
                        }
                        if ( "min_coupan_amt" === $free_shipping_based_on ) {
                            if ( "" !== $free_shipping_coupan_cost && $total_discount_value > $free_shipping_coupan_cost ) {
                                $has_costs = true;
                                $shipping_rate['cost'] = "";
                                $shipping_rate['label'] = ( !empty( $free_shipping_label ) ? $free_shipping_label : $shipping_rate['label'] );
                                $this->add_rate( $shipping_rate );
                            }
                        }
                        if ( "min_simple_product" === $free_shipping_based_on ) {
                            if ( !empty( $cart_array ) ) {
                                if ( !empty( $free_shipping_based_on_product ) ) {
                                    foreach ( $free_shipping_based_on_product as $free_p_id ) {
                                        settype( $free_p_id, 'integer' );
                                        if ( in_array( $free_p_id, $cart_product_ids_arr, true ) ) {
                                            $has_costs = true;
                                            $shipping_rate['cost'] = "";
                                            $shipping_rate['label'] = ( !empty( $free_shipping_label ) ? $free_shipping_label : $shipping_rate['label'] );
                                            $this->add_rate( $shipping_rate );
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    /*** allow free shipping End ***/
                    if ( $has_costs ) {
                        if ( $shipping_rate['cost'] < 0 ) {
                            //customize label of shipping method
                            $shipping_rate['label'] = $shipping_rate['label'];
                        }
                        $this->add_rate( $shipping_rate );
                        //apply rate in cart
                    }
                    do_action(
                        'woocommerce_' . $this->id . '_shipping_add_rate',
                        $this,
                        $shipping_rate,
                        $package
                    );
                }
            }
        }
    }

    /**
     * Count for total cart
     *
     * @param array  $woo_cart_array
     * @param string $type
     * @param string $qws
     *
     * @return int $total
     *
     * @since 3.6
     *
     * @uses  wc_get_product()
     * @uses  WC_Product::is_type()
     * @uses  wp_get_post_terms()
     *
     */
    public function afrsm_get_count_total_cart( $woo_cart_array, $qws ) {
        $total_qws = 0;
        foreach ( $woo_cart_array as $woo_cart_item ) {
            if ( !empty( $woo_cart_item['variation_id'] ) && 0 !== $woo_cart_item['variation_id'] ) {
                $product_id_lan = $woo_cart_item['variation_id'];
            } else {
                $product_id_lan = $woo_cart_item['product_id'];
            }
            if ( 'qty' === $qws ) {
                if ( !$woo_cart_item['data']->is_virtual() ) {
                    $total_qws += $woo_cart_item['quantity'];
                }
            }
            if ( 'weight' === $qws ) {
                $_product = wc_get_product( $product_id_lan );
                if ( $_product instanceof WC_Product ) {
                    if ( !$_product->is_virtual( 'yes' ) || $_product->is_type( 'bundle' ) ) {
                        $total_qws += (int) $woo_cart_item['quantity'] * (float) $_product->get_weight();
                    }
                }
            }
            if ( 'subtotal' === $qws ) {
                $total_qws = self::$admin_object->afrsm_pro_get_cart_subtotal();
            }
        }
        return apply_filters(
            'afrsm_get_count_total_cart_ft',
            $total_qws,
            $woo_cart_array,
            $qws
        );
    }

    /**
     * Check Min and max qty, weight and subtotal
     *
     * @param int|float $min
     * @param int|float $max
     * @param float     $price
     * @param string    $qws
     *
     * @return array
     *
     * @since 3.4
     *
     */
    public function afrsm_check_min_max_qws(
        $min,
        $max,
        $price,
        $qws
    ) {
        $min_val = $min;
        if ( '' === $max || '0' === $max ) {
            $max_val = 2000000000;
        } else {
            $max_val = $max;
        }
        $price_val = $price;
        if ( 'qty' === $qws ) {
            settype( $min_val, 'integer' );
            settype( $max_val, 'integer' );
        } else {
            settype( $min_val, 'float' );
            settype( $max_val, 'float' );
        }
        return array(
            'min'   => $min_val,
            'max'   => $max_val,
            'price' => $price_val,
        );
    }

    /**
     * Add shipping rate
     *
     * @param int|float $min
     * @param int|float $max
     * @param float     $price
     * @param int|float $count_total
     * @param float     $get_cart_total
     * @param float     $shipping_rate_cost
     *
     * @return float $shipping_rate_cost
     *
     * @since 3.4
     *
     */
    public function afrsm_check_percantage_price( $price, $get_cart_total ) {
        if ( !empty( $price ) ) {
            $is_percent = substr( $price, -1 );
            if ( '%' === $is_percent ) {
                $percent = substr( $price, 0, -1 );
                $percent = number_format(
                    $percent,
                    2,
                    '.',
                    ''
                );
                if ( !empty( $percent ) ) {
                    $percent_total = $percent / 100 * $get_cart_total;
                    $price = $percent_total;
                }
            } else {
                $price = $this->afrsm_price_format( $price );
            }
        }
        return $price;
    }

    /**
     * Find unique id based on given array
     *
     * @param array  $is_passed
     * @param string $has_fee_checked
     * @param string $has_fee_based
     * @param string $advance_inside_rule_match
     *
     * @return array
     * @since    3.6
     *
     */
    public function afrsm_pro_check_all_passed_advance_rule(
        $is_passed,
        $has_fee_checked,
        $has_fee_based,
        $advance_inside_rule_match
    ) {
        $get_cart_total = WC()->cart->get_cart_contents_total();
        $main_is_passed = 'no';
        $flag = array();
        $sum_ammount = 0;
        if ( !empty( $is_passed ) ) {
            foreach ( $is_passed as $main_is_passed ) {
                foreach ( $main_is_passed[$has_fee_checked] as $key => $is_passed_value ) {
                    if ( 'yes' === $is_passed_value ) {
                        foreach ( $main_is_passed[$has_fee_based] as $hfb_key => $hfb_is_passed_value ) {
                            if ( $hfb_key === $key ) {
                                $final_price = $this->afrsm_check_percantage_price( $hfb_is_passed_value, $get_cart_total );
                                $sum_ammount += $final_price;
                            }
                        }
                        $flag[$key] = true;
                    } else {
                        $flag[$key] = false;
                    }
                }
            }
            if ( 'any' === $advance_inside_rule_match ) {
                if ( in_array( true, $flag, true ) ) {
                    $main_is_passed = 'yes';
                } else {
                    $main_is_passed = 'no';
                }
            } else {
                if ( in_array( false, $flag, true ) ) {
                    $main_is_passed = 'no';
                } else {
                    $main_is_passed = 'yes';
                }
            }
        }
        return array(
            'flag'         => $main_is_passed,
            'total_amount' => $sum_ammount,
        );
    }

    /**
     * Cgeck rule passed or not
     *
     * @param string    $key
     * @param string    $min
     * @param string    $max
     * @param string    $hbc
     * @param string    $hbp
     * @param float     $price
     * @param int|float $total_qws
     * @param string    $qws
     *
     * @return array
     * @since    3.6
     *
     */
    public function afrsm_check_passed_rule(
        $key,
        $min,
        $max,
        $hbc,
        $hbp,
        $price,
        $total_qws
    ) {
        $is_passed_from_here_prd = array();
        if ( $min <= $total_qws && $total_qws <= $max ) {
            $is_passed_from_here_prd[$hbc][$key] = 'yes';
            $is_passed_from_here_prd[$hbp][$key] = $price;
        } else {
            $is_passed_from_here_prd[$hbc][$key] = 'no';
            $is_passed_from_here_prd[$hbp][$key] = $price;
        }
        return $is_passed_from_here_prd;
    }

    /**
     * Cost for total cart weight in advance pricing rules
     *
     * @param array  $get_condition_array_ap_total_cart_weight
     * @param array  $woo_cart_array
     * @param string $cost_on_total_cart_weight_rule_match
     *
     * @return array $main_is_passed
     * @since 3.4
     *
     * @uses  WC_Cart::get_cart_contents_total()
     * @uses  wp_get_post_terms()
     * @uses  wc_get_product()
     *
     */
    public function afrsm_shipping_advance_pricing_rules_total_cart_weight( $get_condition_array_ap_total_cart_weight, $woo_cart_array, $cost_on_total_cart_weight_rule_match ) {
        if ( !empty( $woo_cart_array ) ) {
            $is_passed_from_here_tcw = array();
            if ( !empty( $get_condition_array_ap_total_cart_weight ) || '' !== $get_condition_array_ap_total_cart_weight ) {
                foreach ( $get_condition_array_ap_total_cart_weight as $key => $get_condition ) {
                    $total_qws = $this->afrsm_get_count_total_cart( $woo_cart_array, 'weight' );
                    $get_min_max = $this->afrsm_check_min_max_qws(
                        $get_condition['ap_fees_ap_total_cart_weight_min_weight'],
                        $get_condition['ap_fees_ap_total_cart_weight_max_weight'],
                        $get_condition['ap_fees_ap_price_total_cart_weight'],
                        'weight'
                    );
                    $is_passed_from_here_tcw[] = $this->afrsm_check_passed_rule(
                        $key,
                        $get_min_max['min'],
                        $get_min_max['max'],
                        'has_fee_based_on_tcw',
                        'has_fee_based_on_tcw_price',
                        $get_condition['ap_fees_ap_price_total_cart_weight'],
                        $total_qws
                    );
                }
            }
            $main_is_passed = $this->afrsm_pro_check_all_passed_advance_rule(
                $is_passed_from_here_tcw,
                'has_fee_based_on_tcw',
                'has_fee_based_on_tcw_price',
                $cost_on_total_cart_weight_rule_match
            );
            return $main_is_passed;
        }
    }

    /**
     * Cost for total cart subtotal in advance pricing rules
     *
     * @param array  $get_condition_array_ap_total_cart_subtotal
     * @param array  $woo_cart_array
     * @param string $cost_on_total_cart_subtotal_rule_match
     *
     * @return array $main_is_passed
     * @since 3.4
     *
     * @uses  WC_Cart::get_cart_contents_total()
     * @uses  wp_get_post_terms()
     * @uses  wc_get_product()
     *
     */
    public function afrsm_shipping_advance_pricing_rules_total_cart_subtotal( $get_condition_array_ap_total_cart_subtotal, $woo_cart_array, $cost_on_total_cart_subtotal_rule_match ) {
        if ( !empty( $woo_cart_array ) ) {
            $is_passed_from_here_tcw = array();
            if ( !empty( $get_condition_array_ap_total_cart_subtotal ) || '' !== $get_condition_array_ap_total_cart_subtotal ) {
                foreach ( $get_condition_array_ap_total_cart_subtotal as $key => $get_condition ) {
                    $get_condition['ap_fees_ap_total_cart_subtotal_min_subtotal'] = self::$admin_object->afrsm_pro_price_based_on_switcher( $get_condition['ap_fees_ap_total_cart_subtotal_min_subtotal'] );
                    $get_condition['ap_fees_ap_total_cart_subtotal_max_subtotal'] = self::$admin_object->afrsm_pro_price_based_on_switcher( $get_condition['ap_fees_ap_total_cart_subtotal_max_subtotal'] );
                    $get_condition['ap_fees_ap_total_cart_subtotal_min_subtotal'] = self::$admin_object->afrsm_woocs_convert_price( $get_condition['ap_fees_ap_total_cart_subtotal_min_subtotal'] );
                    $get_condition['ap_fees_ap_total_cart_subtotal_max_subtotal'] = self::$admin_object->afrsm_woocs_convert_price( $get_condition['ap_fees_ap_total_cart_subtotal_max_subtotal'] );
                    $total_qws = $this->afrsm_get_count_total_cart( $woo_cart_array, 'subtotal' );
                    $get_min_max = $this->afrsm_check_min_max_qws(
                        $get_condition['ap_fees_ap_total_cart_subtotal_min_subtotal'],
                        $get_condition['ap_fees_ap_total_cart_subtotal_max_subtotal'],
                        $get_condition['ap_fees_ap_price_total_cart_subtotal'],
                        'weight'
                    );
                    $is_passed_from_here_tcw[] = $this->afrsm_check_passed_rule(
                        $key,
                        $get_min_max['min'],
                        $get_min_max['max'],
                        'has_fee_based_on_tcs',
                        'has_fee_based_on_tcs_price',
                        $get_condition['ap_fees_ap_price_total_cart_subtotal'],
                        $total_qws
                    );
                }
            }
            $main_is_passed = $this->afrsm_pro_check_all_passed_advance_rule(
                $is_passed_from_here_tcw,
                'has_fee_based_on_tcs',
                'has_fee_based_on_tcs_price',
                $cost_on_total_cart_subtotal_rule_match
            );
            return $main_is_passed;
        }
    }

    /**
     * Match methods.
     *
     * Check all created AFRSM shipping methods have a matching condition group.
     *
     * @param array|object $package List of shipping package data.
     * @param string       $sitepress
     * @param string       $default_lang
     *
     * @return array $matched_methods   List of all matched shipping methods.
     *
     * @uses  afrsm_shipping_match_conditions()
     *
     * @since 3.0.0
     *
     * @uses  get_posts()
     */
    public function afrsm_shipping_match_methods( $package, $sitepress, $default_lang ) {
        $matched_methods = array();
        $sm_args = array(
            'post_type'        => 'wc_afrsm',
            'posts_per_page'   => -1,
            'orderby'          => 'menu_order',
            'order'            => 'ASC',
            'suppress_filters' => false,
            'post_status'      => 'publish',
            'fields'           => 'ids',
        );
        $get_all_shipping = new WP_Query($sm_args);
        if ( !empty( $get_all_shipping->posts ) ) {
            foreach ( $get_all_shipping->posts as $sid ) {
                if ( !empty( $sitepress ) ) {
                    $sm_post_id = apply_filters(
                        'wpml_object_id',
                        $sid,
                        'wc_afrsm',
                        true,
                        $default_lang
                    );
                } else {
                    $sm_post_id = $sid;
                }
                if ( !empty( $sitepress ) ) {
                    if ( version_compare( ICL_SITEPRESS_VERSION, '3.2', '>=' ) ) {
                        $language_information = apply_filters( 'wpml_post_language_details', null, $sm_post_id );
                    } else {
                        $language_information = wpml_get_language_information( $sm_post_id );
                    }
                    $post_id_language_code = $language_information['language_code'];
                } else {
                    $post_id_language_code = self::$admin_object->afrsm_pro_get_default_langugae_with_sitpress();
                }
                if ( $post_id_language_code === $default_lang ) {
                    $is_match = $this->afrsm_shipping_match_conditions( $sm_post_id, $package );
                    // Add to matched methods array
                    if ( true === $is_match ) {
                        $matched_methods[] = $sm_post_id;
                    }
                }
            }
        }
        // reset custom query
        wp_reset_query();
        update_option( 'matched_method', $matched_methods );
        return $matched_methods;
    }

    /**
     * Match conditions.
     *
     * Check if conditions match, if all conditions in one condition group
     * matches it will return TRUE and the shipping method will display.
     *
     * @param array $sm_post_data
     * @param array $package List of shipping package data.
     *
     * @return BOOL TRUE if all the conditions in one of the condition groups matches true.
     * @since 1.0.0
     *
     */
    public function afrsm_shipping_match_conditions( $sm_post_data, $package = array() ) {
        if ( empty( $sm_post_data ) ) {
            return false;
        }
        if ( !empty( $sm_post_data ) ) {
            $final_condition_flag = apply_filters( 'afrsm_condition_match_rules', $sm_post_data, $package );
            if ( $final_condition_flag ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get items in package.
     *
     * @param array|object $package
     *
     * @return int $total_quantity
     * @since 1.0.0
     *
     */
    public function afrsm_shipping_get_package_item_qty( $package ) {
        $total_quantity = 0;
        foreach ( $package['contents'] as $values ) {
            if ( $values['quantity'] > 0 && $values['data']->needs_shipping() ) {
                $total_quantity += $values['quantity'];
            }
        }
        return $total_quantity;
    }

    /**
     * Evaluate a cost from a sum/string.
     *
     * @param string $shipping_cost_sum
     * @param array  $args
     *
     * @return string $shipping_cost_sum if shipping cost is empty then it will return 0
     * @since 1.0.0
     *
     * @uses  wc_get_price_decimal_separator()
     * @uses  WC_Eval_Math_Extra::evaluate()
     *
     */
    protected function afrsm_shipping_evaluate_cost( $shipping_cost_sum, $args = array() ) {
        // Add warning for subclasses.
        if ( !is_array( $args ) || !array_key_exists( 'qty', $args ) || !array_key_exists( 'cost', $args ) ) {
            wc_doing_it_wrong( __FUNCTION__, '$args must contain `cost` and `qty` keys.', '4.0.1' );
        }
        //Inseatd of our, we have added WC core library here.
        include_once WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php';
        // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomFunction
        // Allow 3rd parties to process shipping cost arguments
        $args = apply_filters(
            'woocommerce_evaluate_shipping_cost_args',
            $args,
            $shipping_cost_sum,
            $this
        );
        $locale = localeconv();
        $decimals = array(
            wc_get_price_decimal_separator(),
            $locale['decimal_point'],
            $locale['mon_decimal_point'],
            ','
        );
        $this->fee_cost = $args['cost'];
        // Expand shortcodes
        add_shortcode( 'fee', array($this, 'fee') );
        $shipping_cost_sum = do_shortcode( str_replace( array('[qty]', '[cost]'), array($args['qty'], $args['cost']), $shipping_cost_sum ) );
        remove_shortcode( 'fee', array($this, 'fee') );
        // Remove whitespace from string
        $shipping_cost_sum = preg_replace( '/\\s+/', '', $shipping_cost_sum );
        // Remove locale from string
        $shipping_cost_sum = str_replace( $decimals, '.', $shipping_cost_sum );
        // Trim invalid start/end characters
        $shipping_cost_sum = rtrim( ltrim( $shipping_cost_sum, "\t\n\r\x00\v+*/" ), "\t\n\r\x00\v+-*/" );
        // Do the math.(Instead of our class method callback, we have used WC core method now.)
        return ( $shipping_cost_sum ? WC_Eval_Math::evaluate( $shipping_cost_sum ) : 0 );
    }

    /**
     * Finds and returns shipping classes and the products with said class.
     *
     * @param array|object $package
     *
     * @return array $found_shipping_classes
     * @since 1.0.0
     *
     */
    public function afrsm_shipping_find_shipping_classes( $package ) {
        $found_shipping_classes = array();
        foreach ( $package['contents'] as $item_id => $values ) {
            if ( $values['data']->needs_shipping() ) {
                $found_class = $values['data']->get_shipping_class();
                if ( !empty( $found_class ) ) {
                    if ( !isset( $found_shipping_classes[$found_class] ) ) {
                        $found_shipping_classes[$found_class] = array();
                    }
                    $found_shipping_classes[$found_class][$item_id] = $values;
                }
            }
        }
        return $found_shipping_classes;
    }

    /**
     * Display array column
     *
     * @param array $input
     * @param int   $columnKey
     * @param int   $indexKey
     *
     * @return array $array It will return array if any error generate then it will return false
     * @since  1.0.0
     *
     * @uses   trigger_error()
     *
     */
    public function afrsm_shipping_fee_array_column( array $input, $columnKey, $indexKey = null ) {
        $array = array();
        foreach ( $input as $value ) {
            if ( !isset( $value[$columnKey] ) ) {
                wp_die( sprintf( esc_html_x( 'Key %d does not exist in array', esc_attr( $columnKey ), 'advanced-flat-rate-shipping-for-woocommerce' ) ) );
                return false;
            }
            if ( is_null( $indexKey ) ) {
                $array[] = $value[$columnKey];
            } else {
                if ( !isset( $value[$indexKey] ) ) {
                    wp_die( sprintf( esc_html_x( 'Key %d does not exist in array', esc_attr( $indexKey ), 'advanced-flat-rate-shipping-for-woocommerce' ) ) );
                    return false;
                }
                if ( !is_scalar( $value[$indexKey] ) ) {
                    wp_die( sprintf( esc_html_x( 'Key %d does not contain scalar value', esc_attr( $indexKey ), 'advanced-flat-rate-shipping-for-woocommerce' ) ) );
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }

    /**
     * Work out fee ( shortcode ).
     *
     * @param array $atts
     *
     * @return string $calculated_fee
     * @since 1.0.0
     *
     * @uses  afrsm_shipping_string_sanitize
     *
     */
    public function fee( $atts ) {
        $atts = shortcode_atts( array(
            'percent' => '',
            'min_fee' => '',
            'max_fee' => '',
        ), $atts );
        $atts['percent'] = $this->afrsm_shipping_string_sanitize( $atts['percent'] );
        $atts['min_fee'] = $this->afrsm_shipping_string_sanitize( $atts['min_fee'] );
        $atts['max_fee'] = $this->afrsm_shipping_string_sanitize( $atts['max_fee'] );
        $calculated_fee = 0;
        if ( $atts['percent'] ) {
            $calculated_fee = $this->fee_cost * (floatval( $atts['percent'] ) / 100);
        }
        if ( $atts['min_fee'] && $calculated_fee < $atts['min_fee'] ) {
            $calculated_fee = $atts['min_fee'];
        }
        if ( $atts['max_fee'] && $calculated_fee > $atts['max_fee'] ) {
            $calculated_fee = $atts['max_fee'];
        }
        return $calculated_fee;
    }

    /**
     * Sanitize string
     *
     * @param mixed $string
     *
     * @return string $result
     * @since 1.0.0
     *
     */
    public function afrsm_shipping_string_sanitize( $string ) {
        $result = preg_replace( "/[^ A-Za-z0-9_=.*()+\\-\\[\\]\\/]+/", '', html_entity_decode( $string, ENT_QUOTES ) );
        return $result;
    }

    /**
     * Price format
     *
     * @param string $price
     *
     * @return string $price
     * @since  1.3.3
     *
     */
    public function afrsm_price_format( $price ) {
        $price = floatval( $price );
        return $price;
    }

    /**
     * Price format
     *
     * @param array $exlude_products
     *
     * @return string $exlude_product_subtotal
     * @since  4.1.3
     *
     */
    public function afrsm_cart_exclude_product_subtotal( $exlude_products ) {
        $exlude_product_subtotal = 0;
        $exlude_products = ( is_array( $exlude_products ) ? array_map( 'intval', $exlude_products ) : [] );
        if ( !empty( $exlude_products ) ) {
            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                $product_id = ( $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'] );
                if ( in_array( $product_id, $exlude_products, true ) ) {
                    if ( !empty( $cart_item['line_subtotal'] ) ) {
                        $exlude_product_subtotal += $cart_item['line_subtotal'];
                    }
                }
            }
        }
        return $exlude_product_subtotal;
    }

}
