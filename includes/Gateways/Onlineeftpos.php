<?php
class Onlineeftpos extends BinaryPay
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
            BinaryPay::MOBILENUMBER,
            //BinaryPay::PAYMENT_TYPE,
            //BinaryPay::BANK_ID,
            BinaryPay::ACCOUNTID,
            BinaryPay::RETURN_URL,
            BinaryPay::MERCHANT_URL,
            BinaryPay::AMOUNT,
            BinaryPay::CURRENCY,
            BinaryPay::DESCRIPTION,
            BinaryPay::REFERENCE,
            BinaryPay::USER_AGENT,
            BinaryPay::IP,
        );
        $requestKeys = array_keys($request);

        $this->_verifyKeys($requestKeys, $requireKeys);

        return parent::purchase($request);
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
            //BinaryPay::MOBILENUMBER,
            //BinaryPay::PAYMENT_TYPE,
            //BinaryPay::BANK_ID,
            BinaryPay::ACCOUNTID,
            BinaryPay::AMOUNT,
            BinaryPay::DESCRIPTION,
            BinaryPay::REFUND_ID,
            BinaryPay::ORIGIN_TRANSACTION_ID,
            BinaryPay::USER_AGENT,
            BinaryPay::IP
        );

        $requestKeys = array_keys($request);

        $this->_verifyKeys($requestKeys, $requireKeys);

        return parent::refund($request);
    }
}