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
if ( ! class_exists( 'WooCommerce' ) ) {
	add_action( 'admin_notices', 'wc_latitudepay_missing_wc_notice' );
	return;
}


function wc_latitudepay_missing_wc_notice() {
	/* translators: 1. URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'This plugin requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-payment-gateway-latitudefinance' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

if ( ! function_exists( 'wc_latitudefinance' ) ) {
	/**
	 * Returns the main instance of BinaryPay for WooCommerce
	 *
	 * @since 1.0.0
	 * @package BinaryPay
	 * @return WC_LatitudeFinance_Manager
	 */
	function wc_latitudefinance() {
		return WC_LatitudeFinance_Manager::instance();
	}

	/**
	 * create singleton instance of WC_LatitudeFinance_Manager
	 */
	wc_latitudefinance();
}


class WC_LatitudeFinance_Manager {

	/**
	 * @var WC_LatitudeFinance_Manager
	 */
	public static $instance;

	/**
	 * Array of WC payment gateways provided by the plugin
	 *
	 * @var array
	 */
	public static $gateways = array(
		WC_LatitudeFinance_Method_Genoapay::class,
		WC_LatitudeFinance_Method_Latitudepay::class,
	);

	public function __construct() {
		$this->woocommerce_init();
	}

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Functionality that is included only if WC is active.
	 */
	public function woocommerce_init() {

		/**
		 * Libs @TODO:Tidy. before SPL
		 */
		if ( ! class_exists( 'BinaryPay' ) ) {
			require_once WC_LATITUDEPAY_PATH . 'includes/Libs/Variable.php';
		}

		require_once WC_LATITUDEPAY_PATH . 'includes/Libs/GatewayInterface.php';
		require_once WC_LATITUDEPAY_PATH . 'includes/Libs/Base.php';

		if ( ! class_exists( 'BinaryPay_Exception' ) ) {
			require_once WC_LATITUDEPAY_PATH . 'includes/Libs/Exception.php';
		}

		require_once WC_LATITUDEPAY_PATH . 'includes/Libs/Http.php';

		if ( ! class_exists( 'BinaryPay' ) ) {
			require_once WC_LATITUDEPAY_PATH . 'includes/class-latitudefinance.php';
		}

		if ( ! class_exists( 'Genoapay' ) ) {
			require_once WC_LATITUDEPAY_PATH . 'includes/Gateways/Genoapay.php';
		}

		if ( ! class_exists( 'Latitudepay' ) ) {
			require_once WC_LATITUDEPAY_PATH . 'includes/Gateways/Latitudepay.php';
		}

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
		 * Functions
		 */
		include_once WC_LATITUDEPAY_PATH . '/includes/wc-latitudefinance-functions.php';
		include_once WC_LATITUDEPAY_PATH . '/includes/wc-latitudefinance-hooks.php';
		/**
		 * Assign gateways into plugin
		 */
		apply_filters( 'wc_latitudefinance_payment_gateways', $this->get_payment_gateways() );

		$this->plugins_loaded();
	}

	public function plugins_loaded() {
		// $this->plugin_validations ();
		load_plugin_textdomain( 'woocommerce-payment-gateway-latitudefinance', false, dirname( WC_LATITUDEPAY_PLUGIN_NAME ) . '/i18n/languages' );
	}

	public function plugin_path() {
		return WC_LATITUDEPAY_PATH;
	}

	public function template_path() {
		return WC_LATITUDEPAY_TEMPLATES;
	}

	/**
	 * Return an array of WC payment gateway classes provided by the BinaryPay plugin.
	 * Show payment gateway based on the currency.
	 *
	 * @return array
	 */
	public function get_payment_gateways() {

		$gateway = array();
		switch ( get_woocommerce_currency() ) {
			case 'NZD':
				$gateway[] = WC_LatitudeFinance_Method_Genoapay::class;
				break;
			case 'AUD':
				$gateway[] = WC_LatitudeFinance_Method_Latitudepay::class;
				break;
		}

		return $gateway;
	}


}
