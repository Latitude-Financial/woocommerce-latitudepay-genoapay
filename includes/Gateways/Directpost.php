<?php
class Directpost extends BinaryPay
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
           BinaryPay::ACCOUNTID,
           BinaryPay::AMOUNT,
           BinaryPay::REFERENCE,
           BinaryPay::PARTICULAR,
           BinaryPay::EMAIL,
           BinaryPay::STORE_CARD,
           BinaryPay::CARD_NUMBER,
           BinaryPay::CARD_TYPE,
           BinaryPay::CARD_EXPIRY,
           BinaryPay::CARD_HOLDER,
           BinaryPay::CARD_CSC,
           BinaryPay::CARD_TOKEN_REF
       );

       $requestKeys = array_keys($request);

       if (in_array(BinaryPay::CARD_TOKEN, $requestKeys)) {
           $requireKeys = array(
               BinaryPay::ACCOUNTID,
               BinaryPay::AMOUNT,
               BinaryPay::REFERENCE,
               BinaryPay::PARTICULAR,
               BinaryPay::EMAIL,
               BinaryPay::CARD_TOKEN
           );
       }

       $this->_verifyKeys($requestKeys, $requireKeys);
       return parent::purchase($request);
    }

    /**
     * add
     *
     * Validate user passed in params
     * @param array $request
     */
    public function add($request)
    {
        $requireKeys = array(
            BinaryPay::CARD_NUMBER,
            BinaryPay::CARD_TYPE,
            BinaryPay::CARD_EXPIRY,
            BinaryPay::CARD_HOLDER,
            BinaryPay::CARD_TOKEN_REF,
        );

        $requestKeys = array_keys($request);

        $this->_verifyKeys($requestKeys, $requireKeys);

        return parent::add($request);
    }

    /**
     * update
     *
     * Validate user passed in params
     * @param  array $request
     * @return $this
     */
    public function update($request)
    {
        $requireKeys = array(
            BinaryPay::CARD_TOKEN
        );

        $requestKeys = array_keys($request);

        $this->_verifyKeys($requestKeys, $requireKeys);

        return parent::update($request);
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
            BinaryPay::CARD_TOKEN
        );

        $requestKeys = array_keys($request);

        $this->_verifyKeys($requestKeys, $requireKeys);

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
            BinaryPay::TRANSACTION_ID,
            BinaryPay::AMOUNT,
            BinaryPay::REFERENCE,
            BinaryPay::PARTICULAR,
            BinaryPay::EMAIL
        );

        $requestKeys = array_keys($request);

        $this->_verifyKeys($requestKeys, $requireKeys);

        return parent::refund($request);
    }

    /**
     * delete
     *
     * Validate user passed in params
     * @param  array $request
     * @return $this
     */
    public function delete($request)
    {
        $requireKeys = array(
            BinaryPay::CARD_TOKEN,
        );

        $requestKeys = array_keys($request);

        $this->_verifyKeys($requestKeys, $requireKeys);

        return parent::delete($request);
    }
}