<?php if ($cart) : ?>
    <?php
        $price = $cart->total;
        $containerClass = "wc-latitudefinance-" . $gateway->get_id() . "-container";
        $modalFile = __DIR__ . DIRECTORY_SEPARATOR . "../checkout/" . $gateway->get_id() . DIRECTORY_SEPARATOR . "modal.php";
    ?>
<div style="margin-top: 15px; " class="<?php echo $containerClass ?>">
    <a href="javascript: void(0)" id="<?php echo $gateway->get_id() ?>-popup">
    		<strong style="font-size: 12px;float: left;padding:12px;">
        	or 10 payments of <?php echo wc_price($price / 10) ?> interest-free with
    		</strong>
	        <span style="float:left;">
	            <img src="<?php echo WC_LATITUDEPAY_ASSETS . $gateway->get_id() . '.svg' ?>" style="max-width:180px;float: left;"/>
	            <p style="text-align: center; font-size:10px;">What's this?</p>
	        <span>
    </a>
    <?php include($modalFile) ?>
</div>
<?php endif ?>