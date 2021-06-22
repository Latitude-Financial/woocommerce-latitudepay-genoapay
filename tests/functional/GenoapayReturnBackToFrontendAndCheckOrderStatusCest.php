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
namespace Latitude\Tests\Functional;
use AcceptanceTester;
use Codeception\Scenario;

/**
 * Class GenoapayReturnBackToFrontendAndCheckOrderStatusCest
 * @package Latitude\Tests\Functional
 */
class GenoapayReturnBackToFrontendAndCheckOrderStatusCest
{
    use Genoapay;

    /**
     * @skip
     * @param AcceptanceTester $I
     * @param Scenario $scenario
     * @throws \Exception
     */
    public function iCanReturnBackToFrontendAndCheckTheOrderStatus($I, $scenario) {
        $I->loginAsAdmin();
        $this->doCheckout($I);
        $uri = $I->grabFromCurrentUrl();
        preg_match('/\/checkout\/order-received\/(.*)\/.*/', $uri, $matches);
        if (count($matches) >= 2) {
            $orderId = $matches[1];
            $I->amOnPage('/my-account/view-order/'.$orderId.'/');
            $I->see("Order #".$orderId." was placed");
            $this->doFullRefund($I,$orderId);
        }
    }
}