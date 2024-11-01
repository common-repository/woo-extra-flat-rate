<?php
// If this file is called directly, abort.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	/**
	 * AFRSM_Shipping_Zone class.
	 */
	if ( ! class_exists( 'AFRSM_Shipping_Zone' ) ) {
		
		class AFRSM_Shipping_Zone {
			
			/**
			 * Output the Admin UI
			 */
			private static $active_plugins;
			
			/**
			 * Display output
			 *
			 * @since    1.0.0
			 *
			 * @uses edit_zone_screen
			 * @uses save_zone
			 * @uses add_shipping_zone_form
			 * @uses delete_zone
			 * @uses list_zones_screen
			 *
			 * @access   public
			 */
			public static function afrsm_pro_sz_output() {
				$add_zone    = filter_input( INPUT_GET, 'add_zone', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
				$edit_zone   = filter_input( INPUT_GET, 'edit_zone', FILTER_SANITIZE_NUMBER_INT );
				$delete_zone = filter_input( INPUT_GET, 'delete_zone', FILTER_SANITIZE_NUMBER_INT );
				$cust_nonce  = filter_input( INPUT_GET, 'cust_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
				
				if ( isset( $edit_zone ) && ! empty( $edit_zone ) ) {
					if ( isset( $cust_nonce ) && ! empty( $cust_nonce ) ) {
						$getnonce = wp_verify_nonce( $cust_nonce, 'edit_' . $edit_zone );
						if ( isset( $getnonce ) && 1 === $getnonce ) {
							self::afrsm_pro_sz_edit_zone_screen();
						} else {
							wp_safe_redirect( esc_url( add_query_arg( array( 'page' => 'afrsm-wc-shipping-zones' ), admin_url( 'admin.php' ) ) ) );
							exit;
						}
					}
				} elseif ( isset( $add_zone ) ) {
					self::afrsm_pro_sz_save_zone();
					self::afrsm_pro_sz_add_shipping_zone_form();
				} else if ( ! empty( $delete_zone ) ) {
					self::afrsm_pro_sz_delete_zone( $delete_zone );
				} else {
					self::afrsm_pro_sz_list_zones_screen();
				}
			}
			
			/**
			 * Delete zone
			 *
			 * @param int $id
			 *
			 * @access   public
			 * @since    1.0.0
			 *
			 */
			public static function afrsm_pro_sz_delete_zone( $id ) {
				$cust_nonce = filter_input( INPUT_GET, 'cust_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
				$getnonce   = wp_verify_nonce( $cust_nonce, 'del_' . $id );
				if ( isset( $getnonce ) && 1 === $getnonce ) {
					wp_delete_post( $id );
					wp_safe_redirect( esc_url( add_query_arg( array( 'page' => 'afrsm-wc-shipping-zones' ), admin_url( 'admin.php' ) ) ) );
					exit;
				} else {
					wp_safe_redirect( esc_url( add_query_arg( array( 'page' => 'afrsm-wc-shipping-zones' ), admin_url( 'admin.php' ) ) ) );
					exit;
				}
			}
			
			/**
			 * Count total zone
			 *
			 * @return int $count_zone
			 * @uses get_posts()
			 *
			 * @since    1.0.0
			 *
			 */
			public static function afrsm_pro_sz_count_zone() {
				$zone_args       = array(
					'post_type'      => 'wc_afrsm_zone',
					'post_status'    => array( 'publish', 'draft' ),
					'posts_per_page' => - 1,
					'orderby'        => 'ID',
					'order'          => 'DESC',
				);
				$zone_post_query = new WP_Query( $zone_args );
				$zone_list       = $zone_post_query->posts;
				
				return count( $zone_list );
			}
			
			/**
			 * Save zone when add or edit
			 *
			 * @param int $zone_id
			 *
			 * @return bool false when nonce is not verified, $zone id, $zone_type is blank, Country also blank, Postcode field also blank, saving error when form submit
			 * @since    1.0.0
			 *
			 * @uses afrsm_pro_sz_count_zone()
			 *
			 */
			private static function afrsm_pro_sz_save_zone( $zone_id = 0 ) {
				if ( ! empty( $_POST['add_zone'] ) || ! empty( $_POST['edit_zone'] ) ) {
					
					if ( empty( $_POST['woocommerce_save_zone_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['woocommerce_save_zone_nonce'] ), 'woocommerce_save_zone' ) ) {
						echo '<div class="updated error"><p>' . esc_html__( 'Could not save zone. Please try again.', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</p></div>';
						
						return false;
					}
					
					$fields = array(
						'zone_name',
						'zone_type',
						'zone_enabled',
						'zone_type_countries',
						'zone_type_states',
						'zone_type_cities',
						'cities',
						'zone_type_postcodes',
						'postcodes'
					);
					
					$zone_count = self::afrsm_pro_sz_count_zone();
					$data       = array();
					settype( $zone_id, 'integer' );
                    
					foreach ( $fields as $field ) {
						if ( ! empty( $_POST[ $field ] ) ) {
							if ( is_array( $_POST[ $field ] ) ) {
								if('cities' === $field){
									$data[ $field ] = array_map( 'sanitize_text_field', $_POST[ $field ] );
								}else{
									$data[ $field ] = array_map( 'sanitize_text_field', $_POST[ $field ] );
								}
							} else {
								if('cities' === $field){
                                    $citystr = sanitize_text_field( $_POST[ $field ] );
                                    $data[ $field ] = explode( ' ', $citystr );
								}else{
									$data[ $field ] = sanitize_text_field( $_POST[ $field ] );
								}
							}
						} else {
							$data[ $field ] = '';
						}
						if ('postcodes' === $field) {
                            $data[ $field ] = array_map( 'strtoupper', array_map( 'wc_clean', explode( " ", $data[ $field ] ) ) );
                        } else {
                            $data[ $field ] = is_array( $data[ $field ] ) ? array_map( 'wc_clean', $data[ $field ] ) : wc_clean( $data[ $field ] );
                        }
					}
                    
					
					// If name is left blank...
					if ( empty( $data['zone_name'] ) ) {
						if ( "" !== $zone_id && 0 !== $zone_id ) {
							echo '<div class="updated error"><p>' . esc_html__( 'Zone name is required', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</p></div>';
							
							return false;
						} else {
							$data['zone_name'] = esc_html__( 'Zone', 'advanced-flat-rate-shipping-for-woocommerce' ) . ' ' . ( $zone_count + 1 );
						}
					}
					
					// Check required fields
					if ( empty( $data['zone_type'] ) ) {
						echo '<div class="updated error"><p>' . esc_html__( ' Zone type is required', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</p></div>';
						
						return false;
					}
					
					if ( "" !== $zone_id && 0 !== $zone_id ) {
						$data['zone_enabled'] = $data['zone_enabled'] ? 'publish' : 'draft';
					} else {
						$data['zone_enabled'] = 'publish';
					}
					
					// Determine field we are saving
					$locations_field = 'zone_type_' . $data['zone_type'];
					
					// Get the countries into a nicely formatted array
					if ( ! $data[ $locations_field ] ) {
						$data[ $locations_field ] = array();
					}
					
					if ( is_array( $data[ $locations_field ] ) ) {
						$data[ $locations_field ] = array_filter( array_map( 'strtoupper', array_map( 'sanitize_text_field', $data[ $locations_field ] ) ) );
					} else {
						$data[ $locations_field ] = array( strtoupper( sanitize_text_field( $data[ $locations_field ] ) ) );
					}
					
					// Any set?
					if ( 0 === sizeof( $data[ $locations_field ] ) ) {
						echo '<div class="updated error"><p>' . esc_html__( 'You must choose at least 1 country to add a zone.', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</p></div>';
						
						return false;
					}
					
					// If dealing with a city, grab that field too
					if ( 'cities' === $data['zone_type'] ) {
						$data['cities'] = array_filter( array_unique( $data['cities'] ) );

						if ( 0 === sizeof( $data['cities'] ) ) {
							echo '<div class="updated error"><p>' . esc_html__( 'You must choose at least 1 city to add city zone.', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</p></div>';
							
							return false;
						}
					} else {
						$data['cities'] = array();
					}

					// If dealing with a postcode, grab that field too
					if ( 'postcodes' === $data['zone_type'] ) {
						
						$data['postcodes'] = array_filter( array_unique( $data['postcodes'] ) );
						
						if ( 0 === sizeof( $data['postcodes'] ) ) {
							echo '<div class="updated error"><p>' . esc_html__( 'You must choose at least 1 postcode to add postcode zone.', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</p></div>';
							
							return false;
						}
					} else {
						$data['postcodes'] = array();
					}
					
					
					if ( "" !== $zone_id && 0 !== $zone_id ) {
						$zone_post = array(
							'ID'          => $zone_id,
							'post_title'  => $data['zone_name'],
							'post_status' => $data['zone_enabled'],
							'menu_order'  => $zone_count + 1,
							'post_type'   => 'wc_afrsm_zone',
						);
						$zone_id   = wp_update_post( $zone_post );
					} else {
						$zone_post = array(
							'post_title'  => $data['zone_name'],
							'post_status' => $data['zone_enabled'],
							'menu_order'  => $zone_count + 1,
							'post_type'   => 'wc_afrsm_zone',
						);
						$zone_id   = wp_insert_post( $zone_post );
					}
					
					if ( "" !== $zone_id && 0 !== $zone_id ) {
						if ( $zone_id > 0 ) {
							// Save postcodes
							$location_code = array();
							$location_type = '';
							if ( 'postcodes' === $data['zone_type'] ) {
								$location_code[ $data['zone_type_postcodes'][0] ] = $data['postcodes'];
								$location_type                                    = 'postcode';
							} elseif ( 'cities' === $data['zone_type'] ) {
								$location_code[ $data['zone_type_cities'][0] ] = $data['cities'];
								$location_type                                    = 'city';	
							} elseif ( 'countries' === $data['zone_type'] ) {
								$location_type   = 'country';
								$location_code[] = $data['zone_type_countries'];
							} else {
								$location_type   = 'state';
								$location_code[] = $data['zone_type_states'];
							}
							update_post_meta( $zone_id, 'location_type', $location_type );
							update_post_meta( $zone_id, 'zone_type', $data['zone_type'] );
							update_post_meta( $zone_id, 'location_code', $location_code );
						} else {
							echo '<div class="updated error"><p>' . esc_html__( 'Error saving zone.', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</p></div>';
							
							return false;
						}
					}
					
					if ( "" !== $zone_id && 0 !== $zone_id ) {
						echo '<div class="updated fade"><p>' . sprintf( 'Shipping zone saved.<a href="%s">%s</a>', esc_url( remove_query_arg( 'edit_zone' ) ), esc_html__( 'Back to zones.', 'advanced-flat-rate-shipping-for-woocommerce' ) ) . '</p></div>';
					} else {
						echo '<div class="updated fade"><p>' . esc_html__( 'Shipping zone saved.', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</p></div>';
					}
				}
			}
			
			/**
			 * Edit zone screen
			 *
			 * @since    1.0.0
			 *
			 * @uses afrsm_pro_sz_save_zone()
			 * @uses edit_zone
			 *
			 */
			public static function afrsm_pro_sz_edit_zone_screen() {
				$edit_zone = filter_input( INPUT_GET, 'edit_zone', FILTER_SANITIZE_NUMBER_INT );
				$zone_id   = isset( $edit_zone ) ? $edit_zone : '';
				self::afrsm_pro_sz_save_zone( $zone_id );
				self::afrsm_pro_sz_edit_zone();
			}
			
			/**
			 * Save zone and display list of zone
			 *
			 * @since    1.0.0
			 *
			 * @uses afrsm_pro_sz_save_zone()
			 *
			 */
			public static function afrsm_pro_sz_list_zones_screen() {
				self::afrsm_pro_sz_save_zone();
				include( plugin_dir_path( __FILE__ ) . 'html-zone-list.php' );
			}
			
			/**
			 * Edit zone
			 *
			 * @param int $zone_id
			 *
			 * @since    1.0.0
			 *
			 * @uses WC_Countries::get_allowed_countries()
			 * @uses WC_Countries::get_base_country()
			 * @uses get_post()
			 */
			private static function afrsm_pro_sz_edit_zone() {
				include( plugin_dir_path( __FILE__ ) . 'form-edit-shipping-zone.php' );
			}
			
			/**
			 * list_shipping_zones function.
			 *
			 * @since    1.0.0
			 *
			 * @uses WC_Shipping_Zones_Table class
			 * @uses WC_Shipping_Zones_Table::prepare_items()
			 * @uses WC_Shipping_Zones_Table::display()
			 *
			 * @access public
			 *
			 */
			public static function afrsm_pro_sz_list_shipping_zones() {
				if ( ! class_exists( 'WC_Shipping_Zones_Table' ) ) {
					require_once plugin_dir_path( dirname( __FILE__ ) ) . 'list-tables/class-wc-shipping-zones-table.php';
				}
				echo '<form method="post">';?>
                <h1><?php esc_html_e( 'Shipping Zones', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h1>
                <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'afrsm-wc-shipping-zones&add_zone' ), admin_url( 'admin.php' ) ) ); ?>" class="page-title-action dots-btn-with-brand-color">
					<?php esc_html_e( 'Add Zone', 'advanced-flat-rate-shipping-for-woocommerce' ); ?>
                </a>
                <?php 
				$WC_Shipping_Zones_Table = new WC_Shipping_Zones_Table();
				$WC_Shipping_Zones_Table->prepare_items();
				$WC_Shipping_Zones_Table->display();
				echo '</form>';
			}
			
			/**
			 * add_shipping_zone_form function.
			 *
			 * @since    1.0.0
			 *
			 * @uses WC_Countries::get_allowed_countries()
			 * @uses WC_Countries::get_base_country()
			 */
			public static function afrsm_pro_sz_add_shipping_zone_form() {
				include( plugin_dir_path( __FILE__ ) . 'form-add-shipping-zone.php' );
			}
			
		}
		
	}
