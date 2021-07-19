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
 * Class PurchaseWithGenoaPayTest
 * @package Latitude\Tests\Wpunit
 */
class PurchaseWithGenoaPayTest extends GenoaPay
{
	/**
     * It Should Be Not Able To Purchase With Total Amount Less Than 20.00 AUD
     *
     * @test
     */
    public function itShouldBeNotAbleToPurchaseWithTotalAmountLessThanMinimumAmount()
    {
		$this->tester->createApiTokenSuccess();
		$this->tester->createApiPurchaseFail();
		try {
			WC()->cart->empty_cart();
			WC()->cart->add_to_cart( $this->simple_product->get_id(), 1 );
			WC()->cart->calculate_totals();
			$order = $this->tester->create_order();
			$this->assertLessThan(20, WC()->cart->get_cart_contents_total() ,'Order Total Amount Not Less Than Minimum Amount');

			$result = $this->gateway->process_payment($order->get_id());
		} catch (\Exception $e) {
			$this->assertEquals("Message: Total amount is below minimum purchase value of 20.00", $e->getMessage());
		}
    }

	/**
     * It Should Be Able To Purchase With Total Amount Greater Than 20.00 Dollar
     *
     * @test
     */
    public function itShouldBeAbleToPurchaseWithTotalAmountGreaterThanMinimumAmount()
    {
		$this->tester->createApiTokenSuccess();
		$this->tester->createApiPurchaseSuccess();
		WC()->cart->empty_cart();
		WC()->cart->add_to_cart( $this->simple_product->get_id(), 3 );
		WC()->cart->calculate_totals();
        $order = $this->tester->create_order();
		$this->assertGreaterThanOrEqual(20,WC()->cart->get_cart_contents_total(),'Order Total Amount Not Greater Than Minimum Amount');
		$result = $this->gateway->process_payment($order->get_id());
		$this->assertArrayHasKey( 'result', $result );
		$this->assertContains( 'success', $result );
		$this->assertArrayHasKey( 'redirect', $result );
    }
}