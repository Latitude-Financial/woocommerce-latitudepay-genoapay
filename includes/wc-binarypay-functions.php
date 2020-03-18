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