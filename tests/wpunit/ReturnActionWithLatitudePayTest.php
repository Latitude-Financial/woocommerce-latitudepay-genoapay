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
namespace Latitude\Tests\Wpunit;
use Codeception\Exception\ModuleException;
use tad\WPBrowser\Module\WPLoader\FactoryStore;

/**
 * Class ReturnActionWithGenoaPayTest
 * @package Latitude\Tests\Wpunit
 */
class ReturnActionWithGenoaPayTest extends GenoaPay
{
    /**
     * It Should Be Not Able To Return Action
     *
     * @test
     */
    public function itShouldBeNotAbleToReturnAction()
    {
        $this->tester->createApiTokenSuccess();
		$this->tester->createApiPurchaseSuccess();
        $_GET = [
            'result' => \BinaryPay_Variable::STATUS_COMPLETED,
            'message' => 'Payment Success',
            'wc-api' => 'genoapay_return_action'
        ];
        $this->gateway->return_action();
        $notices = wc_get_notices( 'error' );
        $this->assertIsArray($notices);
        if(is_array($notices[0]) && isset($notices[0]['notice'])){
            $this->assertEquals($notices[0]['notice'], 'You are not allowed to access the return handler directly. If you want to know more about this error message, please contact us.');
        } else {
            $this->assertEquals($notices[0], 'You are not allowed to access the return handler directly. If you want to know more about this error message, please contact us.');
        }
    }
	/**
     * It Should Be Able To Return Action
     *
     * @test
     */
    public function itShouldBeAbleToReturnAction()
    {
        $this->tester->createApiTokenSuccess();
		$this->tester->createApiPurchaseSuccess();
        WC()->cart->empty_cart();
        WC()->cart->add_to_cart( $this->simple_product->get_id(), 3 );
        WC()->cart->calculate_totals();
        $order = $this->tester->create_order();
        $result = $this->gateway->process_payment($order->get_id());
        $_GET = [
            'result' => \BinaryPay_Variable::STATUS_COMPLETED,
            'message' => 'Payment Success',
            'wc-api' => 'genoapay_return_action',
            'purchase_token' => 'xxxxxxxxxxx'
        ];
        $this->gateway->return_action();
        $notices = wc_get_notices( 'error' );
        $this->assertEmpty($notices);
        $headers = xdebug_get_headers();
        $this->assertContains(
            "X-Redirect-By: WordPress",
            $headers
        );
        $this->assertRegExp(
            "/order-received/",
            json_encode($headers)
        );
    }
}