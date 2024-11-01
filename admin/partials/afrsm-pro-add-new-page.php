<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php';
$afrsm_admin_object = new Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin('', '');
$afrsm_object = new Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro('', '');
$get_action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];
/*
 * edit all posted data method define in class-advanced-flat-rate-shipping-for-woocommerce-admin
 */
if ( isset( $get_action ) && 'edit' === $get_action ) {
    $get_id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
    $get_wpnonce = filter_input( INPUT_GET, 'cust_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $get_retrieved_nonce = ( isset( $get_wpnonce ) ? sanitize_text_field( wp_unslash( $get_wpnonce ) ) : '' );
    $get_duplicate_wpnonce = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $get_duplicate_nonce = ( isset( $get_duplicate_wpnonce ) ? sanitize_text_field( wp_unslash( $get_duplicate_wpnonce ) ) : '' );
    if ( !wp_verify_nonce( $get_retrieved_nonce, 'edit_' . $get_id ) && !wp_verify_nonce( $get_duplicate_nonce, 'edit_' . $get_id ) ) {
        die( 'Failed security check' );
    }
    $get_post_id = ( isset( $get_id ) ? sanitize_text_field( wp_unslash( $get_id ) ) : '' );
    $sm_status = get_post_status( $get_post_id );
    $sm_title = __( get_the_title( $get_post_id ), 'advanced-flat-rate-shipping-for-woocommerce' );
    $sm_cost = get_post_meta( $get_post_id, 'sm_product_cost', true );
    $is_allow_free_shipping = get_post_meta( $get_post_id, 'is_allow_free_shipping', true );
    $sm_free_shipping_based_on = get_post_meta( $get_post_id, 'sm_free_shipping_based_on', true );
    $sm_free_shipping_cost = get_post_meta( $get_post_id, 'sm_free_shipping_cost', true );
    $is_free_shipping_exclude_prod = get_post_meta( $get_post_id, 'is_free_shipping_exclude_prod', true );
    $sm_free_shipping_coupan_cost = get_post_meta( $get_post_id, 'sm_free_shipping_coupan_cost', true );
    $sm_free_shipping_label = get_post_meta( $get_post_id, 'sm_free_shipping_label', true );
    $sm_tooltip_type = get_post_meta( $get_post_id, 'sm_tooltip_type', true );
    $sm_tooltip_desc = get_post_meta( $get_post_id, 'sm_tooltip_desc', true );
    $sm_is_log_in_user = get_post_meta( $get_post_id, 'sm_select_log_in_user', true );
    $sm_first_order_for_user = get_post_meta( $get_post_id, 'sm_select_first_order_for_user', true );
    $sm_is_selected_shipping = get_post_meta( $get_post_id, 'sm_select_selected_shipping', true );
    $sm_select_shipping_provider = get_post_meta( $get_post_id, 'sm_select_shipping_provider', true );
    $sm_is_taxable = get_post_meta( $get_post_id, 'sm_select_taxable', true );
    $sm_metabox = get_post_meta( $get_post_id, 'sm_metabox', true );
    if ( is_serialized( $sm_metabox ) ) {
        $sm_metabox = maybe_unserialize( $sm_metabox );
    } else {
        $sm_metabox = $sm_metabox;
    }
    $sm_extra_cost = get_post_meta( $get_post_id, 'sm_extra_cost', true );
    if ( is_serialized( $sm_extra_cost ) ) {
        $sm_extra_cost = maybe_unserialize( $sm_extra_cost );
    } else {
        $sm_extra_cost = $sm_extra_cost;
    }
    $sm_extra_cost_calc_type = get_post_meta( $get_post_id, 'sm_extra_cost_calculation_type', true );
    $ap_rule_status = get_post_meta( $get_post_id, 'ap_rule_status', true );
    $cost_on_total_cart_weight_status = get_post_meta( $get_post_id, 'cost_on_total_cart_weight_status', true );
    $cost_on_total_cart_subtotal_status = get_post_meta( $get_post_id, 'cost_on_total_cart_subtotal_status', true );
    $cost_rule_match = get_post_meta( $get_post_id, 'cost_rule_match', true );
    if ( !empty( $cost_rule_match ) ) {
        if ( is_serialized( $cost_rule_match ) ) {
            $cost_rule_match = maybe_unserialize( $cost_rule_match );
        } else {
            $cost_rule_match = $cost_rule_match;
        }
        if ( array_key_exists( 'general_rule_match', $cost_rule_match ) ) {
            $general_rule_match = $cost_rule_match['general_rule_match'];
        } else {
            $general_rule_match = 'all';
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
        $general_rule_match = 'all';
        $cost_on_total_cart_weight_rule_match = 'any';
        $cost_on_total_cart_subtotal_rule_match = 'any';
    }
    $sm_metabox_ap_total_cart_weight = get_post_meta( $get_post_id, 'sm_metabox_ap_total_cart_weight', true );
    if ( is_serialized( $sm_metabox_ap_total_cart_weight ) ) {
        $sm_metabox_ap_total_cart_weight = maybe_unserialize( $sm_metabox_ap_total_cart_weight );
    } else {
        $sm_metabox_ap_total_cart_weight = $sm_metabox_ap_total_cart_weight;
    }
    $sm_metabox_ap_total_cart_subtotal = get_post_meta( $get_post_id, 'sm_metabox_ap_total_cart_subtotal', true );
    if ( is_serialized( $sm_metabox_ap_total_cart_subtotal ) ) {
        $sm_metabox_ap_total_cart_subtotal = maybe_unserialize( $sm_metabox_ap_total_cart_subtotal );
    } else {
        $sm_metabox_ap_total_cart_subtotal = $sm_metabox_ap_total_cart_subtotal;
    }
} else {
    $get_post_id = '';
    $sm_status = '';
    $sm_title = '';
    $sm_cost = '';
    $sm_free_shipping_based_on = '';
    $is_allow_free_shipping = '';
    $sm_free_shipping_cost = '';
    $is_free_shipping_exclude_prod = '';
    $sm_free_shipping_coupan_cost = '';
    $sm_free_shipping_label = '';
    $sm_tooltip_type = '';
    $sm_tooltip_desc = '';
    $sm_is_log_in_user = '';
    $sm_first_order_for_user = 'no';
    $sm_is_selected_shipping = '';
    $sm_is_taxable = '';
    $sm_select_shipping_provider = '';
    $sm_metabox = array();
    $sm_extra_cost = array();
    $sm_extra_cost_calc_type = '';
    $ap_rule_status = '';
    $general_rule_match = 'all';
    $cost_on_total_cart_weight_status = '';
    $cost_on_total_cart_subtotal_status = '';
    $cost_on_total_cart_weight_rule_match = 'any';
    $cost_on_total_cart_subtotal_rule_match = 'any';
    $sm_metabox_ap_total_cart_weight = array();
    $sm_metabox_ap_total_cart_subtotal = array();
}
$sm_status = ( !empty( $sm_status ) && 'publish' === $sm_status || empty( $sm_status ) ? 'checked' : '' );
$sm_title = ( !empty( $sm_title ) ? esc_attr( stripslashes( $sm_title ) ) : '' );
$sm_cost = ( '' !== $sm_cost ? esc_attr( stripslashes( $sm_cost ) ) : '' );
$sm_free_shipping_based_on = ( '' !== $sm_free_shipping_based_on ? esc_attr( stripslashes( $sm_free_shipping_based_on ) ) : '' );
$is_allow_free_shipping = ( '' !== $is_allow_free_shipping ? esc_attr( stripslashes( $is_allow_free_shipping ) ) : '' );
$sm_free_shipping_label = ( '' !== $sm_free_shipping_label ? esc_attr( stripslashes( $sm_free_shipping_label ) ) : '' );
$sm_tooltip_type = ( !empty( $sm_tooltip_type ) ? $sm_tooltip_type : '' );
$sm_tooltip_desc = ( !empty( $sm_tooltip_desc ) ? $sm_tooltip_desc : '' );
$ap_rule_status = ( !empty( $ap_rule_status ) && 'on' === $ap_rule_status && "" !== $ap_rule_status ? 'checked' : '' );
$cost_on_total_cart_weight_status = ( !empty( $cost_on_total_cart_weight_status ) && 'on' === $cost_on_total_cart_weight_status && "" !== $cost_on_total_cart_weight_status ? 'checked' : '' );
$cost_on_total_cart_subtotal_status = ( !empty( $cost_on_total_cart_subtotal_status ) && 'on' === $cost_on_total_cart_subtotal_status && "" !== $cost_on_total_cart_subtotal_status ? 'checked' : '' );
$submit_text = __( 'Save changes', 'advanced-flat-rate-shipping-for-woocommerce' );
// Shipping Rules Condition
?>
	<div class="text-condtion-is" style="display:none;">
		<select class="text-condition">
			<option value="is_equal_to"><?php 
esc_html_e( 'Equal to ( = )', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
			<option value="less_equal_to"><?php 
esc_html_e( 'Less or Equal to ( <= )', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
			<option value="less_then"><?php 
esc_html_e( 'Less than ( < )', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
			<option value="greater_equal_to"><?php 
esc_html_e( 'Greater or Equal to ( >= )', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
			<option value="greater_then"><?php 
esc_html_e( 'Greater than ( > )', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
			<option value="not_in"><?php 
esc_html_e( 'Not Equal to ( != )', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
		</select>
		<select class="select-condition">
			<option value="is_equal_to"><?php 
esc_html_e( 'Equal to ( = )', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
			<option value="not_in"><?php 
esc_html_e( 'Not Equal to ( != )', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
		</select>
	</div>
	<div class="default-country-box" style="display:none;">
		<?php 
echo wp_kses( $afrsm_admin_object->afrsm_pro_get_country_list(), Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro::afrsm_pro_allowed_html_tags() );
?>
	</div>

	<div class="afrsm-section-left">
		<div class="afrsm-main-table res-cl">
			<h2><?php 
esc_html_e( 'Shipping Method Configuration', 'advanced-flat-rate-shipping-for-woocommerce' );
?></h2>
			<form method="POST" name="feefrm" action="">
				<?php 
wp_nonce_field( 'afrsm_pro_save_action', 'afrsm_pro_conditions_save' );
?>
				<input type="hidden" name="post_type" value="wc_afrsm">
				<input type="hidden" name="fee_post_id" value="<?php 
echo esc_attr( $get_post_id );
?>">
				<table class="form-table table-outer shipping-method-table afrsm-table-tooltip">
					<tbody>
					<?php 
do_action( 'afrsm_status_field_before', $get_post_id );
?>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label>
                                <?php 
esc_html_e( 'Status', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'This method will be visible to customers only if it is enabled.', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
						</th>
						<td class="forminp">
							<label class="switch">
								<input type="checkbox" name="sm_status" value="on" <?php 
echo esc_attr( $sm_status );
?> />
								<div class="slider round"></div>
							</label>
						</td>
					</tr>
					<?php 
do_action( 'afrsm_status_field_after', $get_post_id );
do_action( 'afrsm_sname_field_before', $get_post_id );
?>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label for="fee_settings_product_fee_title">
                                <?php 
esc_html_e( 'Shipping Method Name', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
								<span class="required-star">*</span>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'This name will be visible to the customer at the time of checkout. This should convey the purpose of the charges you are applying to the order.', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
							</label>
						</th>
						<td class="forminp">
							<input type="text" name="fee_settings_product_fee_title" class="text-class" id="fee_settings_product_fee_title" value="<?php 
echo esc_attr( $sm_title );
?>" required="1" placeholder="<?php 
echo esc_attr( 'Enter shipping method name', 'advanced-flat-rate-shipping-for-woocommerce' );
?>" />
						</td>
					</tr>
					<?php 
do_action( 'afrsm_sname_field_after', $get_post_id );
do_action( 'afrsm_scharge_field_before', $get_post_id );
?>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label for="sm_product_cost">
                                <?php 
esc_html_e( 'Shipping Charge', 'advanced-flat-rate-shipping-for-woocommerce' );
?> (<?php 
echo esc_html( get_woocommerce_currency_symbol() );
?>)
								<span class="required-star">*</span>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'You can add a fixed/percentage fee based on the selection above.', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
							</label>
						</th>

						<td class="forminp">
							<div class="product_cost_left_div">
								<input type="text" name="sm_product_cost" required="1" class="text-class" id="sm_product_cost" value="<?php 
echo esc_attr( $sm_cost );
?>" placeholder="<?php 
echo esc_attr( get_woocommerce_currency_symbol() );
?>">
							</div>
							<?php 
?>
                            <div class="description afrsm_dynamic_rules_tooltips">
                                <p><?php 
echo wp_kses( __( 'When customer select this shipping method the amount will be added to the cart subtotal. You can enter fixed amount or make it dynamic using below parameters:', 'advanced-flat-rate-shipping-for-woocommerce' ), array() );
?></p>
                                <div class="afrsm_dynamic_rules_content">
                                    <?php 
echo sprintf( wp_kses( __( '&nbsp;&nbsp;&nbsp;<span>[qty]</span> - total number of items in cart<br/>
                                        &nbsp;&nbsp;&nbsp;<span>[cost]</span> - cost of items<br/>
                                        &nbsp;&nbsp;&nbsp;<span>[fee percent=10 min_fee=20]</span> - Percentage based fee<br/>
                                        &nbsp;&nbsp;&nbsp;<span>[fee percent=10 max_fee=20]</span> - Percentage based fee<br/><br/>
                                        Below are some examples:<br/>
                                        &nbsp;&nbsp;&nbsp;<strong>i.</strong> 10.00 -> To add flat 10.00 shipping charge.<br/>
                                        &nbsp;&nbsp;&nbsp;<strong>ii.</strong> 10.00 * <span>[qty]</span> - To charge 10.00 per quantity in the cart. It will be 50.00 if the cart has 5 quantity.<br/>
                                        &nbsp;&nbsp;&nbsp;<strong>iii.</strong> <span>[fee percent=10 min_fee=20]</span> - This means charge 10 percent of cart subtotal, minimum 20 charge will be applicable.<br/>
                                        &nbsp;&nbsp;&nbsp;<strong>iv.</strong> <span>[fee percent=10 max_fee=20]</span> - This means charge 10 percent of cart subtotal greater than max_fee then maximum 20 charge will be applicable.<br/><br/>
                                        <span class="dashicons dashicons-info-outline"></span>
                                        <a href="https://docs.thedotstore.com/article/101-shipping-fee-configuration-form" target="_blank">View Documentation</a>', 'advanced-flat-rate-shipping-for-woocommerce' ), array(
    'br'     => array(),
    'a'      => array(
        'href'   => array(),
        'title'  => array(),
        'target' => array(),
        'class'  => array(),
    ),
    'span'   => array(
        'class' => array(),
    ),
    'strong' => array(),
) ) );
?>
                                </div>
                            </div>
                            <a href="javascript:void(0);" id="afrsm_chk_advanced_settings" class="afrsm_chk_advanced_settings"><?php 
esc_html_e( 'Advance settings', 'advanced-flat-rate-shipping-for-woocommerce' );
?></a>
						</td>
					</tr>
					<?php 
do_action( 'afrsm_scharge_field_after', $get_post_id );
do_action( 'afrsm_etd_field_before', $get_post_id );
?>
						<tr valign="top" class="afrsm_advanced_setting_section">
							<th class="titledesc" scope="row">
								<label for="sm_estimation_delivery">
                                    <?php 
esc_html_e( 'Estimated Delivery Time', 'advanced-flat-rate-shipping-for-woocommerce' );
?><span class="afrsm-pro-label"></span><?php 
echo wp_kses( wc_help_tip( esc_html__( 'With this feature, you can specify approximately days or time to deliver the order to the customers. It will increase your conversion ratio. (Not for forceall shipping method)', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                                </label>
							</th>
							<td class="forminp">
								<input type="text" class="text-class" placeholder="<?php 
echo esc_attr( 'e.g. (2-5 days)', 'advanced-flat-rate-shipping-for-woocommerce' );
?>" value="" disabled >
							</td>
						</tr>
						<?php 
do_action( 'afrsm_etd_field_after', $get_post_id );
do_action( 'afrsm_start_date_field_before', $get_post_id );
?>
						<tr valign="top" class="afrsm_advanced_setting_section">
							<th class="titledesc" scope="row">
								<label for="sm_start_date">
                                    <?php 
esc_html_e( 'Start Date', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                    <span class="afrsm-pro-label"></span>
                                    <?php 
$html = sprintf( wp_kses( __( 'Select start date on which date shipping method will enable on the website.
										&nbsp;&nbsp;&nbsp;<span class="dashicons dashicons-info-outline"></span>
										<a href="https://docs.thedotstore.com/article/300-how-to-add-start-date-and-end-data" target="_blank">View Documentation</a><br/>', 'advanced-flat-rate-shipping-for-woocommerce' ), array(
    'br'     => array(),
    'a'      => array(
        'href'   => array(),
        'title'  => array(),
        'target' => array(),
        'class'  => array(),
    ),
    'span'   => array(
        'class' => array(),
    ),
    'strong' => array(),
) ) );
echo wp_kses( wc_help_tip( $html ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                                </label>
							</th>
							<td class="forminp">
								<input type="text" class="text-class" id="sm_start_date" value="" placeholder="<?php 
echo esc_attr( 'Select start date', 'advanced-flat-rate-shipping-for-woocommerce' );
?>" disabled>
							</td>
						</tr>
						<?php 
do_action( 'afrsm_start_date_field_after', $get_post_id );
do_action( 'afrsm_end_date_field_before', $get_post_id );
?>
						<tr valign="top" class="afrsm_advanced_setting_section">
							<th class="titledesc" scope="row">
								<label for="sm_end_date">
                                    <?php 
esc_html_e( 'End Date', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                    <span class="afrsm-pro-label"></span>
                                    <?php 
$html = sprintf( wp_kses( __( 'Select end date on which date shipping method will disable on the website.
										&nbsp;&nbsp;&nbsp;<span class="dashicons dashicons-info-outline"></span>
										<a href="https://docs.thedotstore.com/article/300-how-to-add-start-date-and-end-data" target="_blank">View Documentation</a><br/>', 'advanced-flat-rate-shipping-for-woocommerce' ), array(
    'br'     => array(),
    'a'      => array(
        'href'   => array(),
        'title'  => array(),
        'target' => array(),
        'class'  => array(),
    ),
    'span'   => array(
        'class' => array(),
    ),
    'strong' => array(),
) ) );
echo wp_kses( wc_help_tip( $html ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                                </label>
							</th>
							<td class="forminp">
								<input type="text" class="text-class" id="sm_end_date" value="" placeholder="<?php 
echo esc_attr( 'Select end date', 'advanced-flat-rate-shipping-for-woocommerce' );
?>" disabled>
							</td>
						</tr>
						<?php 
do_action( 'afrsm_end_date_field_after', $get_post_id );
do_action( 'afrsm_dow_field_before', $get_post_id );
?>
						<tr valign="top" class="afrsm_advanced_setting_section">
							<th class="titledesc" scope="row">
								<label for="sm_select_day_of_week">
                                    <?php 
esc_html_e( 'Days of the Week', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                    <span class="afrsm-pro-label"></span>
                                    <?php 
$html = sprintf(
    '%s<a href=%s target="_blank">%s</a>',
    esc_html__( 'Select days on which day shipping method will enable on the website. This rule match with current day which is set by wordpress', 'advanced-flat-rate-shipping-for-woocommerce' ),
    esc_url( admin_url( 'options-general.php' ) ),
    esc_html__( 'Timezone', 'advanced-flat-rate-shipping-for-woocommerce' )
);
echo wp_kses( wc_help_tip( $html ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                                </label>
							</th>
							<td class="forminp">
								<?php 
$select_day_week_array = array(
    'sun' => esc_html__( 'Sunday', 'advanced-flat-rate-shipping-for-woocommerce' ),
    'mon' => esc_html__( 'Monday', 'advanced-flat-rate-shipping-for-woocommerce' ),
    'tue' => esc_html__( 'Tuesday', 'advanced-flat-rate-shipping-for-woocommerce' ),
    'wed' => esc_html__( 'Wednesday', 'advanced-flat-rate-shipping-for-woocommerce' ),
    'thu' => esc_html__( 'Thursday', 'advanced-flat-rate-shipping-for-woocommerce' ),
    'fri' => esc_html__( 'Friday', 'advanced-flat-rate-shipping-for-woocommerce' ),
    'sat' => esc_html__( 'Saturday', 'advanced-flat-rate-shipping-for-woocommerce' ),
);
?>
								<select id="sm_select_day_of_week" class="sm_select_day_of_week afrsm_select" multiple="multiple" placeholder='<?php 
echo esc_attr( 'Select days of the week', 'advanced-flat-rate-shipping-for-woocommerce' );
?>' disabled>
									<?php 
foreach ( $select_day_week_array as $value => $name ) {
    ?>
										<option value=""><?php 
    echo esc_html( $name );
    ?></option>
										<?php 
}
?>
								</select>
							</td>
						</tr>
						<?php 
do_action( 'afrsm_dow_field_after', $get_post_id );
do_action( 'afrsm_time_field_before', $get_post_id );
?>
						<tr valign="top" class="afrsm_advanced_setting_section">
							<th class="titledesc" scope="row">
								<label>
                                    <?php 
esc_html_e( 'Time', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                    <span class="afrsm-pro-label"></span>
                                    <?php 
$html = sprintf(
    '%s<a href=%s target="_blank">%s</a>',
    esc_html__( 'Select time on which time shipping method will enable on the website. This rule match with current time which is set by wordpress', 'advanced-flat-rate-shipping-for-woocommerce' ),
    esc_url( admin_url( 'options-general.php' ) ),
    esc_html__( 'Timezone', 'advanced-flat-rate-shipping-for-woocommerce' )
);
echo wp_kses( wc_help_tip( $html ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                                </label>
							</th>
							<td class="forminp">
								<!-- <?php 
esc_html_e( 'From:', 'advanced-flat-rate-shipping-for-woocommerce' );
?></span> -->
								<input type="text" class="text-class afrsm_time_input" id="sm_time_from" value="" placeholder='<?php 
echo esc_attr( 'Select start time', 'advanced-flat-rate-shipping-for-woocommerce' );
?>' autocomplete="off" disabled />
								<span class="sm_time_to"><?php 
esc_html_e( '-', 'advanced-flat-rate-shipping-for-woocommerce' );
?></span>
								<input type="text" class="text-class afrsm_time_input" id="sm_time_to" value="" placeholder='<?php 
echo esc_attr( 'Select end time', 'advanced-flat-rate-shipping-for-woocommerce' );
?>' autocomplete="off" disabled />
                                <a href="javascript:void(0)" class="sm_reset_time"><span class="dashicons dashicons-update"></span></a>
							</td>
						</tr>
						<?php 
do_action( 'afrsm_time_field_after', $get_post_id );
do_action( 'afrsm_is_log_in_user_before', $get_post_id );
?>
					<tr valign="top" class="afrsm_advanced_setting_section">
						<th class="titledesc" scope="row">
							<label for="sm_select_log_in_user">
                                <?php 
esc_html_e( 'Enable for logged in users?', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Display shipping method only for logged in users. Default: No', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
						</th>
						<td class="forminp afrsm-radio-section">
                            <label>
                                <input name="sm_select_log_in_user" class="sm_select_log_in_user" type="radio" value="yes" <?php 
checked( $sm_is_log_in_user, 'yes' );
?>>
                                <?php 
esc_html_e( 'Yes', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </label>
                            <label>
                                <input name="sm_select_log_in_user" class="sm_select_log_in_user" type="radio" value="no" <?php 
( empty( $sm_is_log_in_user ) ? checked( $sm_is_log_in_user, '' ) : checked( $sm_is_log_in_user, 'no' ) );
?>>
                                <?php 
esc_html_e( 'No', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </label>
						</td>
					</tr>
					<?php 
do_action( 'afrsm_is_log_in_user_after', $get_post_id );
do_action( 'afrsm_first_order_for_user_before', $get_post_id );
?>
					<tr valign="top" class="afrsm_advanced_setting_section">
						<th class="titledesc" scope="row">
							<label for="sm_select_first_order_for_user">
                                <?php 
esc_html_e( 'Enable for first order', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Only apply when user will place first order. Default: No', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
						</th>
						<td class="forminp afrsm-radio-section">
                            <label>
                                <input name="sm_select_first_order_for_user" class="sm_select_first_order_for_user" type="radio" value="yes" <?php 
checked( $sm_first_order_for_user, 'yes' );
?>>
                                <?php 
esc_html_e( 'Yes', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </label>
                            <label>
                                <input name="sm_select_first_order_for_user" class="sm_select_first_order_for_user" type="radio" value="no" <?php 
( empty( $sm_first_order_for_user ) ? checked( $sm_first_order_for_user, '' ) : checked( $sm_first_order_for_user, 'no' ) );
?>>
                                <?php 
esc_html_e( 'No', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </label>
						</td>
					</tr>
					<?php 
do_action( 'afrsm_first_order_for_user_after', $get_post_id );
do_action( 'afrsm_free_shipping_status_before', $get_post_id );
?>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label for="sm_free_shipping_cost">
                                <?php 
esc_html_e( 'Allow Free Shipping', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                <?php 
$html = sprintf( wp_kses( __( 'Enable or disable free shipping. Default: False.
									&nbsp;&nbsp;&nbsp;<span class="dashicons dashicons-info-outline"></span>
									<a href="https://docs.thedotstore.com/article/403-advanced-free-shipping-rules" target="_blank">View Documentation</a><br/>', 'advanced-flat-rate-shipping-for-woocommerce' ), array(
    'br'     => array(),
    'a'      => array(
        'href'   => array(),
        'title'  => array(),
        'target' => array(),
        'class'  => array(),
    ),
    'span'   => array(
        'class' => array(),
    ),
    'strong' => array(),
) ) );
echo wp_kses( wc_help_tip( $html ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
						</th>
						<td class="forminp">
							<input type="checkbox" name="is_allow_free_shipping" id="is_allow_free_shipping" class="is_allow_free_shipping" value="on" <?php 
checked( $is_allow_free_shipping, 'on' );
?> />
						</td>
					</tr>
					<?php 
do_action( 'afrsm_free_shipping_status_after', $get_post_id );
do_action( 'afrsm_free_shipping_based_on_before', $get_post_id );
?>
					<tr valign="top" class="free_shipping_section free_shipping_section_top_css">
						<th class="titledesc" scope="row">
							<label for="sm_free_shipping_based_on">
                                <?php 
esc_html_e( 'Free Shipping based on', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Allow free shipping based on order amount, coupon amount or products .', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
						</th>
						<td class="forminp">
							<select name="sm_free_shipping_based_on" id="sm_free_shipping_based_on" class="afrsm_select_log_in_user">
								<option value="min_order_amt" <?php 
echo ( isset( $sm_free_shipping_based_on ) && 'min_order_amt' === $sm_free_shipping_based_on ? 'selected="selected"' : '' );
?>><?php 
esc_html_e( 'Minimum Order Amount', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
								<option value="min_coupan_amt" <?php 
echo ( isset( $sm_free_shipping_based_on ) && 'min_coupan_amt' === $sm_free_shipping_based_on ? 'selected="selected"' : '' );
?>><?php 
esc_html_e( 'Free Shipping on Coupon', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
								<?php 
?>
									<option value="in_pro"><?php 
esc_html_e( 'Free Shipping on Product 🔒', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
									<?php 
?>
							</select>
						</td>
					</tr>
					<?php 
do_action( 'afrsm_free_shipping_based_on_after', $get_post_id );
do_action( 'afrsm_free_shipping_label_before', $get_post_id );
?>
					<tr valign="top" class="free_shipping_section">
						<th class="titledesc" scope="row">
							<label for="sm_free_shipping_label">
                                <?php 
esc_html_e( 'Free Shipping - Label', 'advanced-flat-rate-shipping-for-woocommerce' );
if ( !(afrsfw_fs()->is__premium_only() && afrsfw_fs()->can_use_premium_code()) ) {
    ?><span class="afrsm-pro-label"></span><?php 
}
echo wp_kses( wc_help_tip( esc_html__( 'This name will be visible to the customer at the time of checkout when free shipping is available. For example "Free Shipping", "Free Rate" etc', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
						</th>
						<td class="forminp">
							<?php 
$input_disabled = "disabled";
$free_shipping_placeholder = "Free Shipping";
?>
							<input type="text" name="sm_free_shipping_label" class="text-class" id="sm_free_shipping_label" value="<?php 
echo esc_attr( $sm_free_shipping_label );
?>" placeholder="<?php 
echo esc_attr( $free_shipping_placeholder );
?>" <?php 
echo esc_attr( $input_disabled );
?>>
						</td>
					</tr>
					<?php 
do_action( 'afrsm_free_shipping_label_after', $get_post_id );
do_action( 'afrsm_free_shipping_order_amount_before', $get_post_id );
?>
					<tr valign="top" class="free_shipping_section free_shipping_amt">
						<th class="titledesc" scope="row">
							<label for="sm_free_shipping_cost">
                                <?php 
esc_html_e( 'Free Shipping Order - Amount', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Maximum free shipping order amount', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
						</th>
						<td class="forminp">
							<input type="text" name="sm_free_shipping_cost" class="text-class" id="sm_free_shipping_cost" value="<?php 
echo esc_attr( $sm_free_shipping_cost );
?>" placeholder="<?php 
echo esc_attr( get_woocommerce_currency_symbol() );
?>">
                            <?php 
?>
						</td>
					</tr>
					<?php 
do_action( 'afrsm_free_shipping_order_amount_after', $get_post_id );
?>

					<?php 
?>
					<?php 
do_action( 'afrsm_free_shipping_coupon_amount_before', $get_post_id );
?>
					<tr valign="top" class="free_shipping_section free_shipping_coupon free_shipping_section_bottom_css">
						<th class="titledesc" scope="row">
							<label for="sm_free_shipping_coupan_cost">
                                <?php 
esc_html_e( 'Free Shipping Coupon - Amount', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Maximum free shipping coupon amount', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
						</th>
						<td class="forminp">
							<input type="text" name="sm_free_shipping_coupan_cost" class="text-class" id="sm_free_shipping_coupan_cost" value="<?php 
echo esc_attr( $sm_free_shipping_coupan_cost );
?>" placeholder="<?php 
echo esc_attr( get_woocommerce_currency_symbol() );
?>">
						</td>
					</tr>
					<?php 
do_action( 'afrsm_free_shipping_coupon_amount_after', $get_post_id );
do_action( 'afrsm_tooltip_field_before', $get_post_id );
?>
					<tr valign="top" id="tooltip_section">
						<th class="titledesc" scope="row">
							<label for="sm_tooltip_type">
                                <?php 
esc_html_e( 'Tooltip type', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Set which type of details you want to show on frontside. Default: Tooltip', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
						</th>
						<td class="forminp">
							<select name="sm_tooltip_type" id="sm_tooltip_type" class="afrsm_tooltip_type">
								<option value="tooltip" <?php 
selected( $sm_tooltip_type, "tooltip" );
?>><?php 
esc_html_e( 'Tooltip', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
								<option value="subtitle" <?php 
selected( $sm_tooltip_type, "subtitle" );
?>><?php 
esc_html_e( 'Subtitle', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label for="sm_tooltip_desc">
                                <?php 
esc_html_e( 'Tooltip Description', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Not for dropdown shipping method', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
						</th>
						<td class="forminp">
                            <textarea name="sm_tooltip_desc" rows="3" cols="70" id="sm_tooltip_desc" maxlength="100" placeholder="<?php 
echo esc_attr( 'Enter tooltip description (Max. 100 characters)', 'advanced-flat-rate-shipping-for-woocommerce' );
?>"><?php 
echo wp_kses_post( $sm_tooltip_desc );
?></textarea>
							<p class="tooltip_error error_msg" style="display:none;">
								<?php 
esc_html_e( 'Please enter 100 characters only!', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
							</p>
						</td>
					</tr>
					<?php 
do_action( 'afrsm_tooltip_field_after', $get_post_id );
do_action( 'afrsm_default_shipping_before', $get_post_id );
?>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label for="sm_select_selected_shipping">
                                <?php 
esc_html_e( 'Default selected shipping?', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Set default selected shipping method on cart. Default: No', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
						</th>
						<td class="forminp afrsm-radio-section">
                            <label>
                                <input name="sm_select_selected_shipping" class="sm_select_selected_shipping" type="radio" value="yes" <?php 
checked( $sm_is_selected_shipping, 'yes' );
?>>
                                <?php 
esc_html_e( 'Yes', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </label>
                            <label>
                                <input name="sm_select_selected_shipping" class="sm_select_selected_shipping" type="radio" value="no" <?php 
( empty( $sm_is_selected_shipping ) ? checked( $sm_is_selected_shipping, '' ) : checked( $sm_is_selected_shipping, 'no' ) );
?>>
                                <?php 
esc_html_e( 'No', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </label>
						</td>
					</tr>
					<?php 
do_action( 'afrsm_default_shipping_after', $get_post_id );
do_action( 'afrsm_is_amount_taxable_field_before', $get_post_id );
?>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label for="sm_select_taxable">
                                <?php 
esc_html_e( 'Is Amount Taxable?', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Apply Tax. Default: No', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                            </label>
						</th>
						<td class="forminp afrsm-radio-section">
                            <label>
                                <input name="sm_select_taxable" class="sm_select_taxable" type="radio" value="yes" <?php 
checked( $sm_is_taxable, 'yes' );
?>>
                                <?php 
esc_html_e( 'Yes', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </label>
                            <label>
                                <input name="sm_select_taxable" class="sm_select_taxable" type="radio" value="no" <?php 
( empty( $sm_is_taxable ) ? checked( $sm_is_taxable, '' ) : checked( $sm_is_taxable, 'no' ) );
?>>
                                <?php 
esc_html_e( 'No', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </label>
						</td>
					</tr>
					<?php 
do_action( 'afrsm_is_amount_taxable_field_after', $get_post_id );
if ( is_plugin_active( 'woocommerce-germanized/woocommerce-germanized.php' ) ) {
    do_action( 'afrsm_shipping_provider_before', $get_post_id );
    ?>
						<tr valign="top">
							<th class="titledesc" scope="row">
								<label for="sm_select_shipping_provider">
                                    <?php 
    esc_html_e( 'Shipping Provider', 'advanced-flat-rate-shipping-for-woocommerce' );
    ?>
                                    <?php 
    echo wp_kses( wc_help_tip( esc_html__( 'Shipping provider select for order attahch with this fee as Germenized plugin does', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
        'span' => $allowed_tooltip_html,
    ) );
    ?>
                                </label>
							</th>
							<td class="forminp">
								<select name="sm_select_shipping_provider" id="sm_select_shipping_provider" class="afrsm_select_shipping_provider">
									<?php 
    foreach ( wc_gzd_get_shipping_provider_select() as $provider_k => $provider_v ) {
        ?>
										<option value="<?php 
        echo esc_attr( $provider_k );
        ?>" <?php 
        selected( $sm_select_shipping_provider, $provider_k );
        ?>><?php 
        echo esc_html( $provider_v );
        ?></option>
									<?php 
    }
    ?>
								</select>
							</td>
						</tr>
						<?php 
    do_action( 'afrsm_shipping_provider_after', $get_post_id );
}
?>
					</tbody>
				</table>
				<?php 
$all_shipping_classes = WC()->shipping->get_shipping_classes();
if ( !empty( $all_shipping_classes ) ) {
    ?>
					<div class="shipping-sub-section element-shadow">
						<h2><?php 
    esc_html_e( 'Additional Shipping Charges Based on Shipping Class', 'advanced-flat-rate-shipping-for-woocommerce' );
    ?></h2>
                        <table class="form-table table-outer shipping-method-table">
                            <tbody>
                            <tr valign="top">
                                <th class="forminp" colspan="2">
                                    <?php 
    $html = sprintf(
        '%s<a href=%s>%s</a>.',
        esc_html__( 'These costs can optionally be added based on the ', 'advanced-flat-rate-shipping-for-woocommerce' ),
        esc_url( add_query_arg( array(
            'page'    => 'wc-settings',
            'tab'     => 'shipping',
            'section' => 'classes',
        ), admin_url( 'admin.php' ) ) ),
        esc_html__( 'product shipping class', 'advanced-flat-rate-shipping-for-woocommerce' )
    );
    echo wp_kses_post( $html );
    ?>
                                </th>
                            </tr>
                            <?php 
    foreach ( $all_shipping_classes as $key => $shipping_class ) {
        $shipping_extra_cost = ( isset( $sm_extra_cost["{$shipping_class->term_id}"] ) && '' !== $sm_extra_cost["{$shipping_class->term_id}"] ? $sm_extra_cost["{$shipping_class->term_id}"] : "" );
        ?>
                                <tr valign="top">
                                    <th class="titledesc" scope="row">
                                        <label for="extra_cost_<?php 
        echo esc_attr( $shipping_class->term_id );
        ?>">
                                            <?php 
        echo sprintf( esc_html__( '"%s" shipping class cost', 'advanced-flat-rate-shipping-for-woocommerce' ), esc_html( $shipping_class->name ) );
        ?>
                                        </label>
                                    </th>
                                    <td class="forminp">
                                        <input type="text" name="sm_extra_cost[<?php 
        echo esc_attr( $shipping_class->term_id );
        ?>]" class="text-class" id="extra_cost_<?php 
        echo esc_attr( $shipping_class->term_id );
        ?>" value="<?php 
        echo esc_attr( $shipping_extra_cost );
        ?>" placeholder="<?php 
        echo esc_attr( get_woocommerce_currency_symbol() );
        ?>">
                                    </td>
                                </tr>
                            <?php 
    }
    ?>
                            <tr valign="top">
                                <th class="titledesc" scope="row">
                                    <label for="sm_extra_cost_calculation_type"><?php 
    esc_html_e( 'Calculation type', 'advanced-flat-rate-shipping-for-woocommerce' );
    ?></label>
                                </th>
                                <td class="forminp">
                                    <select name="sm_extra_cost_calculation_type" id="sm_extra_cost_calculation_type">
                                        <option value="per_class" <?php 
    selected( $sm_extra_cost_calc_type, 'per_class' );
    ?>>
                                            <?php 
    esc_html_e( 'Per class: Charge shipping for each shipping class individually', 'advanced-flat-rate-shipping-for-woocommerce' );
    ?>
                                        </option>
                                        <option value="per_order" <?php 
    selected( $sm_extra_cost_calc_type, 'per_order' );
    ?>>
                                            <?php 
    esc_html_e( 'Per order: Charge shipping for the most expensive shipping class', 'advanced-flat-rate-shipping-for-woocommerce' );
    ?>
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            </tbody>
                        </table>
					</div>
				<?php 
}
?>
				<div class="shipping-method-rules">
					<div class="sub-title sub-section">
						<h2><?php 
esc_html_e( 'Shipping Method Rules', 'advanced-flat-rate-shipping-for-woocommerce' );
?></h2>
						<div class="tap">
							<a id="shipping-add-field" class="button" href="javascript:;"><?php 
esc_html_e( '+ Add Rule', 'advanced-flat-rate-shipping-for-woocommerce' );
?></a>
						</div>

						<?php 
$html = sprintf( wp_kses( __( 'Add different conditional rules to display this shipping method to your customer. <a href="https://docs.thedotstore.com/article/102-shipping-rules-or-conditions" target="_blank">View Documentation</a>', 'advanced-flat-rate-shipping-for-woocommerce' ), array(
    'a' => array(
        'href'   => array(),
        'target' => array(),
        'class'  => array(),
    ),
) ) );
echo wp_kses( wc_help_tip( $html ), array(
    'span' => $allowed_tooltip_html,
) );
?>
					</div>
					<div class="tap">
						<table id="tbl-shipping-method" class="tbl_product_fee table-outer tap-cas form-table shipping-method-table">
							<tbody>
								<?php 
$attribute_taxonomies_name = wc_get_attribute_taxonomy_names();
if ( isset( $sm_metabox ) && !empty( $sm_metabox ) ) {
    $i = 2;
    foreach ( $sm_metabox as $key => $productfees ) {
        $fees_conditions = ( isset( $productfees['product_fees_conditions_condition'] ) ? $productfees['product_fees_conditions_condition'] : '' );
        $condition_is = ( isset( $productfees['product_fees_conditions_is'] ) ? $productfees['product_fees_conditions_is'] : '' );
        $condtion_value = ( isset( $productfees['product_fees_conditions_values'] ) ? $productfees['product_fees_conditions_values'] : array() );
        ?>
										<tr id="row_<?php 
        echo esc_attr( $i );
        ?>" valign="top">
											<td class="titledesc th_product_fees_conditions_condition" scope="row">
												<select rel-id="<?php 
        echo esc_attr( $i );
        ?>"
														id="product_fees_conditions_condition_<?php 
        echo esc_attr( $i );
        ?>"
														name="fees[product_fees_conditions_condition][]"
														id="product_fees_conditions_condition"
														class="product_fees_conditions_condition">
													<?php 
        /**
         * Added dynamic function for condition list action.
         *
         * @since  3.8
         *
         * @author jb
         */
        $condition_spe = $afrsm_admin_object->afrsm_conditions_list_action();
        foreach ( $condition_spe as $optg_key => $opt_data ) {
            ?>
														<optgroup label="<?php 
            echo esc_attr( $optg_key );
            ?>">
															<?php 
            foreach ( $opt_data as $opt_key => $opt_value ) {
                ?>
																<option value="<?php 
                echo esc_attr( $opt_key );
                ?>" <?php 
                echo ( $opt_key === $fees_conditions ? 'selected' : '' );
                ?> <?php 
                echo ( false !== strpos( $opt_key, 'in_pro' ) || false !== strpos( $opt_key, 'zone' ) ? 'class="afrsm-pro"' : '' );
                ?>>
																	<?php 
                echo esc_html( $opt_value );
                ?>
																	<?php 
                echo ( false !== strpos( $opt_key, 'in_pro' ) ? '🔒' : '' );
                ?>
																</option>
																<?php 
            }
            ?>

														</optgroup>
														<?php 
        }
        ?>
												</select>
											</td>
											<td class="select_condition_for_in_notin">
												<?php 
        /**
         * Added dynamic function for operator list action.
         *
         * @since  3.8
         *
         * @author jb
         */
        $opr_spe = $afrsm_admin_object->afrsm_operator_list_action( $fees_conditions );
        ?>
												<select name="fees[product_fees_conditions_is][]"
														class="product_fees_conditions_is_<?php 
        echo esc_attr( $i );
        ?>">
													<?php 
        foreach ( $opr_spe as $opr_key => $opr_value ) {
            ?>
														<option value="<?php 
            echo esc_attr( $opr_key );
            ?>" <?php 
            echo ( $opr_key === $condition_is ? 'selected' : '' );
            ?>><?php 
            echo esc_html( $opr_value );
            ?></option>
														<?php 
        }
        ?>
												</select>
											</td>
											<td class="condition-value" id="column_<?php 
        echo esc_attr( $i );
        ?>" <?php 
        if ( $i <= 2 ) {
            echo 'colspan="2"';
        }
        ?>>
												<?php 
        $html = '';
        if ( 'country' === $fees_conditions ) {
            $html .= $afrsm_admin_object->afrsm_pro_get_country_list( $i, $condtion_value );
        } elseif ( 'state' === $fees_conditions ) {
            $html .= $afrsm_admin_object->afrsm_pro_get_states_list( $i, $condtion_value );
        } elseif ( 'postcode' === $fees_conditions ) {
            $html .= '<textarea name = "fees[product_fees_conditions_values][value_' . esc_attr( $i ) . ']">' . wp_kses_post( $condtion_value ) . '</textarea>';
        } elseif ( 'zone' === $fees_conditions ) {
            $html .= $afrsm_admin_object->afrsm_pro_get_zones_list( $i, $condtion_value );
        } elseif ( 'product' === $fees_conditions ) {
            $html .= $afrsm_admin_object->afrsm_pro_get_product_list( $i, $condtion_value );
        } elseif ( 'category' === $fees_conditions ) {
            $html .= $afrsm_admin_object->afrsm_pro_get_category_list( $i, $condtion_value );
        } elseif ( 'tag' === $fees_conditions ) {
            $html .= $afrsm_admin_object->afrsm_pro_get_tag_list( $i, $condtion_value );
        } elseif ( 'user' === $fees_conditions ) {
            $html .= $afrsm_admin_object->afrsm_pro_get_user_list( $i, $condtion_value );
        } elseif ( 'cart_total' === $fees_conditions ) {
            $html .= '<input type = "text" name = "fees[product_fees_conditions_values][value_' . esc_attr( $i ) . ']" id = "product_fees_conditions_values" class = "product_fees_conditions_values price-class" value = "' . esc_attr( $condtion_value ) . '">';
        } elseif ( 'quantity' === $fees_conditions ) {
            $html .= '<input type = "text" name = "fees[product_fees_conditions_values][value_' . esc_attr( $i ) . ']" id = "product_fees_conditions_values" class = "product_fees_conditions_values qty-class" value = "' . esc_attr( $condtion_value ) . '">';
        } elseif ( 'width' === $fees_conditions ) {
            $html .= '<input type = "text" name = "fees[product_fees_conditions_values][value_' . esc_attr( $i ) . ']" id = "product_fees_conditions_values" class = "product_fees_conditions_values measure-class" value = "' . esc_attr( $condtion_value ) . '">';
        } elseif ( 'height' === $fees_conditions ) {
            $html .= '<input type = "text" name = "fees[product_fees_conditions_values][value_' . esc_attr( $i ) . ']" id = "product_fees_conditions_values" class = "product_fees_conditions_values measure-class" value = "' . esc_attr( $condtion_value ) . '">';
        } elseif ( 'length' === $fees_conditions ) {
            $html .= '<input type = "text" name = "fees[product_fees_conditions_values][value_' . esc_attr( $i ) . ']" id = "product_fees_conditions_values" class = "product_fees_conditions_values measure-class" value = "' . esc_attr( $condtion_value ) . '">';
        } elseif ( 'volume' === $fees_conditions ) {
            $html .= '<input type = "text" name = "fees[product_fees_conditions_values][value_' . esc_attr( $i ) . ']" id = "product_fees_conditions_values" class = "product_fees_conditions_values measure-class" value = "' . esc_attr( $condtion_value ) . '">';
        }
        echo wp_kses( apply_filters(
            'afrsm_pro_product_fees_conditions_values_edit_ft',
            $html,
            $i,
            $fees_conditions,
            $condtion_value
        ), Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro::afrsm_pro_allowed_html_tags() );
        ?>
												<input type="hidden"
													name="condition_key[value_<?php 
        echo esc_attr( $i );
        ?>]"
													value="">
											</td>
											<?php 
        if ( $i > 2 ) {
            ?>
											<td>
                                                <?php 
            ?>
												<a id="fee-delete-field" rel-id="<?php 
            echo esc_attr( $i );
            ?>"	class="delete-row" href="javascript:void(0);" title="Delete">
													<i class="dashicons dashicons-trash"></i>
												</a>
											</td>
											<?php 
        }
        ?>
										</tr>
										<?php 
        $i++;
    }
    ?>
									<?php 
} else {
    $i = 1;
    ?>
									<tr id="row_1" valign="top">
										<td class="titledesc th_product_fees_conditions_condition" scope="row">
											<select rel-id="1" id="product_fees_conditions_condition_1"
													name="fees[product_fees_conditions_condition][]"
													id="product_fees_conditions_condition"
													class="product_fees_conditions_condition">
												<?php 
    /**
     * Added dynamic function for condition list action.
     *
     * @since  3.8
     *
     * @author jb
     */
    $condition_spe = $afrsm_admin_object->afrsm_conditions_list_action();
    foreach ( $condition_spe as $optg_key => $opt_data ) {
        ?>
													<optgroup label="<?php 
        echo esc_attr( $optg_key );
        ?>">
														<?php 
        foreach ( $opt_data as $opt_key => $opt_value ) {
            ?>
															<option value="<?php 
            echo esc_attr( $opt_key );
            ?>" <?php 
            echo ( false !== strpos( $opt_key, 'in_pro' ) || false !== strpos( $opt_key, 'zone' ) ? 'class="afrsm-pro"' : '' );
            ?>>
                                                                <?php 
            echo esc_html( $opt_value );
            ?>
                                                                <?php 
            echo ( false !== strpos( $opt_key, 'in_pro' ) ? '🔒' : '' );
            ?>
                                                            </option>
															<?php 
        }
        ?>
													</optgroup>
													<?php 
    }
    ?>
											</select>
										</td>
										<td class="select_condition_for_in_notin">
											<select name="fees[product_fees_conditions_is][]"
													class="product_fees_conditions_is product_fees_conditions_is_1">
												<?php 
    /**
     * Added dynamic function for operator list action.
     *
     * @since  3.8
     *
     * @author jb
     */
    $opr_spe = $afrsm_admin_object->afrsm_operator_list_action( 'country' );
    foreach ( $opr_spe as $opr_key => $opr_value ) {
        ?>
													<option value="<?php 
        echo esc_attr( $opr_key );
        ?>"><?php 
        echo esc_html( $opr_value );
        ?></option>
													<?php 
    }
    ?>
											</select>
										</td>
										<td id="column_1" class="condition-value" colspan="2">
											<?php 
    echo wp_kses( $afrsm_admin_object->afrsm_pro_get_country_list( 1 ), Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro::afrsm_pro_allowed_html_tags() );
    ?>
											<input type="hidden" name="condition_key[value_1][]" value="">
										</td>
									</tr>
								<?php 
}
?>
							</tbody>
						</table>
						<input type="hidden" name="total_row" id="total_row" value="<?php 
echo esc_attr( $i );
?>">
					</div>
				</div>

				<?php 
// Advanced Pricing Section start
?>
				<div id="apm_wrap" class="adv-pricing-rules element-shadow">
					<div class="ap_title sub-section">
						<h2><?php 
esc_html_e( 'Advanced Shipping Price Rules', 'advanced-flat-rate-shipping-for-woocommerce' );
?></h2>
						<label class="switch">
							<input type="checkbox" name="ap_rule_status" value="on" <?php 
echo esc_attr( $ap_rule_status );
?>>
							<div class="slider round"></div>
						</label>
                        <?php 
echo wp_kses( wc_help_tip( esc_html__( 'If enabled this Advanced Pricing button only than below all rule\'s will go for apply to shipping method.', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
					</div>

					<div class="pricing_rules">
						<div class="pricing_rules_tab">
							<ul class="tabs">
								<?php 
/**
 * Added dynamic function for tab list action.
 *
 * @since  3.8
 *
 * @author jb
 */
$tab_array = $afrsm_admin_object->afrsm_advanced_tab_list_action();
if ( !empty( $tab_array ) ) {
    foreach ( $tab_array as $data_tab => $tab_title ) {
        if ( "tab-11" === $data_tab ) {
            $class = " current";
        } else {
            $class = "";
        }
        $pro_tab_class = '';
        if ( "tab-11" !== $data_tab && "tab-12" !== $data_tab ) {
            $pro_tab_class = 'afrsm-pro-tab';
        }
        ?>
										<li class="tab-link<?php 
        echo esc_attr( $class );
        ?> <?php 
        echo esc_attr( $pro_tab_class );
        ?>"
											data-tab="<?php 
        echo esc_attr( $data_tab );
        ?>">
											<?php 
        echo esc_html( $tab_title );
        ?>
										</li>
										<?php 
    }
}
?>
							</ul>
						</div>

						<div class="pricing_rules_tab_content">
							<?php 
?>	
							<?php 
$current_class_free = "current";
do_action( 'afrsm_ap_total_cart_weight_container_before', $get_post_id );
// Advanced Pricing Total Cart Weight start here
?>
							<div class="ap_total_cart_weight_container advance_pricing_rule_box tab-content <?php 
echo esc_attr( $current_class_free );
?>" id="tab-11" data-title="<?php 
esc_attr_e( 'Cost on Total Cart Weight', 'advanced-flat-rate-shipping-for-woocommerce' );
?>">
								<div class="tap-class">
									<div class="predefined_elements">
										<div id="all_cart_weight">
											<option value="total_cart_weight"><?php 
esc_html_e( 'Total Cart Weight', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
										</div>
									</div>
									<div class="sub-title">
										<h2 class="ap-title"><?php 
esc_html_e( 'Cost on Total Cart Weight', 'advanced-flat-rate-shipping-for-woocommerce' );
?></h2>
										<div class="tap">
											<a id="ap-add-field"
												data-filedtitle="total_cart_weight"
												data-qow="weight"
												data-filedtype="label"
												data-filedtitle2="total_cart_weight"
												data-filedcategory=""
												data-relatedtype=""
												class="button"
												href="javascript:;"><?php 
esc_html_e( '+ Add Rule', 'advanced-flat-rate-shipping-for-woocommerce' );
?></a>
											<div class="switch_status_div">
												<label class="switch switch_in_pricing_rules">
													<input type="checkbox"
															name="cost_on_total_cart_weight_status"
															value="on" <?php 
echo esc_attr( $cost_on_total_cart_weight_status );
?>>
													<div class="slider round"></div>
												</label>
                                                <?php 
echo wp_kses( wc_help_tip( esc_html__( AFRSM_PRO_PERTICULAR_FEE_AMOUNT_NOTICE, 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
											</div>
										</div>
										<div class="advance_rule_condition_match_type">
											<p class="switch_in_pricing_rules_description_left">
												<?php 
esc_html_e( 'below', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
											</p>
											<select name="cost_rule_match[cost_on_total_cart_weight_rule_match]"
													id="cost_on_total_cart_weight_rule_match"
													class="arcmt_select">
												<option value="any" <?php 
selected( $cost_on_total_cart_weight_rule_match, 'any' );
?>><?php 
esc_html_e( 'Any One', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
												<option value="all" <?php 
selected( $cost_on_total_cart_weight_rule_match, 'all' );
?>><?php 
esc_html_e( 'All', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
											</select>
											<p class="switch_in_pricing_rules_description">
												<?php 
esc_html_e( 'rule match', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
											</p>
										</div>
										<div class="advance_rule_condition_help">
											<span class="dashicons dashicons-info-outline"></span>
											<a href="<?php 
echo esc_url( 'https://docs.thedotstore.com/article/122-advanced-shipping-price-rules-shipping-cost-on-total-cart-weight' );
?>" target="_blank"><?php 
esc_html_e( 'View Documentation', 'advanced-flat-rate-shipping-for-woocommerce' );
?></a>
										</div>
									</div>
									<table id="tbl_ap_total_cart_weight_method" class="tbl_total_cart_weight table-outer tap-cas form-table advance-shipping-method-table">
										<tbody>
										<tr class="heading">
											<th class="titledesc th_total_cart_weight_fees_conditions_condition" scope="row">
                                                <span><?php 
esc_html_e( 'Total Cart Weight', 'advanced-flat-rate-shipping-for-woocommerce' );
?></span>
                                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Total Cart Weight', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
											</th>
											<th class="titledesc th_total_cart_weight_fees_conditions_condition" scope="row">
                                                <span><?php 
esc_html_e( 'Min Weight ', 'advanced-flat-rate-shipping-for-woocommerce' );
?></span>
                                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'You can set a minimum total cart weight per row before the shipping amount is applied.', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                                            </th>
											<th class="titledesc th_total_cart_weight_fees_conditions_condition" scope="row">
                                                <span><?php 
esc_html_e( 'Max Weight', 'advanced-flat-rate-shipping-for-woocommerce' );
?></span>
                                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'You can set a maximum total cart weight per row before the shipping amount is applied. Leave empty then will set with infinite', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                                            </th>
											<th class="titledesc th_total_cart_weight_fees_conditions_condition" scope="row" colspan="2">
                                                <span><?php 
esc_html_e( 'Shipping Amount', 'advanced-flat-rate-shipping-for-woocommerce' );
?></span>
                                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'A fixed amount (e.g. 5 / -5) percentage (e.g. 5% / -5%) to add as a fee. Percentage and minus amount will apply based on cart subtotal.', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
											</th>
										</tr>
										<?php 
//check advanced pricing value fill proper or unset if not
$filled_total_cart_weight = array();
//check if category AP rules exist
if ( !empty( $sm_metabox_ap_total_cart_weight ) && is_array( $sm_metabox_ap_total_cart_weight ) ) {
    foreach ( $sm_metabox_ap_total_cart_weight as $apcat_arr ) {
        //check that if required field fill or not once save the APR,  if match than fill in array
        if ( !empty( $apcat_arr ) || '' !== $apcat_arr ) {
            if ( '' !== $apcat_arr['ap_fees_total_cart_weight'] && '' !== $apcat_arr['ap_fees_ap_price_total_cart_weight'] && ('' !== $apcat_arr['ap_fees_ap_total_cart_weight_min_weight'] || '' !== $apcat_arr['ap_fees_ap_total_cart_weight_max_weight']) ) {
                //if condition match than fill in array
                $filled_total_cart_weight[] = $apcat_arr;
            }
        }
    }
}
//check APR exist
if ( isset( $filled_total_cart_weight ) && !empty( $filled_total_cart_weight ) ) {
    $cnt_total_cart_weight = 2;
    foreach ( $filled_total_cart_weight as $key => $productfees ) {
        $ap_fees_ap_total_cart_weight_min_weight = ( isset( $productfees['ap_fees_ap_total_cart_weight_min_weight'] ) ? $productfees['ap_fees_ap_total_cart_weight_min_weight'] : '' );
        $ap_fees_ap_total_cart_weight_max_weight = ( isset( $productfees['ap_fees_ap_total_cart_weight_max_weight'] ) ? $productfees['ap_fees_ap_total_cart_weight_max_weight'] : '' );
        $ap_fees_ap_price_total_cart_weight = ( isset( $productfees['ap_fees_ap_price_total_cart_weight'] ) ? $productfees['ap_fees_ap_price_total_cart_weight'] : '' );
        ?>
												<tr id="ap_total_cart_weight_row_<?php 
        echo esc_attr( $cnt_total_cart_weight );
        ?>"
													valign="top" class="ap_total_cart_weight_row_tr">
													<td class="titledesc" scope="row">
														<label><?php 
        echo esc_html_e( 'Cart Weight', 'advanced-flat-rate-shipping-for-woocommerce' );
        ?></label>
														<input type="hidden"
																name="fees[ap_total_cart_weight_fees_conditions_condition][<?php 
        echo esc_attr( $cnt_total_cart_weight );
        ?>][]"
																id="ap_total_cart_weight_fees_conditions_condition_<?php 
        echo esc_attr( $cnt_total_cart_weight );
        ?>">
													</td>
													<td class="column_<?php 
        echo esc_attr( $cnt_total_cart_weight );
        ?> condition-value">
														<input type="text"
																name="fees[ap_fees_ap_total_cart_weight_min_weight][]"
																class="text-class weight-class min-val-class"
																id="ap_fees_ap_total_cart_weight_min_weight[]"
																placeholder="<?php 
        echo esc_attr( 'Min weight', 'advanced-flat-rate-shipping-for-woocommerce' );
        ?>"
																value="<?php 
        echo esc_attr( $ap_fees_ap_total_cart_weight_min_weight );
        ?>">
													</td>
													<td class="column_<?php 
        echo esc_attr( $cnt_total_cart_weight );
        ?> condition-value">
														<input type="text"
																name="fees[ap_fees_ap_total_cart_weight_max_weight][]"
																class="text-class weight-class max-val-class"
																id="ap_fees_ap_total_cart_weight_max_weight[]"
																placeholder="<?php 
        echo esc_attr( 'Max weight', 'advanced-flat-rate-shipping-for-woocommerce' );
        ?>"
																value="<?php 
        echo esc_attr( $ap_fees_ap_total_cart_weight_max_weight );
        ?>">
													</td>
													<td class="column_<?php 
        echo esc_attr( $cnt_total_cart_weight );
        ?> condition-value">
														<input type="text"
																name="fees[ap_fees_ap_price_total_cart_weight][]"
																class="text-class number-field price-val-class"
																id="ap_fees_ap_price_total_cart_weight[]"
																placeholder="<?php 
        echo esc_attr( 'amount', 'advanced-flat-rate-shipping-for-woocommerce' );
        ?>"
																value="<?php 
        echo esc_attr( $ap_fees_ap_price_total_cart_weight );
        ?>">
														<?php 
        $first_char = substr( $ap_fees_ap_price_total_cart_weight, 0, 1 );
        if ( '-' === $first_char ) {
            $html = sprintf( '<p><b style="color: red;">%s</b>%s', esc_html__( 'Note: ', 'advanced-flat-rate-shipping-for-woocommerce' ), esc_html__( 'If entered shipping amount is less than cart subtotal it will reflect with minus sign (EX: $ -10.00) OR If entered shipping amount is more than cart subtotal then the total amount shown as zero (EX: Total: 0): ', 'advanced-flat-rate-shipping-for-woocommerce' ) );
            echo wp_kses_post( $html );
        }
        ?>
													</td>
													<td class="column_<?php 
        echo esc_attr( $cnt_total_cart_weight );
        ?> condition-value">
													<?php 
        ?>
														<a id="ap-total-cart-weight-delete-field"
															rel-id="<?php 
        echo esc_attr( $cnt_total_cart_weight );
        ?>"
															title="Delete" class="delete-row"
															href="javascript:;">
															<i class="dashicons dashicons-trash"></i>
														</a>
													</td>
												</tr>
												<?php 
        $cnt_total_cart_weight++;
    }
    ?>
											<?php 
} else {
    $cnt_total_cart_weight = 1;
}
?>
										</tbody>
									</table>
									<input type="hidden" name="total_row_total_cart_weight"
											id="total_row_total_cart_weight"
											value="<?php 
echo esc_attr( $cnt_total_cart_weight );
?>">
									<!-- Advanced Pricing Category Section end here -->
								</div>
							</div>
							<?php 
//Advanced Pricing Total Cart Weight end here
do_action( 'afrsm_ap_total_cart_weight_container_after', $get_post_id );
do_action( 'afrsm_ap_total_cart_subtotal_container_before', $get_post_id );
?>
							<!-- Advanced Pricing Total Cart Subtotal start here -->
							<div class="ap_total_cart_subtotal_container advance_pricing_rule_box tab-content" id="tab-12" data-title="<?php 
esc_attr_e( 'Cost on Total Cart Subtotal', 'advanced-flat-rate-shipping-for-woocommerce' );
?>">
								<div class="tap-class">
									<div class="predefined_elements">
										<div id="all_cart_subtotal">
											<option value="total_cart_subtotal"><?php 
esc_html_e( 'Total Cart Subtotal', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
										</div>
									</div>
									<div class="sub-title">
										<h2 class="ap-title"><?php 
esc_html_e( 'Cost on Total Cart Subtotal', 'advanced-flat-rate-shipping-for-woocommerce' );
?></h2>
										<div class="tap">
											<a id="ap-add-field"
												data-filedtitle="total_cart_subtotal"
												data-qow="subtotal"
												data-filedtype="label"
												data-filedtitle2="total_cart_subtotal"
												data-filedcategory=""
												data-relatedtype=""
												class="button"
												href="javascript:;"><?php 
esc_html_e( '+ Add Rule', 'advanced-flat-rate-shipping-for-woocommerce' );
?></a>
											<div class="switch_status_div">
												<label class="switch switch_in_pricing_rules">
													<input type="checkbox"
															name="cost_on_total_cart_subtotal_status"
															value="on" <?php 
echo esc_attr( $cost_on_total_cart_subtotal_status );
?>>
													<div class="slider round"></div>
												</label>
                                                <?php 
echo wp_kses( wc_help_tip( esc_html__( AFRSM_PRO_PERTICULAR_FEE_AMOUNT_NOTICE, 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
											</div>
										</div>
										<div class="advance_rule_condition_match_type">
											<p class="switch_in_pricing_rules_description_left">
												<?php 
esc_html_e( 'below', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
											</p>
											<select name="cost_rule_match[cost_on_total_cart_subtotal_rule_match]"
													id="cost_on_total_cart_subtotal_rule_match"
													class="arcmt_select">
												<option value="any" <?php 
selected( $cost_on_total_cart_subtotal_rule_match, 'any' );
?>><?php 
esc_html_e( 'Any One', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
												<option value="all" <?php 
selected( $cost_on_total_cart_subtotal_rule_match, 'all' );
?>><?php 
esc_html_e( 'All', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
											</select>
											<p class="switch_in_pricing_rules_description">
												<?php 
esc_html_e( 'rule match', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
											</p>
										</div>
										<div class="advance_rule_condition_help">
											<span class="dashicons dashicons-info-outline"></span>
											<a href="<?php 
echo esc_url( 'https://docs.thedotstore.com/article/663-advanced-fees-price-rules-cost-on-total-cart-subtotal' );
?>" target="_blank"><?php 
esc_html_e( 'View Documentation', 'advanced-flat-rate-shipping-for-woocommerce' );
?></a>
										</div>
									</div>
									<table id="tbl_ap_total_cart_subtotal_method" class="tbl_total_cart_subtotal table-outer tap-cas form-table advance-shipping-method-table">
										<tbody>
										<tr class="heading">
											<th class="titledesc th_total_cart_subtotal_fees_conditions_condition" scope="row">
                                                <span><?php 
esc_html_e( 'Total Cart Subtotal', 'advanced-flat-rate-shipping-for-woocommerce' );
?></span>
                                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'Total Cart Subtotal', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
											</th>
											<th class="titledesc th_total_cart_subtotal_fees_conditions_condition" scope="row">
                                                <span><?php 
esc_html_e( 'Min Subtotal ', 'advanced-flat-rate-shipping-for-woocommerce' );
?></span>
                                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'You can set a minimum total cart subtotal per row before the shipping amount is
												applied.', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                                            </th>
											<th class="titledesc th_total_cart_subtotal_fees_conditions_condition" scope="row">
                                                <span><?php 
esc_html_e( 'Max Subtotal', 'advanced-flat-rate-shipping-for-woocommerce' );
?></span>
                                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'You can set a maximum total cart subtotal per row before the shipping amount is applied. Leave empty then will set with infinite', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                                            </th>
											<th class="titledesc th_total_cart_subtotal_fees_conditions_condition" scope="row" colspan="2">
                                                <span><?php 
esc_html_e( 'Shipping Amount', 'advanced-flat-rate-shipping-for-woocommerce' );
?></span>
                                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'A fixed amount (e.g. 5 / -5) percentage (e.g. 5% / -5%) to add as a fee. Percentage and minus amount will apply based on cart subtotal.', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
											</th>
										</tr>
										<?php 
//check advanced pricing value fill proper or unset if not
$filled_total_cart_subtotal = array();
//check if category AP rules exist
if ( !empty( $sm_metabox_ap_total_cart_subtotal ) && is_array( $sm_metabox_ap_total_cart_subtotal ) ) {
    foreach ( $sm_metabox_ap_total_cart_subtotal as $apcat_arr ) {
        //check that if required field fill or not once save the APR,  if match than fill in array
        if ( !empty( $apcat_arr ) || $apcat_arr !== '' ) {
            if ( $apcat_arr['ap_fees_total_cart_subtotal'] !== '' && $apcat_arr['ap_fees_ap_price_total_cart_subtotal'] !== '' && ($apcat_arr['ap_fees_ap_total_cart_subtotal_min_subtotal'] !== '' || $apcat_arr['ap_fees_ap_total_cart_subtotal_max_subtotal'] !== '') ) {
                $filled_total_cart_subtotal[] = $apcat_arr;
            }
        }
    }
}
//check APR exist
if ( isset( $filled_total_cart_subtotal ) && !empty( $filled_total_cart_subtotal ) ) {
    $cnt_total_cart_subtotal = 2;
    foreach ( $filled_total_cart_subtotal as $key => $productfees ) {
        $ap_fees_ap_total_cart_subtotal_min_subtotal = ( isset( $productfees['ap_fees_ap_total_cart_subtotal_min_subtotal'] ) ? $productfees['ap_fees_ap_total_cart_subtotal_min_subtotal'] : '' );
        $ap_fees_ap_total_cart_subtotal_max_subtotal = ( isset( $productfees['ap_fees_ap_total_cart_subtotal_max_subtotal'] ) ? $productfees['ap_fees_ap_total_cart_subtotal_max_subtotal'] : '' );
        $ap_fees_ap_price_total_cart_subtotal = ( isset( $productfees['ap_fees_ap_price_total_cart_subtotal'] ) ? $productfees['ap_fees_ap_price_total_cart_subtotal'] : '' );
        ?>
												<tr id="ap_total_cart_subtotal_row_<?php 
        echo esc_attr( $cnt_total_cart_subtotal );
        ?>"
													valign="top" class="ap_total_cart_subtotal_row_tr">
													<td class="titledesc" scope="row">
														<label><?php 
        echo esc_html_e( 'Cart Subtotal', 'advanced-flat-rate-shipping-for-woocommerce' );
        ?></label>
														<input type="hidden"
																name="fees[ap_total_cart_subtotal_fees_conditions_condition][<?php 
        echo esc_attr( $cnt_total_cart_subtotal );
        ?>][]"
																id="ap_total_cart_subtotal_fees_conditions_condition_<?php 
        echo esc_attr( $cnt_total_cart_subtotal );
        ?>">
													</td>
													<td class="column_<?php 
        echo esc_attr( $cnt_total_cart_subtotal );
        ?> condition-value">
														<input type="number"
																name="fees[ap_fees_ap_total_cart_subtotal_min_subtotal][]"
																class="text-class price-class min-val-class subtotal-class"
																id="ap_fees_ap_total_cart_subtotal_min_subtotal[]"
																placeholder="<?php 
        echo esc_attr( 'Min Subtotal', 'advanced-flat-rate-shipping-for-woocommerce' );
        ?>"
																step="0.01"
																value="<?php 
        echo esc_attr( $ap_fees_ap_total_cart_subtotal_min_subtotal );
        ?>"
																min="0.0">
													</td>
													<td class="column_<?php 
        echo esc_attr( $cnt_total_cart_subtotal );
        ?> condition-value">
														<input type="number"
																name="fees[ap_fees_ap_total_cart_subtotal_max_subtotal][]"
																class="text-class price-class max-val-class subtotal-class"
																id="ap_fees_ap_total_cart_subtotal_max_subtotal[]"
																placeholder="<?php 
        echo esc_attr( 'Max Subtotal', 'advanced-flat-rate-shipping-for-woocommerce' );
        ?>"
																step="0.01"
																value="<?php 
        echo esc_attr( $ap_fees_ap_total_cart_subtotal_max_subtotal );
        ?>"
																min="0.0">
													</td>
													<td class="column_<?php 
        echo esc_attr( $cnt_total_cart_subtotal );
        ?> condition-value">
														<input type="text"
																name="fees[ap_fees_ap_price_total_cart_subtotal][]"
																class="text-class number-field price-val-class"
																id="ap_fees_ap_price_total_cart_subtotal[]"
																placeholder="<?php 
        echo esc_attr( 'Amount', 'advanced-flat-rate-shipping-for-woocommerce' );
        ?>"
																value="<?php 
        echo esc_attr( $ap_fees_ap_price_total_cart_subtotal );
        ?>">
														<?php 
        $first_char = substr( $ap_fees_ap_price_total_cart_subtotal, 0, 1 );
        if ( '-' === $first_char ) {
            $html = sprintf( '<p><b style="color: red;">%s</b>%s', esc_html__( 'Note: ', 'advanced-flat-rate-shipping-for-woocommerce' ), esc_html__( 'If entered shipping amount is less than cart subtotal it will reflect with minus sign (EX: $ -10.00) OR If entered shipping amount is more than cart subtotal then the total amount shown as zero (EX: Total: 0): ', 'advanced-flat-rate-shipping-for-woocommerce' ) );
            echo wp_kses_post( $html );
        }
        ?>
													</td>
													<td class="column_<?php 
        echo esc_attr( $cnt_total_cart_subtotal );
        ?> condition-value">
													<?php 
        ?>
														<a id="ap-total-cart-subtotal-delete-field"
															rel-id="<?php 
        echo esc_attr( $cnt_total_cart_subtotal );
        ?>"
															title="Delete" class="delete-row"
															href="javascript:;">
															<i class="dashicons dashicons-trash"></i>
														</a>
													</td>
												</tr>
												<?php 
        $cnt_total_cart_subtotal++;
    }
    ?>
											<?php 
} else {
    $cnt_total_cart_subtotal = 1;
}
?>
										</tbody>
									</table>
									<input type="hidden" name="total_row_total_cart_subtotal"
											id="total_row_total_cart_subtotal"
											value="<?php 
echo esc_attr( $cnt_total_cart_subtotal );
?>">
									<!-- Advanced Pricing Category Section end here -->

								</div>
							</div>
							<!-- Advanced Pricing Total Cart Subtotal end here -->
							<?php 
do_action( 'afrsm_ap_total_cart_subtotal_container_after', $get_post_id );
?>		
						</div>
					</div>
				</div>
				<?php 
// Advanced Pricing Section end
?>
						
				<p class="submit">
					<input type="submit" name="submitFee" class="button button-primary" value="<?php 
echo esc_attr( $submit_text );
?>">
				</p>
			</form>
		</div>
	</div>
<?php 
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php';