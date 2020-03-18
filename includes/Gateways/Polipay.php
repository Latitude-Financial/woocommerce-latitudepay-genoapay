<?php
class Polipay extends BinaryPay
{
    /**
   * purchase
   *
   * Validate user passed in params
   *
   * @param  array  $request
   * @return $this
   */
    public function purchase($request = array())
    {
        $requireKeys = array(
          BinaryPay::AMOUNT,
          BinaryPay::CURRENCY,
          BinaryPay::REFERENCE,
          BinaryPay::MERCHANT_URL,
          BinaryPay::RETURN_URL,
          BinaryPay::MERCHANT_CODE
          // BinaryPay::NOTIFICATION_URL
       );

       $this->_verifyKeys(array_keys($request), $requireKeys);
       return parent::purchase($request);
    }

    /**
     * retrieve
     *
     * Validate user passed in params
     * @param  array $request
     * @return $this
     */
    public function retrieve($request)
    {

        $requireKeys = array(
            BinaryPay::PURCHASE_TOKEN
        );

        $requestKeys = array_keys($request);

        $this->_verifyKeys(array_keys($request), $requireKeys);
        return parent::retrieve($request);
    }

}