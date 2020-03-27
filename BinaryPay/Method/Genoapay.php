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
    protected $return_url = '?wc-api=genoapay_return_action';

    /**
     * @var string
     */
    protected $gateway_class = 'MageBinary_BinaryPay_Method_Genoapay';

    /**
     * @var string
     */
    protected $order_status = self::PENDING_ORDER_STATUS;

    /**
     * @var string
     */
    protected $return_action_name = 'genoapay_return_action';

    public function __construct()
    {
        $this->id                   = MageBinary_BinaryPay_Model_Config::GENOAPAY;
        $this->template             = 'genoapay/info.php';
        $this->default_title        = __('GenoaPay', 'woocommerce-payment-gateway-magebinary-binarypay');
        $this->order_button_text    = __('Place Order with GenoaPay', 'woocommerce-payment-gateway-magebinary-binarypay');
        $this->method_title         = __('GenoaPay', 'woocommerce-payment-gateway-magebinary-binarypay');
        $this->tab_title            = __('GenoaPay', 'woocommerce-payment-gateway-magebinary-binarypay');
        $this->icon                 = WC_BINARYPAY_ASSETS . 'genoapay.svg';

        /**
          * Allow refund and purchase product action
          */
        $this->supports             = array('products', 'refunds');

        /**
         * The description will show in the backend
         */
        $this->method_description   = __('Available to NZ residents who are 18 years old and over and have a valid debit or credit card.', 'woocommerce-payment-gateway-magebinary-binarypay');
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
                'title'     => __('Enable/Disable', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'type'      => 'checkbox',
                'label'     => __('Enable', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'default'   => 'yes'
            ),
            'title' => array(
                'title'         => __('Title', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'type'          => 'text',
                'description'   => __('This controls the title which the user sees during checkout.', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'default'       => __('GenoaPay', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'desc_tip'      => true
            ),
            'description' => array(
                'title'         => __('Customer Message', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'type'          => 'textarea',
                'default'       => ''
            ),
            'min_order_total' => array(
                'title'     => __('Minimum Order Total', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'type'      => 'text',
                'default'   => '200'
            ),
            'max_order_total' => array(
                'title'     => __('Maximum Order Total', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'type'      => 'text',
                'default'   => ''
            ),
            'debug_mode' => array(
                'title'   => esc_html__('Debug Mode', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'type'    => 'select',
                /* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
                'desc'    => sprintf(esc_html__('Show Detailed Error Messages and API requests/responses on the checkout page and/or save them to the %1$sdebug log%2$s', 'woocommerce-payment-gateway-magebinary-binarypay' ), '<a href="' . 'xxxxx' . '">', '</a>'),
                'default' => self::DEBUG_MODE_OFF,
                'options' => array(
                    self::DEBUG_MODE_OFF      => esc_html__('Off', 'woocommerce-payment-gateway-magebinary-binarypay'),
                    self::DEBUG_MODE_CHECKOUT => esc_html__('Show on Checkout Page', 'woocommerce-payment-gateway-magebinary-binarypay' ),
                    self::DEBUG_MODE_LOG      => esc_html__('Save to Log', 'woocommerce-payment-gateway-magebinary-binarypay'),
                    /* translators: show debugging information on both checkout page and in the log */
                    self::DEBUG_MODE_BOTH     => esc_html__('Both', 'woocommerce-payment-gateway-magebinary-binarypay')
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
                        'title'    => esc_html__('Environment', 'woocommerce-payment-gateway-magebinary-binarypay'),
                        'type'     => 'select',
                        'default'  => key($this->get_environments()),  // default to first defined environment
                        'desc_tip' => esc_html__('Select the gateway environment to use for transactions.', 'woocommerce-payment-gateway-magebinary-binarypay'),
                        'options'  => $this->get_environments(),
                    )
                )
            );
        }
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
                'title'       => __('Merchant Account Info', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'type'        => 'title',
                'description' => sprintf(
                    esc_html__('Enter additional merchant account IDs if you do not want to use your GenoaPay account default. %1$sLearn more about merchant account IDs%2$s', 'woocommerce-payment-gateway-magebinary-binarypay' ),
                    '<a href="' . esc_url( "binarypay()->get_documentation_url()" ). '#merchant-account-ids' . '">', '&nbsp;&rarr;</a>'
                ),
            ),
            // production
            'public_key' => array(
                'title'    => __('Public Key', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'type'     => 'text',
                'class'    => 'environment-field production-field',
                'desc_tip' => __('The Public Key for your GenoaPay account.', 'woocommerce-payment-gateway-magebinary-binarypay'),
            ),
            'private_key' => array(
                'title'    => __('Private Key', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'type'     => 'text',
                'class'    => 'environment-field production-field',
                'desc_tip' => __('The Private Key for your GenoaPay account.', 'woocommerce-payment-gateway-magebinary-binarypay'),
            ),
            // sandbox
            'sandbox_public_key' => array(
                'title'    => __('Sandbox Public Key', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'type'     => 'text',
                'class'    => 'environment-field sandbox-field',
                'desc_tip' => __('The Public Key for your GenoaPay sandbox account.', 'woocommerce-payment-gateway-magebinary-binarypay'),
            ),
            'sandbox_private_key' => array(
                'title'    => __('Sandbox Private Key', 'woocommerce-payment-gateway-magebinary-binarypay'),
                'type'     => 'text',
                'class'    => 'environment-field sandbox-field',
                'desc_tip' => __('The Private Key for your GenoaPay sandbox account.', 'woocommerce-payment-gateway-magebinary-binarypay'),
            ),
        );
    }

    /**
     * Inlcude extra stylesheet and scripts for current payment gateway
     */
    public function include_extra_scripts()
    {
        wp_register_style('woocommerce-payment-gateway-magebinary-binarypay', plugins_url('woocommerce-payment-gateway-magebinary-binarypay/assets/css/genoapay/popup.css'));
        wp_enqueue_style('woocommerce-payment-gateway-magebinary-binarypay');

        parent::include_extra_scripts();
    }

    public function redirect_to_payment_page()
    {
    }

    public function return_action()
    {
        $request = new Varien_Object($_GET);
        // save request
        $this->request = $request;
        try {
            // process the order depends on the request
            $this->validate_response()
                 ->process_response()
                 ->process_order();
        } catch (BinaryPay_Exception $e) {
            wc_add_notice($e->getMessage(), 'error', $request->getData());
            wp_redirect($this->redirect_url);
        }catch (InvalidArgumentException $e) {
            wc_add_notice($e->getMessage(), 'error', $request->getData());
            wp_redirect($this->redirect_url);
        }
    }

    /**
     * validate_response
     */
    public function validate_response()
    {
        $request = $this->request;

        if (!$request) {
            throw new InvalidArgumentException(__('Request object cannot be empty!', 'woocommerce-payment-gateway-magebinary-binarypay'));
        }

        $session = $this->get_checkout_session();
        $token = $session->get('purchase_token');
        // Unset session after use
        $session->set('purchase_token', null);

        if (!$this->return_action_name || $this->return_action_name !== $request->getData('wc-api')) {
            throw new BinaryPay_Exception(__('The return action handler is not valid for the request.', 'woocommerce-payment-gateway-magebinary-binarypay'));
        }

        if (!$token || $request->getData('token') !== $token) {
            $this->redirect_url = WC()->cart->get_cart_url();
            $session->set('order_id', null);
            /**
             * @todo If debug then output the request in to the log file
             */
            throw new BinaryPay_Exception(__("You are not allowed to access the return handler directly. If you want to know more about this error message, please contact the us.",'woocommerce-payment-gateway-magebinary-binarypay'));
        }
        return $this;
    }

    /**
     * process_response
     */
    protected function process_response()
    {
        $request = $this->request;
        $result = $request->getData('result');
        switch ($result) {
            case BinaryPay_Variable::STATUS_COMPLETED:
                $this->order_status = self::PROCESSING_ORDER_STATUS;
                $this->order_comment = __('Payment received', 'woocommerce-payment-gateway-magebinary-binarypay');
                // send invoice email
                break;
            case BinaryPay_Variable::STATUS_UNKNOWN:
                $this->order_status = self::FAILED_ORDER_STATUS;
                $this->order_comment = __('Payment failed', 'woocommerce-payment-gateway-magebinary-binarypay');
                break;
            default:
                # code...
                break;
        }
        return $this;
    }

    /**
     * process_order
     */
    protected function process_order()
    {
        $order_id = $this->get_checkout_session()->get('order_id');

        // If the order id in the session has been cleared out, then do nothing to update the order
        if (!$order_id) {
            return;
        }
        // order object
        $order = wc_get_order($order_id);
        $token = $this->request->getData('token');

        // Mark as on-hold (we're awaiting the payment)
        $order->update_status($this->order_status, $this->order_comment);
        // Reduce stock levels
        $order->reduce_order_stock();
        $order->set_transaction_id($token);
        $order->save();
        // Remove cart
        WC()->cart->empty_cart();
        // Redirect
        wp_redirect($this->get_return_url($order));
    }

    /**
     * add_hooks
     */
    public function add_hooks()
    {
        // Add hook to handle the response from remote API
        add_action('woocommerce_api_' . $this->id . '_return_action', array($this, 'return_action'));

        // Execture parent hooks
        parent::add_hooks();
    }

    public function get_purchase_url()
    {
        try {
            $session    = $this->get_checkout_session();
            $gateway    = $this->get_gateway();
            $reference  = $this->get_reference_number();
            $amount     = $this->get_amount();
            $cart       = WC()->cart;
            $customer   = $cart->get_customer();

            $payment = array(
                BinaryPay_Variable::REFERENCE                => (string) $reference,
                BinaryPay_Variable::AMOUNT                   => $amount,
                BinaryPay_Variable::CURRENCY                 => $this->currency_code ?: self::DEFAULT_VALUE,
                BinaryPay_Variable::RETURN_URL               => home_url() . $this->return_url,
                BinaryPay_Variable::MOBILENUMBER             => $customer->get_billing_phone() ?: '0210123456',
                BinaryPay_Variable::EMAIL                    => $customer->get_billing_email(),
                BinaryPay_Variable::FIRSTNAME                => $customer->get_billing_first_name() ?: self::DEFAULT_VALUE,
                BinaryPay_Variable::SURNAME                  => $customer->get_billing_last_name() ?: self::DEFAULT_VALUE,
                BinaryPay_Variable::SHIPPING_ADDRESS         => $customer->get_shipping_address() ?: self::DEFAULT_VALUE,
                BinaryPay_Variable::SHIPPING_COUNTRY_CODE    => $customer->get_shipping_country() ?: self::DEFAULT_VALUE,
                BinaryPay_Variable::SHIPPING_POSTCODE        => $customer->get_shipping_postcode() ?: self::DEFAULT_VALUE,
                BinaryPay_Variable::SHIPPING_SUBURB          => $customer->get_shipping_state() ?: self::DEFAULT_VALUE,
                BinaryPay_Variable::SHIPPING_CITY            => $customer->get_shipping_city() ?: self::DEFAULT_VALUE,
                BinaryPay_Variable::BILLING_ADDRESS          => $this->get_billing_address() ?: self::DEFAULT_VALUE,
                BinaryPay_Variable::BILLING_COUNTRY_CODE     => $customer->get_billing_country() ?: self::DEFAULT_VALUE,
                BinaryPay_Variable::BILLING_POSTCODE         => $customer->get_billing_postcode() ?: self::DEFAULT_VALUE,
                BinaryPay_Variable::BILLING_SUBURB           => $customer->get_billing_state() ?: self::DEFAULT_VALUE,
                BinaryPay_Variable::BILLING_CITY             => $customer->get_billing_city() ?: self::DEFAULT_VALUE,
                BinaryPay_Variable::TAX_AMOUNT               => array_sum($cart->get_taxes()),
                BinaryPay_Variable::PRODUCTS                 => $this->get_quote_products(),
                BinaryPay_Variable::SHIPPING_LINES           => [
                    $this->get_shipping_data()
                ]
            );

            $response       = $gateway->purchase($payment);
            $responseObject = new Varien_Object($response);
            $purchaseUrl    = $responseObject->getData('paymentUrl');
            // Save token into the session
            $this->get_checkout_session()->set('purchase_token', $responseObject->getData('token'));
        } catch (BinaryPay_Exception $e) {
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            $message = $e->getMessage() ?: 'Something massively went wrong. Please try again. If the problem still exists, please contact us';
            throw new Exception($message);
        }
        return $purchaseUrl;
    }

    /**
     * process_refund
     */
    public function process_refund($order_id, $amount = null, $reason = '')
    {
        $gateway = $this->get_gateway();
        $order = wc_get_order($order_id);
        $transaction_id = $order->get_transaction_id();

        /**
         * @todo support to add refund reason via wordpress backend
         */
        $refund = array(
             BinaryPay_Variable::PURCHASE_TOKEN  => $transaction_id,
             BinaryPay_Variable::CURRENCY        => $this->currency_code,
             BinaryPay_Variable::AMOUNT          => $amount,
             BinaryPay_Variable::REFERENCE       => $order->get_id(),
             BinaryPay_Variable::REASON          => '',
             BinaryPay_Variable::PASSWORD        => $this->credentials['password']
        );

        try {
            if (empty($transaction_id)) {
                throw new InvalidArgumentException(sprintf(__ ('The transaction ID for order %1$s is blank. A refund cannot be processed unless there is a valid transaction associated with the order.', 'woocommerce-payment-gateway-magebinary-binarypay' ), $order_id ));
            }
            $response = $gateway->refund($refund);
            $order->update_meta_data('_transaction_status', $response['status']);
            $order->add_order_note (
                sprintf(__('Refund successful in GenoaPay. Amount: %1$s. Refund ID: %2$s', 'woocommerce-payment-gateway-magebinary-binarypay'),
                wc_price($amount, array(
                    'currency' => $order->get_currency()
                )
            ), $response['refundId']));
            $order->save();
        } catch (Exception $e) {
            return new WP_Error('refund-error', sprintf(__('Exception thrown while issuing refund. Reason: %1$s Exception class: %2$s', 'woocommerce-payment-gateway-magebinary-binarypay'), $e->getMessage(), get_class($e)));
        }
        return true;
    }

    /**
     * Process payment
     * After investigation this step is after the order has been placed
     * Therefore we should handle the response then continue to run this function
     */
    public function process_payment($order_id)
    {
        // save the order id, and handle the order creation in the callback action base on the Latitude response
        $this->get_checkout_session()->set('order_id', $order_id);
        // Return thankyou redirect
        return array(
            'result'    => 'success',
            'redirect'  => $this->get_purchase_url()
        );
    }
}