<?php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__)));

class Webpayment extends Directpost
{
    /**
   * purchase
   *
   * Validate user passed in params
   * If it is passed verification, return response
   * @param  array  $request
   * @return $array
   */
    public function purchase($request = array())
    {
        $requireKeys = array(
            BinaryPay::ACCOUNTID,
            BinaryPay::AMOUNT,
            BinaryPay::REFERENCE,
            BinaryPay::PARTICULAR,
            // The Url that will be posted to, after the purchase
            BinaryPay::RETURN_URL,
            BinaryPay::STORE_CARD,
            BinaryPay::DISPLAY_EMAIL
        );

        $requestKeys = array_keys($request);

        if (in_array(BinaryPay::CARD_TOKEN, $requestKeys)) {
          return parent::purchase($request);
        }

        $this->_verifyKeys($requestKeys, $requireKeys);

        return $this->query('purchase', $request);
    }
}