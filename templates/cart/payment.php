<?php
    $price = $cart->total;
    $containerClass = "wc-latitudefinance-" . $gateway->get_id() . "-container";
    $modalFile = __DIR__ . DIRECTORY_SEPARATOR . "../checkout/" . $gateway->get_id() . DIRECTORY_SEPARATOR . "modal.php";

    if ($price < 20 || $price > 1500) {
       $paymentInfo = "Available now.";
    }

    if ($price > 20 && $price < 1500) {
       $weekly = $price / 10;
       $paymentInfo = "10 weekly payments of <strong>$${weekly}</strong>";
    }


    $color = $gateway->get_id() == "latitudepay" ? "rgb(57, 112, 255)" : "rgb(49, 181, 156)";

?>
<div style="display: inline-block; padding: 15px; padding-left:0px;" class="<?php echo $containerClass ?>">
    <a style="text-decoration: none;" href="javascript: void(0)" id="<?php echo $gateway->get_id() ?>-popup">
        <img src="<?php echo WC_LATITUDEPAY_ASSETS . $gateway->get_id() . '.svg' ?>" style="float: left; padding-right: 15px; max-width: 200px; padding-bottom: 7px;padding-top: 7px;"/>

        <span style="font-size: 15px;padding-right: 4px;color: rgb(46, 46, 46);">
            <?php echo $paymentInfo; ?>
        </span>

        <span style="color: <?php echo $color; ?>; font-weight: bold; font-size:13px;">learn more</span>
    </a>

    <?php include($modalFile) ?>
</div>