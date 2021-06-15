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
 * Class GenoaPaySnippetCest
 * @package Latitude\Tests\Functional
 */
class GenoaPaySnippetCest
{
    use Genoapay;

    /**
     * @param $I
     * @param $scenario
     */
    public function iCanSeeGenoapaySnippetDisplayOnProductDetailPage($I, $scenario)
    {
        $I->amOnPage('/product/polo');
        $I->see('Polo');
        $I->seeElement(Locator::find('a', ['id' => 'genoapay-popup']));
        $I->click('#genoapay-popup');
        $I->waitForElementVisible('#g-infomodal-container', 30);
        $I->see("Pay over 10 weeks.");
        $I->see("That's it! We manage automatic weekly payments until you're paid off. Full purchase details can be viewed anytime online.");
        $I->click('#g-infomodal-close');
        $I->waitForElementNotVisible('#g-infomodal-container', 30);
    }

    /**
     * @param $I
     * @param $scenario
     */
    public function iCanSeeGenoapaySnippetDisplayOnShoppingCartPageWithTotalAmountGreaterThanMinimumAmount($I, $scenario)
    {
        $I->amOnPage('/product/polo');
        $I->see('Polo');
        $I->click('.cart button[name=add-to-cart]');

        $I->amOnPage('/cart');
        $I->seeElement(Locator::find('a', ['id' => 'genoapay-popup']));
        $I->click('#genoapay-popup');
        $I->waitForElementVisible('#g-infomodal-container', 30);
        $I->see("Pay over 10 weeks.");
        $I->see("That's it! We manage automatic weekly payments until you're paid off. Full purchase details can be viewed anytime online.");
        $I->click('#g-infomodal-close');
        $I->waitForElementNotVisible('#g-infomodal-container', 30);
    }

    /**
     * @param $I
     * @param $scenario
     */
    public function iCanSeeGenoapaySnippetDisplayOnCheckoutPageWithTotalAmountGreaterThanMinimumAmount($I, $scenario)
    {
        $I->amOnPage('/product/polo');
        $I->see('Polo');
        $I->click('.cart button[name=add-to-cart]');

        $I->amOnPage('/checkout');
        $I->wait(3);
        $I->seeElement(Locator::find('a', ['id' => 'genoapay-popup']));
        $I->waitForElementVisible('#genoapay-popup', 30);
        $I->click('#genoapay-popup');
        $I->waitForElementVisible('#g-infomodal-container', 30);
        $I->see("Pay over 10 weeks.");
        $I->see("That's it! We manage automatic weekly payments until you're paid off. Full purchase details can be viewed anytime online.");
        $I->click('#g-infomodal-close');
        $I->waitForElementNotVisible('#g-infomodal-container', 30);
    }

    /**
     * @param $I
     * @param $scenario
     */
    public function iCanNotSeeGenoapaySnippetDisplayOnShoppingCartPageWithTotalAmountLessThanMinimumAmount($I, $scenario)
    {
        $I->amOnPage('/product/album');
        $I->see('Album');
        $I->see('$15.00');
        $I->click('.cart button[name=add-to-cart]');

        $I->amOnPage('/cart');
        $I->dontSeeElement(Locator::find('a', ['id' => 'genoapay-popup']));
    }

    /**
     * @param $I
     * @param $scenario
     */
    public function iCanNotSeeGenoapaySnippetDisplayOnCheckoutPageWithTotalAmountLessThanMinimumAmount($I, $scenario)
    {
        $I->amOnPage('/product/album');
        $I->see('Album');
        $I->see('$15.00');
        $I->click('.cart button[name=add-to-cart]');

        $I->amOnPage('/checkout');
        $I->wait(3);
        $I->dontSeeElement(Locator::find('a', ['id' => 'genoapay-popup']));
    }
}