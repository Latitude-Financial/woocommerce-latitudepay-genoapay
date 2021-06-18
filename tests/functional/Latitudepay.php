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
use Exception;

/**
 * Trait Latitudepay
 * @package Latitude\Tests\Functional
 */
trait Latitudepay
{
    use Base;

    /**
     * @param $I
     */
    public function _before($I)
    {
        $this->activePlugin($I);
        $this->configLatitudePaySettings($I);
    }

    /**
     * @skip Skip configuration method
     * @param $I
     */
    protected function configLatitudePaySettings($I)
    {
        $I->cli(['option', 'set', 'woocommerce_store_address','29th-Street']);
        $I->cli(['option', 'set', 'woocommerce_store_address_2 "#343"']);
        $I->cli(['option', 'set', 'woocommerce_store_city', 'Melburn']);
        $I->cli(['option', 'set', 'woocommerce_default_country', 'AU:VIC']);
        $I->cli(['option', 'set', 'woocommerce_store_postcode', '3000']);
        $I->cli(['option', 'set', 'woocommerce_currency', 'AUD']);
        $I->cli(['option', 'set', 'woocommerce_product_type', 'both']);
        $I->cli(['option', 'set', 'woocommerce_allow_tracking', 'no']);

        $settings = [
            "enabled" => "yes",
            "environment" => "sandbox",
            "debug_mode" => 'off',
            "sandbox_public_key" => getenv('LATITUDE_API_PUBLIC_KEY', "") ?: getenv('LATITUDE_API_PUBLIC_KEY'),
            "sandbox_private_key" => getenv('LATITUDE_API_PRIVATE_KEY', "") ?: getenv('LATITUDE_API_PRIVATE_KEY'),
            "individual_snippet_enabled" => "yes",
            "snippet_product_page_position" => "woocommerce_single_product_summary",
            "snippet_product_page_hook_priority" => "11",
            "cart_page_snippet_enabled" => "yes",
            "checkout_page_snippet_enabled" => "yes"
        ];
        $settings = json_encode($settings);
        $shippingZone = $I->cliToString(['wc', 'shipping_zone', 'create', '--name=Australia','--user=admin',"--porcelain"]);
        $I->cli(['wc', 'shipping_zone_method', 'create', $shippingZone,'--user=admin','--method_id=flat_rate','--enabled=true',"--porcelain"]);
        $I->cli(['wc', 'payment_gateway', 'update', 'latitudepay','--user=admin','--enabled=true',"--settings='".$settings."'","--porcelain"]);
        $I->cli(['cache', 'flush']);
    }

    /**
     * @skip Skip adding product to cart
     * @param AcceptanceTester $I
     * @throws Exception
     */
    protected function addProductToCart($I) {
        $I->amOnPage('/product/polo');
        $I->see('Polo');
        $I->click('.cart button[name=add-to-cart]');
        $I->amOnPage('/cart');
        $I->see('Polo');
        $I->amOnPage('/checkout');
        $I->waitForElement('#payment_method_latitudepay', 30);
    }

    /**
     * @skip login to payment gateway
     * @param $I
     */
    protected function loginToLatitudePay($I)
    {
        $I->amOnUrl('https://app.uat.latitudepay.com/logout');
        $I->wait(3);
        $I->amOnUrl('https://app.uat.latitudepay.com/');
        $I->wait(3);
        $I->see('Sign in or create an account');
        $I->fillField(['name' => 'emailAddress'], getenv('LATITUDE_USER_EMAIL', "") ?: getenv('LATITUDE_USER_EMAIL'));
        $I->fillField(['name' => 'password'], getenv('LATITUDE_USER_PASSWORD', "") ?: getenv('LATITUDE_USER_PASSWORD'));
        $I->click("button.btn-submit");
        $I->wait(5);
        $uri = $I->grabFromCurrentUrl();
        if(strpos($uri,"/customer/verify-mobile") !== false){
            $I->fillField("#code-0", '0');
            $I->fillField("#code-1", '0');
            $I->fillField("#code-2", '0');
            $I->fillField("#code-3", '0');
            $I->click("button.submit-verification");
            $I->wait(5);
        }
        $I->see('My purchases');
        $I->see('Sign out');
    }

    /**
     * @skip Skip do checkout method
     * @param AcceptanceTester $I
     * @throws Exception
     */
    protected function doCheckout($I) {
        $I->loginAsAdmin();
        $this->addProductToCart($I);
        $I->fillField(['name' => 'billing_first_name'], 'Jon');
        $I->fillField(['name' => 'billing_last_name'], 'Doe');
        $I->fillField(['name' => 'billing_company'], 'abc');
        $I->fillField(['name' => 'billing_address_1'], 'Victoria Street');
        $I->fillField(['name' => 'billing_address_2'], '203');
        $I->fillField(['name' => 'billing_city'], 'Melburn');
        $I->fillField(['name' => 'billing_postcode'], '3000');
        $I->fillField(['name' => 'billing_phone'], '111111111111111');
        $I->fillField(['name' => 'billing_email'], 'jondoe@example.com');
        $I->fillField(['name' => 'order_comments'], 'Order Comments');
        $I->selectOption(['name' => 'billing_country'], 'AU');
        $I->wait(3);
        $I->selectOption(['name' => 'billing_state'], 'VIC');

        $I->click("#place_order");
        $I->wait(10);

        $uri = $I->grabFromCurrentUrl();
        if(strpos($uri,"/?token=") !== false) {
            $I->fillField(['name' => 'emailAddress'], getenv('LATITUDE_USER_EMAIL', "") ?: getenv('LATITUDE_USER_EMAIL'));
            $I->fillField(['name' => 'password'], getenv('LATITUDE_USER_PASSWORD', "") ?: getenv('LATITUDE_USER_PASSWORD'));
            $I->click("button.btn-submit");
            $I->wait(10);
        }

        $uri = $I->grabFromCurrentUrl();
        if(strpos($uri,"/customer/verify-mobile") !== false){
            $I->fillField("#code-0", '0');
            $I->fillField("#code-1", '0');
            $I->fillField("#code-2", '0');
            $I->fillField("#code-3", '0');
            $I->click("button.submit-verification");
            $I->wait(10);
        }

        $uri = $I->grabFromCurrentUrl();
        if(strpos($uri,"/checkout") !== false) {
            $I->wait(3);
            $I->see("your purchase has been cancelled");
            throw new Exception('Your purchase has been cancelled');
        }


        $uri = $I->grabFromCurrentUrl();
        if(strpos($uri,"/customer/create-payment-plan/confirm-purchase") !== false) {
            $I->click("button");
            $I->wait(10);
        }

        $uri = $I->grabFromCurrentUrl();
        if(strpos($uri,"/customer/create-payment-plan/confirm-payment-schedule") !== false) {
            $I->checkOption('#checkboxInput');
            $I->wait(10);
            $I->click('#setupPayments');
            
            while(strpos($I->grabFromCurrentUrl(),"/customer/create-payment-plan/confirm-payment-schedule") !== false){
                $I->wait(1);
            }
            
        }
        $count=0;
        while(strpos($I->grabFromCurrentUrl(),"/?wc-api=latitudepay_return_action&token=") === false){
            if($count > 60){
                break;
            }
            $I->wait(1);
            $count++;
        }
        $uri = $I->grabFromCurrentUrl();
        if(strpos($uri,"/?wc-api=latitudepay_return_action&token=") !== false) {
            $I->amOnPage($uri);
            $I->see("Thank you. Your order has been received");
        }
        $uri = $I->grabFromCurrentUrl();
        preg_match('/\/checkout\/order-received\/(.*)\/.*/', $uri, $matches);
        if (count($matches) >= 2) {
            $orderId = $matches[1];
            return $orderId;
        }
    }

    /**
     * @skip
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    protected function doFullRefund($I,$orderId=null)
    {
        if(!$orderId){
            $uri = $I->grabFromCurrentUrl();
            preg_match('/\/checkout\/order-received\/(.*)\/.*/', $uri, $matches);
            if (count($matches) >= 2) {
                $orderId = $matches[1];
            } 
        }
        if(!$orderId){
            return;
        }
        $I->amOnUrl('http://woocommerce.localhost/');
        $I->amOnAdminPage('/post.php?post=' . $orderId . '&action=edit');
        $I->see('Payment was successful via latitudepay');
        $I->click('button.refund-items');
        $I->wait(1);
        $I->fillField('input.refund_order_item_qty', 1);
        $I->click('button.do-api-refund');
        $I->seeInPopup('Are you sure you wish to process this refund? This action cannot be undone');
        $I->acceptPopup();
        $I->wait(10);
        $uri = $I->grabFromCurrentUrl();
        $I->amOnPage($uri);
        $I->see('Refund successful');
        $I->see('Refund ID:');
    }

    /**
     * @skip
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    protected function doPartialRefund($I,$orderId=null)
    {
        if(!$orderId){
            $uri = $I->grabFromCurrentUrl();
            preg_match('/\/checkout\/order-received\/(.*)\/.*/', $uri, $matches);
            if (count($matches) >= 2) {
                $orderId = $matches[1];
            } 
        }
        if(!$orderId){
            return;
        }
        $I->loginAsAdmin();
        $I->amOnAdminPage('/post.php?post=' . $orderId . '&action=edit');
        $I->see('Payment was successful via latitudepay');
        $I->click('button.refund-items');
        $I->wait(1);

        $I->fillField('input.refund_order_item_qty', 1);
        $I->executeJS("
            var totalItems = document.querySelectorAll('.wc-order-refund-items .wc-order-totals .woocommerce-Price-amount');
            if (totalItems.length >= 1) {
                var item = totalItems[totalItems.length - 1];
                var symbolElem = item.querySelector('.woocommerce-Price-currencySymbol');
                var availableAmount = item.innerText.replace(symbolElem.innerText, '');
                document.getElementById('refund_amount').value = (parseFloat(availableAmount) - 5);
            }
        ");

        $I->click('button.do-api-refund');
        $I->seeInPopup('Are you sure you wish to process this refund? This action cannot be undone');
        $I->acceptPopup();
        $I->wait(10);
        $uri = $I->grabFromCurrentUrl();
        $I->amOnPage($uri);
        $I->see('Refund successful');
        $I->see('Refund ID:');
    }
}