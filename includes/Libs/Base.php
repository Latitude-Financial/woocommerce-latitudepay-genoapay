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

class WC_LatitudeFinance_Base {

	/**
	 * $_attributes
	 *
	 * @var array
	 */
	protected $_attributes = array();

	/**
	 * $gluedString
	 *
	 * @var string
	 */
	protected $gluedString = '';

	/**
	 * @ignore
	 * don't permit an explicit call of the constructor!
	 * (like $t = new Transaction())
	 */
	protected function __construct() {
	}

	/**
	 * Disable cloning of objects
	 *
	 * @ignore
	 */
	protected function __clone() {
	}

	/**
	 * Accessor for instance properties stored in the private $_attributes property
	 *
	 * @ignore
	 * @param string $name
	 * @return mixed
	 */
	public function __get( $name ) {
		if ( array_key_exists( $name, $this->_attributes ) ) {
			return $this->_attributes[ $name ];
		} else {
			trigger_error( 'Undefined property on ' . get_class( $this ) . ': ' . $name, E_USER_NOTICE );
			return null;
		}
	}

	/**
	 * Checks for the existance of a property stored in the private $_attributes property
	 *
	 * @ignore
	 * @param string $name
	 * @return boolean
	 */
	public function __isset( $name ) {
		return array_key_exists( $name, $this->_attributes );
	}

	/**
	 * Mutator for instance properties stored in the private $_attributes property
	 *
	 * @ignore
	 * @param string $key
	 * @param mixed  $value
	 */
	public function _set( $key, $value ) {
		$this->_attributes[ $key ] = $value;
	}


	public function _issets( $keys, array $source, $debug = false ) {
		if ( gettype( $keys ) == 'string' && strpos( $keys, ',' ) !== false ) {
			$keys = explode( $keys );
		}

		$exist = 0;
		foreach ( $keys as $k ) {
			$isset  = isset( $source[ $k ] ) ? 1 : 0;
			$exist += $isset;
			if ( $debug ) {
				echo "$k : $isset \n";
			}
		}

		return count( $keys ) == $exist;
	}

	/**
	 * Convert under_score type array's keys to camelCase type array's keys
	 *
	 * @param   array $array          array to convert
	 * @param   array $arrayHolder    parent array holder for recursive array
	 * @return  array   camelCase array
	 */
	public function camelize( $array, $arrayHolder = array() ) {
		$camelCaseArray = ! empty( $arrayHolder ) ? $arrayHolder : array();
		foreach ( $array as $key => $val ) {
			$newKey = explode( '_', $key );
			array_walk( $newKey, create_function( '&$v', '$v = ucwords($v);' ) );
			$newKey    = implode( '', $newKey );
			$newKey[0] = strtolower( $newKey[0] );
			if ( ! is_array( $val ) ) {
				$camelCaseArray[ $newKey ] = $val;
			} else {
				$camelCaseArray[ $newKey ] = $this->camelize( $val, $camelCaseArray[ $newKey ] );
			}
		}
		return $camelCaseArray;
	}

	/**
	 * arrayFlatten converts multidimentional array to array
	 *
	 * @param  array $array array to convert
	 * @return array single level array
	 */
	public function arrayFlatten( $array ) {
		if ( ! is_array( $array ) ) {
			return false;
		}
		$result = array();
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$result = array_merge( $result, $this->arrayFlatten( $value ) );
			} else {
				$result[ $key ] = $value;
			}
		}
		return $result;
	}

	public function xmlToArray( $xmlObject, $out = array() ) {
		if ( gettype( $xmlObject ) == 'string' ) {
			$xmlObject = simplexml_load_string( $xmlObject );
		}

		foreach ( (array) $xmlObject as $index => $node ) {
			$out[ $index ] = ( is_object( $node ) ) ? $this->xmlToArray( $node ) : $node;
		}
		if ( ! empty( $out ) ) {
			return $out;
		}
	}

	public function toJson( $variable ) {
		// https://stackoverflow.com/questions/42981409/php7-1-json-encode-float-issue/43056278
		if ( version_compare( phpversion(), '7.1', '>=' ) ) {
			ini_set( 'precision', 14 );
			ini_set( 'serialize_precision', -1 );
		}
		return empty( $variable ) ? '' : json_encode( $variable, JSON_UNESCAPED_SLASHES );
	}

	/**
	 * jsonToArray
	 *
	 * Convert json to an array
	 *
	 * @param  json $json
	 * @return array
	 */
	public static function jsonToArray( $json ) {
		return empty( $json ) ? array() : json_decode( $json, true );
	}

	/**
	 * validateRequest
	 *
	 * validate request if come from Kong API or Direct access from the internet
	 *
	 * @return boolean
	 */
	public static function validateRequest() {
		// Validate request header
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
		}

		if ( ! $userAgent || $userAgent !== BinaryPay_Variable::HTTP_USERAGENT ) {
			throw new BinaryPay_Exception(
				'Invalid User Agent. You do not have the permission to access this server directly',
				BinaryPay_Variable::HTTP_ERROR_CODE
			);
			return false;
		}

		// Validate request http method
		if ( isset( $_SERVER['REQUEST_METHOD'] ) ) {
			$requestMethod = $_SERVER['REQUEST_METHOD'];
		}

		if ( ! $requestMethod || $requestMethod !== BinaryPay_Variable::HTTP_REQUEST_METHOD ) {
			throw new BinaryPay_Exception(
				'Invalid request Method. You do not have the permission to access this server directly',
				BinaryPay_Variable::HTTP_ERROR_CODE
			);
			return false;
		}

		return true;
	}

	public static function validateAction( $api, $action ) {
		if ( ! in_array( $action, get_class_methods( $api ) ) ) {
			throw new BinaryPay_Exception(
				'Invalid action method!',
				BinaryPay_Variable::HTTP_ERROR_CODE
			);
		}
		return true;
	}

	/**
	 * Recursively implodes an array with optional key inclusion
	 *
	 * Example of $include_keys output: key, value, key, value, key, value
	 *
	 * @access  public
	 * @param   array  $array         multi-dimensional array to recursively implode
	 * @param   string $glue          value that glues elements together
	 * @param   bool   $include_keys  include keys before their values
	 * @param   bool   $trim_all      trim ALL whitespace from string
	 * @return  string  imploded array
	 */
	public function recursiveImplode( array $array, $glue = ',', $include_keys = false, $trim_all = true ) {
		foreach ( $array as $key => $value ) {
			if ( is_string( $key ) ) {
				$this->gluedString .= $key . $glue;
			}
			is_array( $value ) ? $this->recursiveImplode( $value, $glue, $include_keys, $trim_all ) : $this->gluedString .= trim( json_encode( $value, JSON_UNESCAPED_SLASHES ), '"' ) . $glue;
		}
		// Removes last $glue from string
		strlen( $glue ) > 0 && $this->gluedString = substr( $this->gluedString, 0, -strlen( $glue ) );
		// Trim ALL whitespace
		$trim_all && $this->gluedString = preg_replace( '/(\s)/ixsm', '', $this->gluedString );
		return (string) $this->gluedString;
	}
}
