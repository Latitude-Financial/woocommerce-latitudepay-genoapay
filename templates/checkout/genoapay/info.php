<?php
    $gateway->output_checkout_fields();
    $orderTotal = WC()->cart->total;
?>
<div class="wc-binarypay-genoapay-container">
    <strong style="font-size: 12px">
        <p>10 interest free payments from <?php echo wc_price($orderTotal / 10)  ?></p>
    </strong>
</div>