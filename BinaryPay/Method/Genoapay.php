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

    // public function output_checkout_fields()
    // {
    // }

    public function add_hooks()
    {
        parent::add_hooks();
    }

    public function redirect_to_payment_page()
    {
    }

    public function get_purchase_url()
    {
        try {
            $gateway    = $this->get_gateway();

            $reference          = $this->get_reference_number();
            $amount             = $this->getAmount($isBackend);

            $billingAddress     = $this->_getQuote()->getBillingAddress();
            $shippingAddress    = $this->_getQuote()->getShippingAddress();

            $payment = array(
                BinaryPay::REFERENCE                => (string) $reference,
                BinaryPay::AMOUNT                   => $amount,
                BinaryPay::CURRENCY                 => $this->getCurrencyCode() ?: self::DEFAULT_VALUE,
                BinaryPay::RETURN_URL               => Mage::getUrl($this->_returnUrl),
                BinaryPay::MOBILENUMBER             => $billingAddress->getTelephone() ?: '0210123456',
                BinaryPay::EMAIL                    => $this->_getCustomerEmail() ?: self::DEFAULT_VALUE,
                BinaryPay::FIRSTNAME                => $this->_getFirstName() ?: $billingAddress->getFirstname(),
                BinaryPay::SURNAME                  => $this->_getSurname() ?: $billingAddress->getLastname(),
                BinaryPay::SHIPPING_ADDRESS         => current($shippingAddress->getStreet()) ?: self::DEFAULT_VALUE,
                BinaryPay::SHIPPING_COUNTRY_CODE    => $shippingAddress->getCountryId() ?: self::DEFAULT_VALUE,
                BinaryPay::SHIPPING_POSTCODE        => $shippingAddress->getPostcode() ?: self::DEFAULT_VALUE,
                BinaryPay::SHIPPING_SUBURB          => $shippingAddress->getRegion() ?: self::DEFAULT_VALUE,
                BinaryPay::SHIPPING_CITY            => $shippingAddress->getCity() ?: self::DEFAULT_VALUE,
                BinaryPay::BILLING_ADDRESS          => current($billingAddress->getStreet()) ?: self::DEFAULT_VALUE,
                BinaryPay::BILLING_COUNTRY_CODE     => $billingAddress->getCountryId() ?: self::DEFAULT_VALUE,
                BinaryPay::BILLING_POSTCODE         => $billingAddress->getPostcode() ?: self::DEFAULT_VALUE,
                BinaryPay::BILLING_SUBURB           => $billingAddress->getRegion() ?: self::DEFAULT_VALUE,
                BinaryPay::BILLING_CITY             => $billingAddress->getCity() ?: self::DEFAULT_VALUE,
                BinaryPay::TAX_AMOUNT               => $shippingAddress->getQuote()->getShippingAddress()->getData('tax_amount'),
                BinaryPay::PRODUCTS                 => $this->_getQuoteProducts(),
                BinaryPay::SHIPPING_LINES           => [
                    $this->_getShippingData()
                ]
            );

            $response   = $gateway->purchase($payment);

            $this->setSesionReferense($reference, $response);

            // Mage::getSingleton('checkout/session')->setGenoapayReference($reference);
            // Mage::getSingleton('checkout/session')->setGenoapayToken($response['token']);

            $purchaseUrl = $this->_getPurchaseUrl($response);
        } catch (BinaryPay_Exception $e) {
            Mage::throwException($e->getMessage());
        } catch (Exception $e) {
            $message = $e->getMessage() ?: 'Something massively went wrong. Please try again. If the problem still exists, please contact us';
            Mage::throwException($message);
            Mage::log($e->getTrace(), null, 'BinaryPay.log', true);
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