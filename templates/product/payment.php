<?php global $product; if ($product) : ?>
    <?php
        $price = $product->get_price();
        $containerClass = "wc-latitudefinance-" . $gateway->get_id() . "-container";
        $modalFile = __DIR__ . DIRECTORY_SEPARATOR . "../checkout/" . $gateway->get_id() . DIRECTORY_SEPARATOR . "modal.php";
    ?>
<div style="margin-top: 15px" class="<?php echo $containerClass ?>">
    <a href="javascript: void(0)" id="<?php echo $gateway->get_id() ?>-popup">
        <img src="<?php echo WC_LATITUDEPAY_ASSETS . $gateway->get_id() . '.svg' ?>" style="width:200px; height:40px"/>
    </a>
    <strong style="font-size: 12px">
        <p>10 interest free payments from <?php echo wc_price($price / 10) ?></p>
    </strong>

    <?php include($modalFile) ?>
</div>
<?php endif ?>