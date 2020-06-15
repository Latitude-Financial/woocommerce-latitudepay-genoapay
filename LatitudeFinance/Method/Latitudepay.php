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

if (!class_exists('WC_LatitudeFinance_Method_Abstract')) {
    return;
}

/**
 * @see when I extend from the 'WC_LatitudeFinance_Method_Genoapay' the Woocommerce is not recognize
 * @todo remove the duplication of the code
 */
class WC_LatitudeFinance_Method_Latitudepay extends WC_LatitudeFinance_Method_Abstract
{
    /**
     * @var string
     */
    protected $return_url = '?wc-api=latitudepay_return_action';

    /**
     * @var string
     */
    protected $gateway_class = 'WC_LatitudeFinance_Method_Latitudepay';

    /**
     * @var string
     */
    protected $order_status = self::PENDING_ORDER_STATUS;

    /**
     * @var string
     */
    protected $return_action_name = 'latitudepay_return_action';



    public function __construct()
    {
        $this->id                   = 'latitudepay';
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

}