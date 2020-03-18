<?php
abstract class BinaryPay
{
    /**
     *  API All Response Status
     */
    const STATUS_DECLINED   = 'DECLINED';
    const STATUS_BLOCKED    = 'BLOCKED';
    const STATUS_FAILED     = 'FAILED';
    const STATUS_INPROGRESS = 'INPROGRESS';
    const STATUS_SUCCESSFUL = 'SUCCESSFUL';
    const STATUS_AUTHORISED = 'AUTHORISED';
    const STATUS_SUBMITTED  = 'SUBMITTED';
    const STATUS_REFUNDED   = 'REFUNDED';
    const STATUS_NEW        = 'NEW';
    const STATUS_EXPIRED    = 'EXPIRED';
    const STATUS_UNKNOWN    = 'UNKNOWN';
    const STATUS_APPROVED   = 'APPROVED';
    const STATUS_ERROR      = 'ERROR';
    const STATUS_PROCESSING = 'IN_PROGRESS';

    /**
     *  Account Infomation
     */
    const ACCOUNTID         = 'accountId';
    const USERNAME          = 'username';
    const PASSWORD          = 'password';
    const ENVIRONMENT       = 'environment';


    const GATEWAY_PATH      = 'Gateways';

    const ERROR_SIGNAL      = 'BinaryPay Notice';

    /**
     * API key for binarypay
     */
    const API_KEY           = 'apiKey';

    /**
     *  Purchase or Refund Amount
     */
    const AMOUNT            = 'amount';

    /**
     * Currency Type
     */
    const CURRENCY          = 'currency';

    /**
     * User Agent - user's browser
     */
    const USER_AGENT        = 'userAgent';

    /**
     * Transaction - an array contains transaction details
     */
    const TRANSACTION       = 'transaction';

    /**
     * Product Code - for qcard long term finance
     */
    const PRODUCT_CODE      = 'productCode';

    /**
     * Direct to return URL after purchase
     */
    const REDIRECT          = 'redirecToUrlResponse';

    /**
     * Process number returned by Qcard API
     */
    const PROCESS_NO        = "processNo";

    /**
     * Quantity of line items - qcard
     */
    const QUANTITY          = 'quantity';
    /**
     * ID
     */
    const IP                = 'ip';

    /**
     *  For all Callback Url
     */
    const RETURN_URL        = 'returnUrl';

    /**
     *  Purchase or Refund Reference
     */
    const REFERENCE         = 'reference';

    /**
     *  Purchase or Refund particular
     */
    const PARTICULAR        = 'particular';

    /**
     *  Card Number
     */
    const CARD_NUMBER       = 'cardNumber';

    /**
     *  Card Type
     */
    const CARD_TYPE         = 'cardType';

    /**
     *  Card Expiry
     */
    const CARD_EXPIRY       = 'cardExpiry';

    /**
     *  Card Holder
     */
    const CARD_HOLDER       = 'cardHolder';

    /**
     *  Card CSC
     */
    const CARD_CSC          = 'cardCSC';

    /**
     *  Card Token
     */
    const CARD_TOKEN        = 'cardToken';

    /**
     *  Token Reference
     */
    const CARD_TOKEN_REF    = 'tokenReference';

    /**
     *  Origin Transaction Id
     */
    const ORIGIN_TRANSACTION_ID    = 'originalTransactionId';

    /**
     * Transaction Id
     */
    const TRANSACTION_ID    = 'transactionId';

    /**
     *  Email
     */
    const EMAIL             = 'email';

    /**
     * Merchat
     */
    const MERCHANT          = 'merchant';

    /**
     * Merchant Code
     */
    const MERCHANT_CODE     = 'merchantCode';

    /**
     * Merchant Url
     */
    const MERCHANT_URL      = 'merchantUrl';

    /**
     *  Webpayment Merchant Token
     */
    const MERCHANT_TOKEN    = 'merchantToken';

    /**
     *  Webpayment Store Card
     */
    const STORE_CARD        = 'storeCard';

    /**
     *  Webpayment Force Store Card
     */
    const FORCE_STORE_CARD  = 'forceStoreCard';

    /**
     *  Webpayment Display Email
     */
    const DISPLAY_EMAIL     = 'displayCustomerEmail';

    /**
     *  WebPayment CMD Code
     */
    const CMD    = 'cmd';

    /**
     * Bank Info
     */
    const BANK = 'bank';

    const SKU  = 'sku';

    /**
     * OnlineEFTPOS PayerId
     */
    const MOBILENUMBER      = 'mobileNumber';

    /**
     * OnlineEFTPOS Description
     */
    const DESCRIPTION       = 'description';

    /**
     * PayerId Type
     */
    const PAYMENT_TYPE       = 'paymentType';

    /**
     * Refund Id
     */
    const REFUND_ID          = 'refundId';

    /**
     * Payment Id
     */
    const ORDER_ID           = 'orderId';

    /**
     * Bank Id
     */
    const BANK_ID            = 'bankId';

    /**
     * Term - financenow
     */
    const TERM               = 'term';

    /**
     * Defterm - financenow
     */
    const DEFTERM            = 'defterm';

    /**
     * Deposit - financenow
     */
    const DEPOSIT            = 'deposit';

    /**
     * Rate - financenow
     */
    const RATE               = 'rate';

    /**
     * Customer name - financenow
     */
    const CUSTOMER_NAME      = 'customerName';

    /**
     *Customer email - financenow
     */
    const CUSTOMER_EMAIL     = 'customerEmail';

    /**
     * GENOAPAY
     */
    /**
     * Firstname
     */
    const FIRSTNAME          = 'firstname';

    /**
     * Surname
     */
    const SURNAME            = 'surname';

    /**
     * Shipping Address
     */
    const SHIPPING_ADDRESS   = 'shippingAddress';

    /**
     * Billing Address
     */
    const BILLING_ADDRESS    = 'billingAddress';

    /**
     * Suburb
     */
    const SHIPPING_SUBURB             = 'shippingSuburb';

    /**
     * City
     */
    const SHIPPING_CITY               = 'shippingCity';

    /**
     * Postcode
     */
    const SHIPPING_POSTCODE           = 'shippingPostcode';

    /**
     * Country Code
     */
    const SHIPPING_COUNTRY_CODE       = 'shippingCountryCode';

    /**
     * Suburb
     */
    const BILLING_SUBURB             = 'billingSuburb';

    /**
     * City
     */
    const BILLING_CITY               = 'billingCity';

    /**
     * Postcode
     */
    const BILLING_POSTCODE           = 'billingPostcode';

    /**
     * Country Code
     */
    const BILLING_COUNTRY_CODE       = 'billingCountryCode';

    /**
     * Purchase Token
     */
    const PURCHASE_TOKEN             = 'purchaseToken';

    /**
     * Tax amount
     */
    const TAX_AMOUNT                 = 'taxAmount';

    /**
     * Shipping Lines
     */
    const SHIPPING_LINES             = 'shippingLines';

    /**
     * Product
     */
    const PRODUCTS                   = 'products';

    /**
     * Refund Reason
     */
    const REASON                     = 'reason';

    /**
     * FailureURL POLIPAY
     */
    const FAILURE_URL                = 'FailureURL';

    /**
     * NotificationURL POLIPAY
     */
    const NOTIFICATION_URL           = 'NotificationURL';

    /**
     * CancellationURL POLIPAY
     */
    const CANCELLATION_URL           = 'CancellationURL';

    /**
     * MerchantReferenceFormat POLIPAY
     */
    const REFERENCEFORMAT            = 'ReferenceFormat';


    protected $_HTTP_SUCCESS_STATUS = array(200);

    private $_http;

    // Default options
    private $_config = array('debug' => false);

    public function _issets($keys, array $source, $debug = false)
    {
        if (gettype($keys) == 'string' and strpos($keys, ',') !== false) {
            $keys = explode($keys);
        }

        $exist = 0;
        foreach ($keys as $k) {
            $isset = isset($source[$k]) ? 1 : 0;
            $exist += $isset;
            if ($debug) {
                echo "$k : $isset \n";
            }
        }

        return count($keys) == $exist;
    }

    /**
     * Convert under_score type array's keys to camelCase type array's keys
     * @param   array   $array          array to convert
     * @param   array   $arrayHolder    parent array holder for recursive array
     * @return  array   camelCase array
     */
    public function camelize($array, $arrayHolder = array())
    {
        $camelCaseArray = !empty($arrayHolder) ? $arrayHolder : array();
        foreach ($array as $key => $val) {
            $newKey = @explode('_', $key);
            array_walk($newKey, create_function('&$v', '$v = ucwords($v);'));
            $newKey = @implode('', $newKey);
            $newKey{0} = strtolower($newKey{0});
            if (!is_array($val)) {
                $camelCaseArray[$newKey] = $val;
            } else {
                $camelCaseArray[$newKey] = $this->camelize($val, $camelCaseArray[$newKey]);
            }
        }
        return $camelCaseArray;
    }

    /**
     * arrayFlatten converts multidimentional array to array
     * @param  array $array array to convert
     * @return array single level array
     */
    public function arrayFlatten($array)
    {
        if (!is_array($array)) {
            return false;
        }
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->arrayFlatten($value));
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * xmlToArray
     *
     * Convert Xml to an Array
     * @param  Object $xmlObject
     * @param  array  $out
     * @return array
     */
    public function xmlToArray($xmlObject, $out=array())
    {
        if (gettype($xmlObject) == 'string') {
            $xmlObject = simplexml_load_string($xmlObject);
        }

        foreach ((array) $xmlObject as $index => $node) {
            $out[$index] = (is_object($node)) ? $this->xmlToArray($node) : $node;
        }
        if (!empty($out)) {
            return $out;
        }
    }

    /**
     * toJson
     *
     * Convert array to json
     * @param  array $variable
     * @return $json
     */
    public function toJson($variable)
    {
        return empty($variable) ? false : json_encode($variable);
    }

    /**
     * jsonToArray
     *
     * Convert json to an array
     * @param  json $json
     * @return array
     */
    public function jsonToArray($json)
    {
        return empty($json) ? false : json_decode($json, true);
    }

    /**
     * encrptData: encrptData before send to the server
     * @param  string $data
     * @return string
     */
    public function encryptData(string $data)
    {
        $encryptedHex   = '';
        $publicKey      = openssl_pkey_get_public('file:///' . __DIR__ . '/ssl/public.crt');

        // If string is bigger than 400
        // Split $data to smaller chunks
        strlen($data) > 400 ? $dataSet = str_split($data, 400) : $dataSet[0] = $data;

        foreach ($dataSet as $index => $data) {
            if (!openssl_public_encrypt($data, $encrypted, $publicKey)) {
                while ($msg = openssl_error_string()) {
                    throw new Exception($msg . "\n");
                }
            }
            // Do not add break point at the first
            $breakPoint = $index == 0 ? '' : '--BREAK_POINT--';

            // Add break point to the string
            // Convert encrypted data to Hex
            $encryptedHex .= $breakPoint . bin2hex($encrypted);
        }

        openssl_pkey_free($publicKey);

        return $encryptedHex;
    }

    /**
     * decryptData
     *
     * Decrypt the encrypted data
     * @param  array  $data
     * @return string
     */
    public function decryptData(array $data)
    {
        $response = $data['body'];
        $decryptedHex = '';
        $publicKey = openssl_pkey_get_public('file:///'. __DIR__ . '/ssl/public.crt');

        foreach (explode('--BREAK_POINT--', $response) as $encrypted) {
            if (!openssl_public_decrypt(hex2bin($encrypted), $decrypted, $publicKey)) {
                while ($msg = openssl_error_string()) {
                    throw new Exception($msg . "\n");
                }
            }
            $decryptedHex .= $decrypted;
        }

        openssl_pkey_free($publicKey);

        return $decrypted;
    }

    /**
     * __construct
     *
     * Set Gateway Name to Config
     * @param array $credential
     */
    public function __construct($credential = array())
    {
        $this->setConfig($credential);
        $this->setConfig(
            array(
                'gateway' => get_called_class()
            )
        );
    }

    /**
     * getHeader
     *
     * Set Header to send data with Api Key
     * @return array $headers
     */
    public function getHeader()
    {
        $headers[] = "Content-Type: application/json";
        // $headers[] = "apikey:" . $this->getConfig('apiKey');
        return $headers;
    }

    /**
     * setConfig
     *
     * Set all the configuration data
     * @param array $config
     */
    public function setConfig($config)
    {
        if (is_array($config)) {
            foreach ($config as $k => $value) {
                $this->_config[$k] = $value;
            }
        }
    }

    /**
     * getConfig
     *
     * Get the configuration data
     * @param  string $key
     * @return array | String
     */
    public function getConfig($key = null)
    {
        if (isset($this->_config[$key])) {
            return $this->_config[$key];
        } elseif ($key == null) {
            return $this->_config;
        } else {
            return false;
        }
    }

    /**
     * unsConfig
     *
     * Unset unessary configuration data
     * @param  string $key
     */
    public function unsConfig($key = null)
    {
        if (isset($this->_config[$key])) {
            unset($this->_config[$key]);
        } elseif ($key == null) {
            return $this->_config = array();
        } else {
            return false;
        }
    }

    /**
     * prepare
     *
     * Get Http Object
     *
     */
    public function prepare()
    {
        $this->_http   = new BinaryPay_Http($this->getConfig());
        $this->_http->setHeader($this->getHeader());
    }

    /**
     * _getApiUrl
     *
     * Return Api Url depends on current use environment
     * @return string
     */
    protected function _getApiUrl()
    {
        if ($this->getConfig('environment') === 'sandbox') {
            return 'https://sandbox.binarypay.nz';
        } elseif ($this->getConfig('environment') === 'development') {
            return 'http://sandbox.api.binarypay.test';
        }
        return 'https://api.binarypay.nz';
    }

    /**
     * getGateway
     *
     * Get specific Api
     * @param  string $gateway
     * @param  array $credential
     * @return Object
     */
    public static function getGateway($gateway, $credential)
    {
        if (empty($gateway)) {
            throw new BinaryPay_Exception('Please define a gateway');
        }

        if (empty($credential) || !array_key_exists('username', $credential)) {
            throw new BinaryPay_Exception('Please define credentials');
        }

        $file = __DIR__ . DIRECTORY_SEPARATOR . self::GATEWAY_PATH . DIRECTORY_SEPARATOR . $gateway . '.php';

        if (is_file($file)) {
            require_once $file;
            $gateway = new $gateway($credential);

            return $gateway;
        } else {
            throw new Exception('Cannot find gateway ' . $gateway);
        }
    }

    // POST from here to API:
    // contains: gateway, actions, credentials, $args.
    // When responese has returned from the local API, validate it with local validate function
    // for expctions.
    // Consider kong API key headers auth in the end.
    public function query($action, $request)
    {
        $request['action'] = $action;

        $this->setConfig($request);

        $apiUrl = $this->_getApiUrl();

        $this->prepare();

        $request = $this->getConfig();

        // Mage::log($request);die();

        // Json format request
        $request = $this->toJson($request);
        // Encrpt Request
        // $request = $this->encryptData($request);

        // Post request to Api
        $response = $this->_http->post($apiUrl, $request);

        // Validate response
        $response = $this->validate($response);

        return $response;
    }

    /**
     * isUrlValid
     *
     * Validate Server enviorment
     * if the server can not be reached, return false
     * @param  string $url
     * @return boolean
     */
    public function isUrlValid($url)
    {
        if (empty($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new BinaryPay_Exception(
                'Your domain name is not valid!',
                BinaryPay::ERROR_SIGNAL
            );
        }

        $this->prepare();

        $response = $this->_http->get($url, '');

        if ($response['status'] === 0 || $response['status'] === 404) {
            throw new BinaryPay_Exception(
                'Your server could not be reached!',
                BinaryPay::ERROR_SIGNAL
            );
        }

        return true;
    }

    /**
     * validate
     *
     * Validate response and throw exception if there is an error returns back
     * @param  json  $response
     * @return array $response
     */
    public function validate($response)
    {
        $error = 0;
        $errorCode = $errorMessage = 'Unknown';

        // Get all http success status
        $httpSuccessStatus = $this->_HTTP_SUCCESS_STATUS;

        /* Check http response status */
        if (!in_array((int) $response['status'], $httpSuccessStatus)) {
            $error = 1;
        }

        if (strpos($response['body'], BinaryPay::ERROR_SIGNAL)) {
            $error = 1;
        }

        // Convert Json data to array
        $response   = $this->jsonToArray($response['body']);

        // Return response if there is no error occurred
        if (!$error) {
            return $response;
        }

        $errorCode      = $response['errorCode'];
        $errorMessage   = $response['errorMessage'];
        throw new BinaryPay_Exception(
            sprintf("Code: %s Message: %s", $errorCode, $errorMessage),
            $errorCode
        );
    }

    /**
     * _verifyKeys
     *
     * Verify Keys for client request data
     * @param  array $requestKeys
     * @param  array $requireKeys
     * @return $this
     */
    protected function _verifyKeys($requestKeys, $requireKeys)
    {
        $invalidKeys = array_diff($requireKeys, $requestKeys);

        // Requested data must be exactly same as the server needs
        if (!empty($invalidKeys)) {
            asort($invalidKeys);
            $sortedList = join(', ', $invalidKeys);
            throw new BinaryPay_Exception('invalid keys: '. $sortedList);
        }
    }

    /**
     * clearify
     *
     * Drop all the data that Magento will not needs
     * @param  array $args
     * @return array
     */
    public function clearify($args)
    {
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
            'merchantIdCode'
        );

        foreach ($args as $key => $value) {
            if (is_null($value) || empty($value)) {
                unset($args[$key]);
            }
        }

        $args = array_diff_key($args, array_flip($keys));
        return $args;
    }

    /**
     * purchase
     *
     * Process a purchase transaction
     * @param  array  $request
     * @return array
     */
    public function purchase($request = array())
    {
        return $this->query('purchase', $request);
    }

    /**
     * refund
     *
     * Process a refund transaction
     * @param  array $request
     * @return array
     */
    public function refund($request)
    {
        return $this->query('refund', $request);
    }

    /**
     * add
     *
     * Add card to Paymark
     * @param array $request
     */
    public function add($request)
    {
        return $this->query('add', $request);
    }

    /**
     * delete
     *
     * Delete card from Paymark
     * @param  array $request
     * @return array
     */
    public function delete($request)
    {
        return $this->query('delete', $request);
    }

    /**
     * retrieve
     *
     * Retrieve card info bt card token
     * @param  array $request
     * @return array
     */
    public function retrieve($request)
    {
        return $this->query('retrieve', $request);
    }

    /**
     * update
     *
     * Update card information from Paymark
     * @param  array $request
     * @return array
     */
    public function update($request)
    {
        return $this->query('update', $request);
    }

    /**
     * getTransaction
     *
     * Get transaction information by transactiono id
     * @param  string  $txnId
     * @param  boolean $refundTransaction
     * @return array
     */
    public function getTransaction($txnId, $refundTransaction = false)
    {
        $action = $refundTransaction ? 'getRefundDetails' : 'getTransactionById';
        $request = array(
            BinaryPay::TRANSACTION_ID  => $txnId
        );
        return $this->query($action, $request);
    }
}