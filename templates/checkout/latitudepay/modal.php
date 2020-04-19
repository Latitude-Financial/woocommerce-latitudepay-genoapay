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
<?php if (is_checkout()): ?>
    <a href="javascript: void(0)" id="latitudepay-popup">
        <img style="margin-top: 10px" src="<?php echo WC_LATITUDEPAY_ASSETS . "latitudepay.svg" ?>" />
    </a>
<?php endif ?>

<div class="lp-modal-container" id="lp-modal-container" style="display: none;">
    <div class="lp-modal">
        <div class="lp-content">
            <div class="modal-header lp-header">
                <button id="lp-modal-close" aria-hidden="true" class="lp-close-container" type="button">×</button>
                <h4 class="modal-title text-center">
                <img class="lp-logo" src="<?php echo WC_LATITUDEPAY_ASSETS . 'latitudepay/lpay_modal_logo.png' ?>">
                </h4>
            </div>
            <div class="modal-body p-0">
                <div class="lp-content">
                    <div class="lp-body">
                        <div class="lp-heading lp-block">
                            <div>
                                <?php echo __('How does this work?')?>
                            </div>
                            <div class="lp-bold">
                                <?php echo __('Glad you asked!')?>
                            </div>
                        </div>
                        <ul class="lp-steps lp-block">
                            <li>
                                <img src="<?php echo WC_LATITUDEPAY_ASSETS . 'latitudepay/lp_phone.png' ?>" style="margin-left: auto;margin-right: auto;">
                                <div class="lp-subheading">
                                    <?php echo __('Choose LatitudePay')?>
                                    <br class="lp-line-break">
                                    <?php echo __('at the checkout') ?>
                                </div>
                                <span>
                                    <?php echo __('There is no extra cost to you - just select it as your')?>
                                    <br class="lp-line-break">
                                    <?php echo __('payment option.')?>
                                </span>
                            </li>
                            <li>
                                <img src="<?php echo WC_LATITUDEPAY_ASSETS . 'latitudepay/lp_timer.png' ?>" style="margin-left: auto;margin-right: auto;">
                                <div class="lp-subheading">
                                    <?php echo __('Approval in')?>
                                    <br class="lp-line-break">
                                    <?php echo __('minutes')?>
                                </div>
                                <span>
                                    <?php echo __('Set up your account and we will tell you straight away')?>
                                    <br class="lp-line-break">
                                    <?php echo __('if approved.')?>
                                </span>
                            </li>
                            <li>
                                <img src="<?php echo WC_LATITUDEPAY_ASSETS . 'latitudepay/lp_calender.png' ?>" style="margin-left: auto;margin-right: auto;">
                                <div class="lp-subheading">
                                    <?php echo __('Get it now, pay')?>
                                    <br class="lp-line-break">
                                    <?php echo __('over 10 weeks')?>
                                </div>
                                <span><?php echo __('It is the today way to pay, just 10 easy payments.')?>
                                    <br class="lp-line-break">
                                    <?php echo __('No interest. Ever.')?>
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="lp-requirements lp-block">
                        <div class="lp-subheading">
                            <?php echo __('If you are new to LatitudePay, you will need this stuff:')?>
                        </div>
                        <ul class="lp-requirements-list">
                            <li><?php echo __('Be over 18 years old')?></li>
                            <li><?php echo __('An Australian driver’s licence or passport')?></li>
                            <li><?php echo __('A couple of minutes to sign up,')?>
                                <br class="lp-line-break"><?php echo __('it’s quick and easy')?>
                            </li>
                            <li><?php echo __('A credit/debit card (Visa or Mastercard)')?></li>
                        </ul>
                    </div>
                    <div class="apply-button-container text-center" style="text-align: center;">
                        <a href="https://app.latitudepay.com" class="btn btn_lg btn_primary"><?php echo __('Apply Now')?></a>
                    </div>
                    <div class="lp-footer lp-block">
                        <?php echo __('Subject to approval. Conditions and late fees apply. Payment Plan provided by LatitudePay Australia Pty Ltd ABN 23 633 528 873. For complete terms visit')?><a href="https://latitudepay.com/terms" target="_blank"> <?php echo __('latitudepay.com/terms')?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Pure JS just in case if the merchant website is not using jQuery
    ;(function() {
        var popupTrigger = document.getElementById('latitudepay-popup'),
            popup        = document.getElementById('lp-modal-container'),
            closeBtn     = document.getElementById('lp-modal-close');

        function openPopup(element) {
          element.style.display = 'block';
        }

        function closePopup(element) {
            element.style.display = 'none';
        }

        popupTrigger.addEventListener('click', function(event) {
            // prevent default
            event.preventDefault();
            event.stopImmediatePropagation();
            //document.body.appendChild(popup);
            // popup the latitudepay HTML
            openPopup(popup);
        });

        closeBtn.addEventListener('click', function() {
            closePopup(popup);
        });
    })();
</script>