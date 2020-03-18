<?php
class Flexilong extends BinaryPay
{
    public function getTransaction($args, $refundTransaction = false)
    {
        return $this->query('getTransaction', $args);
    }
}