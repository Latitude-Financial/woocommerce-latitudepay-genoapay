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
 * Class GenoaPay
 * @package Latitude\Tests\Wpunit
 */
class GenoaPay extends Base
{
    /**
	 * Gateway Type
	 *
	 * @var string
	 */
	protected $gatewayType = \WC_LatitudeFinance_Method_Genoapay::class;

	/**
	 * Sets genoapay pay options
	 *
	 */
	protected function settingGateway() {
		$option_key = 'woocommerce_genoapay_settings';
		$options         = get_option( $option_key );
		
		$options = [
			'enabled' => 'yes',
			"sandbox_public_key" => getenv('GENOAPAY_API_PUBLIC_KEY', "") ?: getenv('GENOAPAY_API_PUBLIC_KEY'),
            "sandbox_private_key" => getenv('GENOAPAY_API_PRIVATE_KEY', "") ?: getenv('GENOAPAY_API_PRIVATE_KEY'),
			'debug_mode'=> \WC_LatitudeFinance_Method_Abstract::DEBUG_MODE_OFF,
			'environment'   => 'development',
			'account_id' => ''
		];
		update_option( $option_key, $options);
		update_option( 'woocommerce_default_country', 'NZ:AK' );
		update_option( 'woocommerce_currency', 'NZD' );
	}
}