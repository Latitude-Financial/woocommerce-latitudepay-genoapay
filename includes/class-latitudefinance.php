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
abstract class BinaryPay extends WC_LatitudeFinance_Base implements GatewayInterface {

	private $_http;

	/**
	 * @var boolean
	 */
	protected $_debug;

	// Default options
	private $_config = array(
		'method'                  => 'post',
		'debug'                   => false,
		'http-success-status'     => array( 200, 201, 204, 422, 400 ),
		'api-error-message-field' => array(
			'error',
			'errorMessage',
			'message',
			'errormessage',
			'resultDesc',
			'errorDesc',
			'ErrorMessage',
			'TransactionStatusCode',
		),
		'api-error-code-field'    => array(
			'errorCode',
			'code',
			'errornumber',
			'error',
			'resultCode',
			'errorCode',
			'ErrorCode',
			'TransactionStatusCode',
		),
	);

	const GATEWAY_PATH = 'Gateways';

	public function __construct( $credential = array(), $debugMode = false ) {
		$this->verifyKeys( $this->createSignature(), $credential );
		$this->setConfig( $credential );
		$this->setConfig( array( 'debug' => $debugMode ) );
		parent::__construct();
	}


	/* We univerlized all the possible varaibles that might be passed in from build in order to have same key name at all time */
	public function createSignature() {
		return array(
			'environment',
			'ip',
			'apiKey',
			'password',
			'accountId',
		);
	}

	public function setConfig( $config ) {
		if ( is_array( $config ) ) {
			foreach ( $config as $k => $value ) {
				$this->_config[ $k ] = $value;
			}
		}
	}

	public function getConfig( $key = null ) {
		if ( isset( $this->_config[ $key ] ) ) {
			return $this->_config[ $key ];
		} elseif ( $key == null ) {
			return $this->_config;
		} else {
			return false;
		}
	}

	public function prepare() {
		$http = new WC_LatitudeFinance_Http( $this->getConfig() );
		$http->setHeader( $this->getHeader() );
		return $http;
	}

	public static function getGateway( $gateway, $credential, $debug = false ) {
		if ( empty( $gateway ) ) {
			throw new BinaryPay_Exception( 'Please define a gateway' );
		}

		if ( empty( $credential ) || ! array_key_exists( 'username', $credential ) ) {
			throw new BinaryPay_Exception( 'Please define credentials' );
		}

		if ( $gateway === 'test' ) {
			self::runTest( $credential );
		}

		if ( strpos( $gateway, 'test:' ) !== false ) {
			$gatewaySet = explode( ':', $gateway );
			// If input in single gateway
			if ( count( $gatewaySet ) <= 2 ) {
				self::runTest( $credential, explode( ':', $gateway )[1] );
				return;
			}
			// If input multiple gateways
			self::runTest( $credential, $gatewaySet );
			return;
		}

		$file = __DIR__ . DIRECTORY_SEPARATOR . self::GATEWAY_PATH . DIRECTORY_SEPARATOR . $gateway . '.php';

		if ( is_file( $file ) ) {
			require_once $file;
			$gateway = new $gateway( $credential, $debug );

			return $gateway;
		} else {
			throw new Exception( 'Cannot find gateway ' . $gateway );
		}
	}

	public function verifyKeys( $signature, &$attributes ) {
		if ( ! empty( $attributes ) ) {
			$userKeys    = array_keys( $attributes );
			$invalidKeys = array_diff( $userKeys, $signature );
			if ( ! empty( $invalidKeys ) ) {
				asort( $invalidKeys );
				$sortedList = join( ', ', $invalidKeys );
				throw new BinaryPay_Exception( 'invalid keys: ' . $sortedList );
			}
		}
	}

	public function query() {
		$args = $this->getConfig( 'request' );
		$this->verifyKeys( $this->createSignature(), $args );
		$apiUrl = $this->getConfig( 'url' );
		$http   = $this->prepare();

		/* Request content type*/
		switch ( $this->getConfig( 'request-content-type' ) ) {
			case 'json':
				$args = $this->toJson( $args );
				break;
			case 'xml':
				// $args = $this->toXml($args);
				break;
			default:
				// code...
				break;
		}

		switch ( $this->getConfig( 'method' ) ) {
			case 'put':
				$response = $http->put( $apiUrl, $args );
				break;
			case 'delete':
				$response = $http->delete( $apiUrl, $args );
				break;
			case 'get':
				$response = $http->get( $apiUrl, $args );
				break;
			default:
				$response = $http->post( $apiUrl, $args );
				break;
		}

		$response = $this->validate( $response );
		return $response;
	}

	public function clearify( $args ) {
		$keys = array(
			'originalTransactionId',
			'CardToken',
			'cardToken',
			'storeCard',
			'tokenReference',
			'acquirerResponseCode',
			'merchantToken',
			'cardStored',
			'amountString',
			'merchantToken',
			'authCode',
			'batchNumber',
			'returnUrl',
			'links',
			'href',
			'rel',
			'callbackUrl',
			'merchantUrl',
			'merchantIdCode',
		);

		foreach ( $args as $key => $value ) {
			if ( is_null( $value ) || empty( $value ) ) {
				unset( $args[ $key ] );
			}
		}

		$args = array_diff_key( $args, array_flip( $keys ) );
		return $args;
	}

	public function validate( $response ) {
		$status            = 0;
		$errorMessage      = 'Unknown';
		$httpSuccessStatus = $this->getConfig( 'http-success-status' );

		/* Check http response status */
		if ( in_array( (int) $response['status'], $httpSuccessStatus ) ) {
			$status += 1;
		}

		$successStatus = $this->getConfig( 'api-success-status' );

		/* Some transcations does not require success status*/
		if ( empty( $successStatus ) || ! is_array( $successStatus ) ) {
			$successStatus = array( $successStatus );
		}

		// When action will not have a success status returns
		foreach ( $successStatus as $success ) {
			if ( empty( $success ) || strpos( $response['body'], $success ) !== false ) {
				$status += 1;
				break;
			}
		}

		switch ( $this->getConfig( 'response-content-type' ) ) {
			case 'json':
				$response = self::jsonToArray( $response['body'] );
				break;
			case 'xml':
				$response = $this->xmlToArray( $response['body'] );
				break;
		}

		/* If there's no issues, return the response. */
		if ( $status >= 2 ) {
			return $response;
		}

		/* Find the error message field & code from any response*/
		$response     = $this->arrayFlatten( $response );
		$errorMessage = $this->find( 'api-error-message-field', $response );
		$errorCode    = $this->find( 'api-error-code-field', $response );
		throw new BinaryPay_Exception(
			sprintf( 'Message: %s', $errorMessage ),
			$errorCode
		);
	}

	protected function find( $field, $source ) {
		$config = $this->getConfig( $field );
		if ( ! is_array( $config ) ) {
			return $source[ $config ];
		}

		foreach ( $config as $key ) {
			if ( isset( $source[ $key ] ) ) {
				return $source[ $key ];
			}
		}
		return 'Unknown';
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	static function _getGatewayFileNames() {
		// Path for gateway files
		$gateway       = array();
		$pathToGateway = __DIR__ . DIRECTORY_SEPARATOR . 'Gateways';
		$gatewayFiles  = scandir( $pathToGateway );

		if ( ! $gatewayFiles ) {
			throw new Exception( 'No gateway files found.' );
		}

		foreach ( $gatewayFiles as $file ) {
			$info = pathinfo( $file );
			// Get only files that have .php extension
			if ( $info['extension'] === 'php' ) {
				$fileName  = strtolower( $info['filename'] );
				$gateway[] = $fileName;
			}
		}
		return $gateway;
	}

	static function _runGatewayTest( $gateways, $credential ) {
		if ( is_array( $gateways ) ) {
			foreach ( $gateways as $gateway ) {
				// Format all input to gateway filename standard
				$gateway = strtolower( $gateway );
				$gateway = ucfirst( $gateway );
				echo "\n[-]Starting \033[92m$gateway\033[37m Test:\n";
				// Convert to filename format
				$paymark = self::getGateway( $gateway, $credential );
				$paymark->test();
			}
		} else {
			$gateways = ucfirst( $gateways );
			echo "\n[-]Starting \033[92m$gateways\033[37m Test:\n";
			$paymark = self::getGateway( $gateways, $credential );
			$paymark->test();
		}
		return "\nTesing finished.\n";
	}

	/**
	 * @param $credential
	 * @param string     $gatewayName
	 * @throws Exception
	 */
	static function runTest( $credential, $gatewayName = '' ) {
		echo "\033[37m[+]Starting Testing Procedure...\n";

		// Run a full test
		if ( empty( $gatewayName ) ) {
			// Read Directory, looking for all gateway files
			$gatewayName = self::_getGatewayFileNames();
		}

		// Run test depends on test.php setting
		if ( is_array( $gatewayName ) ) {
			// multiple gateway testing
			array_shift( $gatewayName );
		}
			// Single gateway testing
		self::_runGatewayTest( $gatewayName, $credential );
	}

	public static function log( $message, $debug = false, $file = '' ) {
		if ( php_sapi_name() == 'cli' ) {
			return;
		}
		$file = empty( $file ) ? 'system.log' : $file;

		$logDir  = __DIR__ . '/../log';
		$logFile = $logDir . DIRECTORY_SEPARATOR . $file;

		if ( ! is_dir( $logDir ) ) {
			mkdir( $logDir );
			chmod( $logDir, 0750 );
		}

		if ( ! file_exists( $logFile ) ) {
			file_put_contents( $logFile, '' );
			chmod( $logFile, 0640 );
		}

		$file     = fopen( $logFile, 'a+' );
		$contents = fread( $file, filesize( $logFile ) + 1 );

		// Write any type of messages to the log file
		ob_start();
		print_r( $message );
		$result = ob_get_clean();

		// Add timestamp
		$timestamp = date( "Y/m/d----h:i:sa\n" );
		$result    = $timestamp . $result;

		// Add new line before dump data
		$contents = ( strlen( $contents ) > 1 ) ? "\n" . $result : $result;

		if ( $debug ) {
			debug_print_backtrace();
			$result    = ob_get_clean();
			$contents .= "\n" . $result;
		}

		if ( ob_get_length() ) {
			ob_end_clean();
		}

		fwrite( $file, $contents );

		// Close the file handle to save memory
		fclose( $file );

		return;
	}

	public function purchase( array $args ) {}

	/**
	 *
	 */
	public function refund( $args ) {
		throw new BinaryPay_Exception( 'The "Refund" endpoint does not exist.' );
	}

	public function authrise( $args ) {
		 throw new BinaryPay_Exception( 'The "Authrise" endpoint does not exist.' );
	}

	public function add( $args ) {
		throw new BinaryPay_Exception( 'The "Add" endpoint does not exist.' );
	}

	public function retrieve( array $args ) {
		throw new BinaryPay_Exception( 'The "Retrieve" endpoint does not exist.' );
	}

}
