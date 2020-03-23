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
    /**
      * @var string
      * Debug mode display on checkout
      */
    const DEBUG_MODE_CHECKOUT = 'checkout';

    /**
     * @var string
     * Debug mode log to file and display on checkout
     */
    const DEBUG_MODE_BOTH = 'both';

    /**
     * @var string
     * Debug mode disabled
     */
    const DEBUG_MODE_OFF = 'off';

    /**
     * @var string
     * Debug mode log to file
     */
    const DEBUG_MODE_LOG = 'log';

    /**
     * @var MageBinary_BinaryPay_Method_Abstract
     */
    public $gateway;

    /**
     * @var integer
     */
    protected $min_order_total;

    /**
     * @var integer
     */
    protected $max_order_total;

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
        $this->min_order_total = $this->get_option('min_order_total');
        $this->max_order_total = $this->get_option('max_order_total');
        $this->add_hooks();
    }

    /**
     * is_payment_available
     */
    public function is_payment_available($gateways)
    {
        foreach ($gateways as $index => $gateway) {
            if ($gateway instanceof $this->gateway_class) {
                $orderTotal = WC()->cart->total;
                if ($orderTotal > $this->max_order_total && $this->max_order_total || $orderTotal < $this->min_order_total && !is_null($this->min_order_total)) {
                    unset($gateways[$index]);
                }
            }
        }
        return $gateways;
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

    /**
     * Adds the specific form field by passing the key position, and the array you wanted to be inserted
     *
     * @since 1.0.0
     * @param array $form_fields
     * @param string $key
     * @param array $value
     * @return array
     */
    protected function add_form_fields($form_fields, $key, $value) {
        $keys = array_keys($form_fields);
        $index = array_search($key, $keys);
        $pos = false === $index ? count($form_fields) : $index + 1;
        return array_merge(array_slice($form_fields, 0, $pos), $value, array_slice($form_fields, $pos));
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
     * getGateway
     *
     * Get specific Api
     * @param  string $gateway
     * @param  array $credential
     * @return Object
     */
    public function get_gateway()
    {
        $className = (isset(explode('_', $this->id)[1])) ? ucfirst(explode('_', $this->id)[1]) : ucfirst($this->id);
        $gateway = BinaryPay::getGateway($className, $this->get_credentials());
        return $gateway;
    }

    /**
     * retrieve PostPassword from database
     *
     * @param int $storeId
     *
     * @return string
     */
    public function get_credentials($key = null)
    {
        $credentials = array(
            'username'      => $this->get_option('username'),
            'password'      => $this->get_option('password'),
            'environment'   => $this->get_option('environment'),
            'accountId'     => $this->get_option('account_id')
        );
        return $credentials;
    }

    /**
     * Returns the environment setting, one of the $environments keys, ie
     * 'production'
     *
     * @since 1.0.0
     * @return string the configured environment id
     */
    public function get_environment() {
        return $this->environment;
    }

    public function get_environments(){
        return array(
            'development' => __('Development', 'magebinary-binarypay'),
            'sandbox'     => __('Sandbox', 'magebinary-binarypay'),
            'production'  => __('Production', 'magebinary-binarypay'),
        );
    }

    public function output_checkout_fields()
    {

    }

    /**
     * get_reference_number - get next order Id by last order id, to fix webpayment multiple increment id number bugs
     * @return Integer
     */
    protected function get_reference_number()
    {
        // return $reserveOrderId;
    }

    /**
     * Returns true if all debugging is disabled
     *
     * @since 1.0.0
     * @return boolean if all debuging is disabled
     */
    public function debug_off() {
        return self::DEBUG_MODE_OFF === $this->debug_mode;
    }

    /**
     * Get ID of the gateway
     * @since 1.0.0
     * @return integer
     */
    public function get_id()
    {
        return $this->id;
    }


    /**
     * Add all standard filters
     */
    public function add_hooks() {
        /**
         * This is line is important, we cannot save the options without this lane
         */
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(
                $this, 'process_admin_options'
        ));
        /**
         * Validate if the payment method available for the order or not
         */
        add_filter('woocommerce_available_payment_gateways', array(
                $this, 'is_payment_available'
        ), 10, 1);
    }
}

MageBinary_BinaryPay_Method_Abstract::init();