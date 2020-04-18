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

    $description = $gateway->get_description();
    wc_latitudefinance_spam_bot_field();
?>
<div class="wc-latitudefinance-payment-gateway">
    <?php
        wc_latitudefinance_nonce_field($gateway);
        wc_latitudefinance_device_data_field($gateway);
        if ($description) {
            echo wpautop(wptexturize($description));
        }
    ?>
    <div class="wc-latitudefinance-new-payment-method-container" style="<?php $has_methods ? printf('display: none') : printf('')?>">
        <?php wc_latitudefinance_get_template('checkout/' . $gateway->template, array('gateway' => $gateway))?>
    </div>
</div>