<?php

/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */
defined( 'ABSPATH' ) || exit;
$formatted_destination = ( isset( $formatted_destination ) ? $formatted_destination : WC()->countries->get_formatted_address( $package['destination'], ', ' ) );
// phpcs:ignore
$has_calculated_shipping = !empty( $has_calculated_shipping );
$show_shipping_calculator = !empty( $show_shipping_calculator );
$calculator_text = '';
$shipping_method_format = get_option( 'md_woocommerce_shipping_method_format' );
$afrsm_force_customer_to_select_sm = get_option( 'afrsm_force_customer_to_select_sm' );
$shipping_method_format = ( !empty( $shipping_method_format ) ? $shipping_method_format : 'radio_button_mode' );
?>
<tr class="woocommerce-shipping-totals shipping afrsm_shipping">
	<th><?php 
echo wp_kses_post( $package_name );
?></th>
	<td data-title="<?php 
echo esc_attr( $package_name );
?>">
		<?php 
if ( $available_methods ) {
    ?>
			<?php 
    if ( 'dropdown_mode' === $shipping_method_format ) {
        ?>
				<select name="shipping_method[<?php 
        echo esc_attr( $index );
        ?>]"
				        data-index="<?php 
        echo esc_attr( $index );
        ?>"
				        id="shipping_method_<?php 
        echo esc_attr( $index );
        ?>" class="shipping_method">
				        <?php 
        if ( isset( $afrsm_force_customer_to_select_sm ) && 'on' === $afrsm_force_customer_to_select_sm ) {
            ?>
				        	<option value='' selected="selected"><?php 
            esc_html_e( 'Select a shipping option', 'advanced-flat-rate-shipping-for-woocommerce' );
            ?></option>
				        <?php 
        }
        ?>
					<?php 
        foreach ( $available_methods as $method ) {
            ?>
						<option
							value="<?php 
            echo esc_attr( $method->id );
            ?>" <?php 
            selected( $method->id, $chosen_method );
            ?>><?php 
            echo wp_kses_post( wc_cart_totals_shipping_method_label( $method ) );
            ?></option>
					<?php 
        }
        ?>
				</select>
			<?php 
    } else {
        ?>
				<ul id="shipping_method" class="woocommerce-shipping-methods">
					<?php 
        foreach ( $available_methods as $method ) {
            ?>
						<li>
							<?php 
            $tool_tip_html = '';
            $final_shipping_label = '';
            $get_method_id = '';
            if ( false !== strpos( $method->id, 'advanced_flat_rate_shipping:' ) ) {
                $method_id_explode = explode( ':', $method->id );
                $get_method_id = $method_id_explode[1];
            }
            $sm_estimation_delivery = get_post_meta( $get_method_id, 'sm_estimation_delivery', true );
            $sm_estimation_delivery = ( isset( $sm_estimation_delivery ) && !empty( $sm_estimation_delivery ) ? ' ( ' . $sm_estimation_delivery . ' ) ' : '' );
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
            if ( 1 < count( $available_methods ) ) {
                printf(
                    '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />',
                    $index,
                    esc_attr( sanitize_title( $method->id ) ),
                    esc_attr( $method->id ),
                    checked( $method->id, $chosen_method, false )
                );
                // phpcs:ignore
            } else {
                printf(
                    '<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" />',
                    $index,
                    esc_attr( sanitize_title( $method->id ) ),
                    esc_attr( $method->id )
                );
                // phpcs:ignore
            }
            printf(
                '<label for="shipping_method_%1$s_%2$s">%3$s</label>',
                $index,
                esc_attr( sanitize_title( $method->id ) ),
                wc_cart_totals_shipping_method_label( $method )
            );
            // phpcs:ignore
            do_action( 'woocommerce_after_shipping_rate', $method, $index );
            ?>
						</li>
					<?php 
        }
        ?>
				</ul>
				<?php 
    }
    ?>
			<?php 
    if ( is_cart() ) {
        ?>
				<p class="woocommerce-shipping-destination">
					<?php 
        if ( $formatted_destination ) {
            // Translators: $s shipping destination.
            printf( esc_html__( 'Shipping to %s.', 'advanced-flat-rate-shipping-for-woocommerce' ) . ' ', '<strong>' . esc_html( $formatted_destination ) . '</strong>' );
            $calculator_text = esc_html__( 'Change address', 'advanced-flat-rate-shipping-for-woocommerce' );
        } else {
            echo wp_kses_post( apply_filters( 'woocommerce_shipping_estimate_html', __( 'Shipping options will be updated during checkout.', 'advanced-flat-rate-shipping-for-woocommerce' ) ) );
        }
        ?>
				</p>
			<?php 
    }
    ?>
		<?php 
} elseif ( !$has_calculated_shipping || !$formatted_destination ) {
    if ( is_cart() && 'no' === get_option( 'woocommerce_enable_shipping_calc' ) ) {
        echo wp_kses_post( apply_filters( 'woocommerce_shipping_not_enabled_on_cart_html', __( 'Shipping costs are calculated during checkout.', 'advanced-flat-rate-shipping-for-woocommerce' ) ) );
    } else {
        echo wp_kses_post( apply_filters( 'woocommerce_shipping_may_be_available_html', __( 'Enter your address to view shipping options.', 'advanced-flat-rate-shipping-for-woocommerce' ) ) );
    }
} elseif ( !is_cart() ) {
    echo wp_kses_post( apply_filters( 'woocommerce_no_shipping_available_html', __( 'There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'advanced-flat-rate-shipping-for-woocommerce' ) ) );
} else {
    // Translators: $s shipping destination.
    echo wp_kses_post( apply_filters( 'woocommerce_cart_no_shipping_available_html', sprintf( esc_html__( 'No shipping options were found for %s.', 'advanced-flat-rate-shipping-for-woocommerce' ) . ' ', '<strong>' . esc_html( $formatted_destination ) . '</strong>' ) ) );
    $calculator_text = esc_html__( 'Enter a different address', 'advanced-flat-rate-shipping-for-woocommerce' );
}
?>
		
		<?php 
if ( $show_package_details ) {
    ?>
			<?php 
    echo '<p class="woocommerce-shipping-contents"><small>' . esc_html( $package_details ) . '</small></p>';
    ?>
		<?php 
}
?>
		
		<?php 
if ( $show_shipping_calculator ) {
    ?>
			<?php 
    woocommerce_shipping_calculator( $calculator_text );
    ?>
		<?php 
}
?>
	</td>
</tr>
