<?php
/**
 * Plugin Name: LatitudePay & Genoapay Integrations for WooCommerce
 * Plugin URI: https://www.latitudefinancial.com.au/
 * Description: Genoapay is enabled if the store Currency is NZD and LatitudePay if AUD.
 * Version: 3.0.2
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Latitude Financial Services
 * Author URI: https://latitudepay.com/
 * Text Domain: latitudepay-genoapay-integrations-for-woocommerce
 * Domain Path: /i18n/languages/
 * License: Apache-2.0
 *
 * @package LatitudeFinance
 */
defined( 'ABSPATH' ) || exit;
define( 'WC_LATITUDEPAY_VERSION', '3.0.2' );
define( 'WC_LATITUDEPAY_PATH', plugin_dir_path( __FILE__ ) );
define( 'WC_LATITUDEPAY_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
define( 'WC_LATITUDEPAY_ASSETS', plugin_dir_url( __FILE__ ) . 'assets/' );
define( 'WC_LATITUDEPAY_TEMPLATES', plugin_dir_url( __FILE__ ) . 'templates/' );
define( 'WC_LATITUDEPAY_PLUGIN_NAME', plugin_basename( __FILE__ ) );


function wc_latitudepay_init() {
	require_once WC_LATITUDEPAY_PATH . 'includes/autoload.php';
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_latitudepay_plugin_action_links' );

/**
 * Adds plugin action links.
 */

function wc_latitudepay_plugin_action_links( $links ) {
	$plugin_links = array(
		'<a href="admin.php?page=wc-settings&tab=checkout">' . esc_html__( 'Settings', 'woocommerce-payment-gateway-latitudefinance' ) . '</a>',
		'<a href="https://resources.latitudefinancial.com/docs/latitude-pay/woocommerce/" target="_blank">' . esc_html__( 'Docs', 'woocommerce-payment-gateway-latitudefinance' ) . '</a>',
		'<a href="https://resources.latitudefinancial.com/docs/latitude-pay/merchant-support/" target="_blank">' . esc_html__( 'Support', 'woocommerce-payment-gateway-latitudefinance' ) . '</a>',
	);
	return array_merge( $plugin_links, $links );
}

add_action( 'plugins_loaded', 'wc_latitudepay_init', 11 );

add_action( 'woocommerce_blocks_loaded', 'woocommerce_latitude_finance_woocommerce_blocks_support' );

function woocommerce_latitude_finance_woocommerce_blocks_support() {
	if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
		require_once dirname( __FILE__ ) . '/includes/Blocks/Genoapay.php';
		require_once dirname( __FILE__ ) . '/includes/Blocks/LatitudePay.php';
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $registry ) {
				$registry->register( new WC_Gateway_Genoapay_Blocks_Support() );
				$registry->register( new WC_Gateway_LatitudePay_Blocks_Support() );
			}
		);
	}
}
