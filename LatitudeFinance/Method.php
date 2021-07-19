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

if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
	return;
}

abstract class WC_LatitudeFinance_Method_Abstract extends WC_Payment_Gateway {

	/**
	 * @var string
	 * Debug mode disabled
	 */
	const DEBUG_MODE_OFF = 'off';

	/**
	 * @var string
	 * Debug mode log to file
	 */
	const DEBUG_MODE_LOG = 'log';

	/**
	 * @var string
	 */
	const DEFAULT_VALUE = 'NO_VALUE';

	/**
	 * @var string
	 */
	const ENVIRONMENT_SANDBOX = 'sandbox';

	/**
	 * @var string
	 */
	const ENVIRONMENT_PRODUCTION = 'production';

	/**
	 * @var string
	 */
	const ENVIRONMENT_DEVELOPMENT = 'development';

	/**
	 * @var string
	 */
	const PENDING_ORDER_STATUS = 'pending';

	/**
	 * @var string
	 */
	const PROCESSING_ORDER_STATUS = 'processing';

	/**
	 * @var string
	 */
	const FAILED_ORDER_STATUS = 'failed';

	/**
	 * List of available hooks inside product page
	 */
	const WOOCOMMERCE_PRODUCT_PAGE_POSITIONS = array(
		'woocommerce_before_single_product'         => 'woocommerce_before_single_product',
		'woocommerce_before_single_product_summary' => 'woocommerce_before_single_product_summary',
		'woocommerce_single_product_summary'        => 'woocommerce_single_product_summary',
		'woocommerce_before_add_to_cart_form'       => 'woocommerce_before_add_to_cart_form',
		'woocommerce_before_variations_form'        => 'woocommerce_before_variations_form',
		'woocommerce_product_thumbnails'            => 'woocommerce_product_thumbnails (may not work)',
		'woocommerce_before_add_to_cart_button'     => 'woocommerce_before_add_to_cart_button',
		'woocommerce_before_single_variation'       => 'woocommerce_before_single_variation',
		'woocommerce_single_variation'              => 'woocommerce_single_variation',
		'woocommerce_after_single_variation'        => 'woocommerce_after_single_variation',
		'woocommerce_after_add_to_cart_button'      => 'woocommerce_after_add_to_cart_button',
		'woocommerce_after_variations_form'         => 'woocommerce_after_variations_form',
		'woocommerce_after_add_to_cart_form'        => 'woocommerce_after_add_to_cart_form',
		'woocommerce_product_meta_start'            => 'woocommerce_product_meta_start',
		'woocommerce_product_meta_end'              => 'woocommerce_product_meta_end',
		'woocommerce_share'                         => 'woocommerce_share',
		'woocommerce_after_single_product_summary'  => 'woocommerce_after_single_product_summary',
		'woocommerce_after_single_product'          => 'woocommerce_after_single_product',
	);

	/**
	 * @var WC_LatitudeFinance_Method_Abstract
	 */
	public $gateway;

	/**
	 * @var integer
	 */
	protected $min_order_total;

	/**
	 * @var integer
	 */
	protected $max_order_total;

	/**
	 * @var string
	 */
	protected $environment;

	/**
	 * @var string
	 */
	protected $currency_code;

	/**
	 * @var string
	 */
	protected $order_comment;

	/**
	 * @var string
	 * Woocommerce called tokens, this is for different purpose
	 */
	protected $token;

	/**
	 * @var array
	 * An array which saved all the response we got from the payment provider
	 */
	protected $request;

	/**
	 * @var array
	 * Configuration fetched from the Latitude finance API
	 */
	protected $configuration = array();

	/**
	 * @var boolean
	 */
	// protected $debug = true;

	public function __construct() {
		$this->has_fields       = true;
		$this->nonce_key        = $this->id . '_nonce_key';
		$this->token_key        = $this->id . '_token_key';
		$this->device_data_key  = $this->id . '_device_data';
		$this->save_method_key  = $this->id . '_save_method';
		$this->payment_type_key = $this->id . '_payment_type';
		$this->config_key       = $this->id . '_config_data';

		$this->init_form_fields();
		$this->init_settings();

		// Environment must be set before get the gateway object
		$this->environment = $this->get_option( 'environment', self::ENVIRONMENT_DEVELOPMENT );

		// @TODO need to run this when admin update the config hook.
		// $this->update_configuration_options();

		$this->title           = $this->get_option( 'title', ucfirst( wc_latitudefinance_get_array_data( 'name', $this->configuration, $this->id ) ) );
		$this->description     = $this->get_option( 'description', wc_latitudefinance_get_array_data( 'description', $this->configuration ) );
		$this->min_order_total = $this->get_option( 'min_order_total', wc_latitudefinance_get_array_data( 'minimumAmount', $this->configuration, 20 ) );
		$this->max_order_total = $this->get_option( 'max_order_total', wc_latitudefinance_get_array_data( 'maximumAmount', $this->configuration, 1500 ) );

		$this->currency_code = get_woocommerce_currency();
		$this->credentials   = $this->get_credentials();

		$this->add_hooks();
	}

	public function return_action() {
		// save request
		$this->request = $_GET;
		BinaryPay::log( json_encode( $this->request, JSON_PRETTY_PRINT ), true, 'latitudepay-finance-' . date( 'Y-m-d' ) . '.log' );
		try {
			// process the order depends on the request
			$this->validate_response()
				->process_response();
		} catch ( BinaryPay_Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error', $this->request );
			wp_redirect( $this->redirect_url );
		} catch ( InvalidArgumentException $e ) {
			wc_add_notice( $e->getMessage(), 'error', $this->request );
			wp_redirect( $this->redirect_url );
		}
	}

	/**
	 * validate_response
	 */
	public function validate_response() {
		$request = $this->request;

		if ( ! $request ) {
			throw new InvalidArgumentException( __( 'Request object cannot be empty!', 'woocommerce-payment-gateway-latitudefinance' ) );
		}
		$session = $this->get_checkout_session();
		$token   = $session->get( 'purchase_token' );
		// Unset session after use
		$session->set( 'purchase_token', null );

		if ( ! $this->return_action_name || $this->return_action_name !== wc_latitudefinance_get_array_data( 'wc-api', $request ) ) {
			throw new BinaryPay_Exception( __( 'The return action handler is not valid for the request.', 'woocommerce-payment-gateway-latitudefinance' ) );
		}

		if ( ! $token ) {
			$this->redirect_url = wc_get_checkout_url();
			$session->set( 'order_id', null );
			/**
			 * @todo If debug then output the request in to the log file
			 *       Should also save the orders.
			 */
			throw new BinaryPay_Exception( __( 'You are not allowed to access the return handler directly. If you want to know more about this error message, please contact us.', 'woocommerce-payment-gateway-latitudefinance' ) );
		}
		return $this;
	}

	/**
	 * process_response
	 */
	protected function process_response() {
		// $request is response lol.
		$order   = $this->get_order();
		$request = $this->request;
		$message = wc_latitudefinance_get_array_data( 'message', $request );
		$result  = wc_latitudefinance_get_array_data( 'result', $request );

		switch ( $result ) {
			case BinaryPay_Variable::STATUS_COMPLETED:
				$this->order_status = self::PROCESSING_ORDER_STATUS;

				if ( is_array( $request ) ) {
					$message = sprintf(
						__( 'Payment was successful via %3$s. Amount: %1$s. Payment ID: %2$s', 'woocommerce-payment-gateway-latitudefinance' ),
						wc_price(
							$order->get_total(),
							array(
								'currency' => $order->get_currency(),
							)
						),
						wc_latitudefinance_get_array_data( 'token', $request ),
						str_replace( '_return_action', '', wc_latitudefinance_get_array_data( 'wc-api', $request ) )
					);
				}

				$this->order_comment = __( $message, 'woocommerce-payment-gateway-latitudefinance' );
				$this->process_order();

				break;
			case BinaryPay_Variable::STATUS_UNKNOWN:
				$this->order_status  = self::FAILED_ORDER_STATUS;
				$this->order_comment = __( $message, 'woocommerce-payment-gateway-latitudefinance' );
				break;

			case BinaryPay_Variable::STATUS_FAILED:
				$this->redirect_url = wc_get_checkout_url();
				throw new BinaryPay_Exception( __( 'your purchase has been cancelled.', 'woocommerce-payment-gateway-latitudefinance' ) );
				break;
			default:
				/**
				 * @todo Need more tests for this code
				 */
				$this->order_status  = self::FAILED_ORDER_STATUS;
				$this->order_comment = __( $message, 'woocommerce-payment-gateway-latitudefinance' );
				break;
		}
		return $this;
	}

	protected function get_order() {
		$order_id = $this->get_checkout_session()->get( 'order_id' );

		// If the order id in the session has been cleared out, then do nothing to update the order
		if ( ! $order_id ) {
			return;
		}
		// order object
		return $order = wc_get_order( $order_id );
	}

	/**
	 * process_order
	 */
	protected function process_order() {
		// order object
		$order = $this->get_order();
		$token = wc_latitudefinance_get_array_data( 'token', $this->request );

		// Mark as on-hold (we're awaiting the payment)
		$order->update_status( $this->order_status, $this->order_comment );
		// Reduce stock levels
		$order->reduce_order_stock();
		$order->set_transaction_id( $token );
		$order->save();
		// Remove cart
		WC()->cart->empty_cart();
		// Redirect
		wp_redirect( $this->get_return_url( $order ) );
	}


	/**
	 * update_configuration_options
	 * Update options value based on the config api endpoint.
	 * This function triggers even on the frontend, it slows down the fontend performance
	 * So we only run it on the backend.
	 * Needs to run it in a different hook.
	 * ISSUE: this function is calling side wide and createing performance issue.
	 */
	public function update_configuration_options() {
		// TODO: Check if the options has been updated before.
		// check keywords. It is checked by admin atm.
		if ( ! is_admin() || ! $this->get_configuration() ) {
			return;
		}
		$this->update_option( 'title', ucfirst( wc_latitudefinance_get_array_data( 'name', $this->configuration, $this->id ) ) );
		$this->update_option( 'description', wc_latitudefinance_get_array_data( 'description', $this->configuration ) );
		$this->update_option( 'min_order_total', wc_latitudefinance_get_array_data( 'minimumAmount', $this->configuration, 20 ) );
		$this->update_option( 'max_order_total', wc_latitudefinance_get_array_data( 'maximumAmount', $this->configuration, 1500 ) * 1000 );

	}

	/**
	 * Process payment
	 * After investigation this step is after the order has been placed
	 * Therefore we should handle the response then continue to run this function
	 */
	public function process_payment( $order_id ) {
		// save the order id, and handle the order creation in the callback action base on the Latitude response
		$this->get_checkout_session()->set( 'order_id', $order_id );
		// Return thankyou redirect
		return array(
			'result'   => 'success',
			'redirect' => $this->get_purchase_url(),
		);
	}

	/**
	 * get_configuration
	 * Get the configuration setting from Genopay/Latitypay API
	 */
	public function get_configuration() {
		$gateway = $this->get_gateway();

		// shit fix -> get it once only.
		if ( count( $this->configuration ) > 0 ) {
			return true;
		}

		if ( empty( $this->configuration ) && ! empty( $gateway ) ) {

			/**
			 * Only get the configuration when the sandbox or production environment has been set correctly
			 */
			if ( ( $this->get_option( 'sandbox_public_key' ) &&
					$this->get_option( 'sandbox_private_key' ) )
				||
				( $this->get_option( 'public_key' )
					&& $this->get_option( 'private_key' ) )
			) {
				$this->configuration = $gateway->configuration();
				return true;
			}
		}

		return false;
	}

	/**
	 * is_payment_available
	 */
	public function is_payment_available( $gateways ) {
		if ( is_checkout() ) {
			foreach ( $gateways as $index => $gateway ) {
				if ( $gateway instanceof $this->gateway_class ) {
					$orderTotal = WC()->cart->total;
					if ( $orderTotal > $this->max_order_total && $this->max_order_total || $orderTotal < $this->min_order_total && ! is_null( $this->min_order_total ) ) {
						unset( $gateways[ $index ] );
					}
				}
			}
		}
		return $gateways;
	}

	/**
	 * Adds the specific form field by passing the key position, and the array you wanted to be inserted
	 *
	 * @param array  $form_fields
	 * @param string $key
	 * @param array  $value
	 * @return array
	 * @since 1.0.0
	 */
	protected function add_form_fields( $form_fields, $key, $value ) {
		$keys  = array_keys( $form_fields );
		$index = array_search( $key, $keys );
		$pos   = false === $index ? count( $form_fields ) : $index + 1;
		return array_merge( array_slice( $form_fields, 0, $pos ), $value, array_slice( $form_fields, $pos ) );
	}

	/**
	 * Payment field
	 */
	public function payment_fields() {
		if ( $this->get_option( 'checkout_page_snippet_enabled', 'yes' ) === 'yes' ) {
			/**
			 * Pass in gateway object
			 */
			wc_latitudefinance_get_template(
				'checkout/latitudefinance-payment-method.php',
				array(
					'gateway' => $this,
				)
			);
		}

	}

	/**
	 * Initialize Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		/**
		 * Display the following options as the backend settings
		 */
		$this->form_fields = array(
			'enabled'         => array(
				'title'   => __( 'Enable/Disable Plugin', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable', 'woocommerce-payment-gateway-latitudefinance' ),
				'default' => 'no',
			),
			'title'           => array(
				'title'       => __( 'Title', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-payment-gateway-latitudefinance' ),
				'default'     => __( 'GenoaPay', 'woocommerce-payment-gateway-latitudefinance' ),
				'desc_tip'    => true,
			),
			'description'     => array(
				'title'    => __( 'Customer Message', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'     => 'textarea',
				'default'  => __( $this->description, 'woocommerce-payment-gateway-latitudefinance' ),
				'value'    => __( $this->description, 'woocommerce-payment-gateway-latitudefinance' ),
				'readonly' => true,
				'disabled' => true,
				'desc_tip' => 'This option can be set from your account portal. When the Save Changes button is clicked, this option will update automatically.',
			),
			'min_order_total' => array(
				'title'    => __( 'Minimum Order Total', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'     => 'text',
				'value'    => $this->min_order_total,
				'default'  => $this->min_order_total,
				'readonly' => true,
				'disabled' => true,
				'desc_tip' => 'This option can be set from your account portal. When the Save Changes button is clicked, this option will update automatically.',
			),
			'max_order_total' => array(
				'title'    => __( 'Maximum Order Total', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'     => 'text',
				'value'    => $this->max_order_total,
				'default'  => $this->max_order_total,
				'disabled' => true,
				'css'      => 'display:none',
			),
			'debug_mode'      => array(
				'title'   => esc_html__( 'Debug Mode', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'    => 'select',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'desc'    => sprintf(
					esc_html__(
						'Show Detailed Error Messages and API requests/responses on the checkout page and/or save them to the %1$sdebug log%2$s',
						'woocommerce-payment-gateway-latitudefinance'
					),
					'<a href="' . '#link' . '">',
					'</a>'
				),
				'default' => self::DEBUG_MODE_OFF,
				'options' => array(
					self::DEBUG_MODE_OFF => esc_html__( 'Off', 'woocommerce-payment-gateway-latitudefinance' ),
					self::DEBUG_MODE_LOG => esc_html__( 'Save to Log', 'woocommerce-payment-gateway-latitudefinance' ),
				),
			),
		);

		// add unique method fields added by concrete gateway class
		$gateway_form_fields = $this->get_gateway_form_fields();
		$this->form_fields   = array_merge( $this->form_fields, $gateway_form_fields );

		if ( count( $this->get_environments() ) > 1 ) {
			$this->form_fields = $this->add_form_fields(
				$this->form_fields,
				'description',
				array(
					'environment' => array(
						/* translators: environment as in a software environment (test/production) */
						'title'    => esc_html__( 'Environment', 'woocommerce-payment-gateway-latitudefinance' ),
						'type'     => 'select',
						'default'  => self::ENVIRONMENT_PRODUCTION,  // default to first defined environment
						'desc_tip' => esc_html__(
							'Select the gateway environment to use for transactions.',
							'woocommerce-payment-gateway-latitudefinance'
						),
						'options'  => $this->get_environments(),
					),
				)
			);
		}
	}


	/**
	 * @param $scripts
	 */
	public function enqueue_frontend_scripts( $scripts ) {
		global $wp;
		if ( is_checkout() && ! is_order_received_page() ) {
			$this->enqueue_checkout_scripts( $scripts );
		}

		if ( is_add_payment_method_page() && ! isset( $wp->query_vars['payment-methods'] ) ) {
			$this->enqueue_add_payment_method_scripts( $scripts );
		}

		if ( is_cart() ) {
			$this->enqueue_cart_scripts( $scripts );
		}

		if ( is_product() ) {
			$this->enqueue_product_scripts( $scripts );
		}
	}

	/**
	 * Get specific Api
	 *
	 * @return mixed|void
	 */
	public function get_gateway() {
		try {
			$className = ( isset( explode( '_', $this->id )[1] ) ) ? ucfirst( explode( '_', $this->id )[1] ) : ucfirst( $this->id );
			$gateway   = BinaryPay::getGateway( $className, $this->get_credentials(), $this->get_option( 'debug_mode' ) === self::DEBUG_MODE_LOG );

		} catch ( BinaryPay_Exception $e ) {
			$this->add_admin_error_message( $className . ': ' . $e->getMessage() );
		} catch ( Exception $e ) {
			BinaryPay::log( $e->getMessage(), true, 'latitudepay-finance-' . date( 'Y-m-d' ) . '.log' );
		}

		if ( ! isset( $gateway ) ) {
			$this->add_admin_error_message( 'Failed to initialize the payment gateway. Please contact the merchant for more information' );
			return;
		}

		return $gateway;
	}

	public function add_admin_error_message( $message ) {
		if ( ! is_admin() ) {
			return;
		}
		WC_Admin_Settings::add_error( $message );
	}

	public function add_admin_success_message( $message ) {
		if ( ! is_admin() ) {
			return;
		}
		WC_Admin_Settings::add_message( $message );
	}

	/**
	 * Build credentials data array
	 *
	 * @return array
	 * @throws Exception
	 */
	public function get_credentials() {
		$private_key = '';
		switch ( $this->environment ) {
			case self::ENVIRONMENT_SANDBOX:
			case self::ENVIRONMENT_DEVELOPMENT:
				$public_key  = $this->get_option( 'sandbox_public_key' );
				$private_key = $this->get_option( 'sandbox_private_key' );
				break;
			case self::ENVIRONMENT_PRODUCTION:
				$public_key  = $this->get_option( 'public_key' );
				$private_key = $this->get_option( 'private_key' );
				break;
			default:
				throw new Exception( 'No gateway found' );
		}

		return array(
			'username'    => $public_key,
			'password'    => $private_key,
			'environment' => $this->environment,
			'accountId'   => $this->get_option( 'account_id' ),
		);
	}

	/**
	 * Returns an array of form fields specific for this method
	 *
	 * @return array of form fields
	 * @since 1.0.0
	 */
	protected function get_gateway_form_fields() {
		return array(
			// merchant account ID per currency feature
			'merchant_account_id_title'          => array(
				'title'       => __( 'Merchant Account Info', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'        => 'title',
				'description' => sprintf(
					esc_html__( 'Please enter merchant account to use the payment gateway.', 'woocommerce-payment-gateway-latitudefinance' )
				),
			),
			// production
			'public_key'                         => array(
				'title'    => __( 'API Key', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'The Public Key for your GenoaPay account.', 'woocommerce-payment-gateway-latitudefinance' ),
			),
			'private_key'                        => array(
				'title'    => __( 'API Secret', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'The Private Key for your GenoaPay account.', 'woocommerce-payment-gateway-latitudefinance' ),
			),
			// sandbox
			'sandbox_public_key'                 => array(
				'title'    => __( 'Sandbox API Key', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'     => 'text',
				'class'    => 'environment-field sandbox-field',
				'desc_tip' => __( 'The Public Key for your GenoaPay sandbox account.', 'woocommerce-payment-gateway-latitudefinance' ),
			),
			'sandbox_private_key'                => array(
				'title'    => __( 'Sandbox API Secret', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'     => 'text',
				'class'    => 'environment-field sandbox-field',
				'desc_tip' => __( 'The Private Key for your GenoaPay sandbox account.', 'woocommerce-payment-gateway-latitudefinance' ),
			),
			'individual_snippet_enabled'         => array(
				'title'       => __( 'Payment Info on Individual Product Pages', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'        => 'checkbox',
				'class'       => 'environment-field sandbox-field',
				'description' => esc_html__( sprintf( 'Enable to display %s elements on individual product pages.', $this->get_option( 'title', 'Genoapay' ) ), 'woocommerce-payment-gateway-latitudefinance' ),
				'default'     => 'yes',
			),
			'snippet_product_page_position'      => array(
				'title'       => __( 'Product Price breakdown Position', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'        => 'select',
				'class'       => 'environment-field sandbox-field',
				'description' => __( 'Select where on the Product page you would like the breakdown to display, see <a href="https://www.businessbloomer.com/woocommerce-visual-hook-guide-single-product-page/">here</a> for a visual guide.', 'woocommerce-payment-gateway-latitudefinance' ),
				'options'     => self::WOOCOMMERCE_PRODUCT_PAGE_POSITIONS,
				'default'     => 'woocommerce_single_product_summary',
			),
			'snippet_product_page_hook_priority' => array(
				'title'       => __( 'Product Price Breakdown Hook Priority', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'        => 'number',
				'class'       => 'environment-field sandbox-field',
				'description' => esc_html__( 'Choose hook priority for the product price breakdown hook, default is 11.', 'woocommerce-payment-gateway-latitudefinance' ),
				'default'     => 11,
			),
			'cart_page_snippet_enabled'          => array(
				'title'       => __( 'Payment Info on Cart Page', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'        => 'checkbox',
				'class'       => 'environment-field sandbox-field',
				'description' => esc_html__( sprintf( 'Enable to display %s elements on cart page.', $this->get_option( 'title', 'Genoapay' ) ), 'woocommerce-payment-gateway-latitudefinance' ),
				'default'     => 'yes',
			),
			'checkout_page_snippet_enabled'      => array(
				'title'       => __( 'Payment Info on Checkout Page', 'woocommerce-payment-gateway-latitudefinance' ),
				'type'        => 'checkbox',
				'class'       => 'environment-field sandbox-field',
				'description' => esc_html__( sprintf( 'Enable to display %s elements on checkout page.', $this->get_option( 'title', 'Genoapay' ) ), 'woocommerce-payment-gateway-latitudefinance' ),
				'default'     => 'yes',
			),
		);
	}

	/**
	 * Returns the environment setting, one of the $environments keys, ie
	 * 'production'
	 *
	 * @return string the configured environment id
	 * @since 1.0.0
	 */
	public function get_environment() {
		return $this->environment;
	}

	public function get_environments() {
		return array(
			self::ENVIRONMENT_SANDBOX    => __( 'Sandbox', 'woocommerce-payment-gateway-latitudefinance' ),
			self::ENVIRONMENT_PRODUCTION => __( 'Production', 'woocommerce-payment-gateway-latitudefinance' ),
		);
	}

	public function output_checkout_fields() {

	}

	/**
	 * get_reference_number - get next order Id by last order id, to fix webpayment multiple increment id number bugs
	 *
	 * @return integer
	 * @throws Exception
	 */
	protected function get_reference_number() {
		$session  = $this->get_checkout_session();
		$order_id = $session->get( 'order_id' );

		if ( empty( $order_id ) ) {
			throw new Exception( 'Cannot identify the current order id number to process the payment.' );
		}

		return $order_id;
	}

	/**
	 * get_amount - get amount of the current quote
	 */
	protected function get_amount() {
		$amount = WC()->cart->total;
		return $amount;
	}

	/**
	 * _getQuoteProducts
	 *
	 * @return array
	 */
	protected function get_quote_products() {
		$items         = WC()->cart->get_cart();
		$isTaxIncluded = WC()->cart->display_prices_including_tax();

		$products = array();
		foreach ( $items as $_item ) {
			if ( ! isset( $_item['data'] ) ) {
				throw new Exception( '"data" must be defined in the item array.' );
			}
			$_product          = $_item['data']->get_data();
			$product_price     = ( $isTaxIncluded ) ? wc_get_price_including_tax( $_item['data'] ) : wc_get_price_excluding_tax( $_item['data'] );
			$product_line_item = array(
				'name'        => wc_latitudefinance_get_array_data( 'title', $_product ) ?
					htmlspecialchars( wc_latitudefinance_get_array_data( 'title', $_product ) ) :
					htmlspecialchars( wc_latitudefinance_get_array_data( 'name', $_product ) ),
				'price'       => array(
					'amount'   => round( $product_price, 2 ),
					'currency' => $this->currency_code,
				),
				'sku'         => wc_latitudefinance_get_array_data( 'sku', $_product ),
				'quantity'    => wc_latitudefinance_get_array_data( 'quantity', $_item, 0 ),
				'taxIncluded' => (int) $isTaxIncluded,
			);
			array_push( $products, $product_line_item );
		}

		return $products;
	}

	protected function get_billing_address() {
		global $woocommerce;
		$address  = $woocommerce->cart->get_customer()->get_billing_address();
		$address2 = $woocommerce->cart->get_customer()->get_billing_address_2();

		if ( $address2 ) {
			$address .= ', ' . $address2;
		}

		return $address;
	}

	/**
	 * get_carrier_method_name
	 *
	 * @return string shipping_method
	 */
	protected function get_carrier_method_name() {
		$shipping_method = current( WC()->session->get( 'chosen_shipping_methods' ) );
		switch ( $shipping_method ) {
			case 'flat_rate:1':
				$shipping_method = 'flat_rate';
				break;
		}
		return $shipping_method;
	}

	/**
	 * get_shipping_data
	 *
	 * @return array
	 */
	protected function get_shipping_data() {
		$shippingDetail = array(
			'carrier'     => $this->get_carrier_method_name() ?: 'flat_rate',
			'price'       => array(
				'amount'   => WC()->cart->get_shipping_total(),
				'currency' => $this->currency_code,
			),
			'taxIncluded' => 0,
		);
		return $shippingDetail;
	}

	/**
	 * Returns true if all debugging is disabled
	 *
	 * @return boolean if all debuging is disabled
	 * @since 1.0.0
	 */
	public function debug_off() {
		return self::DEBUG_MODE_OFF === $this->debug_mode;
	}

	/**
	 * Get ID of the gateway
	 *
	 * @return integer
	 * @since 1.0.0
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * get_checkout_session
	 */
	public function get_checkout_session() {
		return WC()->session;
	}

	/**
	 * Add all standard filters
	 * This hook will only be called when the gateway object has been initialized
	 */
	public function add_hooks() {
		/**
		 * This is line is important, we cannot save the options without this lane
		 */
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'process_admin_options',
			)
		);

		/**
		 * Validate if the payment method available for the order or not
		 */
		add_filter(
			'woocommerce_available_payment_gateways',
			array(
				$this,
				'is_payment_available',
			),
			10,
			1
		);

		/**
		 * Unified the this with the product page CSS
		 * Include extra CSS and Javascript files
		 */
		// add_action('wp_enqueue_scripts', array($this, 'include_extra_scripts'));
	}

	public function process_admin_options() {
		parent::process_admin_options();
		$this->update_configuration_options();
	}


	public function get_purchase_url() {
		try {
			$session   = $this->get_checkout_session();
			$gateway   = $this->get_gateway();
			$reference = $this->get_reference_number();
			$amount    = $this->get_amount();
			$cart      = WC()->cart;
			$customer  = $cart->get_customer();
			$shipping  = $this->get_shipping_data();

			$payment = array(
				BinaryPay_Variable::REFERENCE             => (string) $reference,
				BinaryPay_Variable::AMOUNT                => $amount,
				BinaryPay_Variable::CURRENCY              => $this->currency_code ?: self::DEFAULT_VALUE,
				BinaryPay_Variable::RETURN_URL            => home_url() . $this->return_url,
				BinaryPay_Variable::MOBILENUMBER          => $customer->get_billing_phone() ?: '0210123456',
				BinaryPay_Variable::EMAIL                 => $customer->get_billing_email(),
				BinaryPay_Variable::FIRSTNAME             => $customer->get_billing_first_name() ?: self::DEFAULT_VALUE,
				BinaryPay_Variable::SURNAME               => $customer->get_billing_last_name() ?: self::DEFAULT_VALUE,
				BinaryPay_Variable::SHIPPING_ADDRESS      => $customer->get_shipping_address() ?: self::DEFAULT_VALUE,
				BinaryPay_Variable::SHIPPING_COUNTRY_CODE => $customer->get_shipping_country() ?: self::DEFAULT_VALUE,
				BinaryPay_Variable::SHIPPING_POSTCODE     => $customer->get_shipping_postcode() ?: self::DEFAULT_VALUE,
				BinaryPay_Variable::SHIPPING_SUBURB       => $customer->get_shipping_state() ?: self::DEFAULT_VALUE,
				BinaryPay_Variable::SHIPPING_CITY         => $customer->get_shipping_city() ?: self::DEFAULT_VALUE,
				BinaryPay_Variable::BILLING_ADDRESS       => $this->get_billing_address() ?: self::DEFAULT_VALUE,
				BinaryPay_Variable::BILLING_COUNTRY_CODE  => $customer->get_billing_country() ?: self::DEFAULT_VALUE,
				BinaryPay_Variable::BILLING_POSTCODE      => $customer->get_billing_postcode() ?: self::DEFAULT_VALUE,
				BinaryPay_Variable::BILLING_SUBURB        => $customer->get_billing_state() ?: self::DEFAULT_VALUE,
				BinaryPay_Variable::BILLING_CITY          => $customer->get_billing_city() ?: self::DEFAULT_VALUE,
				BinaryPay_Variable::TAX_AMOUNT            => array_sum( $cart->get_taxes() ),
				BinaryPay_Variable::PRODUCTS              => $this->get_quote_products(),
				BinaryPay_Variable::SHIPPING_LINES        => array( $shipping ),
			);

			$response = $gateway->purchase( $payment );

			$purchaseUrl = wc_latitudefinance_get_array_data( 'paymentUrl', $response );
			// Save token into the session
			$this->get_checkout_session()->set( 'purchase_token', wc_latitudefinance_get_array_data( 'token', $response ) );
		} catch ( BinaryPay_Exception $e ) {
			BinaryPay::log( $e->getMessage(), true, 'latitudepay-finance-' . $this->id . '-' . date( 'Y-m-d' ) . '.log' );
			throw new Exception( $e->getMessage() );
		} catch ( Exception $e ) {
			$message = $e->getMessage() ?: 'Something massively went wrong. Please try again. If the problem still exists, please contact us';
			BinaryPay::log( $message, true, 'latitudepay-finance-' . date( 'Y-m-d' ) . '.log' );
			throw new Exception( $message );
		}
		return $purchaseUrl;
	}

	/**
	 * process_refund
	 *
	 * @todo : BinaryPay::log($e->getMessage(), true, 'woocommerce-genoapay.log'); extension wide.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$gateway        = $this->get_gateway();
		$order          = wc_get_order( $order_id );
		$transaction_id = $order->get_transaction_id();

		/**
		 * @todo support to add refund reason via WordPress backend
		 */
		$refund = array(
			BinaryPay_Variable::PURCHASE_TOKEN => $transaction_id,
			BinaryPay_Variable::CURRENCY       => $this->currency_code,
			BinaryPay_Variable::AMOUNT         => $amount,
			BinaryPay_Variable::REFERENCE      => $order->get_id(),
			BinaryPay_Variable::REASON         => '',
			BinaryPay_Variable::PASSWORD       => $this->credentials['password'],
		);

		try {
			if ( empty( $transaction_id ) ) {
				throw new InvalidArgumentException( sprintf( __( 'The transaction ID for order %1$s is blank. A refund cannot be processed unless there is a valid transaction associated with the order.', 'woocommerce-payment-gateway-latitudefinance' ), $order_id ) );
			}
			$response = $gateway->refund( $refund );
			$order->update_meta_data( '_transaction_status', $response['status'] );
			$order->add_order_note(
				sprintf(
					__( 'Refund successful. Amount: %1$s. Refund ID: %2$s', 'woocommerce-payment-gateway-latitudefinance' ),
					wc_price(
						$amount,
						array(
							'currency' => $order->get_currency(),
						)
					),
					$response['refundId']
				)
			);
			$order->save();
		} catch ( Exception $e ) {
			BinaryPay::log( $e->getMessage(), true, 'latitudepay-finance-' . date( 'Y-m-d' ) . '.log' );
			return new WP_Error(
				'refund-error',
				sprintf(
					__(
						'Exception thrown while issuing refund. Reason: %1$s Exception class: %2$s',
						'woocommerce-payment-gateway-latitudefinance'
					),
					$e->getMessage(),
					get_class( $e )
				)
			);
		}
		return true;
	}

	/**
	 * Check if orderTotal is valid
	 *
	 * @param $orderTotal
	 * @return bool
	 */
	private function _isValidOrderAmount( $orderTotal ) {
		if ( $this->max_order_total && $this->min_order_total ) {
			return $orderTotal >= $this->min_order_total && $orderTotal <= $this->max_order_total;
		}
		if ( $this->max_order_total && ! $this->min_order_total ) {
			return $orderTotal <= $this->max_order_total;
		}

		if ( ! $this->max_order_total && $this->min_order_total ) {
			return $orderTotal >= $this->min_order_total;
		}
		return true;
	}
}
