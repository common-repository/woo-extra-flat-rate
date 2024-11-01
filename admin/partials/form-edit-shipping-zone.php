<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php';
$countries = WC()->countries->get_allowed_countries();
$allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];
$get_zone_id = filter_input( INPUT_GET, 'edit_zone', FILTER_SANITIZE_NUMBER_INT );
$zone_list = get_post( $get_zone_id );
$zone_title = $zone_list->post_title;
$zone_status = $zone_list->post_status;
$zone_type = get_post_meta( $get_zone_id, 'zone_type', true );
$location_type = get_post_meta( $get_zone_id, 'location_type', true );
$get_location_code = get_post_meta( $get_zone_id, 'location_code', true );
$location_code = array();
$postcode_state = array();
$country_code = array();
$state_code = array();
$city_code = array();
$city_state = array();
if ( 'country' === $location_type ) {
    if ( !empty( $get_location_code ) ) {
        foreach ( $get_location_code as $location_code_key => $sub_location_code_val ) {
            foreach ( $sub_location_code_val as $sub_location_code_val ) {
                $country_code[] = $sub_location_code_val;
            }
        }
    }
}
if ( 'state' === $location_type ) {
    if ( !empty( $get_location_code ) ) {
        foreach ( $get_location_code as $location_code_key => $sub_location_code_val ) {
            foreach ( $sub_location_code_val as $sub_location_code_val ) {
                $state_code[] = $sub_location_code_val;
            }
        }
    }
}
if ( 'postcode' === $location_type ) {
    if ( !empty( $get_location_code ) ) {
        foreach ( $get_location_code as $location_code_key => $location_code_val ) {
            $postcode_state[] = $location_code_key;
            $location_code = $location_code_val;
        }
    }
}
?>

    <div class="afrsm-section-left afrsm-pro-list-shipping-zones">
        <div class="edit-inner">
            <div class="afrsm-pro-zone-table res-cl">
                <h2><?php 
esc_html_e( 'Edit Shipping Zone', 'advanced-flat-rate-shipping-for-woocommerce' );
?> &mdash; <?php 
echo esc_html( $zone_title );
?></h2>
            </div>
            <form id="add-zone" class="afrsm-shipping-zone" method="post">
                <table class="form-table table-outer">
                    <tbody>
                        <tr>
                            <th>
                                <label for="zone_name"><?php 
esc_html_e( 'Name', 'advanced-flat-rate-shipping-for-woocommerce' );
?></label>
                            </th>
                            <td>
                                <input type="text" name="zone_name" id="zone_name" class="input-text"
                                    placeholder="<?php 
echo esc_attr( 'Enter a name which describes this zone', 'advanced-flat-rate-shipping-for-woocommerce' );
?>"
                                    value="<?php 
echo esc_attr( $zone_title );
?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="zone_name"><?php 
esc_html_e( 'Enable', 'advanced-flat-rate-shipping-for-woocommerce' );
?></label>
                            </th>
                            <td>
                                <label><input type="checkbox" name="zone_enabled" value="1" id="zone_enabled" class="input-checkbox" <?php 
checked( $zone_status, 'publish' );
?> /> <?php 
esc_html_e( 'Enable this zone', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th><?php 
esc_html_e( 'Zone Type', 'advanced-flat-rate-shipping-for-woocommerce' );
?></th>
                            <td>
                                <div class="zone_type_options zone_type_countries" id="zone_type_countries">
                                    <label>
                                        <input type="radio" name="zone_type" value="countries" id="zone_type" class="input-radio" <?php 
checked( $zone_type, 'countries' );
?> /> 
                                        <?php 
esc_html_e( 'Country based', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                    </label>
                                    <div class="zone_type_selectbox">
                                        <select multiple="multiple" name="zone_type_countries[]" style="width:450px;" data-placeholder="<?php 
esc_attr_e( 'Choose countries&hellip;', 'advanced-flat-rate-shipping-for-woocommerce' );
?>" class="chosen-select">
                                            <?php 
foreach ( $countries as $key => $val ) {
    echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $country_code, true ) ) . '>' . esc_html( $val ) . '</option>';
}
?>
                                        </select>
                                        <p class="btngrp">
                                            <button class="select_all button"><?php 
esc_html_e( 'All', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="select_none button"><?php 
esc_html_e( 'None', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="button select_africa"><?php 
esc_html_e( 'Africa Country', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="button select_antarctica"><?php 
esc_html_e( 'Antarctica Country', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="button select_asia"><?php 
esc_html_e( 'Asia Country', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="button select_europe"><?php 
esc_html_e( 'EU States', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="button select_northamerica"><?php 
esc_html_e( 'North America', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="button select_oceania"><?php 
esc_html_e( 'Oceania', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="button select_southamerica"><?php 
esc_html_e( 'South America', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                        </p>
                                    </div>
                                </div>
                                <div class="zone_type_options zone_type_states" id="zone_type_states">
                                    <label>
                                        <input type="radio" name="zone_type" value="states" id="zone_type"class="input-radio" <?php 
checked( $zone_type, 'states' );
?> /> 
                                        <?php 
esc_html_e( 'States based', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                    </label>
                                    <div class="zone_type_selectbox">
                                        <select multiple="multiple" name="zone_type_states[]" style="width:450px;" data-placeholder="<?php 
esc_attr_e( 'Choose states/counties&hellip;', 'advanced-flat-rate-shipping-for-woocommerce' );
?>" class="chosen-select wp-enhanced-select">
                                            <?php 
foreach ( $countries as $key => $val ) {
    echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $state_code, true ), true, false ) . '>' . esc_html( $val ) . '</option>';
    $states = WC()->countries->get_states( $key );
    if ( !empty( $states ) ) {
        foreach ( $states as $state_key => $state_value ) {
            echo '<option value="' . esc_attr( $key . ':' . $state_key ) . '" ' . selected( in_array( $key . ':' . $state_key, $state_code, true ), true, false ) . '>' . esc_html( $val . ' &gt; ' . $state_value ) . '</option>';
        }
    }
}
?>
                                        </select>
                                        <p class="btngrp">
                                            <button class="select_all button"><?php 
esc_html_e( 'All', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="select_none button"><?php 
esc_html_e( 'None', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="button select_africa_states"><?php 
esc_html_e( 'Africa States', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="button select_asia_states"><?php 
esc_html_e( 'Asia States', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="button select_europe"><?php 
esc_html_e( 'EU States', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="button select_us_states"><?php 
esc_html_e( 'US States', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                            <button class="button select_oceania_states"><?php 
esc_html_e( 'Oceania States', 'advanced-flat-rate-shipping-for-woocommerce' );
?></button>
                                        </p>
                                    </div>
                                </div>
                                <?php 
?>
                                <div class="zone_type_options zone_type_postcodes" id="zone_type_postcodes">
                                    <label>
                                        <input type="radio" name="zone_type" value="postcodes" id="zone_type" class="input-radio" <?php 
checked( $zone_type, 'postcodes' );
?> /> 
                                        <?php 
esc_html_e( 'Postcodes/zips based', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                    </label>
                                    <div class="zone_type_selectbox">
                                        <select name="zone_type_postcodes" style="width:450px;"
                                                data-placeholder="<?php 
esc_attr_e( 'Choose countries&hellip;', 'advanced-flat-rate-shipping-for-woocommerce' );
?>"
                                                title="Country" class="chosen-select">
                                            <?php 
foreach ( $countries as $key => $val ) {
    echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $postcode_state, true ), true, false ) . '>' . esc_html( $val ) . '</option>';
    $states = WC()->countries->get_states( $key );
    if ( !empty( $states ) ) {
        foreach ( $states as $state_key => $state_value ) {
            echo '<option value="' . esc_attr( $key . ':' . $state_key ) . '" ' . selected( in_array( $key . ':' . $state_key, $postcode_state, true ), true, false ) . '>' . esc_html( $val . ' &gt; ' . $state_value ) . '</option>';
        }
    }
}
?>
                                        </select>

                                        <p>
                                            <label for="postcodes" class="postcodes">
                                                <?php 
esc_html_e( 'Postcodes', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                                <?php 
echo wp_kses( wc_help_tip( esc_html__( 'List 1 postcode per line. Wildcards (*) and ranges (for numeric postcodes) are supported. If you have space in postcode then please add = (equal to) instead of space EX: Postcode - ES2 ABS then You can enter ES2=*', 'advanced-flat-rate-shipping-for-woocommerce' ) ), array(
    'span' => $allowed_tooltip_html,
) );
?>
                                            </label>

                                            <textarea name="postcodes" id="postcodes" class="input-text large-text" cols="25" rows="5" placeholder="<?php 
esc_attr_e( "Postcode 1\nPostcode 2", 'advanced-flat-rate-shipping-for-woocommerce' );
?>"><?php 
if ( !empty( $location_code ) ) {
    foreach ( $location_code as $location ) {
        echo esc_textarea( $location ) . "\n";
    }
}
?></textarea>
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                            <!-- <p class="submit"> -->
                                <input type="submit" class="button button button-primary" name="edit_zone" value="<?php 
esc_attr_e( 'Save changes', 'advanced-flat-rate-shipping-for-woocommerce' );
?>"/>
                                <?php 
wp_nonce_field( 'woocommerce_save_zone', 'woocommerce_save_zone_nonce' );
?>
                            <!-- </p> -->
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </form>
        </div>

    </div>

<?php 
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php';