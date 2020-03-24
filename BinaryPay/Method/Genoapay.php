<?php
/**
* Magento BinaryPay Payment Extension
*
* NOTICE OF LICENSE
*
* Copyright 2020 MageBinary
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

if (!class_exists('MageBinary_BinaryPay_Method_Abstract')) {
    return;
}

class MageBinary_BinaryPay_Method_Genoapay extends MageBinary_BinaryPay_Method_Abstract
{
    /**
     * @var string
     */
    protected $gateway_class = 'MageBinary_BinaryPay_Method_Genoapay';

    /**
     * @var string
     */
    protected $return_url = '/api/v1/binarypay/return';

    public function __construct()
    {
        $this->id                   = MageBinary_BinaryPay_Model_Config::GENOAPAY;
        $this->template             = 'genoapay/info.php';
        $this->default_title        = __('GenoaPay', 'magebinary-binarypay');
        $this->order_button_text    = __('Place Order with GenoaPay', 'magebinary-binarypay');
        $this->method_title         = __('GenoaPay', 'magebinary-binarypay');
        $this->tab_title            = __('GenoaPay', 'magebinary-binarypay');
        $this->icon                 = WC_BINARYPAY_ASSETS . 'genoapay.svg';

        /**
         * The description will show in the backend
         */
        $this->method_description   = __('Available to NZ residents who are 18 years old and over and have a valid debit or credit card.', 'magebinary-binarypay');
        parent::__construct();
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
                'title'     => __('Enable/Disable', 'magebinary-binarypay'),
                'type'      => 'checkbox',
                'label'     => __('Enable', 'magebinary-binarypay'),
                'default'   => 'yes'
            ),
            'title' => array(
                'title'         => __('Title', 'magebinary-binarypay'),
                'type'          => 'text',
                'description'   => __('This controls the title which the user sees during checkout.', 'magebinary-binarypay'),
                'default'       => __('GenoaPay', 'magebinary-binarypay'),
                'desc_tip'      => true
            ),
            'description' => array(
                'title'         => __('Customer Message', 'magebinary-binarypay'),
                'type'          => 'textarea',
                'default'       => ''
            ),
            'min_order_total' => array(
                'title'     => __('Minimum Order Total', 'magebinary-binarypay'),
                'type'      => 'text',
                'default'   => ''
            ),
            'max_order_total' => array(
                'title'     => __('Maximum Order Total', 'magebinary-binarypay'),
                'type'      => 'text',
                'default'   => ''
            ),
            'debug_mode' => array(
                'title'   => esc_html__( 'Debug Mode', 'magebinary-binarypay' ),
                'type'    => 'select',
                /* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
                'desc'    => sprintf(esc_html__( 'Show Detailed Error Messages and API requests/responses on the checkout page and/or save them to the %1$sdebug log%2$s', 'magebinary-binarypay' ), '<a href="' . 'xxxxx' . '">', '</a>' ),
                'default' => self::DEBUG_MODE_OFF,
                'options' => array(
                    self::DEBUG_MODE_OFF      => esc_html__( 'Off', 'magebinary-binarypay' ),
                    self::DEBUG_MODE_CHECKOUT => esc_html__( 'Show on Checkout Page', 'magebinary-binarypay' ),
                    self::DEBUG_MODE_LOG      => esc_html__( 'Save to Log', 'magebinary-binarypay' ),
                    /* translators: show debugging information on both checkout page and in the log */
                    self::DEBUG_MODE_BOTH     => esc_html__( 'Both', 'magebinary-binarypay' )
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
                        'title'    => esc_html__('Environment', 'magebinary-binarypay'),
                        'type'     => 'select',
                        'default'  => key($this->get_environments()),  // default to first defined environment
                        'desc_tip' => esc_html__('Select the gateway environment to use for transactions.', 'magebinary-binarypay'),
                        'options'  => $this->get_environments(),
                    )
                )
            );
        }
    }

    /**
     * Returns an array of form fields specific for this method
     *
     * @since 3.0.0
     * @see SV_WC_Payment_Gateway::get_gateway_form_fields()
     * @return array of form fields
     */
    protected function get_gateway_form_fields() {

        return array(
            // merchant account ID per currency feature
            'merchant_account_id_title' => array(
                'title'       => __('Merchant Account Info', 'magebinary-binarypay'),
                'type'        => 'title',
                'description' => sprintf(
                    esc_html__( 'Enter additional merchant account IDs if you do not want to use your GenoaPay account default. %1$sLearn more about merchant account IDs%2$s', 'magebinary-binarypay' ),
                    '<a href="' . esc_url( "binarypay()->get_documentation_url()" ). '#merchant-account-ids' . '">', '&nbsp;&rarr;</a>'
                ),
            ),
            // production
            'public_key' => array(
                'title'    => __('Public Key', 'magebinary-binarypay'),
                'type'     => 'text',
                'class'    => 'environment-field production-field',
                'desc_tip' => __('The Public Key for your GenoaPay account.', 'magebinary-binarypay'),
            ),
            'private_key' => array(
                'title'    => __('Private Key', 'magebinary-binarypay'),
                'type'     => 'text',
                'class'    => 'environment-field production-field',
                'desc_tip' => __('The Private Key for your GenoaPay account.', 'magebinary-binarypay'),
            ),
            // sandbox
            'sandbox_public_key' => array(
                'title'    => __('Sandbox Public Key', 'magebinary-binarypay'),
                'type'     => 'text',
                'class'    => 'environment-field sandbox-field',
                'desc_tip' => __('The Public Key for your GenoaPay sandbox account.', 'magebinary-binarypay'),
            ),
            'sandbox_private_key' => array(
                'title'    => __('Sandbox Private Key', 'magebinary-binarypay'),
                'type'     => 'text',
                'class'    => 'environment-field sandbox-field',
                'desc_tip' => __('The Private Key for your GenoaPay sandbox account.', 'magebinary-binarypay'),
            ),
        );
    }

    public function add_hooks()
    {
        parent::add_hooks();
    }

    public function redirect_to_payment_page()
    {
    }

    public function get_purchase_url()
    {
        global $woocommerce;

        try {
            $gateway    = $this->get_gateway();

            $reference          = $this->get_reference_number();
            $amount             = $this->get_amount();

            $currencyCode       = $this->currency_code;
            $customer           = $woocommerce->cart->get_customer();
            $cart               = $woocommerce->cart;

            $payment = array(
                BinaryPay::REFERENCE                => (string) $reference,
                BinaryPay::AMOUNT                   => $amount,
                BinaryPay::CURRENCY                 => $currencyCode ?: self::DEFAULT_VALUE,
                BinaryPay::RETURN_URL               => $this->return_url,
                BinaryPay::MOBILENUMBER             => $customer->get_billing_phone() ?: '0210123456',
                BinaryPay::EMAIL                    => $customer->get_billing_email(),
                BinaryPay::FIRSTNAME                => $customer->get_billing_first_name() ?: self::DEFAULT_VALUE,
                BinaryPay::SURNAME                  => $customer->get_billing_last_name() ?: self::DEFAULT_VALUE,
                BinaryPay::SHIPPING_ADDRESS         => $customer->get_shipping_address() ?: self::DEFAULT_VALUE,
                BinaryPay::SHIPPING_COUNTRY_CODE    => $customer->get_shipping_country() ?: self::DEFAULT_VALUE,
                BinaryPay::SHIPPING_POSTCODE        => $customer->get_shipping_postcode() ?: self::DEFAULT_VALUE,
                BinaryPay::SHIPPING_SUBURB          => $customer->get_shipping_state() ?: self::DEFAULT_VALUE,
                BinaryPay::SHIPPING_CITY            => $customer->get_shipping_city() ?: self::DEFAULT_VALUE,
                BinaryPay::BILLING_ADDRESS          => $this->get_billing_address() ?: self::DEFAULT_VALUE,
                BinaryPay::BILLING_COUNTRY_CODE     => $customer->get_billing_country() ?: self::DEFAULT_VALUE,
                BinaryPay::BILLING_POSTCODE         => $customer->get_billing_postcode() ?: self::DEFAULT_VALUE,
                BinaryPay::BILLING_SUBURB           => $customer->get_billing_state() ?: self::DEFAULT_VALUE,
                BinaryPay::BILLING_CITY             => $customer->get_billing_city() ?: self::DEFAULT_VALUE,
                BinaryPay::TAX_AMOUNT               => array_sum($cart->get_taxes()),
                BinaryPay::PRODUCTS                 => $this->get_quote_products(),
                BinaryPay::SHIPPING_LINES           => [
                    $this->get_shipping_data()
                ]
            );

            echo '<pre>';
            print_r($payment);
            echo '</pre>';
            die();

            $response   = $gateway->purchase($payment);
            echo '<pre>';
            print_r($response);
            echo '</pre>';
            die();
            $this->setSesionReferense($reference, $response);

            $purchaseUrl = $this->_getPurchaseUrl($response);
        } catch (BinaryPay_Exception $e) {
            var_dump($e->getMessage());die();
            // Mage::throwException($e->getMessage());
        } catch (Exception $e) {
            $message = $e->getMessage() ?: 'Something massively went wrong. Please try again. If the problem still exists, please contact us';
            var_dump($e->getMessage());die();
            // Mage::throwException($message);
            // Mage::log($e->getTrace(), null, 'BinaryPay.log', true);
        }

        return $purchaseUrl;
    }

    /**
     * Process payment
     * After investigation this step is after the order has been placed
     * So we can handle the response then trigger this function
     */
    public function process_payment($order_id)
    {
        $order = wc_get_order( $order_id );
        // Mark as on-hold (we're awaiting the payment)
        $order->update_status('pending', __( 'Awaiting offline payment', 'wc-gateway-offline' ) );
        // Reduce stock levels
        $order->reduce_order_stock();
        // Remove cart
        WC()->cart->empty_cart();
        // Return thankyou redirect
        return array(
            'result'    => 'success',
            'redirect'  => $this->get_return_url($order)
        );
    }

    public function process_refund($order_id, $amount = null, $reason = '')
    {
    }
}