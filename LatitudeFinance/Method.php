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

defined( 'ABSPATH' ) || exit;

if (!class_exists('WC_Payment_Gateway')) {
    return;
}

abstract class MageBinary_BinaryPay_Method_Abstract extends WC_Payment_Gateway
{
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
     * @var array
     * An array which saved all the response we got from the payment provider
     */
    protected $request;

    /**
     * @var array
     * Configuration fetched from the Latitude finance API
     */
    protected $configuration = [];

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

        // Environment must be set before get the gateway object
        $this->environment     = $this->get_option('environment', self::ENVIRONMENT_DEVELOPMENT);
        if ($this->get_option('sandbox_public_key') &&  $this->get_option('sandbox_private_key') || $this->get_option('public_key') && $this->get_option('private_key')) {
            $this->configuration   = $this->get_gateway()->configuration();
        }
        $this->title           = $this->get_option('title', ucfirst(wc_latitudefinance_get_array_data('name', $this->configuration, $this->id)));
        $this->description     = $this->get_option('description', wc_latitudefinance_get_array_data('description', $this->configuration));
        $this->min_order_total = $this->get_option('min_order_total', wc_latitudefinance_get_array_data('minimumAmount', $this->configuration, 20));
        $this->max_order_total = $this->get_option('max_order_total', wc_latitudefinance_get_array_data('maximumAmount', $this->configuration, 1500));
        $this->currency_code   = get_woocommerce_currency();
        $this->credentials     = $this->get_credentials();

        $this->add_hooks();
    }

    /**
     * is_payment_available
     */
    public function is_payment_available($gateways)
    {
        if (is_checkout()) {
            foreach ($gateways as $index => $gateway) {
                if ($gateway instanceof $this->gateway_class) {
                    $orderTotal = WC()->cart->total;
                    if ($orderTotal > $this->max_order_total && $this->max_order_total || $orderTotal < $this->min_order_total && !is_null($this->min_order_total)) {
                        unset($gateways[$index]);
                    }
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

    /**
     * Payment field
     */
    public function payment_fields() {
        /**
         * Pass in gateway object
         */
        wc_latitudefinance_get_template('checkout/latitudefinance-payment-method.php', array(
            'gateway' => $this
        ));
    }

    /**
     * Initialize Gateway Settings Form Fields
     */
    public function init_form_fields()
    {
        /**
         * Display the following options as the backend settings
         */
        $this->form_fields = array(
            'enabled' => array(
                'title'     => __('Enable/Disable', 'woocommerce-payment-gateway-latitudefinance'),
                'type'      => 'checkbox',
                'label'     => __('Enable', 'woocommerce-payment-gateway-latitudefinance'),
                'default'   => 'yes'
            ),
            'title' => array(
                'title'         => __('Title', 'woocommerce-payment-gateway-latitudefinance'),
                'type'          => 'text',
                'description'   => __('This controls the title which the user sees during checkout.', 'woocommerce-payment-gateway-latitudefinance'),
                'default'       => __('GenoaPay', 'woocommerce-payment-gateway-latitudefinance'),
                'desc_tip'      => true
            ),
            'description' => array(
                'title'         => __('Customer Message', 'woocommerce-payment-gateway-latitudefinance'),
                'type'          => 'textarea',
                'default'       => __($this->description, 'woocommerce-payment-gateway-latitudefinance'),
                'value'         => __($this->description, 'woocommerce-payment-gateway-latitudefinance'),
                'readonly'     => true
            ),
            'min_order_total' => array(
                'title'     => __('Minimum Order Total', 'woocommerce-payment-gateway-latitudefinance'),
                'type'      => 'text',
                'value'     => $this->min_order_total,
                'default'   => $this->min_order_total,
                'readonly'  => true
            ),
            'max_order_total' => array(
                'title'     => __('Maximum Order Total', 'woocommerce-payment-gateway-latitudefinance'),
                'type'      => 'text',
                'value'     => $this->max_order_total,
                'default'   => $this->max_order_total
            ),
            'debug_mode' => array(
                'title'   => esc_html__('Debug Mode', 'woocommerce-payment-gateway-latitudefinance'),
                'type'    => 'select',
                /* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
                'desc'    => sprintf(esc_html__('Show Detailed Error Messages and API requests/responses on the checkout page and/or save them to the %1$sdebug log%2$s', 'woocommerce-payment-gateway-latitudefinance' ), '<a href="' . 'xxxxx' . '">', '</a>'),
                'default' => self::DEBUG_MODE_OFF,
                'options' => array(
                    self::DEBUG_MODE_OFF      => esc_html__('Off', 'woocommerce-payment-gateway-latitudefinance'),
                    self::DEBUG_MODE_LOG      => esc_html__('Save to Log', 'woocommerce-payment-gateway-latitudefinance'),
                )
            )
        );

        // add unique method fields added by concrete gateway class
        $gateway_form_fields = $this->get_gateway_form_fields();
        $this->form_fields = array_merge( $this->form_fields, $gateway_form_fields );

        if (count($this->get_environments()) > 1) {
            $this->form_fields = $this->add_form_fields($this->form_fields, 'description', array(
                    'environment' => array(
                        /* translators: environment as in a software environment (test/production) */
                        'title'    => esc_html__('Environment', 'woocommerce-payment-gateway-latitudefinance'),
                        'type'     => 'select',
                        'default'  => self::ENVIRONMENT_SANDBOX,  // default to first defined environment
                        'desc_tip' => esc_html__('Select the gateway environment to use for transactions.', 'woocommerce-payment-gateway-latitudefinance'),
                        'options'  => $this->get_environments(),
                    )
                )
            );
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
            case self::ENVIRONMENT_SANDBOX:
            case self::ENVIRONMENT_DEVELOPMENT:
                $public_key = $this->get_option('sandbox_public_key');
                $private_key = $this->get_option('sandbox_private_key');
                break;
            case self::ENVIRONMENT_PRODUCTION:
                $public_key = $this->get_option('public_key');
                $private_key = $this->get_option('private_key');
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
     * Returns an array of form fields specific for this method
     *
     * @since 1.0.0
     * @return array of form fields
     */
    protected function get_gateway_form_fields() {
        return array(
            // merchant account ID per currency feature
            'merchant_account_id_title' => array(
                'title'       => __('Merchant Account Info', 'woocommerce-payment-gateway-latitudefinance'),
                'type'        => 'title',
                'description' => sprintf(
                    esc_html__('Enter additional merchant account IDs if you do not want to use your GenoaPay account default. %1$sLearn more about merchant account IDs%2$s', 'woocommerce-payment-gateway-latitudefinance' ),
                    '<a href="' . esc_url( "latitudefinance()->get_documentation_url()" ). '#merchant-account-ids' . '">', '&nbsp;&rarr;</a>'
                ),
            ),
            // production
            'public_key' => array(
                'title'    => __('Public Key', 'woocommerce-payment-gateway-latitudefinance'),
                'type'     => 'text',
                'class'    => 'environment-field production-field',
                'desc_tip' => __('The Public Key for your GenoaPay account.', 'woocommerce-payment-gateway-latitudefinance'),
            ),
            'private_key' => array(
                'title'    => __('Private Key', 'woocommerce-payment-gateway-latitudefinance'),
                'type'     => 'text',
                'class'    => 'environment-field production-field',
                'desc_tip' => __('The Private Key for your GenoaPay account.', 'woocommerce-payment-gateway-latitudefinance'),
            ),
            // sandbox
            'sandbox_public_key' => array(
                'title'    => __('Sandbox Public Key', 'woocommerce-payment-gateway-latitudefinance'),
                'type'     => 'text',
                'class'    => 'environment-field sandbox-field',
                'desc_tip' => __('The Public Key for your GenoaPay sandbox account.', 'woocommerce-payment-gateway-latitudefinance'),
            ),
            'sandbox_private_key' => array(
                'title'    => __('Sandbox Private Key', 'woocommerce-payment-gateway-latitudefinance'),
                'type'     => 'text',
                'class'    => 'environment-field sandbox-field',
                'desc_tip' => __('The Private Key for your GenoaPay sandbox account.', 'woocommerce-payment-gateway-latitudefinance'),
            ),
        );
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
            self::ENVIRONMENT_DEVELOPMENT => __('Development', 'woocommerce-payment-gateway-latitudefinance'),
            self::ENVIRONMENT_SANDBOX     => __('Sandbox', 'woocommerce-payment-gateway-latitudefinance'),
            self::ENVIRONMENT_PRODUCTION  => __('Production', 'woocommerce-payment-gateway-latitudefinance'),
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
            if (!isset($_item['data'])) {
                throw new Exception('"data" must be defined in the item array.');
            }
            $_product = ($_item['data'] instanceof WC_Product_Simple) ? $_item['data']->get_data() : $_item['data']->get_parent_data();
            $product_price = ($isTaxIncluded) ? wc_get_price_including_tax($_item['data']) : wc_get_price_excluding_tax($_item['data']);
            $product_line_item = [
                'name'          => wc_latitudefinance_get_array_data('title', $_product) ?: wc_latitudefinance_get_array_data('name', $_product),
                'price' => [
                    'amount'    => $product_price,
                    'currency'  => $this->currency_code
                ],
                'sku'           => wc_latitudefinance_get_array_data('sku', $_product),
                'quantity'      => wc_latitudefinance_get_array_data('quantity', $_item, 0),
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
     * This hook will only be called when the gateway object has been initialized
     */
    public function add_hooks()
    {
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
         * Unified the this with the product page CSS
         * Include extra CSS and Javascript files
         */
        // add_action('wp_enqueue_scripts', array($this, 'include_extra_scripts'));
    }
}