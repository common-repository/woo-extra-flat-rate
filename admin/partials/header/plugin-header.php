<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
global $afrsfw_fs;
$plugin_slug = '';
$plugin_slug = 'basic_flat_rate';
$afrsm_admin_object = new Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin('', '');
$current_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$getting_started = ( isset( $current_page ) && 'afrsm-pro-get-started' === $current_page ? 'active' : '' );
?>
<div class="wrap">
    <div id="dotsstoremain" class="afrsm-section">
        <div class="all-pad">
            <?php 
$afrsm_admin_object->afrsm_get_promotional_bar( $plugin_slug );
?>
            <hr class="wp-header-end" />
            <header class="dots-header">
                <div class="dots-plugin-details">
                    <div class="dots-header-left">
                        <div class="dots-logo-main">
                            <img src="<?php 
echo esc_url( AFRSM_PRO_PLUGIN_URL . 'admin/images/advance-flat-rate.png' );
?>">
                        </div>
                        <div class="plugin-name">
                            <div class="title"><?php 
echo esc_html( AFRSM_PRO_PLUGIN_NAME );
?></div>
                        </div>
                        <span class="version-label <?php 
echo esc_attr( $plugin_slug );
?>"><?php 
echo esc_html( AFRSM_VERSION_LABEL );
?></span>
                        <span class="version-number"><?php 
echo esc_html( AFRSM_PRO_PLUGIN_VERSION );
?></span>
                    </div>
                    <div class="dots-header-right">
                        <div class="button-dots">
                            <a target="_blank" href="<?php 
echo esc_url( 'http://www.thedotstore.com/support/?utm_source=plugin_header_menu_link&utm_medium=header_menu&utm_campaign=plugin&utm_id=menu_link_flat_rate_shipping' );
?>">
                                <?php 
esc_html_e( 'Support', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </a>
                        </div>
                        <div class="button-dots">
                            <a target="_blank" href="<?php 
echo esc_url( 'https://www.thedotstore.com/feature-requests/?utm_source=plugin_header_menu_link&utm_medium=header_menu&utm_campaign=plugin&utm_id=menu_link_flat_rate_shipping' );
?>">
                                <?php 
esc_html_e( 'Suggest', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </a>
                        </div>
                        <div class="button-dots <?php 
echo ( $afrsfw_fs->is__premium_only() && $afrsfw_fs->can_use_premium_code() ? '' : 'last-link-button' );
?>">
                            <a target="_blank" href="<?php 
echo esc_url( 'https://docs.thedotstore.com/collection/81-flat-rate-shipping-plugin-for-woocommerce' );
?>">
                                <?php 
esc_html_e( 'Help', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </a>
                        </div>
                        <?php 
?>
                            <div class="button-dots">
                                <a target="_blank" class="dots-upgrade-btn" href="javascript:void(0);">
                                    <?php 
esc_html_e( 'Upgrade Now', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                </a>
                            </div>
                        <?php 
?>
                    </div>
                </div>
                <div class="dots-bottom-menu-main">
                    <?php 
$afrsm_admin_object->afrsm_pro_menus( $current_page );
?>
                    <div class="dots-getting-started">
                        <a href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'afrsm-pro-get-started',
), admin_url( 'admin.php' ) ) );
?>" class="<?php 
echo esc_attr( $getting_started );
?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 4.75a7.25 7.25 0 100 14.5 7.25 7.25 0 000-14.5zM3.25 12a8.75 8.75 0 1117.5 0 8.75 8.75 0 01-17.5 0zM12 8.75a1.5 1.5 0 01.167 2.99c-.465.052-.917.44-.917 1.01V14h1.5v-.845A3 3 0 109 10.25h1.5a1.5 1.5 0 011.5-1.5zM11.25 15v1.5h1.5V15h-1.5z" fill="#a0a0a0"></path></svg></a>
                    </div>
                </div>
            </header>
            <!-- Upgrade to pro popup -->
            <?php 
if ( !(afrsfw_fs()->is__premium_only() && afrsfw_fs()->can_use_premium_code()) ) {
    require_once AFRSM_PRO_PLUGIN_DIR_PATH . 'admin/partials/afrsm-upgrade-popup.php';
}
?>
            <div class="dots-settings-inner-main">
                <div class="dots-settings-left-side">
                    <?php 
$afrsm_admin_object->afrsm_submenus( $current_page );