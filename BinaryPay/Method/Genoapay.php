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
    public function __construct()
    {
        parent::__construct();

        $this->id                   = 'binarypay_genoapay';
        $this->default_title        = __('GenoaPay', 'magebinary-binarypay');
        $this->order_button_text    = __('Place Order with GenoaPay', 'magebinary-binarypay');
        $this->method_title         = __('GenoaPay', 'magebinary-binarypay');
        $this->tab_title            = __('GenoaPay', 'magebinary-binarypay');
        $this->method_description   = __('Available to NZ residents who are 18 years old and over and have a valid debit or credit card.', 'magebinary-binarypay');
    }
}