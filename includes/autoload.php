<?php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__)));
require_once('Variable.php');
require_once('GatewayInterface.php');
require_once('Base.php');
require_once('Exception.php');
require_once('Http.php');
require_once('Config.php');
require_once('Varien/Object.php');


spl_autoload_register(
    function ($className) {
        $libName = 'BinaryPay';

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

class WC_BinaryPay_Manager
{
    /**
     * @var WC_BinaryPay_Manager
     */
    public static $instance;

    /**
     * Array of WC payment gateways provided by the plugin
     *
     * @var array
     */
    private $gateways = array();

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

        add_action('plugins_loaded', array($this,
            'admin_includes'
        ), 20);
    }

    /**
     * Functionality that is included only if WC is active.
     */
    public function woocommerce_init() {
        /**
         * Functions
         */
        include_once WC_BINARYPAY_PATH . '/includes/wc-binarypay-functions.php';
        include_once WC_BINARYPAY_PATH . '/includes/wc-binarypay-hooks.php';

        /**
         * Settings
         */

        /**
         * Gateways*
         */
        include_once WC_BINARYPAY_PATH . '/BinaryPay/Method.php';
        include_once WC_BINARYPAY_PATH . '/BinaryPay/Method/Offline.php';
        include_once WC_BINARYPAY_PATH . '/BinaryPay/Method/Genoapay.php';

        /**
         * Assign gateways into plugin
         */
        $this->gateways = apply_filters('wc_binarypay_payment_gateways', array(
            'MageBinary_BinaryPay_Method_Genoapay'
        ));
    }

    public function admin_includes() {
        if (is_admin() && function_exists('WC')) {
        }
    }

    public function plugins_loaded() {
        // $this->plugin_validations ();
        load_plugin_textdomain('woocommerce-payment-gateway-magebinary-binarypay', false, dirname(WC_BINARYPAY_PLUGIN_NAME) . '/i18n/languages' );
    }

    public function plugin_path() {
        return WC_BINARYPAY_PATH;
    }

    public function template_path() {
        return WC_BINARYPAY_TEMPLATES;
    }

    /**
     * Return an array of WC payment gateway classes provided by the BinaryPay plugin.
     *
     * @return array
     */
    public function get_payment_gateways() {
        return $this->gateways;
    }
}

if (function_exists('binarypay')) {
    throw new Exception('"binarypay" function already existed.');
} else {
    /**
     * Returns the main instance of BinaryPay for WooCommerce
     *
     * @since 1.0.0
     * @package BinaryPay
     * @return WC_BinaryPay_Manager
     */
    function binarypay() {
        return WC_BinaryPay_Manager::instance();
    }

    /**
     * create singleton instance of WC_BinaryPay_Manager
     */
    binarypay();
}




