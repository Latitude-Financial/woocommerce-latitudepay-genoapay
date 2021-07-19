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
 * Class RefundWithGenoaPayTest
 * @package Latitude\Tests\Wpunit
 */
class RefundWithGenoaPayTest extends GenoaPay
{
    /**
     * @var string
     */
    protected $transactionId = 'xxx-xxx-xxx-xxx';
    /**
     * It Should Be Not Able To Refund Not Valid TransactionId
     *
     * @test
     */
    public function itShouldBeNotAbleToRefundOrderWithMissingTransactionId()
    {
        $this->tester->createApiTokenSuccess();
		$this->tester->createApiPurchaseSuccess();

        WC()->cart->empty_cart();
        WC()->cart->add_to_cart( $this->simple_product->get_id(), 3 );
        WC()->cart->calculate_totals();
        $order = $this->tester->create_order();
        $this->gateway->process_payment($order->get_id());
        $token = $this->transactionId;
        $this->tester->createApiRefundOrderFail($token);
        $result = $this->gateway->process_refund($order->get_id(),10,'Refund order');
        $this->assertInstanceOf(\WP_Error::class, $result );
        $this->assertRegExp("/A refund cannot be processed unless there is a valid transaction associated with the order/",$result->errors['refund-error'][0]);
    }
	/**
     * It Should Be Able To Refund
     *
     * @test
     */
    public function itShouldBeNotAbleToRefundOrderWithWrongTransactionId()
    {
        $this->tester->createApiTokenSuccess();
		$this->tester->createApiPurchaseSuccess();
        WC()->cart->empty_cart();
        WC()->cart->add_to_cart( $this->simple_product->get_id(), 3 );
        WC()->cart->calculate_totals();
        $order = $this->tester->create_order();
        $this->gateway->process_payment($order->get_id());
        $order->set_transaction_id($this->transactionId);
        $order->save();
        $token = $this->transactionId;
        $this->tester->createApiRefundOrderFail($token);
        $result = $this->gateway->process_refund($order->get_id(),10,'Refund order');

        $this->assertInstanceOf(\WP_Error::class, $result );
        $this->assertRegExp("/Reason: Message: Resource not found/",$result->errors['refund-error'][0]);
    }

    /**
     * It Should Be Able To Refund
     *
     * @test
     */
    public function itShouldBeAbleToRefundOrder()
    {
        $this->tester->createApiTokenSuccess();
		$this->tester->createApiPurchaseSuccess();
        WC()->cart->empty_cart();   
        WC()->cart->add_to_cart( $this->simple_product->get_id(), 3 );
        WC()->cart->calculate_totals();
        $order = $this->tester->create_order();
        $this->gateway->process_payment($order->get_id());
        $order->set_transaction_id($this->transactionId);
        $order->save();
        $token = $this->transactionId;
        $this->tester->createApiRefundOrderSuccess($token);
        $result = $this->gateway->process_refund($order->get_id(),10,'Refund order');
        $this->assertTrue($result );
    }
}