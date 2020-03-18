<?php

class BinaryPay_Http
{
    const HTTP_REQUEST_GET      = 'GET';
    const HTTP_REQUEST_POST     = 'POST';
    const HTTP_REQUEST_PUT      = 'PUT';
    const HTTP_REQUEST_DELETE   = 'DELETE';

    protected $_config;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function post($path, $params = null)
    {
        $response = $this->_doRequest(self::HTTP_REQUEST_POST, $path, $params);
        return $response;
    }

    public function put($path, $params = null)
    {
        $response = $this->_doRequest(self::HTTP_REQUEST_PUT, $path, $params);
        return $response;
    }

    public function get($path, $params = null)
    {
        $response = $this->_doRequest(self::HTTP_REQUEST_GET, $path, $params);
        return $response;
    }

    public function delete($path, $params = null)
    {
        $response = $this->_doRequest(self::HTTP_REQUEST_DELETE, $path, $params);
        return $response;
    }

    private function _doRequest($httpVerb, $path, $requestBody = null)
    {
        return $this->_doUrlRequest($httpVerb, $path, $requestBody);
    }

    public function _doUrlRequest($httpVerb, $url, $requestBody = null)
    {
        //TODO: Add debug tag which shows every step of the requests.
        if (is_array($requestBody)) {
            $requestBody = http_build_query($requestBody);
        }

        if ($httpVerb == self::HTTP_REQUEST_GET) {
            $url = trim($url) . '?' .$requestBody;
        }

        $curl = curl_init();
        $headers = $this->_getHeader();
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            //CURLOPT_ENCODING => "gzip",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => 'MageBinary BinaryPay API Integration Engine',
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpVerb,
            CURLOPT_HTTPHEADER => $headers,
            CURLINFO_HEADER_OUT => true
        ));

        if(!empty($requestBody) && $httpVerb != self::HTTP_REQUEST_GET) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $requestBody);
        }

        // Mage::log($requestBody);die();

        /* Adding SSL support with setConfig. It must comes with CA string */
        /* @TODO: This might still need some work in the future.           */
        // if (isset($this->_config['ssl']) && isset($this->_config['ssl-ca'])) {
        //     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        //     curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        //     curl_setopt($curl, CURLOPT_CAINFO, $this->_config['ssl-ca']);
        // }
        $response   = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response = array('status' => $httpStatus, 'body' => $response);
        /*TODO: TIDY*/
        if ($this->_config['debug']) {
            $response['request-header'] = "\n".curl_getinfo($curl, CURLINFO_HEADER_OUT);
            $response['request-header'] .= $requestBody."\n\n";
            print_r($response);
        }
        curl_close($curl);
        return $response;

    }

    protected function _getHeader()
    {
        if (!isset($this->_headers)) {
            throw new BinaryPay_Exception('No HTTP headers set');
        }

        return $this->_headers;
    }


    public function setHeader($header)
    {
        if (!isset($this->_headers)) {
            $this->_headers = $header;
        }
    }
}
