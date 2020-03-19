<?php
/**
 *
 * @since 1.0.0
 * @package BinaryPay/Functions
 * @param array $gateways
 */
function wc_binarypay_payment_gateways($gateways) {
    return array_merge($gateways, binarypay()->get_payment_gateways());
}

/**
 *
 * @since 1.0.0
 * @package BinaryPay/Functions
 * @todo Implement the way to prevent spam bot
 */
function wc_binarypay_spam_bot_field() {
    echo('<input type="checkbox" value="1" name="braintree_customer_email" style="display: none" autocomplete="off" tabindex="-1"/>');
}

/**
 *
 * @since 1.0.0
 * @package BinaryPay/Functions
 * @param string $template_name
 * @param array $args
 */
function wc_binarypay_get_template($template_name, $args = array()) {
    return wc_get_template($template_name, $args, binarypay()->template_path(), binarypay()->plugin_path() . 'templates/');
}

/**
 *
 * @since 1.0.0
 * @package BinaryPay/Functions
 * @param string $key
 * @param string $class
 */
function wc_binarypay_hidden_field($key, $class = '') {
    printf('<input type="hidden" id="%1$s" name="%1$s" class="%2$s"/>', $key, $class);
}

/**
 * Echo an input field for a binarypay payment_method_nonce.
 *
 * @since 1.0.0
 * @package BinaryPay/Functions
 * @param MageBinary_BinaryPay_Method_Abstract $gateway
 */
function wc_binarypay_nonce_field($gateway, $value = '') {
    wc_binarypay_hidden_field($gateway->nonce_key, 'wc-binarypay-payment-nonce');
}

/**
 *
 * @since 1.0.0
 * @package BinaryPay/Functions
 * @param MageBinary_BinaryPay_Method_Abstract $gateway
 */
function wc_binarypay_device_data_field($gateway) {
    wc_binarypay_hidden_field($gateway->device_data_key, 'wc-binarypay-device-data');
}