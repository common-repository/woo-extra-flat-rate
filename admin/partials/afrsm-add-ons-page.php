<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' );
	
    $slug = afrsfw_fs()->get_slug();

	$open_addon_slug = fs_request_get( 'slug' );

	$open_addon = false;

    $is_data_debug_mode = afrsfw_fs()->is_data_debug_mode();
    $is_whitelabeled    = afrsfw_fs()->is_whitelabeled();

    $addons = afrsfw_fs()->get_addons();
    $has_addons = ( is_array( $addons ) && 0 < count( $addons ) );

    $account_addon_ids = afrsfw_fs()->get_updated_account_addons();

    $download_latest_text = fs_text_x_inline( 'Download Latest', 'as download latest version', 'download-latest', $slug );
    $view_details_text    = fs_text_inline( 'View details', 'view-details', $slug );
    
    $fs_blog_id = ( is_multisite() && ! is_network_admin() ) ? get_current_blog_id() : 0;
    //phpcs:disable
?>
<div class="afrsm-section-left">
    <div class="afrsm-main-table res-cl element-shadow">
        <h2><?php echo sprintf( '%1$s %2$s', esc_html__( 'Add Ons for', 'advanced-flat-rate-shipping-for-woocommerce' ), esc_html(afrsfw_fs()->get_plugin_name()) );?></h2>
        <div id="fs_addons" class="wrap fs-section">
            <div id="poststuff">
                <?php if ( ! $has_addons ) : ?>
                    <h3><?php echo esc_html( sprintf(
                            '%s... %s',
                            fs_text_x_inline( 'Oops', 'exclamation', 'oops', $slug ),
                            fs_text_inline( 'We couldn\'t load the add-ons list. It\'s probably an issue on our side, please try to come back in few minutes.', 'add-ons-missing', $slug )
                        ) ) ?></h3>
                <?php endif ?>
                <div class="fs-cards-list">
                    <?php if ( $has_addons ) : ?>
                        <?php
                        $plans_and_pricing_by_addon_id = afrsfw_fs()->_get_addons_plans_and_pricing_map_by_id();

                        $active_plugins_directories_map = Freemius::get_active_plugins_directories_map( $fs_blog_id );
                        ?>
                        <?php
                            $hide_all_addons_data = false;

                            if ( afrsfw_fs()->is_whitelabeled_by_flag() ) {
                                $hide_all_addons_data = true;

                                $addon_ids        = afrsfw_fs()->get_updated_account_addons();
                                $installed_addons = afrsfw_fs()->get_installed_addons();
                                foreach ( $installed_addons as $fs_addon ) {
                                    $addon_ids[] = $fs_addon->get_id();
                                }

                                if ( ! empty( $addon_ids ) ) {
                                    $addon_ids = array_unique( $addon_ids );
                                }

                                foreach ( $addon_ids as $addon_id ) {
                                    $addon = afrsfw_fs()->get_addon( $addon_id );

                                    if ( ! is_object( $addon ) ) {
                                        continue;
                                    }

                                    $addon_storage = FS_Storage::instance( WP_FS__MODULE_TYPE_PLUGIN, $addon->slug );

                                    if ( ! $addon_storage->is_whitelabeled ) {
                                        $hide_all_addons_data = false;
                                        break;
                                    }

                                    if ( $is_data_debug_mode ) {
                                        $is_whitelabeled = false;
                                    }
                                }
                            }
                        ?>
                        <?php foreach ( $addons as $addon ) : ?>
                            <?php
                            $basename = afrsfw_fs()->get_addon_basename( $addon->id );

                            $is_addon_installed = file_exists( fs_normalize_path( WP_PLUGIN_DIR . '/' . $basename ) );

                            if ( ! $is_addon_installed && $hide_all_addons_data ) {
                                continue;
                            }

                            $is_addon_activated = $is_addon_installed ?
                                afrsfw_fs()->is_addon_activated( $addon->id ) :
                                false;

                            $is_plugin_active = (
                                $is_addon_activated ||
                                isset( $active_plugins_directories_map[ dirname( $basename ) ] )
                            );

                            $open_addon = ( $open_addon || ( $open_addon_slug === $addon->slug ) );

                            $price     = 0;
                            $has_trial = false;
                            $has_free_plan = false;
                            $has_paid_plan = false;

                            if ( isset( $plans_and_pricing_by_addon_id[$addon->id] ) ) {
                                $plans = $plans_and_pricing_by_addon_id[$addon->id];

                                if ( is_array( $plans ) && 0 < count( $plans ) ) {
                                    foreach ( $plans as $plan ) {
                                        if ( ! isset( $plan->pricing ) ||
                                            ! is_array( $plan->pricing ) ||
                                            0 === count( $plan->pricing )
                                        ) {
                                            // No pricing means a free plan.
                                            $has_free_plan = true;
                                            continue;
                                        }


                                        $has_paid_plan = true;
                                        $has_trial     = $has_trial || ( is_numeric( $plan->trial_period ) && ( $plan->trial_period > 0 ) );

                                        $min_price = 999999;
                                        foreach ( $plan->pricing as $pricing ) {
                                            $pricing = new FS_Pricing( $pricing );

                                            if ( ! $pricing->is_usd() ) {
                                                /**
                                                 * Skip non-USD pricing.
                                                 *
                                                 * @author Leo Fajardo (@leorw)
                                                 * @since 2.3.1
                                                 */
                                                continue;
                                            }

                                            if ( $pricing->has_annual() ) {
                                                $min_price = min( $min_price, $pricing->annual_price );
                                            } else if ( $pricing->has_monthly() ) {
                                                $min_price = min( $min_price, 12 * $pricing->monthly_price );
                                            }
                                        }

                                        if ( $min_price < 999999 ) {
                                            $price = $min_price;
                                        }

                                    }
                                }

                                if ( ! $has_paid_plan && ! $has_free_plan ) {
                                    continue;
                                }
                            }
                            ?>
                            <div class="fs-card fs-addon" data-slug="<?php echo esc_attr($addon->slug); ?>">
                                <?php
                                    $view_details_link = sprintf( '<a href="%s" aria-label="%s" data-title="%s"',
                                        esc_url( network_admin_url( 'plugin-install.php?fs_allow_updater_and_dialog=true' . ( ! empty( $fs_blog_id ) ? '&fs_blog_id=' . $fs_blog_id : '' ) . '&tab=plugin-information&parent_plugin_id=' . afrsfw_fs()->get_id() . '&plugin=' . $addon->slug .
                                                                    '&TB_iframe=true&width=600&height=550' ) ),
                                        esc_attr( sprintf( fs_text_inline( 'More information about %s', 'more-information-about-x', $slug ), $addon->title ) ),
                                        esc_attr( $addon->title )
                                    ) . ' class="thickbox%s">%s</a>';

                                    echo sprintf(
                                        $view_details_link,
                                        /**
                                         * Additional class.
                                         *
                                         * @author Leo Fajardo (@leorw)
                                         * @since 2.2.4
                                         */
                                        ' fs-overlay',
                                        /**
                                         * Set the view details link text to an empty string since it is an overlay that
                                         * doesn't really need a text and whose purpose is to open the details dialog when
                                         * the card is clicked.
                                         *
                                         * @author Leo Fajardo (@leorw)
                                         * @since 2.2.4
                                         */
                                        ''
                                    );
                                ?>
                                <?php
                                    if ( is_null( $addon->info ) ) {
                                        $addon->info = new stdClass();
                                    }
                                    if ( ! isset( $addon->info->banner_url ) ) {
                                        $addon->info->banner_url = '//dashboard.freemius.com/assets/img/marketing/blueprint-300x100.jpg';
                                    }
                                    if ( ! isset( $addon->info->short_description ) ) {
                                        $addon->info->short_description = 'What\'s the one thing your add-on does really, really well?';
                                    }
                                ?>
                                <div class="fs-inner">
                                    <!-- <div class="fs-card-banner" style="background-image: url('<?php echo $addon->info->banner_url ?>');"> -->
                                    <div class="fs-card-banner">
                                        <img src="<?php echo $addon->info->banner_url ?>" />
                                        <?php
                                            if ( $is_plugin_active || $is_addon_installed ) {
                                                echo sprintf(
                                                    '<span class="fs-badge fs-installed-addon-badge">%s</span>',
                                                    esc_html( $is_plugin_active ?
                                                        fs_text_x_inline( 'Active', 'active add-on', 'active-addon', $slug ) :
                                                        fs_text_x_inline( 'Installed', 'installed add-on', 'installed-addon', $slug )
                                                    )
                                                );
                                            }
                                        ?>
                                    </div>
                                    <div class="fs-card-details">
                                        <div class="asfrm-card-title-prce-wrap">
                                            <h3 class="fs-title"><?php echo $addon->title ?></h3>
                                            <h4 class="fs-offer">
                                                <span class="fs-price">
                                                    <?php
                                                    if ( $is_whitelabeled ) {
                                                        echo '&nbsp;';
                                                    } else {
                                                        $descriptors = array();

                                                        if ($has_free_plan)
                                                            $descriptors[] = fs_text_inline( 'Free', 'free', $slug );
                                                        if ($has_paid_plan && $price > 0)
                                                            $descriptors[] = '$' . number_format( $price, 2 );
                                                        if ($has_trial)
                                                            $descriptors[] = fs_text_x_inline( 'Trial', 'trial period',  'trial', $slug );

                                                        echo implode(' - ', $descriptors);

                                                    } ?>
                                                </span>
                                            </h4>
                                        </div>
                                        <p class="fs-description">
                                            <?php echo ! empty( $addon->info->short_description ) ? $addon->info->short_description : 'SHORT DESCRIPTION' ?>
                                        </p>
                                        <?php
                                            $is_free_only_wp_org_compliant = ( ! $has_paid_plan && $addon->is_wp_org_compliant );

                                            $is_allowed_to_install = (
                                                afrsfw_fs()->is_allowed_to_install() ||
                                                $is_free_only_wp_org_compliant
                                            );

                                            $show_premium_activation_or_installation_action = true;

                                            if ( ! in_array( $addon->id, $account_addon_ids, true ) ) {
                                                $show_premium_activation_or_installation_action = false;
                                            } else if ( $is_addon_installed ) {
                                                /**
                                                 * If any add-on's version (free or premium) is installed, check if the
                                                 * premium version can be activated and show the relevant action. Otherwise,
                                                 * show the relevant action for the free version.
                                                 *
                                                 * @author Leo Fajardo (@leorw)
                                                 * @since 2.4.5
                                                 */
                                                $fs_addon = $is_addon_activated ?
                                                    afrsfw_fs()->get_addon_instance( $addon->id ) :
                                                    null;

                                                $premium_plugin_basename = is_object( $fs_addon ) ?
                                                    $fs_addon->premium_plugin_basename() :
                                                    "{$addon->premium_slug}/{$addon->slug}.php";

                                                if (
                                                    ( $is_addon_activated && $fs_addon->is_premium() ) ||
                                                    file_exists( fs_normalize_path( WP_PLUGIN_DIR . '/' . $premium_plugin_basename ) )
                                                ) {
                                                    $basename = $premium_plugin_basename;
                                                }

                                                $show_premium_activation_or_installation_action = (
                                                    ( ! $is_addon_activated || ! $fs_addon->is_premium() ) &&
                                                    /**
                                                     * This check is needed for cases when an active add-on doesn't have an
                                                     * associated Freemius instance.
                                                     *
                                                     * @author Leo Fajardo (@leorw)
                                                     * @since 2.4.5
                                                     */
                                                    ( ! $is_plugin_active )
                                                );
                                            }
                                        ?>
                                        <?php if ( ! $show_premium_activation_or_installation_action ) : ?>
                                            <p class="fs-cta">
                                                <a class="button button-primary button-large">
                                                    <?php echo esc_html( $view_details_text ) ?>
                                                </a>
                                            </p>
                                        <?php else : ?>
                                            <?php
                                                $latest_download_local_url = $is_free_only_wp_org_compliant ?
                                                    null :
                                                    afrsfw_fs()->_get_latest_download_local_url( $addon->id );
                                            ?>

                                            <div class="fs-cta fs-dropdown">
                                                <div class="button-group">
                                                    <?php if ( $is_allowed_to_install ) : ?>
                                                    <?php
                                                        if ( ! $is_addon_installed ) {
                                                            echo sprintf(
                                                                '<a class="button button-primary" href="%s">%s</a>',
                                                                wp_nonce_url( self_admin_url( 'update.php?' . ( ( $has_paid_plan || ! $addon->is_wp_org_compliant ) ? 'fs_allow_updater_and_dialog=true&' : '' ) . 'action=install-plugin&plugin=' . $addon->slug ), 'install-plugin_' . $addon->slug ),
                                                                fs_esc_html_inline( 'Install Now', 'install-now', $slug )
                                                            );
                                                        } else {
                                                            echo sprintf(
                                                                '<a class="button button-primary edit" href="%s" title="%s" target="_parent">%s</a>',
                                                                wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $basename, 'activate-plugin_' . $basename ),
                                                                fs_esc_attr_inline( 'Activate this add-on', 'activate-this-addon', $addon->slug ),
                                                                fs_text_inline( 'Activate', 'activate', $addon->slug )
                                                            );
                                                        }
                                                    ?>
                                                    <?php else : ?>
                                                        <a target="_blank" rel="noopener" class="button button-primary" href="<?php echo $latest_download_local_url ?>"><?php echo esc_html( $download_latest_text ) ?></a>
                                                    <?php endif ?>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php //phpcs:enable
require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php' ); ?>