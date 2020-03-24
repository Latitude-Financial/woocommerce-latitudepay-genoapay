<?php
/**
 * @version 1.0.0
 * @package BinaryPay/Templates
 * @var MageBinary_BinaryPay_Method_Abstract $gateway
 */
wc_binarypay_spam_bot_field();
var_dump($gateway->get_purchase_url());die();
?>
<div class="wc-binarypay-payment-gateway <?php if($has_methods){?>has_methods<?php }?>">
    <?php
        wc_binarypay_nonce_field($gateway);
        wc_binarypay_device_data_field($gateway);
    ?>
    <?php
        $description = $gateway->get_description();
        if ($description) {
            echo wpautop(wptexturize($description));
        }
    ?>
    <?php if ($has_methods) : ?>
    <input type="radio" class="wc-binarypay-payment-type" id="<?php echo $gateway->id?>_use_nonce" name="<?php echo $gateway->payment_type_key?>" value="nonce"/>
    <label class="wc-binarypay-label-payment-type"  for="<?php echo $gateway->id?>_use_nonce"><?php //echo $gateway->get_new_method_label()?></label>
    <?php endif; ?>
    <div class="wc-binarypay-new-payment-method-container" style="<?php $has_methods ? printf('display: none') : printf('')?>">
        <?php wc_binarypay_get_template('checkout/' . $gateway->template, array('gateway' => $gateway))?>
    </div>
    <?php
    if ($methods) {
        wc_binarypay_get_template('payment-methods.php', array(
                'gateway' => $gateway,
                'methods' => $methods
        ));
    }
    ?>
</div>