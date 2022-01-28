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

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_LatitudeFinance_Method_Abstract' ) ) {
	return;
}

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @see when I extend from the 'WC_LatitudeFinance_Method_Genoapay' the Woocommerce is not recognize
 * @todo remove the duplication of the code
 */
class WC_LatitudeFinance_Method_Latitudepay extends WC_LatitudeFinance_Method_Abstract {

	/**
	 * @var string
	 */
	const METHOD_LATITUDEPAY = 'latitudepay';

	/**
	 * @var string
	 */
	protected $return_url = '?wc-api=latitudepay_return_action';

	/**
	 * @var string
	 */
	protected $gateway_class = 'WC_LatitudeFinance_Method_Latitudepay';

	/**
	 * @var string
	 */
	protected $order_status = self::PENDING_ORDER_STATUS;

	/**
	 * @var string
	 */
	protected $return_action_name = 'latitudepay_return_action';

	/**
	 * @var float
	 */
	private $amount = 0.00;

	/**
	 * @var bool
	 */
	private $isFullBlock = false;

	/**
	 * @var array
	 */
	protected $lpay_plus_payment_terms;

	/**
	 * @var array
	 */
	protected $lpay_services;

	/**
	 * WC_LatitudeFinance_Method_Latitudepay constructor.
	 */
	public function __construct() {
		$this->id = self::METHOD_LATITUDEPAY;
		$this->template = 'latitudepay/info.php';
		$this->default_title = __( 'LatitudePay', 'woocommerce-payment-gateway-latitudefinance' );
		$this->order_button_text = __( 'Proceed with LatitudePay', 'woocommerce-payment-gateway-latitudefinance' );
		$this->icon = WC_LATITUDEPAY_ASSETS . 'latitudepay.svg?v=2';

		/**
		 * Allow refund and purchase product action
		 */
		$this->supports = array( 'products', 'refunds' );

		/**
		 * The description will show in the backend
		 */
		$this->method_description = __(
			'Available to AU residents who are 18 years old and over and have a valid debit or credit card.',
			'woocommerce-payment-gateway-latitudefinance'
		);

		add_action( 'wp_footer', array( $this, 'latitudepay_footer_modal_script' ) );

		parent::__construct();
		$this->title = $this->method_title = $this->tab_title = __( $this->getMethodTitle(), 'woocommerce-payment-gateway-latitudefinance' );
		$this->lpay_services = $this->get_option( 'lpay_services', wc_latitudefinance_get_array_data( 'lpay_services', $this->configuration, false ) );
		$this->lpay_plus_payment_terms = $this->get_option( 'lpay_plus_payment_terms', wc_latitudefinance_get_array_data( 'lpay_plus_payment_terms', $this->configuration, array() ) );
	}

	/**
	 * Initialize Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		parent::init_form_fields();
		$this->form_fields = $this->add_form_fields(
			$this->form_fields,
			'sandbox_private_key',
			array(
				'lpay_services' => array(
					'title' => __( 'Which Payment Options do you want to offer?', 'woocommerce-payment-gateway-latitudefinance' ),
					'type' => 'select',
					'class' => 'environment-field sandbox-field',
					'description' => 'LatitudePay (Enable this option if you want to offer just LatitudePay)<br/>LatitudePay+ (Enable this option if you want to offer just LatitudePay+)<br/>Co-Presentment (Enable this option if you want to offer both LatitudePay & LatitudePay+)',
					'default' => 'LPAY',
					'options' => array(
						'LPAY' => esc_html__( 'LatitudePay', 'woocommerce-payment-gateway-latitudefinance' ),
						'LPAYPLUS' => esc_html__( 'LatitudePay+', 'woocommerce-payment-gateway-latitudefinance' ),
						'LPAY,LPAYPLUS' => esc_html__( 'Co-Presentment', 'woocommerce-payment-gateway-latitudefinance' ),
					),
				),
			)
		);

		$this->form_fields = $this->add_form_fields(
			$this->form_fields,
			'lpay_services',
			array(
				'lpay_plus_payment_terms' => array(
					'title' => __( 'Payment Term', 'woocommerce-payment-gateway-latitudefinance' ),
					'type' => 'multiselect',
					'show_if_checked' => 'yes',
					'checkboxgroup'   => 'end',
					'class' => 'wc-enhanced-select',
					'description' => __( 'Please select the following payment terms you would like to offer your customers.<br/>The following payment terms will be reflected on your Modal.<br/>Please check your merchant contract to confirm the payment terms you have been approved for.', 'woocommerce-payment-gateway-latitudefinance' ),
					'default' => '',
					'options' => array(
						6 => esc_html__( '6 months', 'woocommerce-payment-gateway-latitudefinance' ),
						12 => esc_html__( '12 months', 'woocommerce-payment-gateway-latitudefinance' ),
						18 => esc_html__( '18 months', 'woocommerce-payment-gateway-latitudefinance' ),
						24 => esc_html__( '24 months', 'woocommerce-payment-gateway-latitudefinance' ),
					),
				),
			)
		);
	}

	/**
	 * Append modal script into page footer if the current template is in cart or product pages
	 *
	 * @return string|void
	 */
	public function latitudepay_footer_modal_script() {
		if ( is_product() || is_cart()
		) {
			include __DIR__ . DIRECTORY_SEPARATOR . '../../templates/images_api/modal.php';
		}
	}

	/**
	 * add_hooks
	 */
	public function add_hooks() {
		// Add hook to handle the response from remote API
		add_action( 'woocommerce_api_' . $this->id . '_return_action', array( $this, 'return_action' ) );

		// Execture parent hooks
		parent::add_hooks();
	}

	/**
	 * Show payment snippet and modal from images API
	 */
	public function generate_snippet_html() {
		if ( is_product() ) {
			include __DIR__ . DIRECTORY_SEPARATOR . '../../templates/images_api/snippet.php';
		}

		if ( is_cart() && $this->get_option( 'cart_page_snippet_enabled', 'yes' ) === 'yes' ) {
			include __DIR__ . DIRECTORY_SEPARATOR . '../../templates/images_api/snippet.php';
		}

		if ( is_checkout() && $this->get_option( 'checkout_page_snippet_enabled', 'yes' ) === 'yes' ) {
			include __DIR__ . DIRECTORY_SEPARATOR . '../../templates/images_api/snippet.php';
			include __DIR__ . DIRECTORY_SEPARATOR . '../../templates/images_api/modal.php';
		}
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param $flag
	 * @return $this
	 */
	public function setIsFullBlock( $flag ) {
		$this->isFullBlock = $flag;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isFullBlock() {
		return $this->isFullBlock;
	}

	/**
	 * @return array
	 */
	public function getTerms() {
		return $this->lpay_plus_payment_terms;
	}

	/**
	 * @return string
	 */
	public function getServices() {
		return $this->lpay_services;
	}

	/**
	 * @return string
	 */
	public function getSnippetPath() {
		return 'snippet.svg';
	}

	/**
	 * @param $amount
	 * @return $this
	 */
	public function setAmount( $amount ) {
		$this->amount = $amount;
		return $this;
	}

	/**
	 * @return float
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * @return string
	 */
	public function getSnippetUrl() {
		$params = array(
			'amount' => $this->getAmount(),
			'services' => array( 'LPAY' ),
		);
		if ( $this->isFullBlock() ) {
			$params['full_block'] = '1';
		}
		if ( $this->getServices() ) {
			$params['services'] = $this->getServices();
		}
		if ( $this->getTerms() ) {
			$params['terms'] = $this->getTerms();
		}
		if ( is_checkout() ) {
			$params['style'] = 'checkout';
		}
		if ( is_cart() ) {
			$params['style'] = 'cart';
		}
		foreach ( $params as &$param ) {
			if ( is_array( $param ) ) {
				$param = implode( ',', $param );
			}
		}
		$url = $this->getImagesApiUrl() . $this->getSnippetPath() . '?' . build_query( $params );
		return $url;
	}

	/**
	 * @return string
	 */
	public static function getMethodTitle() {
		$currency = get_woocommerce_currency();
		switch ( $currency ) {
			case 'NZD':
				return 'Genoapay';
			default:
				return 'LatitudePay';
		}
	}
}
