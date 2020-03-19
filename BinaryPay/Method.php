<?php
/**
* Magento BinaryPay Payment Extension
*
* NOTICE OF LICENSE
*
* Copyright 2017 MageBinary
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
* @category    MageBinary
* @package     MageBinary_BinaryPay
* @author      MageBinary Team
* @copyright   Copyright (c) 2017 - 2020 MageBinary (http://www.magebinary.com)
* @license     http://www.apache.org/licenses/LICENSE-2.0
*/

if (!class_exists('WC_Payment_Gateway')) {
    return;
}

abstract class MageBinary_BinaryPay_Method_Abstract extends WC_Payment_Gateway
{
    public $gateway;

    const WC_BINARYPAY_SPAM_COUNT = 0;

    public function __construct()
    {
        $this->has_fields = true;
        $this->nonce_key = $this->id . '_nonce_key';
        $this->token_key = $this->id . '_token_key';
        $this->device_data_key = $this->id . '_device_data';
        $this->save_method_key = $this->id . '_save_method';
        $this->payment_type_key = $this->id . '_payment_type';
        $this->config_key = $this->id . '_config_data';

        $this->init_form_fields();
        $this->init_settings();
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->add_hooks();
    }

    public static function init()
    {
        add_filter('woocommerce_available_payment_gateways', array(
            __CLASS__, 'available_payment_gateways'
        ));
    }

    public static function available_payment_gateways($gateways) {
        global $wp;
        if (is_add_payment_method_page() && isset($wp->query_vars['add-payment-method'])) {
            // unset($gateways['braintree_paypal']);
        }
        return $gateways;
    }

    public function payment_fields() {
        // $this->enqueue_frontend_scripts ( binarypay()->frontend_scripts );
        if (is_checkout() || $this->is_change_payment_request()) {
            $user = wp_get_current_user();
            $methods = $this->get_tokens();
            wc_binarypay_get_template('checkout/binarypay-payment-method.php', array(
                    'methods' => $methods,
                    'has_methods' => (bool) $methods,
                    'gateway' => $this
            ) );
        } else {
            wc_binarypay_get_template('checkout/binarypay-payment-method.php', array(
                    'methods' => array(),
                    'has_methods' => false,
                    'gateway' => $this
            ) );
        }
    }

    /**
     *
     * @param WC_Braintree_Frontend_Scripts $scripts
     */
    public function enqueue_frontend_scripts($scripts) {
        global $wp;
        if (is_checkout() && !is_order_received_page()) {
            $this->enqueue_checkout_scripts($scripts);
        }

        if (is_add_payment_method_page() && ! isset($wp->query_vars[ 'payment-methods' ])) {
            $this->enqueue_add_payment_method_scripts($scripts);
        }

        if (is_cart()) {
            $this->enqueue_cart_scripts($scripts);
        }

        if (is_product()) {
            $this->enqueue_product_scripts($scripts);
        }
    }

    /**
     * Add all standard filters
     */
    public function add_hooks() {
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(
                $this, 'process_admin_options'
        ));
    }
}

MageBinary_BinaryPay_Method_Abstract::init();