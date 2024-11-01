<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 *
 * @package    Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro
 * @subpackage Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro
 * @subpackage Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro/public
 * @author     Multidots <inquiry@multidots.in>
 */
class Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Public {
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    private static $admin_object = null;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version     The version of this plugin.
     *
     * @since    1.0.0
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        self::$admin_object = new Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin('', '');
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function afrsm_pro_enqueue_styles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/advanced-flat-rate-shipping-for-woocommerce-public.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            'font-awesome-min',
            plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css',
            array(),
            $this->version
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function afrsm_pro_enqueue_scripts() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/advanced-flat-rate-shipping-for-woocommerce-public.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    /**
     * This function return the template from this plugin, if it exists
     *
     * @param string $template
     * @param string $template_name that is only the filename
     * @param string $template_path
     *
     * @return string
     * @since    1.0.0
     *
     */
    public function afrsm_pro_wc_locate_template_sm_conditions( $template, $template_name, $template_path ) {
        global $woocommerce;
        $_template = $template;
        if ( !$template_path ) {
            $template_path = $woocommerce->template_url;
        }
        $plugin_path = advanced_flat_rate_shipping_for_woocommerce_pro_plugin_path() . '/woocommerce/';
        $template = locate_template( array($template_path . $template_name, $template_name) );
        // Modification: Get the template from this plugin, if it exists
        if ( !$template && file_exists( $plugin_path . $template_name ) ) {
            $template = $plugin_path . $template_name;
        }
        if ( !$template ) {
            $template = $_template;
        }
        // Return what we found
        return $template;
    }

    /**
     * Price Format
     *
     * @param float $price price would be display here
     *
     * @return float $price
     * @since  3.6.1
     */
    public function afrswp_fraction_price_format( $price ) {
        $args = array(
            'decimal_separator'  => wc_get_price_decimal_separator(),
            'thousand_separator' => wc_get_price_thousand_separator(),
            'decimals'           => wc_get_price_decimals(),
            'price_format'       => get_woocommerce_price_format(),
        );
        $price = floatval( $price );
        $price = number_format(
            $price,
            $args['decimals'],
            $args['decimal_separator'],
            $args['thousand_separator']
        );
        return $price;
    }

    /**
     * Default Shipping method
     *
     * @param $method
     * @param $available_methods
     *
     * @return array
     * @since  3.6
     */
    public function afrsm_set_default_shipping_method( $method, $available_methods ) {
        $afrsm_default_shipping_methods = array();
        if ( $available_methods ) {
            foreach ( $available_methods as $afrsm_method ) {
                $get_method_id = '';
                if ( false !== strpos( $afrsm_method->id, 'advanced_flat_rate_shipping:' ) ) {
                    $method_id_explode = explode( ':', $afrsm_method->id );
                    $get_method_id = $method_id_explode[1];
                }
                $sm_is_selected_shipping = get_post_meta( $get_method_id, 'sm_select_selected_shipping', true );
                if ( "yes" === $sm_is_selected_shipping ) {
                    $afrsm_default_shipping_methods[] = $afrsm_method->id;
                }
            }
        }
        if ( empty( $afrsm_default_shipping_methods ) ) {
            return $method;
        } else {
            foreach ( $afrsm_default_shipping_methods as $afrsm_default_method ) {
                if ( array_key_exists( $afrsm_default_method, $available_methods ) ) {
                    return $afrsm_default_method;
                }
            }
        }
    }

    public function afrsm_pro_wc_cart_shipping_method_label_callback( $label, $method ) {
        $get_method_id = '';
        $method_id = ( $method->get_id() ? $method->get_id() : '' );
        if ( false !== strpos( $method_id, 'advanced_flat_rate_shipping:' ) ) {
            $method_id_explode = explode( ':', $method_id );
            $get_method_id = end( $method_id_explode );
        }
        $sm_estimation_delivery = get_post_meta( $get_method_id, 'sm_estimation_delivery', true );
        $sm_estimation_delivery = ( isset( $sm_estimation_delivery ) && !empty( $sm_estimation_delivery ) ? ' (' . $sm_estimation_delivery . ') ' : '' );
        if ( "forceall" === $method_id ) {
            $forceall_label = ( get_option( 'forceall_label' ) ? get_option( 'forceall_label' ) : esc_html__( 'Combine Shipping', 'advanced-flat-rate-shipping-for-woocommerce' ) );
            $method->set_label( $forceall_label );
            return $label;
        } else {
            return $label . "<span>" . $sm_estimation_delivery . "</span>";
        }
    }

    public function afrsm_add_tooltip_and_subtitle_callback( $method ) {
        $tool_tip_html = '';
        $final_shipping_label = '';
        $get_method_id = '';
        if ( "forceall" === $method->id && is_checkout() ) {
            $new_lin_force_all_lable = '';
            $forceall_label = ( get_option( 'forceall_label' ) ? get_option( 'forceall_label' ) : esc_html__( 'Combine Shipping', 'advanced-flat-rate-shipping-for-woocommerce' ) );
            $get_param_cart = $this->afrsm_pro_forceall_label_for_cart__premium_only(
                $new_lin_force_all_lable,
                $tool_tip_html,
                $method,
                $forceall_label
            );
            if ( is_plugin_active( 'checkout-for-woocommerce/checkout-for-woocommerce.php' ) ) {
                $tool_tip_html = $get_param_cart['tool_tip_html'];
            }
        } else {
            if ( false !== strpos( $method->id, 'advanced_flat_rate_shipping:' ) ) {
                $method_id_explode = explode( ':', $method->id );
                $get_method_id = $method_id_explode[1];
            }
            $sm_tooltip_type = get_post_meta( $get_method_id, 'sm_tooltip_type', true );
            $sm_tooltip_type = ( isset( $sm_tooltip_type ) && !empty( $sm_tooltip_type ) ? $sm_tooltip_type : esc_html__( 'tooltip', 'advanced-flat-rate-shipping-for-woocommerce' ) );
            $sm_tooltip_desc = get_post_meta( $get_method_id, 'sm_tooltip_desc', true );
            $sm_tooltip_desc = ( isset( $sm_tooltip_desc ) && !empty( $sm_tooltip_desc ) ? $sm_tooltip_desc : '' );
            $final_shipping_label .= $sm_tooltip_desc;
            if ( !empty( $final_shipping_label ) ) {
                if ( "tooltip" === $sm_tooltip_type ) {
                    $tool_tip_html .= '<div class="extra-flate-tool-tip"><a data-tooltip="' . esc_attr( $final_shipping_label ) . '"><i class="fa fa-question-circle fa-lg"></i></a></div>';
                } else {
                    $tool_tip_html .= '<div class="extra-flate-subtitle">' . esc_html( $final_shipping_label ) . '</div>';
                }
            }
        }
        echo wp_kses( $tool_tip_html, Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro::afrsm_pro_allowed_html_tags() );
    }

}
