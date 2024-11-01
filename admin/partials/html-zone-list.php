<?php
// If this file is called directly, abort.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' );
	$zone_migration = get_option( 'zone_migration' );
?>

    <div class="afrsm-section-left">
        <div class="advance_zone_listing">
            <!-- <div class="right_button_add_zone">
				<?php
					if ( 'done' !== $zone_migration ) {
						?>
                        <a href="javascript:void(0);" class="button-primary" id="fetch_old_shipping_zone">
							<?php esc_html_e( 'Fetch Your Old Shipping Zone', 'advanced-flat-rate-shipping-for-woocommerce' ); ?>
                        </a>
						<?php
					}
				?>
                <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'afrsm-wc-shipping-zones&add_zone' ), admin_url( 'admin.php' ) ) ); ?>" class="button button-large button-secondary">
					<?php esc_html_e( 'Add Zone', 'advanced-flat-rate-shipping-for-woocommerce' ); ?>
                </a>
            </div> -->
            <div class="wc-col-wrap">
				<?php self::afrsm_pro_sz_list_shipping_zones(); ?>
            </div>
        </div>
    </div>

<?php
	require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php' );
