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

/**
 * Class AuthenticationTest
 * @package Latitude\Tests\Wpunit
 */
class AuthenticationTest extends Base
{
    /**
	 * Gateway Type
	 *
	 * @var string
	 */
	protected $gatewayType = \WC_LatitudeFinance_Method_Genoapay::class;

	/**
     * It Should Be Able To Authenticate With Latitudepay
     *
     * @test
     */
    public function itShouldBeAbleToAuthenticateWithLatitudepay()
    {
        $this->tester->createApiTokenSuccess();
		$credentials = [
            'username'      => 'xxxxxxxxxxx',
            'password'      => 'xxxxxxxxxxx',
            'environment'   => 'development',
            'accountId'     => ''
		];
        $gateway = new \Latitudepay($credentials);
    }

    /**
     * It Should Be Not Able To Authenticate With Latitudepay
     *
     * @test
     */
    public function itShouldBeNotAbleToAuthenticateWithLatitudepay()
    {
        $this->tester->createApiTokenFail();
        try {
			$credentials = [
                'username'      => 'xxxxxxxxxxxx',
                'password'      => 'xxxxxxxxxxxx',
                'environment'   => 'development',
                'accountId'     => ''
            ];
            $gateway = new \Latitudepay($credentials);
		} catch (\BinaryPay_Exception $e) {
			$this->assertEquals("Message: Invalid client credentials", $e->getMessage());
		}
    }

	/**
     * It Should Be Able To Authenticate With Genoapay
     *
     * @test
     */
    public function itShouldBeAbleToAuthenticateWithGenoapay()
    {
        $this->tester->createApiTokenSuccess();
		$credentials = [
            'username'      => 'xxxxxxxxxxxx',
            'password'      => 'xxxxxxxxxxxx',
            'environment'   => 'development',
            'accountId'     => ''
		];
        $gateway = new \Genoapay($credentials);
    }

    /**
     * It Should Be Not Able To Authenticate With Genoapay
     *
     * @test
     */
    public function itShouldBeNotAbleToAuthenticateWithGenoapay()
    {
        $this->tester->createApiTokenFail();
        try {
			$credentials = [
                'username'      => 'xxxxxxxxxxxx',
                'password'      => 'xxxxxxxxxxxx',
                'environment'   => 'development',
                'accountId'     => ''
            ];
            $gateway = new \Latitudepay($credentials);
		} catch (\BinaryPay_Exception $e) {
			$this->assertEquals("Message: Invalid client credentials", $e->getMessage());
		}
    }
}