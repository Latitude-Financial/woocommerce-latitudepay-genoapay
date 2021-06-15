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
use \Codeception\Util\Locator;

/**
 * Class LatitudePaySnippetCest
 * @package Latitude\Tests\Functional
 */
class LatitudePaySnippetCest
{
    use Latitudepay;

    /**
     * @param $I
     * @param $scenario
     */
    public function iCanSeeLatitudepaySnippetDisplayOnProductDetailPage($I, $scenario)
    {
        $I->amOnPage('/product/polo');
        $I->see('Polo');
        $I->seeElement('img.lpay_snippet');
        $I->click('img.lpay_snippet');
        $I->waitForElementVisible('.lpay-modal-wrapper', 30);
    }

    /**
     * @param $I
     * @param $scenario
     */
    public function iCanSeeLatitudepaySnippetDisplayOnShoppingCartPageWithTotalAmountGreaterThanMinimumAmount($I, $scenario)
    {
        $I->amOnPage('/product/polo');
        $I->see('Polo');
        $I->click('.cart button[name=add-to-cart]');

        $I->amOnPage('/cart');
        $I->seeElement('img.lpay_snippet');
        $I->click('img.lpay_snippet');
        $I->waitForElementVisible('.lpay-modal-wrapper', 30);
    }

    /**
     * @param $I
     * @param $scenario
     */
    public function iCanSeeLatitudepaySnippetDisplayOnCheckoutPageWithTotalAmountGreaterThanMinimumAmount($I, $scenario)
    {
        $I->amOnPage('/product/polo');
        $I->see('Polo');
        $I->click('.cart button[name=add-to-cart]');

        $I->amOnPage('/checkout');
        $I->wait(3);
        $I->seeElement('img.lpay_snippet');
        $I->click('img.lpay_snippet');
        $I->waitForElementVisible('.lpay-modal-wrapper', 30);
    }

    /**
     * @param $I
     * @param $scenario
     */
    public function iCanNotSeeLatitudepaySnippetDisplayOnShoppingCartPageWithTotalAmountLessThanMinimumAmount($I, $scenario)
    {
        $I->amOnPage('/product/album');
        $I->see('Album');
        $I->see('$15.00');
        $I->click('.cart button[name=add-to-cart]');

        $I->amOnPage('/cart');
        $I->dontSeeElement('img.lpay_snippet');
    }

    /**
     * @param $I
     * @param $scenario
     */
    public function iCanNotSeeLatitudepaySnippetDisplayOnCheckoutPageWithTotalAmountLessThanMinimumAmount($I, $scenario)
    {
        $I->amOnPage('/product/album');
        $I->see('Album');
        $I->see('$15.00');
        $I->click('.cart button[name=add-to-cart]');

        $I->amOnPage('/checkout');
        $I->wait(3);
        $I->dontSeeElement('img.lpay_snippet');
    }
}