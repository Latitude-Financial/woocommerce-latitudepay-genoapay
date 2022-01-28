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

if ( ! class_exists( 'WC_LatitudeFinance_Method_Abstract' ) ) {
	return;
}


class WC_LatitudeFinance_Method_Genoapay extends WC_LatitudeFinance_Method_Abstract {

	/**
	 * @var string
	 */
	protected $return_url = '?wc-api=genoapay_return_action';

	/**
	 * @var string
	 */
	protected $gateway_class = 'WC_LatitudeFinance_Method_Genoapay';

	/**
	 * @var string
	 */
	protected $order_status = self::PENDING_ORDER_STATUS;

	/**
	 * @var string
	 */
	protected $return_action_name = 'genoapay_return_action';

	/**
	 * @var bool
	 */
	private $isFullBlock = false;

	/**
	 * @var float
	 */
	private $amount = 0.00;

	public function __construct() {
		$this->id                   = 'genoapay';
		$this->template             = 'genoapay/info.php';
		$this->default_title        = __( 'GenoaPay', 'woocommerce-payment-gateway-latitudefinance' );
		$this->order_button_text    = __( 'Proceed with GenoaPay', 'woocommerce-payment-gateway-latitudefinance' );
		$this->method_title         = __( 'GenoaPay', 'woocommerce-payment-gateway-latitudefinance' );
		$this->tab_title            = __( 'GenoaPay', 'woocommerce-payment-gateway-latitudefinance' );
		$this->icon                 = WC_LATITUDEPAY_ASSETS . 'genoapay.svg';

		/**
		  * Allow refund and purchase product action
		  */
		$this->supports             = array( 'products', 'refunds' );

		/**
		 * The description will show in the backend
		 */
		$this->method_description   = __( 'Available to NZ residents who are 18 years old and over and have a valid debit or credit card.', 'woocommerce-payment-gateway-latitudefinance' );
		parent::__construct();
	}

	/**
	 * add_hooks
	 */
	public function add_hooks() {
		// Add hook to handle the response from remote API
		add_action( 'woocommerce_api_' . $this->id . '_return_action', array( $this, 'return_action' ) );

		add_action( 'wp_footer', array( $this, 'genoapay_footer_modal_script' ) );

		// Execture parent hooks
		parent::add_hooks();
	}

	/**
	 * Append modal script into page footer if the current template is in cart or product pages
	 *
	 * @return string|void
	 */
	public function genoapay_footer_modal_script() {
		if ( is_product() || is_cart()
		) {
			include __DIR__ . DIRECTORY_SEPARATOR . '../../templates/images_api/modal.php';
		}
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
	 * @return string
	 */
	public function getSnippetUrl() {
		$params = array(
			'amount' => $this->getAmount(),
			'services' => array( 'GPAY' ),
		);
		if ( $this->isFullBlock() ) {
			$params['full_block'] = '1';
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
}
