<?php

class BinaryPay_Exception extends Exception
{
    /**
     * BinaryPay_Exception
     * @param Exception $errorMessage exception portal.
     */
    public function __construct($errorMessage, $code = null)
    {
        $this->message = $errorMessage;
        if (!empty($code)) {
            $this->code = $code;
        }
    }

}