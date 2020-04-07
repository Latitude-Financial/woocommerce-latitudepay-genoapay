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