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
        $this->default_title        = __('GenoaPay', 'woocommerce-payment-gateway-latitudefinance');
        $this->order_button_text    = __('Proceed with GenoaPay', 'woocommerce-payment-gateway-latitudefinance');
        $this->method_title         = __('GenoaPay', 'woocommerce-payment-gateway-latitudefinance');
        $this->tab_title            = __('GenoaPay', 'woocommerce-payment-gateway-latitudefinance');
        $this->icon                 = WC_LATITUDEPAY_ASSETS . 'genoapay.svg';

        /**
          * Allow refund and purchase product action
          */
        $this->supports             = array('products', 'refunds');

        /**
         * The description will show in the backend
         */
        $this->method_description   = __('Available to NZ residents who are 18 years old and over and have a valid debit or credit card.', 'woocommerce-payment-gateway-latitudefinance');
        parent::__construct();
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
            throw new InvalidArgumentException(__('Request object cannot be empty!', 'woocommerce-payment-gateway-latitudefinance'));
        }

        $session = $this->get_checkout_session();
        $token = $session->get('purchase_token');
        // Unset session after use
        $session->set('purchase_token', null);

        if (!$this->return_action_name || $this->return_action_name !== $request->getData('wc-api')) {
            throw new BinaryPay_Exception(__('The return action handler is not valid for the request.', 'woocommerce-payment-gateway-latitudefinance'));
        }

        if (!$token || $request->getData('token') !== $token) {
            $this->redirect_url = WC()->cart->get_cart_url();
            $session->set('order_id', null);
            /**
             * @todo If debug then output the request in to the log file
             */
            throw new BinaryPay_Exception(__("You are not allowed to access the return handler directly. If you want to know more about this error message, please contact the us.",'woocommerce-payment-gateway-latitudefinance'));
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
                $this->order_comment = __('Payment received', 'woocommerce-payment-gateway-latitudefinance');
                // send invoice email
                break;
            case BinaryPay_Variable::STATUS_UNKNOWN:
                $this->order_status = self::FAILED_ORDER_STATUS;
                $this->order_comment = __('Payment failed', 'woocommerce-payment-gateway-latitudefinance');
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
            BinaryPay::log($e->getMessage(), true, 'woocommerce-genoapay.log');
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            $message = $e->getMessage() ?: 'Something massively went wrong. Please try again. If the problem still exists, please contact us';
            BinaryPay::log($message, true, 'woocommerce-genoapay.log');
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
                throw new InvalidArgumentException(sprintf(__ ('The transaction ID for order %1$s is blank. A refund cannot be processed unless there is a valid transaction associated with the order.', 'woocommerce-payment-gateway-latitudefinance' ), $order_id ));
            }
            $response = $gateway->refund($refund);
            $order->update_meta_data('_transaction_status', $response['status']);
            $order->add_order_note (
                sprintf(__('Refund successful in GenoaPay. Amount: %1$s. Refund ID: %2$s', 'woocommerce-payment-gateway-latitudefinance'),
                wc_price($amount, array(
                    'currency' => $order->get_currency()
                )
            ), $response['refundId']));
            $order->save();
        } catch (Exception $e) {
            BinaryPay::log($e->getMessage(), true, 'woocommerce-genoapay.log');
            return new WP_Error('refund-error', sprintf(__('Exception thrown while issuing refund. Reason: %1$s Exception class: %2$s', 'woocommerce-payment-gateway-latitudefinance'), $e->getMessage(), get_class($e)));
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