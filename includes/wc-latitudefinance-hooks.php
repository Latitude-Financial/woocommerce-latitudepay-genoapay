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

/**
 * Add custom payment gateway to Woocommerce payment gateways
 *
 * @todo : this line somehow breaks my order page.
 */
add_filter( 'woocommerce_payment_gateways', 'wc_latitudefinance_payment_gateways' );

add_action( 'woocommerce_proceed_to_checkout', 'wc_latitudefinance_show_payment_options' );
add_action( 'wp_enqueue_scripts', 'wc_latitudefinance_include_extra_scripts' );
foreach ( array_keys( WC_LatitudeFinance_Method_Abstract::WOOCOMMERCE_PRODUCT_PAGE_POSITIONS ) as $hook ) {
	add_action( $hook, 'wc_latitudefinance_show_snippet_in_product_page' );
}
