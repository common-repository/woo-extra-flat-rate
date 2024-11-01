<?php
/**
 * Handles free plugin user dashboard
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' );

// Get product details from Freemius via API
$annual_plugin_price = '';
$monthly_plugin_price = '';
$plugin_details = array(
    'product_id' => 43542,
);

$api_url = add_query_arg(wp_rand(), '', AFRSM_STORE_URL . 'wp-json/dotstore-product-fs-data/v2/dotstore-product-fs-data');
$final_api_url = add_query_arg($plugin_details, $api_url);

if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
    $api_response = vip_safe_wp_remote_get( $final_api_url, 3, 1, 20 );
} else {
    $api_response = wp_remote_get( $final_api_url ); // phpcs:ignore
}

if ( ( !is_wp_error($api_response)) && (200 === wp_remote_retrieve_response_code( $api_response ) ) ) {
	$api_response_body = wp_remote_retrieve_body($api_response);
	$plugin_pricing = json_decode( $api_response_body, true );

	if ( isset( $plugin_pricing ) && ! empty( $plugin_pricing ) ) {
		$first_element = reset( $plugin_pricing );
        if ( ! empty( $first_element['price_data'] ) ) {
            $first_price = reset( $first_element['price_data'] )['annual_price'];
        } else {
            $first_price = "0";
        }

        if( "0" !== $first_price ){
        	$annual_plugin_price = $first_price;
        	$monthly_plugin_price = round( intval( $first_price  ) / 12 );
        }
	}
}

// Set plugin key features content
$plugin_key_features = array(
    array(
        'title' => esc_html__( 'Advanced Shipping Rules', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'description' => esc_html__( 'Easily set advanced shipping rules based on product, category, tag, subtotal, quantity, and more with our Premium Flat Rate plugin.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'popup_image' => esc_url( AFRSM_PRO_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-1.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'Our powerful tool empowers online store owners to create unlimited advanced shipping rules that match their business needs perfectly. Set rules based on item quantity, product weight, categories, user roles, shipping class, etc.', 'advanced-flat-rate-shipping-for-woocommerce' )
        ),
        'popup_examples' => array(
            esc_html__( 'According to the rule, add $15 shipping charges for orders with subtotals ranging from $50 to $100.', 'advanced-flat-rate-shipping-for-woocommerce' ),
            esc_html__( 'Reward loyal customers by providing free shipping on all orders for users with specific roles, enhancing their shopping satisfaction.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        )
    ),
    array(
        'title' => esc_html__( 'Flexible Tiered Shipping Solutions', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'description' => esc_html__( 'Using various dynamic variables, our flat rate shipping formulas allow you to set shipping fees effortlessly.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'popup_image' => esc_url( AFRSM_PRO_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-2.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'Our cutting-edge plugin empowers you to effortlessly set up multiple shipping charges based on various ranges such as products, categories, shipping class, weight, total sale, and more.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        ),
        'popup_examples' => array(
            esc_html__( 'Ex, lets you offer special shipping rates to customers. If a customer adds ‘Product 1’ or ‘Product 2’ to their cart with Quantity of the cart must be greater than 3.', 'advanced-flat-rate-shipping-for-woocommerce' ),
            esc_html__( 'For example, customers who pay cash on delivery have a flat shipping fee of $5.', 'advanced-flat-rate-shipping-for-woocommerce' )
        )
    ),
    array(
        'title' => esc_html__( 'Personalized Shipping for Every User', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'description' => esc_html__( 'Set up user-based shipping costs to capitalize on demand from specific users or user groups.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'popup_image' => esc_url( AFRSM_PRO_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-3.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'Our user-based shipping feature allows customized shipping options for different user roles or groups. Provide a tailored shipping experience that delights your customers and boosts loyalty.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        ),
        'popup_examples' => array(
            esc_html__( 'E.g, we can select a user role from a plugin setting like "Customer", So once the user login with the customer role then this shipping will apply.', 'advanced-flat-rate-shipping-for-woocommerce' ),
            esc_html__( 'User-specific shipping charges.', 'advanced-flat-rate-shipping-for-woocommerce' )
        )
    ),
    array(
        'title' => esc_html__( 'Force All Shipping', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'description' => esc_html__( 'Leverage user demand with the option to merge bulk orders of multiple products into one, non-optional shipping cost.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'popup_image' => esc_url( AFRSM_PRO_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-4.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'When multiple shipping methods are enabled, and If the store owner wants to charge the sum of all applicable shipping method charges, then this feature is very useful.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        ),
        'popup_examples' => array(
            esc_html__( 'Merge all shipping methods to one shipping method.', 'advanced-flat-rate-shipping-for-woocommerce' ),
            esc_html__( 'This option will allow you to display the highest or lowest available shipping method.', 'advanced-flat-rate-shipping-for-woocommerce' ),
            esc_html__( 'Merge WooCommerce shipping method with us using this option.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        )
    ),
    array(
        'title' => esc_html__( 'Time-Bound Shipping', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'description' => esc_html__( 'Effortlessly configure shipping for special days like festivals, nights, holidays, and more. Provide timely deliveries for a seamless customer experience.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'popup_image' => esc_url( AFRSM_PRO_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-5.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'Timely Deliveries, Every Occasion. Our time-based shipping method lets you configure shipping options for festivals, nights, holidays, and more, ensuring a seamless customer experience.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        ),
        'popup_examples' => array(
            esc_html__( 'We can display shipping methods for deliveries from 03-07-2024 to 18-07-2024, between 5 AM to 11 AM.', 'advanced-flat-rate-shipping-for-woocommerce' ),
            esc_html__( 'With our day-wise shipping feature, schedule deliveries based on specific days or weekdays.', 'advanced-flat-rate-shipping-for-woocommerce' )
        )
    ),
    array(
        'title' => esc_html__( 'Zone-Based Shipping', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'description' => esc_html__( 'Benefit from assigning zone-based shipping costs defined by postcode, city, state, and more.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'popup_image' => esc_url( AFRSM_PRO_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-6.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'It allows you to set customized shipping costs based on postcodes, cities, states, and more. Provide a seamless shopping experience with shipping fees specific to each customer\'s location.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        ),
        'popup_examples' => array(
            esc_html__( 'Apply the designated shipping method for seamless delivery within European countries.', 'advanced-flat-rate-shipping-for-woocommerce' ),
            esc_html__( 'Our shipping solution allows you to ship to specific postcodes, cities, countries, and states from the list.', 'advanced-flat-rate-shipping-for-woocommerce' )
        )
    ),
    array(
        'title' => esc_html__( 'Customize Free Shipping Rules', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'description' => esc_html__( 'Effortlessly set conditions for free shipping based on order value or specific products, and coupon usage.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'popup_image' => esc_url( AFRSM_PRO_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-7.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'Unlock the power to offer free shipping based on order amount, specific products, and coupon usage.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        ),
        'popup_examples' => array(
            esc_html__( 'E.g., Set free shipping for orders with a subtotal greater than $60. Exclude "Product 2" from the subtotal calculation.', 'advanced-flat-rate-shipping-for-woocommerce' ),
            esc_html__( 'E.g., Customers can enjoy free shipping on their orders by using a coupon. To qualify, the coupon total must be greater than $50.', 'advanced-flat-rate-shipping-for-woocommerce' )
        )
    ),
    array(
        'title' => esc_html__( 'Weight-Based Shipping Charges', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'description' => esc_html__( 'Our feature allows you to set shipping charges based on product weight. Provide accurate and fair shipping charges.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        'popup_image' => esc_url( AFRSM_PRO_PLUGIN_URL . 'admin/images/pro-features-img/feature-box-img-8.jpeg' ),
        'popup_content' => array(
        	esc_html__( 'Our feature calculates shipping costs based on product weight, ensuring fair and reliable rates for customers.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        ),
        'popup_examples' => array(
            esc_html__( 'E.g., orders weighing 5 kgs or less have a flat shipping fee of $3 0. For each additional 2 kgs, $5 is added to the total shipping charge.', 'advanced-flat-rate-shipping-for-woocommerce' ),
            esc_html__( 'E.g., 9 kgs order would have a shipping fee of $40, Calculation is like $30(base charge) + 14kgs of product weight in the cart(where only add additional charge on 4kgs(9kgs - 5kgs )) which is calculated as $10.', 'advanced-flat-rate-shipping-for-woocommerce' )
        )
    )
);
?>
	<div class="wcpfc-section-left">
		<div class="dotstore-upgrade-dashboard">
			<div class="premium-benefits-section">
				<h2><?php esc_html_e( 'Upgrade to Unlock Premium Features', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h2>
				<p><?php esc_html_e( 'Check out the advanced features, simplify product management & reduce returns by upgrading to premium!', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></p>
			</div>
			<div class="premium-plugin-details">
				<div class="premium-key-fetures">
					<h3><?php esc_html_e( 'Discover Our Top Key Features', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h3>
					<ul>
						<?php 
						if ( isset( $plugin_key_features ) && ! empty( $plugin_key_features ) ) {
							foreach( $plugin_key_features as $key_feature ) {
								?>
								<li>
									<h4><?php echo esc_html( $key_feature['title'] ); ?><span class="premium-feature-popup"></span></h4>
									<p><?php echo esc_html( $key_feature['description'] ); ?></p>
									<div class="feature-explanation-popup-main">
										<div class="feature-explanation-popup-outer">
											<div class="feature-explanation-popup-inner">
												<div class="feature-explanation-popup">
													<span class="dashicons dashicons-no-alt popup-close-btn" title="<?php esc_attr_e('Close', 'advanced-flat-rate-shipping-for-woocommerce'); ?>"></span>
													<div class="popup-body-content">
														<div class="feature-content">
															<h4><?php echo esc_html( $key_feature['title'] ); ?></h4>
															<?php 
															if ( isset( $key_feature['popup_content'] ) && ! empty( $key_feature['popup_content'] ) ) {
																foreach( $key_feature['popup_content'] as $feature_content ) {
																	?>
																	<p><?php echo esc_html( $feature_content ); ?></p>
																	<?php
																}
															}
															?>
															<ul>
																<?php 
																if ( isset( $key_feature['popup_examples'] ) && ! empty( $key_feature['popup_examples'] ) ) {
																	foreach( $key_feature['popup_examples'] as $feature_example ) {
																		?>
																		<li><?php echo esc_html( $feature_example ); ?></li>
																		<?php
																	}
																}
																?>
															</ul>
														</div>
														<div class="feature-image">
															<img src="<?php echo esc_url( $key_feature['popup_image'] ); ?>" alt="<?php echo esc_attr( $key_feature['title'] ); ?>">
														</div>
													</div>
												</div>		
											</div>
										</div>
									</div>
								</li>
								<?php
							}
						}
						?>
					</ul>
				</div>
				<div class="premium-plugin-buy">
					<div class="premium-buy-price-box">
						<div class="price-box-top">
							<div class="pricing-icon">
								<img src="<?php echo esc_url( AFRSM_PRO_PLUGIN_URL . 'admin/images/premium-upgrade-img/pricing-1.svg' ); ?>" alt="<?php esc_attr_e( 'Personal Plan', 'advanced-flat-rate-shipping-for-woocommerce' ); ?>">
							</div>
							<h4><?php esc_html_e( 'Personal', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h4>
						</div>
						<div class="price-box-middle">
							<?php
							if ( ! empty( $annual_plugin_price ) ) {
								?>
								<div class="monthly-price-wrap"><?php echo esc_html( '$' . $monthly_plugin_price ); ?><span class="seprater">/</span><span><?php esc_html_e( 'month', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></span></div>
								<div class="yearly-price-wrap"><?php echo sprintf( esc_html__( 'Pay $%s today. Renews in 12 months.', 'advanced-flat-rate-shipping-for-woocommerce' ), esc_html( $annual_plugin_price ) ); ?></div>
								<?php	
							}
							?>
							<span class="for-site"><?php esc_html_e( '1 site', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></span>
							<p class="price-desc"><?php esc_html_e( 'Great for website owners with a single WooCommerce Store', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></p>
						</div>
						<div class="price-box-bottom">
							<a href="javascript:void(0);" class="upgrade-now"><?php esc_html_e( 'Get The Premium Version', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></a>
							<p class="trusted-by"><?php esc_html_e( 'Trusted by 100,000+ store owners and WP experts!', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></p>
						</div>
					</div>
					<div class="premium-satisfaction-guarantee premium-satisfaction-guarantee-2">
						<div class="money-back-img">
							<img src="<?php echo esc_url(AFRSM_PRO_PLUGIN_URL . 'admin/images/premium-upgrade-img/14-Days-Money-Back-Guarantee.png'); ?>" alt="<?php esc_attr_e('14-Day money-back guarantee', 'advanced-flat-rate-shipping-for-woocommerce'); ?>">
						</div>
						<div class="money-back-content">
							<h2><?php esc_html_e( '14-Day Satisfaction Guarantee', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h2>
							<p><?php esc_html_e( 'You are fully protected by our 100% Satisfaction Guarantee. If over the next 14 days you are unhappy with our plugin or have an issue that we are unable to resolve, we\'ll happily consider offering a 100% refund of your money.', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></p>
						</div>
					</div>
					<div class="plugin-customer-review">
						<h3><?php esc_html_e( 'Best solution for complex shipping', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h3>
						<p>
							<?php echo wp_kses( __( 'After struggling for a long time, we finally managed to <strong>fully automate our shipping</strong> thanks to the Flat Rate Shipping plugin, which <strong>saved us a lot of valuable time.</strong>', 'advanced-flat-rate-shipping-for-woocommerce' ), array(
					                'strong' => array(),
					            ) ); 
				            ?>
			            </p>
						<div class="review-customer">
							<div class="customer-img">
								<img src="<?php echo esc_url(AFRSM_PRO_PLUGIN_URL . 'admin/images/premium-upgrade-img/customer-profile-img.jpeg'); ?>" alt="<?php esc_attr_e('Customer Profile Image', 'advanced-flat-rate-shipping-for-woocommerce'); ?>">
							</div>
							<div class="customer-name">
								<span><?php esc_html_e( 'Pim Grootnibbelink', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></span>
								<div class="customer-rating-bottom">
									<div class="customer-ratings">
										<span class="dashicons dashicons-star-filled"></span>
										<span class="dashicons dashicons-star-filled"></span>
										<span class="dashicons dashicons-star-filled"></span>
										<span class="dashicons dashicons-star-filled"></span>
										<span class="dashicons dashicons-star-filled"></span>
									</div>
									<div class="verified-customer">
										<span class="dashicons dashicons-yes-alt"></span>
										<?php esc_html_e( 'Verified Customer', 'advanced-flat-rate-shipping-for-woocommerce' ); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="upgrade-to-pro-faqs">
				<h2><?php esc_html_e( 'FAQs', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h2>
				<div class="upgrade-faqs-main">
					<div class="upgrade-faqs-list">
						<div class="upgrade-faqs-header">
							<h3><?php esc_html_e( 'Do you offer support for the plugin? What’s it like?', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h3>
						</div>
						<div class="upgrade-faqs-body">
							<p>
							<?php 
								echo sprintf(
								    esc_html__('Yes! You can read our %s or submit a %s. We are very responsive and strive to do our best to help you.', 'advanced-flat-rate-shipping-for-woocommerce'),
								    '<a href="' . esc_url('https://docs.thedotstore.com/collection/81-flat-rate-shipping') . '" target="_blank">' . esc_html__('knowledge base', 'advanced-flat-rate-shipping-for-woocommerce') . '</a>',
								    '<a href="' . esc_url('https://www.thedotstore.com/support-ticket/') . '" target="_blank">' . esc_html__('support ticket', 'advanced-flat-rate-shipping-for-woocommerce') . '</a>',
								);

							?>
							</p>
						</div>
					</div>
					<div class="upgrade-faqs-list">
						<div class="upgrade-faqs-header">
							<h3><?php esc_html_e( 'What payment methods do you accept?', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h3>
						</div>
						<div class="upgrade-faqs-body">
							<p><?php esc_html_e( 'You can pay with your credit card using Stripe checkout. Or your PayPal account.', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></p>
						</div>
					</div>
					<div class="upgrade-faqs-list">
						<div class="upgrade-faqs-header">
							<h3><?php esc_html_e( 'What’s your refund policy?', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h3>
						</div>
						<div class="upgrade-faqs-body">
							<p><?php esc_html_e( 'We have a 14-day money-back guarantee.', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></p>
						</div>
					</div>
					<div class="upgrade-faqs-list">
						<div class="upgrade-faqs-header">
							<h3><?php esc_html_e( 'I have more questions…', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h3>
						</div>
						<div class="upgrade-faqs-body">
							<p>
							<?php 
								echo sprintf(
								    esc_html__('No problem, we’re happy to help! Please reach out at %s.', 'advanced-flat-rate-shipping-for-woocommerce'),
								    '<a href="' . esc_url('mailto:hello@thedotstore.com') . '" target="_blank">' . esc_html('hello@thedotstore.com') . '</a>',
								);

							?>
							</p>
						</div>
					</div>
				</div>
			</div>
			<div class="upgrade-to-premium-btn">
				<a href="javascript:void(0);" target="_blank" class="upgrade-now"><?php esc_html_e( 'Get The Premium Version', 'advanced-flat-rate-shipping-for-woocommerce' ); ?><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="crown" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-crown fa-w-20 fa-3x" width="22" height="20"><path fill="#000" d="M528 448H112c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h416c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm64-320c-26.5 0-48 21.5-48 48 0 7.1 1.6 13.7 4.4 19.8L476 239.2c-15.4 9.2-35.3 4-44.2-11.6L350.3 85C361 76.2 368 63 368 48c0-26.5-21.5-48-48-48s-48 21.5-48 48c0 15 7 28.2 17.7 37l-81.5 142.6c-8.9 15.6-28.9 20.8-44.2 11.6l-72.3-43.4c2.7-6 4.4-12.7 4.4-19.8 0-26.5-21.5-48-48-48S0 149.5 0 176s21.5 48 48 48c2.6 0 5.2-.4 7.7-.8L128 416h384l72.3-192.8c2.5.4 5.1.8 7.7.8 26.5 0 48-21.5 48-48s-21.5-48-48-48z" class=""></path></svg></a>
			</div>
		</div>
	</div>
</div>
</div>
</div>
</div>
<?php 
