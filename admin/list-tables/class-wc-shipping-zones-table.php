<?php
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}
	
	/**
	 * WC_Shipping_Zones_Table class.
	 *
	 * @extends WP_List_Table
	 */
	if ( ! class_exists( 'WC_Shipping_Zones_Table' ) ) {
		
		class WC_Shipping_Zones_Table extends WP_List_Table {
			
			public $index = 0;
			private static $active_plugins;
			
			/**
			 * Constructor
			 */
			public function __construct() {
				parent::__construct( array(
					'singular' => 'Shipping Zone',
					'plural'   => 'Shipping Zones',
					'ajax'     => false
				) );
			}
			
			/**
			 * Output the zone name column.
			 *
			 * @param object $item
			 *
			 * @return string
			 */
			public function column_zone_name( $item ) {
				$editurl = esc_url( add_query_arg( 'edit_zone', $item->ID, admin_url( 'admin.php?page=afrsm-wc-shipping-zones' ) ) );
				$editurl = str_replace( '#038;', '&', $editurl );
				
				$delurl    = esc_url( add_query_arg( 'delete_zone', $item->ID, admin_url( 'admin.php?page=afrsm-wc-shipping-zones' ) ) );
				$delurl    = str_replace( '#038;', '&', $delurl );
				$zone_name = '<strong>
                            <a href="' . wp_nonce_url( $editurl, 'edit_' . $item->ID, 'cust_nonce' ) . '" class="configure_methods">' . esc_html( $item->post_title ) . '</a>
                        </strong>
                        <input type="hidden" class="zone_id" name="zone_id[]" value="' . esc_attr( $item->ID ) . '" />
                        <div class="row-actions">';
				
				if ( $item->ID > 0 ) {
					$zone_name .= '<span class="edit"><a href="' . wp_nonce_url( $editurl, 'edit_' . $item->ID, 'cust_nonce' ) . '">' . __( 'Edit', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</a></span>';
				}
				if ( $item->ID > 0 ) {
					$zone_name .= '&nbsp;|&nbsp;<span class="delete"><a href="' . wp_nonce_url( $delurl, 'del_' . $item->ID, 'cust_nonce' ) . '">' . __( 'Delete', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</a></span>';
				}
				$zone_name .= '</div>';
				
				return $zone_name;
			}
			
			/**
			 * Output the zone id column.
			 *
			 * @param object $item
			 *
			 * @return string
			 */
			public function column_zone_id( $item ) {
				$zone_id = $item->ID;
				
				return $zone_id;
			}
			
			/**
			 * Output the zone type column.
			 *
			 * @param object $item
			 *
			 * @return string
			 */
			public function column_zone_type( $item ) {
				if ( 0 === $item->ID ) {
					return esc_html__( 'Everywhere', 'advanced-flat-rate-shipping-for-woocommerce' );
				}
				
				$postcode_state = array();
				$postcode_list  = array();
				
				$locations_prepend = "";
				$locations_append  = "";
				$locations_list    = array();
                $how_much_show     = 8;
				
				$location_type = get_post_meta( $item->ID, 'location_type', true );
				$zone_type     = get_post_meta( $item->ID, 'zone_type', true );
				
				if ( 'postcode' !== $location_type ) {
					$location_code_arr_new = get_post_meta( $item->ID, 'location_code', true );
					$location_code_arr     = array();
					if ( ! empty( $location_code_arr_new ) ) {
						foreach ( $location_code_arr_new as $location_code_key => $location_code_arr_val ) {
							$count = count( $location_code_arr_val );
							foreach ( $location_code_arr_val as $location_code_arr_val_key => $location_code_sub_arr_val ) {
								if ( $location_code_arr_val_key >= $how_much_show ) {
									$locations_append = ' ' . sprintf( esc_html__( 'and %s others', 'advanced-flat-rate-shipping-for-woocommerce' ), ( $count - $how_much_show ) );
									break;
								}
								$location_code_arr[] = $location_code_sub_arr_val;
							}
						}
					}
				} else {
					$postcode_code_arr = get_post_meta( $item->ID, 'location_code', true );
					
					if ( ! empty( $postcode_code_arr ) ) {
						foreach ( $postcode_code_arr as $location_code_key => $location_code_val ) {
							$postcode_state[]  = $location_code_key;
							$postcode_list_arr = $location_code_val;
						}
					}
					
					$count = count( $postcode_list_arr );
					
					foreach ( $postcode_list_arr as $postcode_list_arr_key => $postcode_list_arr_key_val ) {
						if ( $postcode_list_arr_key >= $how_much_show ) {
							$locations_append = ' ' . sprintf( esc_html__( 'and %s others', 'advanced-flat-rate-shipping-for-woocommerce' ), ( $count - $how_much_show ) );
							break;
						}
						$postcode_list[] = $postcode_list_arr_key_val;
					}
				}
			
				switch ( $location_type ) {
					case "country" :
					case "state" :
						foreach ( $location_code_arr as $location_code_key => $location_code ) {
							if ( strstr( $location_code, ':' ) ) {
								$split_code = explode( ':', $location_code );
								if ( ! isset( WC()->countries->states[ $split_code[0] ][ $split_code[1] ] ) ) {
									continue;
								}
								$location_name = WC()->countries->states[ $split_code[0] ][ $split_code[1] ];
							} else {
								if ( ! isset( WC()->countries->countries[ $location_code ] ) ) {
									continue;
								}
								$location_name = WC()->countries->countries[ $location_code ];
							}
							
							$locations_list[] = $location_name;
						}
						break;
                    case "city" :
                        foreach ( $location_code_arr as $location_name ) {
                            $locations_list[] = $location_name;
                        }
                        break;
					case "postcode" :
						if ( strstr( $postcode_state[0], ':' ) ) {
							$split_code = explode( ':', $postcode_state[0] );
							if ( ! isset( WC()->countries->states[ $split_code[0] ][ $split_code[1] ] ) ) {
								break;
							}
							$location_name = WC()->countries->states[ $split_code[0] ][ $split_code[1] ];
						} else {
							if ( ! isset( WC()->countries->countries[ $postcode_state[0] ] ) ) {
								break;
							}
							$location_name = WC()->countries->countries[ $postcode_state[0] ];
						}
						
						$locations_prepend = sprintf( esc_html__( 'Within %s:', 'advanced-flat-rate-shipping-for-woocommerce' ), $location_name ) . ' ';
						$locations_list    = $postcode_list;
				}
				
				switch ( $zone_type ) {
					case "countries" :
						return '<strong>' . esc_html__( 'Countries', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</strong><br/>' . $locations_prepend . implode( ', ', $locations_list ) . $locations_append;
					case "states" :
						return '<strong>' . esc_html__( 'Countries and states', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</strong><br/>' . $locations_prepend . implode( ', ', $locations_list ) . $locations_append;
					case "postcodes" :
						return '<strong>' . esc_html__( 'Postcodes', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</strong><br/>' . $locations_prepend . implode( ', ', $locations_list ) . $locations_append;
                    case "cities" :
                        return '<strong>' . esc_html__( 'Cities', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</strong><br/>' . $locations_prepend . implode( ', ', $locations_list ) . $locations_append;
				}
			}
			
			/**
			 * Output the zone enabled column.
			 *
			 * @param object $item
			 *
			 * @return string
			 */
			public function column_enabled( $item ) {
				if ( 'publish' === $item->post_status ) {
					return '&#10004;';
				} else {
					return '&ndash;';
				}
				
			}
			
			/**
			 * Checkbox column
			 *
			 * @param string
			 *
			 * @return mixed
			 */
			public function column_cb( $item ) {
				if ( ! $item->ID ) {
					return;
				}
				
				return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', 'zone_id_cb', esc_attr( $item->ID ) );
			}
			
			/**
			 * get_columns function.
			 * @return  array
			 */
			public function get_columns() {
				return array(
					'cb'        => '<input type="checkbox" />',
					'zone_id'   => esc_html__( 'ID', 'advanced-flat-rate-shipping-for-woocommerce' ),
					'zone_name' => esc_html__( 'Name', 'advanced-flat-rate-shipping-for-woocommerce' ),
					'zone_type' => esc_html__( 'Type', 'advanced-flat-rate-shipping-for-woocommerce' ),
					'enabled'   => esc_html__( 'Enabled', 'advanced-flat-rate-shipping-for-woocommerce' )
				);
			}
			
			/**
			 * Get bulk actions
			 */
			public function get_bulk_actions() {
				$actions = array(
					'disable' => esc_html__( 'Disable', 'advanced-flat-rate-shipping-for-woocommerce' ),
					'enable'  => esc_html__( 'Enable', 'advanced-flat-rate-shipping-for-woocommerce' ),
					'delete'  => esc_html__( 'Delete', 'advanced-flat-rate-shipping-for-woocommerce' )
				);
				
				return $actions;
			}
			
			/**
			 * Process bulk actions
			 */
			public function process_bulk_action() {
				if ( ! isset( $_POST['zone_id_cb'] ) ) {
					return;
				}
				
				$delete_nonce = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
				$deletenonce  = wp_verify_nonce( $delete_nonce, 'bulk-shippingzones' );
				
				if ( ! isset( $deletenonce ) && 1 !== $deletenonce ) {
					return;
				}
				
				$items = array_filter( array_map( 'absint', $_POST['zone_id_cb'] ) );
				
				if ( ! $items ) {
					return;
				}
				
				if ( 'delete' === $this->current_action() ) {
					
					foreach ( $items as $id ) {
						wp_delete_post( $id );
					}
					
					echo '<div class="updated success"><p>' . esc_html__( 'Shipping zones deleted', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</p></div>';
				} elseif ( 'enable' === $this->current_action() ) {
					
					foreach ( $items as $id ) {
						$enable_post = array(
							'post_type'   => 'wc_afrsm_zone',
							'ID'          => $id,
							'post_status' => 'publish'
						);
						
						wp_update_post( $enable_post );
					}
					
					echo '<div class="updated success"><p>' . esc_html__( 'Shipping zones enabled', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</p></div>';
				} elseif ( 'disable' === $this->current_action() ) {
					
					foreach ( $items as $id ) {
						$disable_post = array(
							'post_type'   => 'wc_afrsm_zone',
							'ID'          => $id,
							'post_status' => 'draft'
						);
						
						wp_update_post( $disable_post );
					}
					
					echo '<div class="updated success"><p>' . esc_html__( 'Shipping zones disabled', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</p></div>';
				}
			}
			
			/**
			 * Get Zones to display
			 */
			public function prepare_items() {
				$this->_column_headers = array( $this->get_columns(), array(), array() );
				$this->process_bulk_action();
				$zone_args  = array(
					'post_type'      => 'wc_afrsm_zone',
					'post_status'    => array( 'publish', 'draft' ),
					'posts_per_page' => - 1,
					'orderby'        => 'ID',
					'order'          => 'DESC',
				);
				$zone_query = new WP_Query( $zone_args );
				$zone_list  = $zone_query->posts;
				
				$this->items = $zone_list;
			}
			
		}
	}