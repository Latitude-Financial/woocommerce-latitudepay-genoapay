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
 * @todo : this line somehow breaks my order page.
 */
add_filter('woocommerce_payment_gateways', 'wc_latitudefinance_payment_gateways');

/**
 * Template hooks
 */
add_action( 'woocommerce_single_product_summary', 'wc_latitudefinance_show_product_checkout_gateways', 11 ); 

/**
 * @see https://jira.magebinary.com/browse/SP-2545
 * [GenoaPay] Remove shopping cart message (note). (Note: If the cart total amount is less than 20 or greater than 1500 then you will not be able to proceed the checkout with Latitudepay)
 */
add_action('woocommerce_after_cart_totals', 'wc_latitudefinance_show_payment_options');

/**
 * Include extra CSS and Javascript files
 */
add_action('wp_enqueue_scripts', 'wc_latitudefinance_include_extra_scripts');