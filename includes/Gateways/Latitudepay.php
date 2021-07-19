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

class Latitudepay extends Genoapay {


	public function getHeader() {
		$headers   = array();
		$headers[] = 'api-version: ' . self::API_VERSION;

		if ( $this->getConfig( 'request-content-type' ) == 'json' ) {
			$headers[] = 'Content-Type: application/com.latitudepay.ecom-v3.0+json';
			$headers[] = 'Accept: application/com.latitudepay.ecom-v3.0+json';
		}

		$headers[] = 'Authorization: ' . $this->getAuth();
		return $headers;
	}

	/**
	 * @description main function to query API.
	 * @param  array  request body
	 * @return array  returns API response
	 */

	public function getApiUrl() {
		switch ( $this->getConfig( BinaryPay_Variable::ENVIRONMENT ) ) {
			case 'production':
				$url = 'https://api.latitudepay.com/';
				break;
			case 'sandbox':
				$url = 'https://api.uat.latitudepay.com/';
				break;
			case 'development':
				$url = getenv( 'GATEWAY_API_URL', true ) ?: getenv( 'GATEWAY_API_URL' );
				break;
		}

		return $url;
	}

}
