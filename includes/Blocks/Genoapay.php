<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class WC_Gateway_Genoapay_Blocks_Support extends AbstractPaymentMethodType {

	/**
	 * @var string
	 */
	protected $name = 'genoapay';

	/**
	 * @inerhitDoc
	 */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_latitudepay_settings' );
	}

	/**
	 * @inerhitDoc
	 */
	public function get_payment_method_data() {
		$payment_method_data = array(
			'title'          => $this->settings['title'],
			'description'    => $this->settings['description'],
			'supports'       => array( 'products', 'refunds' ),
			'icon'           => WC_LATITUDEPAY_URL . '/assets/' . $this->name . '.svg',
			'id'             => $this->name,
			'snippet_url'    => $this->get_checkout_message_url(),
			'modal_script_url'    => $this->get_payment_gateway()->getImagesApiUrl() . 'util.js',
		);
		return $payment_method_data;
	}

	/**
	 * @inerhitDoc
	 */
	public function is_active() {
		$payment_gateway = $this->get_payment_gateway();
		return $payment_gateway ? $payment_gateway->is_available() : false;
	}

	/**
	 * @inerhitDoc
	 */
	public function get_payment_method_script_handles() {
		$asset_path   = WC_LATITUDEPAY_PATH . '/assets/js/blocks/build/index.asset.php';
		$version      = WC_LATITUDEPAY_VERSION;
		$dependencies = array();
		if ( file_exists( $asset_path ) ) {
			$asset        = require $asset_path;
			$version      = is_array( $asset ) && isset( $asset['version'] )
				? $asset['version']
				: $version;
			$dependencies = is_array( $asset ) && isset( $asset['dependencies'] )
				? $asset['dependencies']
				: $dependencies;
		}
		wp_register_script(
			'wc-' . $this->name . '-blocks-integration',
			WC_LATITUDEPAY_URL . '/assets/js/blocks/build/index.js',
			$dependencies,
			$version,
			true
		);

		wp_enqueue_script(
			'wc-' . $this->name . '-images-api-script',
			WC_LATITUDEPAY_URL . '/assets/js/blocks/util.js',
			array( 'jquery' ),
			null,
			true
		);

		wp_set_script_translations(
			'wc-' . $this->name . '-blocks-integration',
			'latitudepay-genoapay-integrations-for-woocommerce'
		);
		return array( 'wc-' . $this->name . '-blocks-integration' );
	}

	/**
	 * Get the instance of current payment method
	 *
	 * @return false|LatitudeFinance_Payment_Method_Interface
	 */
	protected function get_payment_gateway() {
		// $payment_gateways_class = WC()->payment_gateways();
		// $payment_gateways       = $payment_gateways_class->payment_gateways();
		$payment_gateways       = WC()->payment_gateways->payment_gateways();
		if ( ! isset( $payment_gateways[ $this->name ] ) ) {
			return false;
		}
		return $payment_gateways[ $this->name ];
	}

	/**
	 * Get checkout message snippet URL
	 *
	 * @return string
	 */
	protected function get_checkout_message_url() {
		/**
		 * @var WC_Cart $cart
		 */
		$cart = WC()->cart;
		$gateway = $this->get_payment_gateway();
		$gateway->setAmount( $cart->total );
		return $gateway->getSnippetUrl();
	}
}
