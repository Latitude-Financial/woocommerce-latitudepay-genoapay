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

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__)));
require_once('Variable.php');
require_once('GatewayInterface.php');
require_once('Base.php');
require_once('Exception.php');
require_once('Http.php');
require_once('Config.php');

spl_autoload_register(
    function ($className) {
        $libName = 'LatitudeFinance';

        if ($className != $libName) {
            $file = __DIR__ . DIRECTORY_SEPARATOR . 'Gateways' . DIRECTORY_SEPARATOR . $className . '.php';
            if (is_file($file)) {
                require_once $file;
                return;
            }
        }

        if (strpos($className, $libName) !== 0) {
            return;
        }

        $fileName = __DIR__ . DIRECTORY_SEPARATOR;

        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $fileName .= str_replace($libName.'_', DIRECTORY_SEPARATOR, $className) . '.php';
        if (is_file($fileName)) {
            require_once $fileName;
        }
    }
);

class WC_LatitudeFinance_Manager
{
    /**
     * @var WC_LatitudeFinance_Manager
     */
    public static $instance;

    /**
     * Array of WC payment gateways provided by the plugin
     *
     * @var array
     */
    public static $gateways = [
        MageBinary_BinaryPay_Method_Genoapay::class,
        MageBinary_BinaryPay_Method_Latitudepay::class
    ];

    public function __construct() {
        $this->add_hooks();
    }

    public static function instance() {
        if (!self::$instance) {
            self::$instance = new self ();
        }
        return self::$instance;
    }

    private function add_hooks() {
        add_action('woocommerce_init', array($this,
            'woocommerce_init'
        ), 10);

        add_action('plugins_loaded', array($this,
            'plugins_loaded'
        ), 10);

        // add_action('plugins_loaded', array($this,
        //     'admin_includes'
        // ), 20);
    }

    /**
     * Functionality that is included only if WC is active.
     */
    public function woocommerce_init() {
        /**
         * Functions
         */
        include_once WC_LATITUDEPAY_PATH . '/includes/wc-latitudefinance-functions.php';
        include_once WC_LATITUDEPAY_PATH . '/includes/wc-latitudefinance-hooks.php';

        /**
         * Settings
         */

        /**
         * Gateways*
         */
        include_once WC_LATITUDEPAY_PATH . '/LatitudeFinance/Method.php';
        include_once WC_LATITUDEPAY_PATH . '/LatitudeFinance/Method/Genoapay.php';
        include_once WC_LATITUDEPAY_PATH . '/LatitudeFinance/Method/Latitudepay.php';

        /**
         * Assign gateways into plugin
         */
        apply_filters('wc_latitudefinance_payment_gateways', self::$gateways);
    }

    public function plugins_loaded() {
        // $this->plugin_validations ();
        load_plugin_textdomain('woocommerce-payment-gateway-latitudefinance', false, dirname(WC_LATITUDEPAY_PLUGIN_NAME) . '/i18n/languages' );
    }

    public function plugin_path() {
        return WC_LATITUDEPAY_PATH;
    }

    public function template_path() {
        return WC_LATITUDEPAY_TEMPLATES;
    }

    /**
     * Return an array of WC payment gateway classes provided by the BinaryPay plugin.
     *
     * @return array
     */
    public function get_payment_gateways() {
        return self::$gateways;
    }
}

if (function_exists('latitudefinance')) {
    throw new Exception('"latitudefinance" function already existed.');
} else {
    /**
     * Returns the main instance of BinaryPay for WooCommerce
     *
     * @since 1.0.0
     * @package BinaryPay
     * @return WC_LatitudeFinance_Manager
     */
    function latitudefinance() {
        return WC_LatitudeFinance_Manager::instance();
    }

    /**
     * create singleton instance of WC_LatitudeFinance_Manager
     */
    latitudefinance();
}
