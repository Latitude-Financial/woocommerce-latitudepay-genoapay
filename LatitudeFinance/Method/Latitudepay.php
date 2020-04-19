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

if (!class_exists('MageBinary_BinaryPay_Method_Abstract')) {
    return;
}

/**
 * @see when I extend from the 'MageBinary_BinaryPay_Method_Genoapay' the Woocommerce is not recognize
 * @todo remove the duplication of the code
 */
class MageBinary_BinaryPay_Method_Latitudepay extends MageBinary_BinaryPay_Method_Abstract
{
    /**
     * @var string
     */
    protected $return_url = '?wc-api=latitudepay_return_action';

    /**
     * @var string
     */
    protected $gateway_class = 'MageBinary_BinaryPay_Method_Latitudepay';

    /**
     * @var string
     */
    protected $order_status = self::PENDING_ORDER_STATUS;

    public function __construct()
    {
        $this->id                   = MageBinary_BinaryPay_Model_Config::LATITUDEPAY;
        $this->template             = 'latitudepay/info.php';
        $this->default_title        = __('LatitudePay', 'woocommerce-payment-gateway-latitudefinance');
        $this->order_button_text    = __('Proceed with LatitudePay', 'woocommerce-payment-gateway-latitudefinance');
        $this->method_title         = __('LatitudePay', 'woocommerce-payment-gateway-latitudefinance');
        $this->tab_title            = __('LatitudePay', 'woocommerce-payment-gateway-latitudefinance');
        $this->icon                 = WC_LATITUDEPAY_ASSETS . 'latitudepay.svg';

        /**
          * Allow refund and purchase product action
          */
        $this->supports             = array('products', 'refunds');

        /**
         * The description will show in the backend
         */
        $this->method_description   = __('Available to AU residents who are 18 years old and over and have a valid debit or credit card.', 'woocommerce-payment-gateway-latitudefinance');
        parent::__construct();
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

}