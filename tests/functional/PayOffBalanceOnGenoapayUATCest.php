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
use \Codeception\Util\Locator;

/**
 * Class PayOffBalanceOnGenoapayUATCest
 * @package Latitude\Tests\Functional
 */
class PayOffBalanceOnGenoapayUATCest
{
    use Genoapay;

    /**
     * @skip
     * @param AcceptanceTester $I
     * @param Scenario $scenario
     */
    public function iCanPayOffBalanceOnGenoapayUATSite($I, $scenario) {
        $orderId = $this->doCheckout($I);
        $this->loginToGenoaPay($I);
        $I->canSeeElement('div.purchase-item-link');
        $I->click(Locator::firstElement('div.purchase-item-link'));
        $I->wait(5);
        $I->canSeeElement('button.view-make-payment');
        $I->click('button.view-make-payment');
        $I->wait(15);
        $I->canSeeElement('#dpsMakePaymentModal');
        $I->canSeeElement('div.dps-payment-content');
        $I->canSeeElement('button.do-make-payment');
        $this->doFullRefund($I,$orderId);
        
        // $I->click('button.do-make-payment');
        // $I->waitForElementNotVisible('#dpsMakePaymentModal', 30);
        // $I->wait(10);
        // $I->see('Thank you. Your payment of');
    }
}