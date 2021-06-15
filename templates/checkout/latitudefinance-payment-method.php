<?php
	/**
	 * Woocommerce LatitudeFinance Payment Extension
	 *
	 * NOTICE OF LICENSE
	 *
	 * Copyright 2020 LatitudeFinance
	 *
	 * Licensed under the Apache License, Version 2.0 (the "License");
	 * you may not use this file except in compliance with the License.
	 * You may obtain a copy of the License at
	 *
	 *   http://www.apache.org/licenses/LICENSE-2.0
	 *
	 * Unless required by applicable law or agreed to in writing, software
	 * distributed under the License is distributed on an "AS IS" BASIS,
	 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	 * See the License for the specific language governing permissions and
	 * limitations under the License.
	 *
	 * @category    LatitudeFinance
	 * @package     Latitude_Finance
	 * @author      MageBinary Team
	 * @copyright   Copyright (c) 2020 LatitudeFinance (https://www.latitudefinancial.com.au/)
	 * @license     http://www.apache.org/licenses/LICENSE-2.0
	 */

if ( ! isset( $gateway ) ) {
	return;
}

	$description = $gateway->get_description();
	wc_latitudefinance_spam_bot_field();
	$color         = $gateway->get_id() == 'latitudepay' ? 'rgb(57, 112, 255)' : 'rgb(49, 181, 156)';
	$modalFile     = __DIR__ . DIRECTORY_SEPARATOR . '../checkout/' . $gateway->get_id() . DIRECTORY_SEPARATOR . 'modal.php';
	$paymentInfo   = 'Available now.';
	$price         = WC()->cart->total;
	$saved_methods = wc_get_customer_saved_methods_list( get_current_user_id() );
	$has_methods   = (bool) $saved_methods;

if ( $price >= 20 && $price <= 1500 ) {
	$weekly      = number_format( round( $price / 10, 2 ), 2 );
	$paymentInfo = "10 weekly payments of <strong style='color:${color}'>$${weekly}</strong>";
}

?>
<div class="wc-latitudefinance-payment-gateway">
	<?php
		wc_latitudefinance_nonce_field( $gateway );
		wc_latitudefinance_device_data_field( $gateway );
	?>

		<div class="wc-latitudefinance-new-payment-method-container" style="<?php $has_methods ? printf( 'display: none' ) : printf( '' ); ?>">
			<?php if ( $gateway->id === WC_LatitudeFinance_Method_Latitudepay::METHOD_LATITUDEPAY ) : ?>
				<?php
					/**
					 * @var WC_LatitudeFinance_Method_Latitudepay $gateway
					 */
					$gateway->setAmount( $price )->setIsFullBlock( true );
					echo $gateway->generate_snippet_html();
				?>
			<?php else : ?>
				<div style="margin:10px 0px;">

					<img src="<?php echo WC_LATITUDEPAY_ASSETS . $gateway->get_id() . '.svg'; ?>" style="padding-right: 5px; max-width: 110px;padding-bottom:15px;"/>
					<p style="font-weight: 700; line-height: 24px;">Shop now pay later.
						<a style="text-decoration: none;" href="javascript: void(0)" id="<?php echo $gateway->get_id(); ?>-popup">
							<span style="text-decoration: none;font-weight: bold; font-size:13px; marigin-left: 5px; color: <?php echo $color; ?>">Learn more</span>
						</a>
					</p>

					<p><?php echo $paymentInfo; ?></p>

					<?php include $modalFile; ?>
				</div>

				<p style="font-size: 22px; color:<?php echo $color; ?>; font-weight: 600; margin-bottom: 20px">No interest.<span style="text-decoration: underline; margin-left: 5px;">Ever.</span></p>
			<?php endif; ?>
		</div>
</div>
