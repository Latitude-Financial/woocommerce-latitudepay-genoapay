<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use donatj\MockWebServer\ResponseByMethod;
class WpunitTester extends \Codeception\Actor
{
    use _generated\WpunitTesterActions;
	public static $server;

	public static function setServer($server)
	{
		self::$server = $server;
	}

    /**
     * Define custom actions here
     */
    public function create_a_post()
    {
        yield wp_insert_post([
            'post_title' => 'test',
            'post_status' => 'publish',
        ]);

        yield self::getModule('WPLoader')->factory()->post->create();
    }

    /**
	 * Delete an order.
	 *
	 * @param int $order_id ID of the order to delete.
	 */
	public static function delete_order( $order_id ) {

		$order = wc_get_order( $order_id );

		// Delete all products in the order.
		foreach ( $order->get_items() as $item ) {
			self::delete_product( $item['product_id'] );
		}

		self::delete_simple_flat_rate();

		// Delete the order post.
		$order->delete( true );
	}

	/**
	 * Create a order.
	 *
	 * @since   2.4
	 * @version 3.0 New parameter $product.
	 *
	 * @param int        $customer_id The ID of the customer the order is for.
	 * @param WC_Product $product The product to add to the order.
	 *
	 * @return WC_Order
	 */
	public static function create_order( $customer_id = 1, $product = null, $qty=4 ) {

		if ( ! is_a( $product, 'WC_Product' ) ) {
			$product = self::create_simple_product();
		}

		self::create_simple_flat_rate();

		$order_data = [
			'status'        => 'pending',
			'customer_id'   => $customer_id,
			'customer_note' => '',
			'total'         => '',
		];

		$_SERVER['REMOTE_ADDR'] = '127.0.0.1'; // Required, else wc_create_order throws an exception.
		$order                  = wc_create_order( $order_data );

		// Add order products.
		$item = new WC_Order_Item_Product();
		$item->set_props(
			[
				'product'  => $product,
				'quantity' => $qty,
				'subtotal' => wc_get_price_excluding_tax( $product, [ 'qty' => $qty ] ),
				'total'    => wc_get_price_excluding_tax( $product, [ 'qty' => $qty ] ),
			]
		);
		$item->save();
		$order->add_item( $item );

		// Set billing address.
		$order->set_billing_first_name( 'Jeroen' );
		$order->set_billing_last_name( 'Sormani' );
		$order->set_billing_company( 'WooCompany' );
		$order->set_billing_address_1( 'WooAddress' );
		$order->set_billing_address_2( '' );
		$order->set_billing_city( 'WooCity' );
		$order->set_billing_state( 'NY' );
		$order->set_billing_postcode( '12345' );
		$order->set_billing_country( 'US' );
		$order->set_billing_email( 'admin@example.org' );
		$order->set_billing_phone( '555-32123' );

		// Add shipping costs.
		$shipping_taxes = WC_Tax::calc_shipping_tax( '10', WC_Tax::get_shipping_tax_rates() );
		$rate           = new WC_Shipping_Rate( 'flat_rate_shipping', 'Flat rate shipping', '10', $shipping_taxes, 'flat_rate' );
		$item           = new WC_Order_Item_Shipping();
		$item->set_props(
			[
				'method_title' => $rate->label,
				'method_id'    => $rate->id,
				'total'        => wc_format_decimal( $rate->cost ),
				'taxes'        => $rate->taxes,
			]
		);
		foreach ( $rate->get_meta_data() as $key => $value ) {
			$item->add_meta_data( $key, $value, true );
		}
		$order->add_item( $item );

		// Set payment gateway.
		$payment_gateways = WC()->payment_gateways->payment_gateways();
		$order->set_payment_method( $payment_gateways['bacs'] );

		// Set totals.
		$order->set_shipping_total( 10 );
		$order->set_discount_total( 0 );
		$order->set_discount_tax( 0 );
		$order->set_cart_tax( 0 );
		$order->set_shipping_tax( 0 );
		$order->set_total( 50 ); // 4 x $10 simple helper product
		$order->save();

		return $order;
	}

	/**
	 * Helper function to create order with fees and shipping objects.
	 *
	 * @param int        $customer_id The ID of the customer the order is for.
	 * @param WC_Product $product The product to add to the order.
	 *
	 * @return WC_Order
	 */
	public static function create_order_with_fees_and_shipping( $customer_id = 1, $product = null ) {
		$order = self::create_order( $customer_id, $product );

		$fee_item = new WC_Order_Item_Fee();
		$fee_item->set_order_id( $order->get_id() );
		$fee_item->set_name( 'Testing fees' );
		$fee_item->set_total( 100 );

		$shipping_item = new WC_Order_Item_Shipping();
		$shipping_item->set_order_id( $order->get_id() );
		$shipping_item->set_name( 'Flat shipping' );
		$shipping_item->set_total( 25 );

		$order->add_item( $fee_item );
		$order->add_item( $shipping_item );
		$order->save();
		$order->calculate_totals( true );

		return $order;
	}


    /**
	 * Delete a product.
	 *
	 * @param int $product_id ID to delete.
	 */
	public static function delete_product( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product ) {
			$product->delete( true );
		}
	}

	/**
	 * Create simple product.
	 *
	 * @since 2.3
	 * @param bool  $save Save or return object.
	 * @param array $props Properties to be set in the new product, as an associative array.
	 * @return WC_Product_Simple
	 */
	public static function create_simple_product( $save = true, $props = [] ) {
		$product       = new WC_Product_Simple();
		$default_props =
			[
				'name'          => 'Dummy Product',
				'regular_price' => 10,
				'price'         => 10,
				'sku'           => 'DUMMY SKU',
				'manage_stock'  => false,
				'tax_status'    => 'taxable',
				'downloadable'  => false,
				'virtual'       => false,
				'stock_status'  => 'instock',
				'weight'        => '1.1',
			];

		$product->set_props( array_merge( $default_props, $props ) );

		if ( $save ) {
			$product->save();
			return wc_get_product( $product->get_id() );
		} else {
			return $product;
		}
	}

	/**
	 * Create external product.
	 *
	 * @since 3.0.0
	 * @return WC_Product_External
	 */
	public static function create_external_product() {
		$product = new WC_Product_External();
		$product->set_props(
			[
				'name'          => 'Dummy External Product',
				'regular_price' => 10,
				'sku'           => 'DUMMY EXTERNAL SKU',
				'product_url'   => 'http://woocommerce.com',
				'button_text'   => 'Buy external product',
			]
		);
		$product->save();

		return wc_get_product( $product->get_id() );
	}

	/**
	 * Create grouped product.
	 *
	 * @since 3.0.0
	 * @return WC_Product_Grouped
	 */
	public static function create_grouped_product() {
		$simple_product_1 = self::create_simple_product();
		$simple_product_2 = self::create_simple_product();
		$product          = new WC_Product_Grouped();
		$product->set_props(
			[
				'name' => 'Dummy Grouped Product',
				'sku'  => 'DUMMY GROUPED SKU',
			]
		);
		$product->set_children( [ $simple_product_1->get_id(), $simple_product_2->get_id() ] );
		$product->save();

		return wc_get_product( $product->get_id() );
	}

	/**
	 * Create a dummy variation product or configure an existing product object with dummy data.
	 *
	 *
	 * @since 2.3
	 * @param WC_Product_Variable|null $product Product object to configure, or null to create a new one.
	 * @return WC_Product_Variable
	 */
	public static function create_variation_product( $product = null ) {
		$is_new_product = is_null( $product );
		if ( $is_new_product ) {
			$product = new WC_Product_Variable();
		}

		$product->set_props(
			[
				'name' => 'Dummy Variable Product',
				'sku'  => 'DUMMY VARIABLE SKU',
			]
		);

		$attributes = [];

		$attributes[] = self::create_product_attribute_object( 'size', [ 'small', 'large', 'huge' ] );
		$attributes[] = self::create_product_attribute_object( 'colour', [ 'red', 'blue' ] );
		$attributes[] = self::create_product_attribute_object( 'number', [ '0', '1', '2' ] );

		$product->set_attributes( $attributes );
		$product->save();

		$variations = [];

		$variations[] = self::create_product_variation_object(
			$product->get_id(),
			'DUMMY SKU VARIABLE SMALL',
			10,
			[ 'pa_size' => 'small' ]
		);

		$variations[] = self::create_product_variation_object(
			$product->get_id(),
			'DUMMY SKU VARIABLE LARGE',
			15,
			[ 'pa_size' => 'large' ]
		);

		$variations[] = self::create_product_variation_object(
			$product->get_id(),
			'DUMMY SKU VARIABLE HUGE RED 0',
			16,
			[
				'pa_size'   => 'huge',
				'pa_colour' => 'red',
				'pa_number' => '0',
			]
		);

		$variations[] = self::create_product_variation_object(
			$product->get_id(),
			'DUMMY SKU VARIABLE HUGE RED 2',
			17,
			[
				'pa_size'   => 'huge',
				'pa_colour' => 'red',
				'pa_number' => '2',
			]
		);

		$variations[] = self::create_product_variation_object(
			$product->get_id(),
			'DUMMY SKU VARIABLE HUGE BLUE 2',
			18,
			[
				'pa_size'   => 'huge',
				'pa_colour' => 'blue',
				'pa_number' => '2',
			]
		);

		$variations[] = self::create_product_variation_object(
			$product->get_id(),
			'DUMMY SKU VARIABLE HUGE BLUE ANY NUMBER',
			19,
			[
				'pa_size'   => 'huge',
				'pa_colour' => 'blue',
				'pa_number' => '',
			]
		);

		if ( $is_new_product ) {
			return wc_get_product( $product->get_id() );
		}

		$variation_ids = array_map(
			function( $variation ) {
				return $variation->get_id();
			},
			$variations
		);
		$product->set_children( $variation_ids );
		return $product;
	}

	/**
	 * Creates an instance of WC_Product_Variation with the supplied parameters, optionally persisting it to the database.
	 *
	 * @param string $parent_id Parent product id.
	 * @param string $sku SKU for the variation.
	 * @param int    $price Price of the variation.
	 * @param array  $attributes Attributes that define the variation, e.g. ['pa_color'=>'red'].
	 * @param bool   $save If true, the object will be saved to the database after being created and configured.
	 *
	 * @return WC_Product_Variation The created object.
	 */
	public static function create_product_variation_object( $parent_id, $sku, $price, $attributes, $save = true ) {
		$variation = new WC_Product_Variation();
		$variation->set_props(
			[
				'parent_id'     => $parent_id,
				'sku'           => $sku,
				'regular_price' => $price,
			]
		);
		$variation->set_attributes( $attributes );
		if ( $save ) {
			$variation->save();
		}
		return $variation;
	}

	/**
	 * Creates an instance of WC_Product_Attribute with the supplied parameters.
	 *
	 * @param string $raw_name Attribute raw name (without 'pa_' prefix).
	 * @param array  $terms Possible values for the attribute.
	 *
	 * @return WC_Product_Attribute The created attribute object.
	 */
	public static function create_product_attribute_object( $raw_name = 'size', $terms = [ 'small' ] ) {
		$attribute      = new WC_Product_Attribute();
		$attribute_data = self::create_attribute( $raw_name, $terms );
		$attribute->set_id( $attribute_data['attribute_id'] );
		$attribute->set_name( $attribute_data['attribute_taxonomy'] );
		$attribute->set_options( $attribute_data['term_ids'] );
		$attribute->set_position( 1 );
		$attribute->set_visible( true );
		$attribute->set_variation( true );
		return $attribute;
	}

	/**
	 * Create a dummy attribute.
	 *
	 * @since 2.3
	 *
	 * @param string        $raw_name Name of attribute to create.
	 * @param array(string) $terms          Terms to create for the attribute.
	 * @return array
	 */
	public static function create_attribute( $raw_name = 'size', $terms = [ 'small' ] ) {
		global $wpdb, $wc_product_attributes;

		// Make sure caches are clean.
		delete_transient( 'wc_attribute_taxonomies' );
		if ( is_callable( [ 'WC_Cache_Helper', 'invalidate_cache_group' ] ) ) {
			WC_Cache_Helper::invalidate_cache_group( 'woocommerce-attributes' );
		}

		// These are exported as labels, so convert the label to a name if possible first.
		$attribute_labels = wp_list_pluck( wc_get_attribute_taxonomies(), 'attribute_label', 'attribute_name' );
		$attribute_name   = array_search( $raw_name, $attribute_labels, true );

		if ( ! $attribute_name ) {
			$attribute_name = wc_sanitize_taxonomy_name( $raw_name );
		}

		$attribute_id = wc_attribute_taxonomy_id_by_name( $attribute_name );

		if ( ! $attribute_id ) {
			$taxonomy_name = wc_attribute_taxonomy_name( $attribute_name );

			// Degister taxonomy which other tests may have created...
			unregister_taxonomy( $taxonomy_name );

			$attribute_id = wc_create_attribute(
				[
					'name'         => $raw_name,
					'slug'         => $attribute_name,
					'type'         => 'select',
					'order_by'     => 'menu_order',
					'has_archives' => 0,
				]
			);

			// Register as taxonomy.
			register_taxonomy(
				$taxonomy_name,
				apply_filters( 'woocommerce_taxonomy_objects_' . $taxonomy_name, [ 'product' ] ),
				apply_filters(
					'woocommerce_taxonomy_args_' . $taxonomy_name,
					[
						'labels'       => [
							'name' => $raw_name,
						],
						'hierarchical' => false,
						'show_ui'      => false,
						'query_var'    => true,
						'rewrite'      => false,
					]
				)
			);

			// Set product attributes global.
			$wc_product_attributes = [];

			foreach ( wc_get_attribute_taxonomies() as $taxonomy ) {
				$wc_product_attributes[ wc_attribute_taxonomy_name( $taxonomy->attribute_name ) ] = $taxonomy;
			}
		}

		$attribute = wc_get_attribute( $attribute_id );
		$return    = [
			'attribute_name'     => $attribute->name,
			'attribute_taxonomy' => $attribute->slug,
			'attribute_id'       => $attribute_id,
			'term_ids'           => [],
		];

		foreach ( $terms as $term ) {
			$result = term_exists( $term, $attribute->slug );

			if ( ! $result ) {
				$result               = wp_insert_term( $term, $attribute->slug );
				$return['term_ids'][] = $result['term_id'];
			} else {
				$return['term_ids'][] = $result['term_id'];
			}
		}

		return $return;
	}

	/**
	 * Delete an attribute.
	 *
	 * @param int $attribute_id ID to delete.
	 *
	 * @since 2.3
	 */
	public static function delete_attribute( $attribute_id ) {
		global $wpdb;

		$attribute_id = absint( $attribute_id );

		$wpdb->query(
			$wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = %d", $attribute_id )
		);
	}

	/**
	 * Creates a new product review on a specific product.
	 *
	 * @since 3.0
	 * @param int    $product_id integer Product ID that the review is for.
	 * @param string $review_content string Content to use for the product review.
	 * @return integer Product Review ID.
	 */
	public static function create_product_review( $product_id, $review_content = 'Review content here' ) {
		$data = [
			'comment_post_ID'      => $product_id,
			'comment_author'       => 'admin',
			'comment_author_email' => 'woo@woo.local',
			'comment_author_url'   => '',
			'comment_date'         => '2016-01-01T11:11:11',
			'comment_content'      => $review_content,
			'comment_approved'     => 1,
			'comment_type'         => 'review',
		];
		return wp_insert_comment( $data );
	}

	/**
	 * A helper function for hooking into save_post during the test_product_meta_save_post test.
	 * @since 3.0.1
	 *
	 * @param int $id ID to update.
	 */
	public static function save_post_test_update_meta_data_direct( $id ) {
		update_post_meta( $id, '_test2', 'world' );
	}


    /**
	 * Create a simple flat rate at the cost of 10.
	 *
	 * @since 2.3
	 *
	 * @param float $cost Optional. Cost of flat rate method.
	 */
	public static function create_simple_flat_rate( $cost = 10 ) {
		$flat_rate_settings = [
			'enabled'      => 'yes',
			'title'        => 'Flat rate',
			'availability' => 'all',
			'countries'    => '',
			'tax_status'   => 'taxable',
			'cost'         => $cost,
		];

		update_option( 'woocommerce_flat_rate_settings', $flat_rate_settings );
		update_option( 'woocommerce_flat_rate', [] );
		WC_Cache_Helper::get_transient_version( 'shipping', true );
		WC()->shipping()->load_shipping_methods();
	}

	/**
	 * Helper function to set customer address so that shipping can be calculated.
	 */
	public static function force_customer_us_address() {
		add_filter( 'woocommerce_customer_get_shipping_country', [ self::class, 'force_customer_us_country' ] );
		add_filter( 'woocommerce_customer_get_shipping_state', [ self::class, 'force_customer_us_state' ] );
		add_filter( 'woocommerce_customer_get_shipping_postcode', [ self::class, 'force_customer_us_postcode' ] );
	}

	/**
	 * Helper that can be hooked to a filter to force the customer's shipping state to be NY.
	 *
	 * @since 4.4.0
	 * @param string $state State code.
	 * @return string
	 */
	public static function force_customer_us_state( $state ) {
		return 'NY';
	}

	/**
	 * Helper that can be hooked to a filter to force the customer's shipping country to be US.
	 *
	 * @since 4.4.0
	 * @param string $country Country code.
	 * @return string
	 */
	public static function force_customer_us_country( $country ) {
		return 'US';
	}

	/**
	 * Helper that can be hooked to a filter to force the customer's shipping postal code to be 12345.
	 *
	 * @since 4.4.0
	 * @param string $postcode Postal code.
	 * @return string
	 */
	public static function force_customer_us_postcode( $postcode ) {
		return '12345';
	}

	/**
	 * Delete the simple flat rate.
	 *
	 * @since 2.3
	 */
	public static function delete_simple_flat_rate() {
		delete_option( 'woocommerce_flat_rate_settings' );
		delete_option( 'woocommerce_flat_rate' );
		WC_Cache_Helper::get_transient_version( 'shipping', true );
		WC()->shipping()->unregister_shipping_methods();
	}

	/**
	 * Get LatitudePay Api Response Headers
	 *
	 * @since 2.3
	 */
	public static function getLatitudePayApiResponseHeaders() {
		$headers = [ 
			"Accept" => "application/com.latitudepay.ecom-v3.0+json",
			"Cache-Control" => "no-cache",
			"Content-Type" => "application/com.latitudepay.ecom-v3.0+json"
		];
		return $headers;
	}

	/**
	 * Create Api Token Fail
	 *
	 * @since 2.3
	 */
	public function createApiTokenFail()
	{
		$headers = $this->getLatitudePayApiResponseHeaders();
		$data = [
			"error" => "Invalid client credentials"
		];
		self::$server->setResponseOfPath('/v3/token', new ResponseByMethod([
            ResponseByMethod::METHOD_POST => new Response(
                json_encode($data), 
                $headers
                , 401
            ),
        ]));
	}

	/**
	 * Create Api Token Success
	 *
	 * @since 2.3
	 */
	public function createApiTokenSuccess()
	{
		$headers = $this->getLatitudePayApiResponseHeaders();
		$data = [
			"authToken" => "xxxxxxxxxxxxxxxxxxxxxxxx",
			"expiryDate" =>"2029-08-24T14:15:22Z"
		];
		self::$server->setResponseOfPath('/v3/token', new ResponseByMethod([
            ResponseByMethod::METHOD_POST => new Response(
                json_encode($data), 
                $headers
                , 200
            ),
        ]));
	}

	/**
	 * Create Api Purchase Success
	 *
	 * @since 2.3
	 */
	public function createApiPurchaseSuccess()
	{
		$headers = $this->getLatitudePayApiResponseHeaders();
		$data = [
			'token' => 'xxxxxxxxxxxxxxxxxxxxxxxx',
			'paymentUrl' => self::$server->getServerRoot().'/',
			'expiryDate' => '2029-08-24T14:15:22Z',
		];
		self::$server->setResponseOfPath('/v3/sale/online', new ResponseByMethod([
            ResponseByMethod::METHOD_POST => new Response(
                json_encode($data), 
                $headers
                , 200
            ),
        ]));
	}

	/**
	 * Create Api Purchase Fail
	 *
	 * @since 2.3
	 */
	public function createApiPurchaseFail()
	{
		$headers = $this->getLatitudePayApiResponseHeaders();
		$data = [
			'error' => 'Total amount is below minimum purchase value of 20.00'
		];
		self::$server->setResponseOfPath('/v3/sale/online', new ResponseByMethod([
            ResponseByMethod::METHOD_POST => new Response(
                json_encode($data), 
                $headers
                , 401
            ),
        ]));
	}

	/**
	 * Create Api Purchase Success
	 *
	 * @since 2.3
	 */
	public function createApiRefundOrderSuccess($token)
	{
		$headers = $this->getLatitudePayApiResponseHeaders();
		$data = [
			'refundId' => '11111111111111',
			'refundDate' => '2029-08-24T14:15:22Z',
			'reference' => $token,
			'commissionAmount' => 10,
			'status' => 'Failed'
		];
		self::$server->setResponseOfPath('/v3/sale/'.$token.'/refund', new ResponseByMethod([
            ResponseByMethod::METHOD_POST => new Response(
                json_encode($data), 
                $headers
                , 200
            ),
        ]));
	}

	/**
	 * Create Api Purchase Fail
	 *
	 * @since 2.3
	 */
	public function createApiRefundOrderFail($token)
	{
		$headers = $this->getLatitudePayApiResponseHeaders();
		$data = [
			'error' => 'Reason: Message: Resource not found'
		];
		self::$server->setResponseOfPath('/v3/sale/'.$token.'/refund', new ResponseByMethod([
            ResponseByMethod::METHOD_POST => new Response(
                json_encode($data), 
                $headers
                , 401
            ),
        ]));
	}
}
