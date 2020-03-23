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
class MageBinary_BinaryPay_Model_Config
{
    /**
     * Paymark payment method - Directpost
     */
    const PAYMARK_DIRECTPOST            = 'paymark_directpost';

    /**
     * Paymark payment method - Webpayment
     */
    const PAYMARK_WEBPAYMENT            = 'paymark_webpayment';

    /**
     * Paymark payment method - OnlineEFTPOS
     */
    const PAYMARK_ONLINEEFTPOS          = 'paymark_onlineeftpos';

    /**
     * Qcard payment method - Flexi Long term
     */
    const QCARD_FLEXILONG               = 'qcard_flexilong';

    /**
     * Genoapay
     */
    const GENOAPAY                      = 'genoapay';

    /**
     * Latitudepay
     */
    const LATITUDEPAY                   = 'latitudepay';

    /**
     * Polipay
     */
    const POLIPAY                       = 'polipay';

    /**
     * Successful redirect page URL
     */
    const SUCCESSFUL_PAGE_URL           = 'checkout/onepage/success';

    /**
     * Checkout cart page URL
     */
    const CHECKOUT_CART_PAGE_URL         = 'checkout/cart';
}