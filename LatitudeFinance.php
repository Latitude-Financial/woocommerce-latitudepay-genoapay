<?php
/**
 * Plugin Name: LatitudePay & GenoaPay Integrations for WooCommerce
 * Plugin URL: https://www.latitudefinancial.com.au/
 * Description: LatitudePay & Genoapay plugin supports both platforms. Genoapay is enabled if the store Currency is NZD and LatitudePay is enabled if the store currency is AUD.
 * Version: 2.0.9
 * Author: Latitude Financial Services
 * Author URL: https://Latitudepay.com/
 * Text Domain: latitudepay-genoapay-integrations-for-woocommerce
 * Domain Path: /i18n/languages/
 * License: Apache-2.0
 *
 * @package LatitudeFinance
 */
// defined( 'ABSPATH' ) || exit;
define( 'WC_LATITUDEPAY_PATH', plugin_dir_path( __FILE__ ) );
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
		'<a href="">' . esc_html__( 'Docs', 'woocommerce-payment-gateway-latitudefinance' ) . '</a>',
		'<a href="">' . esc_html__( 'Support', 'woocommerce-payment-gateway-latitudefinance' ) . '</a>',
	);
	return array_merge( $plugin_links, $links );
}

add_action( 'plugins_loaded', 'wc_latitudepay_init', 11 );
