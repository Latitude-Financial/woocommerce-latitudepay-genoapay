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
     * @var string
     */
    const DEFAULT_VALUE = 'NO_VALUE';

    /**
     * @var string
     */
    const ENVIRONMENT_SANDBOX = 'sandbox';

    /**
     * @var string
     */
    const ENVIRONMENT_PRODUCTION = 'production';

    /**
     * @var string
     */
    const ENVIRONMENT_DEVELOPMENT = 'development';

    /**
     * @var string
     */
    const PENDING_ORDER_STATUS = 'pending';

    /**
     * @var string
     */
    const PROCESSING_ORDER_STATUS = 'processing';

    /**
     * @var string
     */
    const FAILED_ORDER_STATUS = 'failed';

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

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    protected $currency_code;

    /**
     * @var string
     */
    protected $order_comment;

    /**
     * @var string
     * Woocommerce called tokens, this is for different purpose
     */
    protected $token;

    /**
     * @var Varien_Object
     * An object which saved all the response we got from the payment provider
     */
    protected $request;

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
        $this->min_order_total = $this->get_option('min_order_total', 200);
        $this->max_order_total = $this->get_option('max_order_total');
        $this->environment     = $this->get_option('environment');
        $this->currency_code   = get_woocommerce_currency();
        $this->credentials     = $this->get_credentials();

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
    public function get_credentials()
    {
        $public_key = $private_key = '';
        switch ($this->environment) {
            case MageBinary_BinaryPay_Method_Abstract::ENVIRONMENT_SANDBOX:
            case MageBinary_BinaryPay_Method_Abstract::ENVIRONMENT_DEVELOPMENT:
                $public_key = $this->get_option('sandbox_public_key');
                $private_key = $this->get_option('sandbox_private_key');
                break;
            case MageBinary_BinaryPay_Method_Abstract::ENVIRONMENT_PRODUCTION:
                $public_key = $this->get_option('sandbox_public_key');
                $private_key = $this->get_option('sandbox_private_key');
                break;
            default:
                throw new Exception('xxxxxxx');
                break;
        }

        $credentials = array(
            'username'      => $public_key,
            'password'      => $private_key,
            'environment'   => $this->environment,
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
            self::ENVIRONMENT_DEVELOPMENT => __('Development', 'woocommerce-payment-gateway-magebinary-binarypay'),
            self::ENVIRONMENT_SANDBOX     => __('Sandbox', 'woocommerce-payment-gateway-magebinary-binarypay'),
            self::ENVIRONMENT_PRODUCTION  => __('Production', 'woocommerce-payment-gateway-magebinary-binarypay'),
        );
    }

    public function output_checkout_fields()
    {

    }

    /**
     * get_reference_number - get next order Id by last order id, to fix webpayment multiple increment id number bugs
     * @return integer
     */
    protected function get_reference_number()
    {
        $session = $this->get_checkout_session();
        $order_id = $session->get('order_id');

        if (empty($order_id)) {
            throw new WP_Error('Cannot identify the current order id number to process the payment.');
        }

        return $order_id;
    }

    /**
     * get_amount - get amount of the current quote
     */
    protected function get_amount()
    {
        $amount = WC()->cart->total;
        return $amount;
    }

    /**
     * _getQuoteProducts
     * @return array
     */
    protected function get_quote_products()
    {
        $items = WC()->cart->get_cart();
        $isTaxIncluded = WC()->cart->display_prices_including_tax();

        $products = [];
        foreach ($items as $_item) {
            $_item = new Varien_Object($_item);
            $_product = ($_item['data'] instanceof WC_Product_Simple) ? $_item['data']->get_data() : $_item['data']->get_parent_data();
            $_product = new Varien_Object($_product);
            $product_price = ($isTaxIncluded) ? wc_get_price_including_tax($_item['data']) : wc_get_price_excluding_tax($_item['data']);
            $product_line_item = [
                'name'          => $_product->getData('title') ?: $_product->getData('name'),
                'price' => [
                    'amount'    => $product_price,
                    'currency'  => $this->currency_code
                ],
                'sku'           => $_product->getData('sku'),
                'quantity'      => $_item->getData('quantity'),
                'taxIncluded'   => (int) $isTaxIncluded
            ];
            array_push($products, $product_line_item);
        }
        return $products;
    }

    protected function get_billing_address()
    {
        global $woocommerce;
        $address = $woocommerce->cart->get_customer()->get_billing_address();
        $address2 = $woocommerce->cart->get_customer()->get_billing_address_2();

        if ($address2) {
            $address .= ', ' . $address2;
        }

        return $address;
    }

    /**
     * get_carrier_method_name
     * @return string shipping_method
     */
    protected function get_carrier_method_name()
    {
        $shipping_method = current(WC()->session->get('chosen_shipping_methods'));
        switch ($shipping_method) {
            case 'flat_rate:1':
                $shipping_method = "flatrate";
                break;
            default:
                # code...
                break;
        }
        return $shipping_method;
    }

    /**
     * get_shipping_data
     * @return array
     */
    protected function get_shipping_data()
    {
        $shippingDetail = [
            'carrier' => $this->get_carrier_method_name() ?: self::DEFAULT_VALUE,
            'price' => [
                'amount' => WC()->cart->get_shipping_total(),
                'currency' => $this->currency_code
            ],
            'taxIncluded' => 0
        ];
        return $shippingDetail;
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
     * get_checkout_session
     */
    public function get_checkout_session()
    {
        return WC()->session;
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

        /**
         * Include extra CSS and Javascript files
         */
        add_action('wp_enqueue_scripts', array($this, 'include_extra_scripts'));
    }

    public function include_extra_scripts() {
        return;
    }
}