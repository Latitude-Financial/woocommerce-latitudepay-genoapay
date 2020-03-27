<?php

interface GatewayInterface
{
    public function purchase(array $args);

    public function refund($args);

    public function authrise($args);

    /* Save credit card */
    public function add($args);

    /**
     * Retrieve a payment status
     * @param  array  $args
     */
    public function retrieve(array $args);

    public function validate($args);

    public function createSignature();
}
