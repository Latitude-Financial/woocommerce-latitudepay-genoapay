<?php
class Latitudepay extends Genoapay
{

    public function getHeader()
    {
        $headers[] = "api-version: " . self::API_VERSION;

        if ($this->getConfig('request-content-type') == 'json') {
            $headers[] = "Content-Type: application/com.latitudepay.ecom-v3.0+json";
            $headers[] = "Accept: application/com.latitudepay.ecom-v3.0+json";
        }

        $headers[] = "Authorization: " . $this->getAuth();
        return $headers;
    }

  /**
     * @description main function to query API.
     * @param  array  request body
     * @return array  returns API response
     */

    public function getApiUrl()
    {
        switch ($this->getConfig(BinaryPay_Variable::ENVIRONMENT)) {
            case 'production':
                $url = 'https://api.latitudepay.com/';
                break;
            case 'sandbox':
            case 'development':
                $url = 'https://api.uat.latitudepay.com/';
                break;
        }

        return $url;
    }

}