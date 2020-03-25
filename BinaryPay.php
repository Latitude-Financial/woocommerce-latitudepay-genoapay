<?php
/**
 * Plugin Name: BinaryPay
 * Plugin URI: https://binarypay.nz/
 * Description: An ecommerce payment solution
 * Version: 0.0.1
 * Author: MageBinary
 * Author URI: https://magebinary.com/
 * Text Domain: woocommerce-payment-gateway-magebinary-binarypay
 * Domain Path: /i18n/languages/
 *
 * @package MageBinary
 */

defined( 'ABSPATH' ) || exit;

define('WC_BINARYPAY_PATH', plugin_dir_path ( __FILE__ ));
define('WC_BINARYPAY_ASSETS', plugin_dir_url( __FILE__ ) . 'assets/');
define('WC_BINARYPAY_TEMPLATES', plugin_dir_url( __FILE__ ) . 'templates/');
define('WC_BINARYPAY_PLUGIN_NAME', plugin_basename ( __FILE__ ));

add_action('plugins_loaded', 'wc_binarypay_init', 11);
function wc_binarypay_init() {
    require_once(WC_BINARYPAY_PATH . 'includes/autoload.php');
    require_once(WC_BINARYPAY_PATH . 'includes/class-binarypay.php');
}