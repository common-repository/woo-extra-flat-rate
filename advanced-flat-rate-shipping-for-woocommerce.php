<?php

/**
 * Plugin Name:         Advanced Flat Rate Shipping For WooCommerce
 * Plugin URI:          https://www.thedotstore.com/advanced-flat-rate-shipping-method-for-woocommerce
 * Description:         Using Advanced Flat Rate Shipping plugin, you can create multiple flat rate shipping methods. Using this plugin you can configure different parameters on which a particular Flat Rate Shipping method becomes available to the customers at the time of checkout.
 * Version:             4.3.0
 * Author:              theDotstore
 * Author URI:          https://www.thedotstore.com/
 * License:             GPL-2.0+
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:         advanced-flat-rate-shipping-for-woocommerce
 * Domain Path:         /languages
 * Requires Plugins:    woocommerce
 *
 *
 * WC requires at least: 3.0
 * WP tested up to:     6.5.5
 * WC tested up to:     9.0.2
 * Requires PHP:        7.2
 * Requires at least:   5.0
 */
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'afrsfw_fs' ) ) {
    afrsfw_fs()->set_basename( false, __FILE__ );
} else {
    if ( !function_exists( 'afrsfw_fs' ) ) {
        // Create a helper function for easy SDK access.
        function afrsfw_fs() {
            global $afrsfw_fs;
            if ( !isset( $afrsfw_fs ) ) {
                // Activate multisite network integration.
                if ( !defined( 'WP_FS__PRODUCT_3379_MULTISITE' ) ) {
                    define( 'WP_FS__PRODUCT_3379_MULTISITE', true );
                }
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $afrsfw_fs = fs_dynamic_init( array(
                    'id'              => '3379',
                    'slug'            => 'advanced-flat-rate-shipping-for-woocommerce',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_8db0d3a414717fb20558c5268291b',
                    'is_premium'      => false,
                    'premium_suffix'  => 'Premium',
                    'has_addons'      => true,
                    'has_paid_plans'  => true,
                    'trial'           => array(
                        'days'               => 14,
                        'is_require_payment' => true,
                    ),
                    'has_affiliation' => 'selected',
                    'menu'            => array(
                        'slug'       => 'afrsm-pro-list',
                        'first-path' => 'admin.php?page=afrsm-pro-list',
                        'contact'    => false,
                        'support'    => false,
                        'network'    => true,
                    ),
                    'is_live'         => true,
                ) );
            }
            return $afrsfw_fs;
        }

        // Init Freemius.
        afrsfw_fs();
        // Signal that SDK was initiated.
        do_action( 'afrsfw_fs_loaded' );
        afrsfw_fs()->get_upgrade_url();
        function afrsfw_fs_settings_url() {
            return admin_url( 'admin.php?page=afrsm-pro-list' );
        }

        afrsfw_fs()->add_filter( 'connect_url', 'afrsfw_fs_settings_url' );
        afrsfw_fs()->add_filter( 'after_skip_url', 'afrsfw_fs_settings_url' );
        afrsfw_fs()->add_filter( 'after_connect_url', 'afrsfw_fs_settings_url' );
        afrsfw_fs()->add_filter( 'after_pending_connect_url', 'afrsfw_fs_settings_url' );
    }
}
if ( !defined( 'AFRSM_PRO_PLUGIN_VERSION' ) ) {
    define( 'AFRSM_PRO_PLUGIN_VERSION', 'v4.3.0' );
}
if ( !defined( 'AFRSM_PRO_PLUGIN_URL' ) ) {
    define( 'AFRSM_PRO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'AFRSM_PLUGIN_DIR' ) ) {
    define( 'AFRSM_PLUGIN_DIR', dirname( __FILE__ ) );
}
if ( !defined( 'AFRSM_PRO_PLUGIN_DIR_PATH' ) ) {
    define( 'AFRSM_PRO_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'AFRSM_PRO_SLUG' ) ) {
    define( 'AFRSM_PRO_SLUG', 'advanced-flat-rate-shipping-for-woocommerce' );
}
if ( !defined( 'AFRSM_PRO_PLUGIN_BASENAME' ) ) {
    define( 'AFRSM_PRO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( !defined( 'AFRSM_PRO_PLUGIN_NAME' ) ) {
    define( 'AFRSM_PRO_PLUGIN_NAME', 'Flat Rate Shipping' );
}
if ( !defined( 'AFRSM_PRO_TEXT_DOMAIN' ) ) {
    define( 'AFRSM_PRO_TEXT_DOMAIN', 'advanced-flat-rate-shipping-for-woocommerce' );
}
if ( !defined( 'AFRSM_PRO_FEE_AMOUNT_NOTICE' ) ) {
    define( 'AFRSM_PRO_FEE_AMOUNT_NOTICE', 'If entered shipping amount is less than cart subtotal it will reflect with minus sign (EX: $ -10.00) <b>OR</b> If entered shipping amount is more than cart subtotal then the total amount shown as zero (EX: Total: 0)' );
}
if ( !defined( 'AFRSM_PRO_PERTICULAR_FEE_AMOUNT_NOTICE' ) ) {
    define( 'AFRSM_PRO_PERTICULAR_FEE_AMOUNT_NOTICE', 'You can turn off this button, if you do not need to apply this shipping amount.' );
}
if ( !defined( 'AFRSM_VERSION_LABEL' ) ) {
    define( 'AFRSM_VERSION_LABEL', 'FREE' );
}
if ( !defined( 'AFRSM_STORE_URL' ) ) {
    define( 'AFRSM_STORE_URL', 'https://www.thedotstore.com/' );
}
/**
 * Hide freemius account tab
 *
 * @since    4.2.0
 */
if ( !function_exists( 'afrsm_hide_account_tab' ) ) {
    function afrsm_hide_account_tab() {
        return true;
    }

    afrsfw_fs()->add_filter( 'hide_account_tabs', 'afrsm_hide_account_tab' );
}
/**
 * Include plugin header on freemius account page
 *
 * @since    4.2.0
 */
if ( !function_exists( 'afrsm_load_plugin_header_after_account' ) ) {
    function afrsm_load_plugin_header_after_account() {
        require_once plugin_dir_path( __FILE__ ) . 'admin/partials/header/plugin-header.php';
    }

    afrsfw_fs()->add_action( 'after_account_details', 'afrsm_load_plugin_header_after_account' );
}
/**
 * Hide billing and payments details from freemius account page
 *
 * @since    4.2.0
 */
if ( !function_exists( 'afrsm_hide_billing_and_payments_info' ) ) {
    function afrsm_hide_billing_and_payments_info() {
        return true;
    }

    afrsfw_fs()->add_action( 'hide_billing_and_payments_info', 'afrsm_hide_billing_and_payments_info' );
}
/**
 * Hide powerd by popup from freemius account page
 *
 * @since    3.9.3
 */
if ( !function_exists( 'afrsm_hide_freemius_powered_by' ) ) {
    function afrsm_hide_freemius_powered_by() {
        return true;
    }

    afrsfw_fs()->add_action( 'hide_freemius_powered_by', 'afrsm_hide_freemius_powered_by' );
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-advanced-flat-rate-shipping-for-woocommerce-activator.php
 */
if ( function_exists( 'activate_advanced_flat_rate_shipping_for_woocommerce_pro' ) ) {
    /** If the free version actitivated then first deactivate it */
    deactivate_plugins( '/woo-extra-flat-rate/advanced-flat-rate-shipping-for-woocommerce.php', true );
} else {
    function activate_advanced_flat_rate_shipping_for_woocommerce_pro() {
        set_transient( 'afrsm-admin-notice', true );
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-advanced-flat-rate-shipping-for-woocommerce-activator.php';
        Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-advanced-flat-rate-shipping-for-woocommerce-deactivator.php
     */
    if ( !function_exists( 'deactivate_advanced_flat_rate_shipping_for_woocommerce_pro' ) ) {
        function deactivate_advanced_flat_rate_shipping_for_woocommerce_pro() {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-advanced-flat-rate-shipping-for-woocommerce-deactivator.php';
            Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Deactivator::deactivate();
        }

    }
    register_activation_hook( __FILE__, 'activate_advanced_flat_rate_shipping_for_woocommerce_pro' );
    register_deactivation_hook( __FILE__, 'deactivate_advanced_flat_rate_shipping_for_woocommerce_pro' );
    add_action( 'admin_init', 'afrsm_pro_deactivate_plugin' );
    if ( !function_exists( 'afrsm_pro_deactivate_plugin' ) ) {
        function afrsm_pro_deactivate_plugin() {
            if ( is_multisite() ) {
                $active_plugins = get_option( 'active_plugins', array() );
                if ( is_multisite() ) {
                    $network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
                    $active_plugins = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
                    $active_plugins = array_unique( $active_plugins );
                }
                if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', $active_plugins ), true ) ) {
                    deactivate_plugins( '/woo-extra-flat-rate/advanced-flat-rate-shipping-for-woocommerce.php', true );
                    //WordPress ORG name
                    deactivate_plugins( 'advanced-flat-rate-shipping-for-woocommerce/advanced-flat-rate-shipping-for-woocommerce.php', true );
                    // Freemius name
                }
            } else {
                if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
                    deactivate_plugins( '/woo-extra-flat-rate/advanced-flat-rate-shipping-for-woocommerce.php', true );
                    //WordPress ORG name
                    deactivate_plugins( 'advanced-flat-rate-shipping-for-woocommerce/advanced-flat-rate-shipping-for-woocommerce.php', true );
                    // Freemius name
                }
            }
        }

    }
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-advanced-flat-rate-shipping-for-woocommerce.php';
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    if ( !function_exists( 'run_advanced_flat_rate_shipping_for_woocommerce_pro' ) ) {
        function run_advanced_flat_rate_shipping_for_woocommerce_pro() {
            $plugin = new Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro();
            $plugin->run();
        }

    }
    run_advanced_flat_rate_shipping_for_woocommerce_pro();
    if ( !function_exists( 'advanced_flat_rate_shipping_for_woocommerce_pro_plugin_path' ) ) {
        function advanced_flat_rate_shipping_for_woocommerce_pro_plugin_path() {
            return untrailingslashit( plugin_dir_path( __FILE__ ) );
        }

    }
    /**
     * Helper function for logging
     *
     * For valid levels, see `WC_Log_Levels` class
     *
     * Description of levels:
     *     'emergency': System is unusable.
     *     'alert': Action must be taken immediately.
     *     'critical': Critical conditions.
     *     'error': Error conditions.
     *     'warning': Warning conditions.
     *     'notice': Normal but significant condition.
     *     'info': Informational messages.
     *     'debug': Debug-level messages.
     *
     * @param string $message
     *
     * @return mixed log
     */
    if ( !function_exists( 'advanced_flat_rate_shipping_for_woocommerce_pro_plugin_path' ) ) {
        function afrsm_log(  $message, $level = 'debug'  ) {
            $chk_enable_logging = get_option( 'chk_enable_logging' );
            if ( 'off' === $chk_enable_logging ) {
                return;
            }
            $logger = wc_get_logger();
            $context = array(
                'source' => 'advanced-flat-rate-shipping-for-woocommerce',
            );
            return $logger->log( $level, $message, $context );
        }

    }
    //HPOS Compatibility declare
    add_action( 'before_woocommerce_init', 'afrsm_hpos_compatibility_declaration' );
    if ( !function_exists( 'afrsm_hpos_compatibility_declaration' ) ) {
        function afrsm_hpos_compatibility_declaration() {
            if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
            }
        }

    }
    /**
     * Start plugin setup wizard before license activation screen
     *
     * @since    4.2.0
     */
    if ( !function_exists( 'afrsm_load_plugin_setup_wizard_connect_before' ) ) {
        function afrsm_load_plugin_setup_wizard_connect_before() {
            require_once plugin_dir_path( __FILE__ ) . 'admin/partials/afrsm-plugin-setup-wizard.php';
            ?>
            <div class="tab-panel" id="step5">
                <div class="ds-wizard-wrap">
                    <div class="ds-wizard-content">
                        <h2 class="cta-title">
                            <?php 
            esc_html_e( 'Activate Plugin', 'advanced-flat-rate-shipping-for-woocommerce' );
            ?>
                        </h2>
                    </div>
            <?php 
        }

        afrsfw_fs()->add_action( 'connect/before', 'afrsm_load_plugin_setup_wizard_connect_before' );
    }
    /**
     * End plugin setup wizard after license activation screen
     *
     * @since    4.2.0
     */
    if ( !function_exists( 'afrsm_load_plugin_setup_wizard_connect_after' ) ) {
        function afrsm_load_plugin_setup_wizard_connect_after() {
            ?>
            </div>
            </div>
            </div>
            </div>
            <?php 
        }

        afrsfw_fs()->add_action( 'connect/after', 'afrsm_load_plugin_setup_wizard_connect_after' );
    }
}
/**
 * Returns the main instance of AFRSMPA.
 *
 * @return Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin
 *
 * @since  3.8
 *
 * @author jb
 */
if ( !function_exists( 'AFRSMPA' ) ) {
    function AFRSMPA() {
        // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
        return new Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin('', '');
    }

    $GLOBALS['afrsfwpa'] = AFRSMPA();
}