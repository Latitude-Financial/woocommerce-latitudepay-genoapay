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
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use donatj\MockWebServer\ResponseByMethod;

/**
 * Class Base
 * @package Latitude\Tests\Wpunit
 */
class Base extends \Codeception\TestCase\WPTestCase
{
	/** @var MockWebServer */
	protected static $server;

    /**
     * @var \WpunitTester
     */
    protected $tester;

    /**
	 * Gateway under test.
	 *
	 * @var \WC_LatitudeFinance_Method
	 */
	protected $gateway;

	/**
	 * Test product to add to the cart
	 * @var WC_Product_Simple
	 */
	protected $simple_product;

	/**
	 * Test shipping zone.
	 *
	 * @var WC_Shipping_Zone
	 */
	protected $zone;

	/**
	 * Flat rate shipping method instance id
	 *
	 * @var int
	 */
	protected $flat_rate_id;

	/**
	 * Flat rate shipping method instance id
	 *
	 * @var int
	 */
	protected $local_pickup_id;

    /**
	 * Sets up things all tests need.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->settingGateway();
		$this->gateway = new $this->gatewayType;
		$this->simple_product = $this->tester->create_simple_product();

		$zone = new \WC_Shipping_Zone();
		$zone->set_zone_name( 'Worldwide' );
		$zone->set_zone_order( 1 );
		$zone->save();

		$this->flat_rate_id = $zone->add_shipping_method( 'flat_rate' );
		$this->setShippingMethodCost( $this->flat_rate_id, '5' );

		$this->local_pickup_id = $zone->add_shipping_method( 'local_pickup' );
		$this->setShippingMethodCost( $this->local_pickup_id, '1' );

		$this->zone = $zone;
		WC()->session->init();
		$this->tester->setServer(self::$server);
	}

    /**
     *
     */
    protected function _before()
    {
    }

    /**
     *
     */
    protected function _after()
    {
		WC()->cart->empty_cart();
		WC()->session->cleanup_sessions();
		$this->zone->delete();
    }

    /**
	 * Helper function to update test order meta data
	 */
	protected function updateOrderMeta( $order, $key, $value ) {
		$order->update_meta_data( $key, $value );
	}

	/**
	 * Sets shipping method cost
	 *
	 * @param string $instance_id Shipping method instance id
	 * @param string $cost        Shipping method cost in USD
	 */
	protected function setShippingMethodCost( $instance_id, $cost ) {
		$method          = \WC_Shipping_Zones::get_shipping_method( $instance_id );
		$option_key      = $method->get_instance_option_key();
		$options         = get_option( $option_key );
		$options['cost'] = $cost;
		update_option( $option_key, $options );
	}

    /**
	 * Sets genoapay pay options
	 *
	 */
	protected function settingGateway() {
		
	}

    /**
     *
     */
    public static function setUpBeforeClass(): void {
		self::$server = new MockWebServer;
		self::$server->start();
		$apiUrl = self::$server->getServerRoot().'/';
		putenv("GATEWAY_API_URL=$apiUrl");
	}

    /**
     *
     */
    static function tearDownAfterClass(): void {
		// stopping the web server during tear down allows us to reuse the port for later tests
		self::$server->stop();
	}
}