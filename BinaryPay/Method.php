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

    public function __construct() {
        $this->init_form_fields();
        $this->init_settings();
        $this->add_hooks();
    }

    public static function init() {
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
     * Add all standard filters
     */
    public function add_hooks() {
    }
}