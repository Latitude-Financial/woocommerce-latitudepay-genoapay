<?php
class Genoapay extends BinaryPay
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
           BinaryPay::RETURN_URL,
           BinaryPay::CURRENCY,
           BinaryPay::REFERENCE,
           BinaryPay::MOBILENUMBER,
           BinaryPay::FIRSTNAME,
           BinaryPay::SURNAME,
           BinaryPay::EMAIL,
           BinaryPay::SHIPPING_ADDRESS,
           BinaryPay::SHIPPING_SUBURB,
           BinaryPay::SHIPPING_CITY,
           BinaryPay::SHIPPING_POSTCODE,
           BinaryPay::SHIPPING_COUNTRY_CODE,
           BinaryPay::BILLING_ADDRESS,
           BinaryPay::BILLING_SUBURB,
           BinaryPay::BILLING_CITY,
           BinaryPay::BILLING_POSTCODE,
           BinaryPay::BILLING_COUNTRY_CODE,
           BinaryPay::PRODUCTS,
           BinaryPay::TAX_AMOUNT,
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

    /**
     * refund
     *
     * Validate user passed in params
     * @param  array $request
     * @return $this
     */
    public function refund($request)
    {
        $requireKeys = array(
            BinaryPay::AMOUNT,
            BinaryPay::PURCHASE_TOKEN,
            BinaryPay::CURRENCY,
            BinaryPay::REASON,
            BinaryPay::REFERENCE
        );

        $requestKeys = array_keys($request);

        $this->_verifyKeys($requestKeys, $requireKeys);

        return parent::refund($request);
    }
}