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
 *
 * @since 1.0.0
 * @package LatitudeFinance/Functions
 * @param array $gateways
 */
function wc_latitudefinance_payment_gateways( $gateways ) {
	return array_merge( $gateways, wc_latitudefinance()->get_payment_gateways() );
}

/**
 *
 * @since 1.0.0
 * @package LatitudeFinance/Functions
 * @todo Implement the way to prevent spam bot
 */
function wc_latitudefinance_spam_bot_field() {
	echo( '<input type="checkbox" value="1" name="latitude_finance_customer_email" style="display: none" autocomplete="off" tabindex="-1"/>' );
}

/**
 *
 * @since 1.0.0
 * @package LatitudeFinance/Functions
 * @param string $template_name
 * @param array  $args
 */
function wc_latitudefinance_get_template( $template_name, $args = array() ) {
	return wc_get_template( $template_name, $args, wc_latitudefinance()->template_path(), wc_latitudefinance()->plugin_path() . 'templates/' );
}

/**
 *
 * @since 1.0.0
 * @package LatitudeFinance/Functions
 * @param string $key
 * @param string $class
 */
function wc_latitudefinance_hidden_field( $key, $class = '' ) {
	printf( '<input type="hidden" id="%1$s" name="%1$s" class="%2$s"/>', $key, $class );
}

/**
 * Echo an input field for a latitudefinance payment_method_nonce.
 *
 * @since 1.0.0
 * @package LatitudeFinance/Functions
 * @param WC_LatitudeFinance_Method_Abstract $gateway
 */
function wc_latitudefinance_nonce_field( $gateway, $value = '' ) {
	wc_latitudefinance_hidden_field( $gateway->nonce_key, 'wc-latitudefinance-payment-nonce' );
}

/**
 *
 * @since 1.0.0
 * @package LatitudeFinance/Functions
 * @param WC_LatitudeFinance_Method_Abstract $gateway
 */
function wc_latitudefinance_device_data_field( $gateway ) {
	wc_latitudefinance_hidden_field( $gateway->device_data_key, 'wc-latitudefinance-device-data' );
}

/**
 * Check the current configuration to get the right place and display the payment snippet
 */
function wc_latitudefinance_show_snippet_in_product_page() {
	foreach ( WC()->payment_gateways()->get_available_payment_gateways() as $id => $gateway ) {
		$gateways[ $id ] = $gateway;
		if ( in_array( get_class( $gateway ), WC_LatitudeFinance_Manager::$gateways ) ) {
			if ( $gateway->get_option( 'individual_snippet_enabled', 'yes' ) === 'yes' ) {
				add_action(
					$gateway->get_option( 'snippet_product_page_position', 'woocommerce_single_product_summary' ),
					'wc_latitudefinance_show_product_checkout_gateways',
					$gateway->get_option( 'snippet_product_page_hook_priority', 11 )
				);
			}
		}
	}
}

/**
 * @since 1.0.0
 * @package LatitudeFinance/Functions
 */
function wc_latitudefinance_show_product_checkout_gateways() {
	$gateways = array();
	foreach ( WC()->payment_gateways()->get_available_payment_gateways() as $id => $gateway ) {
		$gateways[ $id ] = $gateway;

		if ( in_array( get_class( $gateway ), WC_LatitudeFinance_Manager::$gateways ) ) {
			wc_latitudefinance_get_template(
				'product/payment.php',
				array(
					'gateway' => $gateway,
				)
			);
		}
	}
}

/**
 * @since 1.0.0
 * @package LatitudeFinance/Functions
 */
function wc_latitudefinance_include_extra_scripts() {
	foreach ( WC()->payment_gateways()->get_available_payment_gateways() as $id => $gateway ) {
		if ( $id && in_array( get_class( $gateway ), WC_LatitudeFinance_Manager::$gateways ) ) {
			$file = WC_LATITUDEPAY_ASSETS . 'css/' . $id . '/styles.css';
			// enqueue the files only if it meets the following condition
			// 1. is product page
			// 2. OR is checkout page
			if ( is_product() || is_checkout() || is_cart() ) {
				wp_register_style( 'woocommerce-payment-gateway-latitudefinance-' . $id, $file );
				wp_enqueue_style( 'woocommerce-payment-gateway-latitudefinance-' . $id );
			}

			/**
			 * is cart page
			 */
			if ( is_cart() ) {
				wp_register_style(
					'woocommerce-payment-gateway-latitudefinance-cart',
					WC_LATITUDEPAY_ASSETS . 'css/common.css'
				);
				wp_enqueue_style( 'woocommerce-payment-gateway-latitudefinance-cart' );
			}
		}
	}
}

function wc_latitudefinance_show_payment_banners() {
	$gateways  = array();
	$cartTotal = WC()->cart->total;

	// Check cart total is not empty.
	if ( ! $cartTotal ) {
		return;
	}

	foreach ( WC()->payment_gateways()->get_available_payment_gateways() as $id => $gateway ) {
		$gateways[ $id ] = $gateway;
		if ( in_array( get_class( $gateway ), WC_LatitudeFinance_Manager::$gateways ) ) {
			$min = floor( $gateway->get_option( 'min_order_total' ) );
			$max = floor( $gateway->get_option( 'max_order_total' ) );
			// Check if it is supported by the gateway.
			if ( $cartTotal < $min || $cartTotal > $max ) {
				continue;
			}

			if ( in_array( get_class( $gateway ), WC_LatitudeFinance_Manager::$gateways ) ) {
				wc_latitudefinance_get_template(
					'cart/payment.php',
					array(
						'gateway' => $gateway,
						'cart'    => WC()->cart,
					)
				);
			}
		}
	}
}

/**
 * @see https://jira.magebinary.com/browse/SP-2545
 * [GenoaPay] Remove shopping cart message (note). (Note: If the cart total amount is less than 20 or greater than 1500 then you will not be able to proceed the checkout with Latitudepay)
 * @package LatitudeFinance/Functions
 */
function wc_latitudefinance_show_payment_options() {
	$gateways  = array();
	$cartTotal = WC()->cart->total;

	// Check cart total is not empty.
	if ( ! $cartTotal ) {
		return;
	}

	foreach ( WC()->payment_gateways()->get_available_payment_gateways() as $id => $gateway ) {
		$gateways[ $id ] = $gateway;
		if ( in_array( get_class( $gateway ), WC_LatitudeFinance_Manager::$gateways ) ) {

			// Check if it is supported by the gateway.
			$min = floor( $gateway->get_option( 'min_order_total' ) );
			$max = floor( $gateway->get_option( 'max_order_total' ) );
			// Check if it is supported by the gateway.
			if ( $cartTotal < $min || $cartTotal > $max ) {
				continue;
			}

			if ( in_array( get_class( $gateway ), WC_LatitudeFinance_Manager::$gateways ) ) {
				wc_latitudefinance_get_template(
					'cart/payment.php',
					array(
						'gateway' => $gateway,
						'cart'    => WC()->cart,
					)
				);
			}
		}
	}
}

/**
 * @since 1.0.0
 */
function wc_latitudefinance_get_array_data( $key, $array, $default = '' ) {
	$value = isset( $array[ $key ] ) ? $array[ $key ] : $default;
	return $value;
}
