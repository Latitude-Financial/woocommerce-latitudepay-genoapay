<?php
/**
 * Plugin Name: LatitudeFinance
 * Plugin URI: https://www.latitudefinancial.com.au/
 * Description: An online ecommerce payment solution
 * Version: 0.0.1
 * Author: MageBinary
 * Author URI: https://magebinary.com/
 * Text Domain: woocommerce-payment-gateway-latitudefinance
 * Domain Path: /i18n/languages/
 * License: Apache-2.0
 *
 * @package LatitudeFinance
 */
defined( 'ABSPATH' ) || exit;
define('WC_LATITUDEPAY_PATH', plugin_dir_path ( __FILE__ ));
define('WC_LATITUDEPAY_ASSETS', plugin_dir_url( __FILE__ ) . 'assets/');
define('WC_LATITUDEPAY_TEMPLATES', plugin_dir_url( __FILE__ ) . 'templates/');
define('WC_LATITUDEPAY_PLUGIN_NAME', plugin_basename ( __FILE__ ));

add_action('plugins_loaded', 'wc_latitudepay_init', 11);

function wc_latitudepay_init()
{
    require_once(WC_LATITUDEPAY_PATH . 'includes/autoload.php');
}