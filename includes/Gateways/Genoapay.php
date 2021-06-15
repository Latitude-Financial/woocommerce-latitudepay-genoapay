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

class Genoapay extends BinaryPay {

	const API_VERSION = 'v3';

	const TOKEN_ENDPOINT           = 'token';
	const PURCHASE_ENDPOINT        = 'sale/online';
	const PURCHASE_STATUS_ENDPOINT = 'sale/pos';
	const CONFIGURATON_ENDPOINT    = 'configuration';

	const STATUS_SUCCESSFUL            = 200;
	const STATUS_INVALID               = 400;
	const STATUS_ACCESS_DENIED         = 403;
	const STATUS_INTERNAL_SERVER_ERROR = 500;

	public function __construct( $credential = array(), $debug = false ) {
		parent::__construct( $credential, $debug );
		$this->setConfig(
			array(
				'api-error-status'        => array(
					BinaryPay_Variable::STATUS_DECLINED,
					BinaryPay_Variable::STATUS_BLOCKED,
					BinaryPay_Variable::STATUS_FAILED,
					BinaryPay_Variable::STATUS_INPROGRESS,
				),
				'api-success-status'      => array(
					BinaryPay_Variable::STATUS_SUCCESSFUL,
				),
				'http-success-status'     => array( 200 ),
				'api-error-message-field' => 'error',
			)
		);

		$this->getToken();
	}

	public function getHeader() {
		$headers   = array();
		$headers[] = 'api-version: ' . self::API_VERSION;

		if ( $this->getConfig( 'request-content-type' ) == 'json' ) {
			$headers[] = 'Content-Type: application/com.genoapay.ecom-v3.0+json';
			$headers[] = 'Accept: application/com.genoapay.ecom-v3.0+json';
		}

		$headers[] = 'Authorization: ' . $this->getAuth();
		return $headers;
	}

	public function getAuth() {
		if ( ! $this->_issets( array( BinaryPay_Variable::USERNAME, BinaryPay_Variable::PASSWORD ), $this->getConfig() ) ) {
			throw new BinaryPay_Exception( 'HTTP ERROR: Cannot set authentication header' );
		}

		if ( $this->getConfig( 'authToken' ) ) {
			$encodedAuth = 'Bearer ' . $this->getConfig( 'authToken' );
		} else {
			$authString  = $this->getConfig( BinaryPay_Variable::USERNAME ) . ':' . $this->getConfig( BinaryPay_Variable::PASSWORD );
			$encodedAuth = 'Basic ' . base64_encode( $authString );
		}
		return $encodedAuth;
	}

	/**
	 * getToken
	 *
	 * @return [type]
	 */
	public function getToken() {
		$url = $this->getApiUrl() . self::API_VERSION . DIRECTORY_SEPARATOR . self::TOKEN_ENDPOINT;

		if ( ! $this->getConfig( 'authToken' ) ) {
			$this->setConfig(
				array(
					'method'                => 'post',
					'request-content-type'  => 'json',
					'response-content-type' => 'json',
					'api-success-status'    => 'authToken',
					'url'                   => $url,
					'request'               => array(),
				)
			);
			$this->setConfig( $this->query() );
		}
	}

	/**
	 * @description main function to query API.
	 * @param  array  request body
	 * @return string  returns API response
	 */

	public function getApiUrl() {
		switch ( $this->getConfig( BinaryPay_Variable::ENVIRONMENT ) ) {
			case 'production':
				$url = 'https://api.genoapay.com/';
				break;
			case 'sandbox':
				$url = 'https://api.uat.genoapay.com/';
				break;
			case 'development':
				$url = getenv( 'GATEWAY_API_URL', true ) ?: getenv( 'GATEWAY_API_URL' );
				break;
		}

		return $url;
	}

	/**
	 * getPurchaseUrl
	 */
	public function getPurchaseUrl() {
		return $this->getApiUrl() . self::API_VERSION . DIRECTORY_SEPARATOR . self::PURCHASE_ENDPOINT;
	}

	/**
	 * getRefundUrl
	 *
	 * @return string
	 */
	public function getRefundUrl( $token ) {
		return $this->getApiUrl() . self::API_VERSION . DIRECTORY_SEPARATOR . 'sale' . DIRECTORY_SEPARATOR . $token . DIRECTORY_SEPARATOR . 'refund';
	}

	/**
	 * getPurchaseStatusUrl
	 *
	 * @return string
	 */
	public function getPurchaseStatusUrl( $token ) {
		return $this->getApiUrl() . self::API_VERSION . DIRECTORY_SEPARATOR . self::PURCHASE_STATUS_ENDPOINT . DIRECTORY_SEPARATOR . $token . DIRECTORY_SEPARATOR . 'status';
	}

	/**
	 * getConfigurationUrl
	 *
	 * @return string
	 */
	public function getConfigurationUrl() {
		return $this->getApiUrl() . self::API_VERSION . DIRECTORY_SEPARATOR . self::CONFIGURATON_ENDPOINT;
	}

	/**
	 * creates a full array signature of a valid gateway request
	 *
	 * @return array gateway request signature format
	 */
	public function createSignature() {
		return array_merge(
			array(
				BinaryPay_Variable::ENVIRONMENT,
				BinaryPay_Variable::USERNAME,
				BinaryPay_Variable::PASSWORD,
				BinaryPay_Variable::AMOUNT,
				BinaryPay_Variable::REFERENCE,
				'returnUrls',
				'totalAmount',
				'billingAddress',
				'customer',
				'shippingAddress',
				BinaryPay_Variable::TAX_AMOUNT,
				BinaryPay_Variable::PRODUCTS,
				BinaryPay_Variable::CURRENCY,
				BinaryPay_Variable::REASON,
				BinaryPay_Variable::SHIPPING_LINES,
			),
			parent::createSignature()
		);
	}

	/**
	 * Get configuration back from Latitude Finance API
	 *
	 * @return array
	 */
	public function configuration( array $args = array() ) {
		$url     = $this->getConfigurationUrl();
		$request = array();

		$this->setConfig(
			array(
				'method'                => 'get',
				'request-content-type'  => 'json',
				'response-content-type' => 'json',
				'api-success-status'    => 'name',
				'url'                   => $url,
				'request'               => $request,
			)
		);

		return $this->query();
	}

	/**
	 * Pass in purchase payment info as below:
	 * TODO: Cannot support address in customer for now, since the array structure
	 *
	 * @param  array
	 * @return response
	 */
	public function purchase( array $args = array() ) {
		$url     = $this->getPurchaseUrl();
		$request = array(
			'totalAmount'     => array(
				'amount'   => round( $args[ BinaryPay_Variable::AMOUNT ], 2 ),
				'currency' => $args[ BinaryPay_Variable::CURRENCY ],
			),
			'returnUrls'      => array(
				'successUrl' => $args[ BinaryPay_Variable::RETURN_URL ],
				'failUrl'    => $args[ BinaryPay_Variable::RETURN_URL ],
			),
			'reference'       => $args[ BinaryPay_Variable::REFERENCE ],
			'customer'        => array(
				'mobileNumber' => $args[ BinaryPay_Variable::MOBILENUMBER ],
				'firstName'    => $args[ BinaryPay_Variable::FIRSTNAME ],
				'surname'      => $args[ BinaryPay_Variable::SURNAME ],
				'email'        => $args[ BinaryPay_Variable::EMAIL ],
			),
			'shippingAddress' => array(
				'addressLine1' => $args[ BinaryPay_Variable::SHIPPING_ADDRESS ],
				'suburb'       => $args[ BinaryPay_Variable::SHIPPING_SUBURB ],
				'cityTown'     => $args[ BinaryPay_Variable::SHIPPING_CITY ],
				'postcode'     => $args[ BinaryPay_Variable::SHIPPING_POSTCODE ],
				'countryCode'  => $args[ BinaryPay_Variable::SHIPPING_COUNTRY_CODE ],
			),
			'billingAddress'  => array(
				'addressLine1' => $args[ BinaryPay_Variable::BILLING_ADDRESS ],
				'suburb'       => $args[ BinaryPay_Variable::BILLING_SUBURB ],
				'cityTown'     => $args[ BinaryPay_Variable::BILLING_CITY ],
				'postcode'     => $args[ BinaryPay_Variable::BILLING_POSTCODE ],
				'countryCode'  => $args[ BinaryPay_Variable::BILLING_COUNTRY_CODE ],
			),
			'products'        => $args[ BinaryPay_Variable::PRODUCTS ],
			'taxAmount'       => array(
				'amount'   => round( $args[ BinaryPay_Variable::TAX_AMOUNT ], 2 ),
				'currency' => $args[ BinaryPay_Variable::CURRENCY ],
			),
			'shippingLines'   => $args[ BinaryPay_Variable::SHIPPING_LINES ],
		);

		// signature
		$signature = hash_hmac( 'sha256', base64_encode( $this->recursiveImplode( $request, '', true ) ), $this->getConfig( 'password' ) );

		// Clean implode buffer
		$this->gluedString = '';

		$this->setConfig(
			array(
				'method'                => 'post',
				'request-content-type'  => 'json',
				'response-content-type' => 'json',
				'api-success-status'    => 'token',
				'url'                   => $url . '?signature=' . $signature,
				'request'               => $request,
			)
		);

		return $this->query();
	}

	/**
	 * refund request
	 *
	 * @param  array $args
	 * @return response
	 */
	public function refund( $args ) {
		$token = $args[ BinaryPay_Variable::PURCHASE_TOKEN ];

		$request = array(
			'amount'    => array(
				'amount'   => round( $args[ BinaryPay_Variable::AMOUNT ], 2 ),
				'currency' => $args[ BinaryPay_Variable::CURRENCY ],
			),
			'reason'    => $args[ BinaryPay_Variable::REASON ],
			'reference' => $args[ BinaryPay_Variable::REFERENCE ],
		);

		// signature
		$signature = hash_hmac( 'sha256', base64_encode( $this->recursiveImplode( $request, '', true ) ), $this->getConfig( 'password' ) );

		// Clean implode buffer
		$this->gluedString = '';

		$this->setConfig(
			array(
				'method'                => 'post',
				'request-content-type'  => 'json',
				'response-content-type' => 'json',
				'api-success-status'    => 'refundId',
				'url'                   => $this->getRefundUrl( $token ) . '?signature=' . $signature,
				'request'               => $request,
			)
		);
		return $this->query();
	}

	/**
	 * retrieve
	 *
	 * @param  array $args
	 * @return array
	 */
	public function retrieve( array $args ) {
		$this->setConfig(
			array(
				'method'                => 'get',
				'request-content-type'  => 'json',
				'response-content-type' => 'json',
				'api-success-status'    => 'status',
				'url'                   => $this->getPurchaseStatusUrl( $args[ BinaryPay_Variable::PURCHASE_TOKEN ] ),
				'request'               => array(),
			)
		);

		return $this->query();
	}
}
