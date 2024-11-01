<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

////////////////////////
////// New Layout //////
////////////////////////

/**
 * AFRSM_Rule_Listing_Page class.
 */
if ( ! class_exists( 'AFRSM_Rule_Listing_Page' ) ) {

	class AFRSM_Rule_Listing_Page {

		/**
		 * Output the Admin UI
		 *
		 * @since 3.5
		 */
		const post_type = 'wc_afrsm';
		private static $admin_object = null;

		/**
		 * Display output
		 *
		 * @since 3.5
		 *
		 * @uses Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin
		 * @uses afrsm_sj_save_method
		 * @uses afrsm_sj_add_shipping_method_form
		 * @uses afrsm_sj_edit_method_screen
		 * @uses afrsm_sj_delete_method
		 * @uses afrsm_sj_duplicate_method
		 * @uses afrsm_sj_list_methods_screen
		 * @uses Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin::afrsm_updated_message()
		 *
		 * @access   public
		 */
		public static function afrsm_sj_output() {
			self::$admin_object = new Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin( '', '' );
			$action             = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$post_id_request    = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
			$cust_nonce         = filter_input( INPUT_GET, 'cust_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$get_afrsm_add      = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$message = filter_input( INPUT_GET, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			if ( isset( $message ) && ! empty( $message ) ) {
				self::$admin_object->afrsm_updated_message( $message, "" );
			}
			if ( isset( $action ) && ! empty( $action ) ) {
				if ( 'add' === $action ) {
					self::afrsm_sj_save_method();
					self::afrsm_sj_add_shipping_method_form();
				} elseif ( 'edit' === $action ) {
					if ( isset( $cust_nonce ) && ! empty( $cust_nonce ) ) {
						$getnonce = wp_verify_nonce( $cust_nonce, 'edit_' . $post_id_request );
						if ( isset( $getnonce ) && 1 === $getnonce ) {
							self::afrsm_sj_save_method();
							self::afrsm_sj_edit_method();
						} else {
							wp_safe_redirect( add_query_arg( array(
								'page' => 'afrsm-pro-list'
							), admin_url( 'admin.php' ) ) );
							exit;
						}
					} elseif ( isset( $get_afrsm_add ) && ! empty( $get_afrsm_add ) ) {
						if ( ! wp_verify_nonce( $get_afrsm_add, 'afrsm_add' ) ) {
							$message = 'nonce_check';
						} else {
							self::afrsm_sj_save_method();
							self::afrsm_sj_edit_method();
						}
					}
				} elseif ( 'delete' === $action ) {
					self::afrsm_sj_delete_method( $post_id_request );
				} elseif ( 'duplicate' === $action ) {
					self::afrsm_sj_duplicate_method( $post_id_request );
				} else {
					self::afrsm_sj_list_methods_screen();
				}
			} else {
				self::afrsm_sj_list_methods_screen();
			}
			
		}

		/**
		 * Delete shipping method
		 *
		 * @param int $id
		 *
		 * @access   public
		 * @uses Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin::afrsm_updated_message()
		 *
		 * @since    3.5
		 *
		 */
		public static function afrsm_sj_delete_method( $id ) {
			$cust_nonce = filter_input( INPUT_GET, 'cust_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

			$getnonce = wp_verify_nonce( $cust_nonce, 'del_' . $id );
			if ( isset( $getnonce ) && 1 === $getnonce ) {
				wp_delete_post( $id );
				wp_safe_redirect( add_query_arg( array(
					'page'    => 'afrsm-pro-list',
					'message' => 'deleted'
				), admin_url( 'admin.php' ) ) );
				exit;
			} else {
				self::$admin_object->afrsm_updated_message( 'nonce_check', "" );
			}
		}

		/**
		 * Duplicate shipping method
		 *
		 * @param int $id
		 *
		 * @access   public
		 * @uses Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin::afrsm_updated_message()
		 *
		 * @since    1.0.0
		 *
		 */
		public static function afrsm_sj_duplicate_method( $id ) {
			$cust_nonce = filter_input( INPUT_GET, 'cust_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

			$getnonce    = wp_verify_nonce( $cust_nonce, 'duplicate_' . $id );
			$post_id     = isset( $id ) ? absint( $id ) : '';
			$new_post_id = '';
			if ( isset( $getnonce ) && 1 === $getnonce ) {
				if ( ! empty( $post_id ) || "" !== $post_id ) {
					$post            = get_post( $post_id );
					$current_user    = wp_get_current_user();
					$new_post_author = $current_user->ID;
					if ( isset( $post ) && null !== $post ) {
						$args           = array(
							'comment_status' => $post->comment_status,
							'ping_status'    => $post->ping_status,
							'post_author'    => $new_post_author,
							'post_content'   => $post->post_content,
							'post_excerpt'   => $post->post_excerpt,
							'post_name'      => $post->post_name,
							'post_parent'    => $post->post_parent,
							'post_password'  => $post->post_password,
							'post_status'    => 'draft',
							'post_title'     => $post->post_title . '-duplicate',
							'post_type'      => self::post_type,
							'to_ping'        => $post->to_ping,
							'menu_order'     => $post->menu_order
						);
						$new_post_id    = wp_insert_post( $args );
						$post_meta_data = get_post_meta( $post_id );
						if ( 0 !== count( $post_meta_data ) ) {
							foreach ( $post_meta_data as $meta_key => $meta_data ) {
								if ( '_wp_old_slug' === $meta_key ) {
									continue;
								}

								$meta_value = maybe_unserialize( $meta_data[0] );

								update_post_meta( $new_post_id, $meta_key, $meta_value );
							}
						}
						$default_lang = self::$admin_object->afrsm_pro_get_default_langugae_with_sitpress();
						$getSortOrder = get_option( 'sm_sortable_order_' . $default_lang );
			
						if ( ! empty( $getSortOrder ) ) {
							foreach ( $getSortOrder as $getSortOrder_id ) {
								settype( $getSortOrder_id, 'integer' );
							}
							array_unshift( $getSortOrder, $new_post_id );
						}
						update_option( 'sm_sortable_order_' . $default_lang, $getSortOrder );
					}
					$afrsm_add   = wp_create_nonce( 'edit_' . $new_post_id );
					wp_safe_redirect( add_query_arg( array(
						'page'     		=> 'afrsm-pro-list',
						'action'   		=> 'edit',
						'id'     		=> $new_post_id,
						'cust_nonce' 	=> $afrsm_add,
						'message'  		=> 'duplicated'
					), admin_url( 'admin.php' ) ) );
					exit();
				} else {
					wp_safe_redirect( add_query_arg( array(
						'page'    => 'afrsm-pro-list',
						'message' => 'failed'
					), admin_url( 'admin.php' ) ) );
					exit();
				}
			} else {
				self::$admin_object->afrsm_updated_message( 'nonce_check', "" );
			}
		}

		/**
		 * Save shipping method when add or edit
		 *
		 * @param int $method_id
		 *
		 * @return bool false when nonce is not verified, $zone id, $zone_type is blank, Country also blank, Postcode field also blank, saving error when form submit
		 *
		 * @since    3.5
		 *
		 * @uses Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin::afrsm_updated_message()
		 */
		private static function afrsm_sj_save_method( ) {
			$afrsm_admin_object = new Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin( '', '' );
			$post_data = $_POST; //phpcs:ignore
			$afrsm_admin_object->afrsm_pro_fees_conditions_save( $post_data );
		}

		/**
		 * Edit discount rule
		 *
		 * @since    3.5
		 */
		private static function afrsm_sj_edit_method() {
			include( plugin_dir_path( __FILE__ ) . 'afrsm-pro-add-new-page.php' );
		}

		/**
		 * Add discount rule
		 *
		 * @since    3.5
		 */
		public static function afrsm_sj_add_shipping_method_form() {
			include( plugin_dir_path( __FILE__ ) . 'afrsm-pro-add-new-page.php' );
		}

		/**
		 * list_shipping_methods function.
		 *
		 * @since    3.5
		 *
		 * @uses WC_Advanced_Flat_Rate_Shipping_Table class
		 * @uses WC_Advanced_Flat_Rate_Shipping_Table::process_bulk_action()
		 * @uses WC_Advanced_Flat_Rate_Shipping_Table::prepare_items()
		 * @uses WC_Advanced_Flat_Rate_Shipping_Table::search_box()
		 * @uses WC_Advanced_Flat_Rate_Shipping_Table::display()
		 *
		 * @access public
		 *
		 */
		public static function afrsm_sj_list_methods_screen() {
			if ( ! class_exists( 'WC_Advanced_Flat_Rate_Shipping_Table' ) ) {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'list-tables/class-wc-flat-rate-rule-table.php';
			}
			$link = add_query_arg( array(
				'page'   => 'afrsm-pro-list',
				'action' => 'add'
			), admin_url( 'admin.php' ) );

			require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' );
			wp_nonce_field('sorting_conditional_fee_action','sorting_conditional_fee');
			$WC_Advanced_Flat_Rate_Shipping_Table = new WC_Advanced_Flat_Rate_Shipping_Table();
			?>
			<div class="wrap">
				<form method="post" enctype="multipart/form-data">
					<div class="afrsm-section-left">
						<div class="afrsm-main-table res-cl afrsm-add-rule-page">
							<h1><?php esc_html_e( 'Shipping Methods', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h1>
							<?php
				            if ( !( afrsfw_fs()->is__premium_only() && afrsfw_fs()->can_use_premium_code() ) ) {
				            	$valid_rules = get_option( 'afrsm_limited_allowed_rules', '' );
								if ( ! empty( $valid_rules ) && intval( $valid_rules ) >= 10 ) {
									?>
									<a href="javascript:void(0);" class="page-title-action dots-btn-with-brand-color upgrade-now"><?php echo esc_html__( 'Add New 🔒', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></a>
									<?php
								} else {
									?>
									<a href="<?php echo esc_url( $link ); ?>" class="page-title-action dots-btn-with-brand-color"><?php echo esc_html__( 'Add New', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></a>
									<?php
								}
				            } else {
								?>
								<a href="<?php echo esc_url( $link ); ?>" class="page-title-action dots-btn-with-brand-color"><?php echo esc_html__( 'Add New', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></a>
								<?php
							}
							
							$WC_Advanced_Flat_Rate_Shipping_Table->process_bulk_action();
							$WC_Advanced_Flat_Rate_Shipping_Table->prepare_items();
							$request_s = filter_input( INPUT_POST, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
							if ( isset( $request_s ) && ! empty( $request_s ) ) {
								echo sprintf( '<span class="subtitle">' . esc_html__( 'Search results for &#8220;%s&#8221;', 'advanced-flat-rate-shipping-for-woocommerce' ) . '</span>', esc_html( $request_s ) );
							}
							$WC_Advanced_Flat_Rate_Shipping_Table->search_box( esc_html__( 'Search', 'advanced-flat-rate-shipping-for-woocommerce' ), 'shipping-method' );
							$WC_Advanced_Flat_Rate_Shipping_Table->display();
                            ?>
						</div>

					</div>
				</form>
			</div>

			<?php
			require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php' );
		}
	}
}
?>