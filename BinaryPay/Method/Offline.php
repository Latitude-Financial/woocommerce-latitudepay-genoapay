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

class MageBinary_BinaryPay_Method_Offline extends MageBinary_BinaryPay_Method_Abstract
{
    public function __construct()
    {
        parent::__construct();

        $this->id                   = 'binarypay_offline';
        $this->default_title        = __('Offline', 'magebinary-binarypay');
        $this->order_button_text    = __('Place Order with Offline Payment', 'magebinary-binarypay');
        $this->method_title         = __('BinaryPay Offline Gateway', 'magebinary-binarypay');
        $this->tab_title            = __('Offline', 'magebinary-binarypay');
        $this->method_description   = __('BinaryPay Offline Gateway', 'magebinary-binarypay');
    }

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

    /**
     * Initialize Gateway Settings Form Fields
     */
    public function init_form_fields()
    {
        $this->form_fields = apply_filters('wc_offline_form_fields', array(
            'enabled' => array(
                'title'   => __( 'Enable/Disable', 'magebinary-binarypay' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable Offline Payment', 'magebinary-binarypay' ),
                'default' => 'yes'
            ),
            'title' => array(
                'title'       => __( 'Title', 'magebinary-binarypay' ),
                'type'        => 'text',
                'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'magebinary-binarypay' ),
                'default'     => __( 'Offline Payment', 'magebinary-binarypay' ),
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => __( 'Description', 'magebinary-binarypay' ),
                'type'        => 'textarea',
                'description' => __( 'Payment method description that the customer will see on your checkout.', 'magebinary-binarypay' ),
                'default'     => __( 'Please remit payment to Store Name upon pickup or delivery.', 'magebinary-binarypay' ),
                'desc_tip'    => true,
            ),
            'instructions' => array(
                'title'       => __( 'Instructions', 'magebinary-binarypay' ),
                'type'        => 'textarea',
                'description' => __( 'Instructions that will be added to the thank you page and emails.', 'magebinary-binarypay' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
        ));
    }
}