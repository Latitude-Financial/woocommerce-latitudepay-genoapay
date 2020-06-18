<?php
/**
* Woocommerce LatitudeFinance Payment Extension
*
* NOTICE OF LICENSE
*
* Copyright 2020 LatitudeFinance
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*   http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*
* @category    LatitudeFinance
* @package     Latitude_Finance
* @author      MageBinary Team
* @copyright   Copyright (c) 2020 LatitudeFinance (https://www.latitudefinancial.com.au/)
* @license     http://www.apache.org/licenses/LICENSE-2.0
*/
?>
<?php global $product; if ($product) : ?>
    <?php
        $price = $product->get_price();
        $containerClass = "wc-latitudefinance-" . $gateway->get_id() . "-container";
        $modalFile = __DIR__ . DIRECTORY_SEPARATOR . "../checkout/" . $gateway->get_id() . DIRECTORY_SEPARATOR . "modal.php";

        $paymentInfo = "Available now.";

        if ($price >= 20 && $price <= 1500) {
           $weekly = $price / 10;
           $paymentInfo = "10 weekly payments of <strong>$${weekly}</strong>";
        }


        $color = $gateway->get_id() == "latitudepay" ? "rgb(57, 112, 255)" : "rgb(49, 181, 156)";

    ?>
<div style="display: inline-block; padding: 5px;" class="<?php echo $containerClass ?>">
    <a style="text-decoration: none;display: flex;" href="javascript: void(0)" id="<?php echo $gateway->get_id() ?>-popup">
        <img src="<?php echo WC_LATITUDEPAY_ASSETS . $gateway->get_id() . '.svg' ?>" style="float: left;padding-right: 5px; max-width: 150px;"/>

        <span style="font-size: 15px;padding-right: 5px;color: rgb(46, 46, 46);">
            <?php echo $paymentInfo; ?>
        </span>

        <span style="color: <?php echo $color; ?>; font-weight: bold; font-size:13px;">learn more</span>
    </a>

    <?php include($modalFile) ?>
</div>
<?php endif ?>
