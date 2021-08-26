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
$gateway->output_checkout_fields();
$price = WC()->cart->total;

$paymentInfo = 'Available now.';

if ( $price >= 20 && $price <= 1500 ) {
	$weekly      = number_format( round( $price / 10, 2 ), 2 );
	$paymentInfo = "10 weekly payments of <strong>$${weekly}</strong>";
}

?>
<div class="wc-binarypay-genoapay-container">
	<strong style="font-size: 12px">
		<p><?php echo $paymentInfo; ?></p>
	</strong>

	<?php require 'modal.php'; ?>
</div>
