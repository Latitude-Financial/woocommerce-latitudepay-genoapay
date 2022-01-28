=== LatitudePay & Genoapay Integrations for WooCommerce ===
Tags: latitudepay, genoapay, woocommerce, bnpl, buynowpaylater
Requires at least: 4.4
Tested up to: 5.8
Stable tag: 2.2.1
Requires PHP: 5.6
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Provides LatitudePay and Genoapay payment option for WooCommerce.

== Description ==
LatitudePay/Genoapay Buy Now Pay Later (BNPL) payment plugin for WooCommerce provides a seamless integration between your store and LatitudePay in Australia or Genoapay in New Zealand. LatitudePay is enabled automatically if the currency is set as Australian Dollars and Genoapay is enabled if the currency is set as New Zealand Dollars. It also enables the price point snippets on product display pages. There are zero surprises with LatitudePay & Genoapay. It does what it says on the box: 10 weekly payments instead of all at once. No interest, and no waiting for your purchases.

== Installation ==

Please refer to our [latest installation guide](https://resources.latitudefinancial.com/docs/latitude-pay/woocommerce/).

== Frequently Asked Questions ==
= Where do I find more information about LatitudePay and Genoapay? =

Please visit the official websites below for more information.
[LatitudePay (Australia)](https://latitudepay.com)
[Genoapay (New Zealand)](https://genoapay.com)

= What do I do if I need help? =

Please send your integration related issues to [Integration Support](mailto:integrationsupport@latitudefinancial.com)

== Changelog ==
= 2.2.1 =
* Fix backend logic for displaying snippet on cart page 
* Fix plugin compatibility for WordPress deployed on Windows-based server

= 2.2 =
* Some important security improvements and bug fixes

= 2.1 =
* Fixed the CSS issue with the LatitudePay logo at checkout
* Support Woocommerce blocks extension to show the payment method from the checkout page
* Plugin Improvements

= 2.0.12 =
* Fix the issue with snippets width

= 2.0.11 =
* Fix the issue with default payment method title inside admin dashboard

= 2.0.10 =
* Implement LatitudePay+

= 2.0.9 =
* Implement Images API snippets and modals
* Improve logging function

= 2.0.8 =
* Fix invalid signature issue by replace all symbols from products' name by spaces

= 2.0.7 =
* Fix deprecated functions in new PHP version issue

= 2.0.6 =
* Fix the style issue in Latitude popup

= 2.0.5 =
* Fix the conflict that cause empty cart issue

= 2.0.4 =
* Fix conflicts issue with paypal
* Fix other small bugs.

= 2.0.3 =
* Add configurations to toggle (opt-in / opt-out) payment snippets at product and cart pages
* Fix style issue on Genoapay popup

= 2.0.2 =
* Fix invalid signature issue

= 2.0 =
* Performance improvements.
* Fixed shopping cart errors in some installations.
* Changed min & max comparing methods.
* Use get_data for all product types.
