<?php
	
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' );
	$get_status = filter_input( INPUT_GET, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$msg        = '';

	if ( 'success' === $get_status ) {
		$msg   = esc_html__( 'Import successfully', 'advanced-flat-rate-shipping-for-woocommerce' );
	}
    $allowed_tooltip_html   = wp_kses_allowed_html( 'post' )['span'];
    if( !empty($msg) ) {
        echo sprintf( '<div id="message" class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( $msg ) ); 
    } 
?>

<div class="afrsm-section-left">
    <div class="afrsm-main-table afrsm-half res-cl">
        <h2><?php echo esc_html__( 'Step 1 - Import &amp; Export Shipping Zone', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h2>
        <table class="table-outer">
            <tbody>
            <tr>
                <td scope="row" class="titledesc">
                    <label><?php echo esc_html__( 'Export Zone', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></label>
                </td>
                <td>
                    <form method="post">
                        <div class="afrsm_main_container">
                            <p class="afrsm_button_container"><?php submit_button( esc_html__( 'Export', 'advanced-flat-rate-shipping-for-woocommerce' ), 'primary', 'submit', false ); ?>
                            <?php echo wp_kses( wc_help_tip( esc_html__( 'Export the zone settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?></p>
                            <p class="afrsm_content_container">
								<?php wp_nonce_field( 'afrsm_zone_export_save_action_nonce', 'afrsm_zone_export_action_nonce' ); ?>
                                <input type="hidden" name="afrsm_zone_export_action" value="zone_export_settings"/>
                            </p>
                        </div>
                    </form>
                </td>
            </tr>
            <tr>
                <td scope="row" class="titledesc">
                    <label><?php echo esc_html__( 'Import Zone', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></label>
                </td>
                <td>
                    <form method="post" enctype="multipart/form-data">
                        <div class="afrsm_main_container">
                            <p>
                                <input type="file" name="zone_import_file"/>
                            </p>
                            <p class="afrsm_button_container">
                                <input type="hidden" name="afrsm_zone_import_action" value="zone_import_settings"/>
								<?php wp_nonce_field( 'afrsm_zone_import_action_nonce', 'afrsm_zone_import_action_nonce' ); ?>
								<?php
									$other_attributes = array( 'id' => 'afrsm_zone_import_setting' );
								?>
								<?php submit_button( esc_html__( 'Import', 'advanced-flat-rate-shipping-for-woocommerce' ), 'primary', 'submit', false, $other_attributes ); ?>
                                <?php echo wp_kses( wc_help_tip( esc_html__( 'Import the zone settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                            </p>
                        </div>
                    </form>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
	<div class="afrsm-main-table afrsm-half res-cl">
		<h2><?php echo esc_html__( 'Step 2 - Import &amp; Export Shipping Method', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h2>
		<table class="table-outer">
			<tbody>
			<tr>
				<td scope="row" class="titledesc">
                    <label for="blogname"><?php echo esc_html__( 'Export Shipping Settings', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></label>
                </td>
				<td>
					<form method="post">
						<div class="afrsm_main_container">
							<p class="afrsm_button_container"><?php submit_button( esc_html__( 'Export', 'advanced-flat-rate-shipping-for-woocommerce' ), 'primary', 'submit', false ); ?>
                            <?php echo wp_kses( wc_help_tip( esc_html__( 'Export the shipping method settings for this site as a .json file. This allows you to easily import the configuration into another site. Please make sure simple product and variation products slugs must be unique.', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?></p>
							<p class="afrsm_content_container">
								<?php wp_nonce_field( 'afrsm_export_save_action_nonce', 'afrsm_export_action_nonce' ); ?>
								<input type="hidden" name="afrsm_export_action" value="export_settings"/>
							</p>
						</div>
					</form>
				</td>
			</tr>
			<tr>
				<td scope="row" class="titledesc">
                    <label for="blogname"><?php echo esc_html__( 'Import Shipping Settings', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></label>
                </td>
				<td>
					<form method="post" enctype="multipart/form-data">
						<div class="afrsm_main_container">
							<p>
								<input type="file" name="import_file"/>
							</p>
							<p class="afrsm_button_container">
								<input type="hidden" name="afrsm_import_action" value="import_settings"/>
								<?php wp_nonce_field( 'afrsm_import_action_nonce', 'afrsm_import_action_nonce' ); ?>
								<?php
								$other_attributes = array( 'id' => 'afrsm_import_setting' );
								?>
								<?php submit_button( esc_html__( 'Import', 'advanced-flat-rate-shipping-for-woocommerce' ), 'primary', 'submit', false, $other_attributes ); ?>
                                <?php echo wp_kses( wc_help_tip( esc_html__( 'Import the shipping method settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
							</p>
						</div>
					</form>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
<?php require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php' ); ?>