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

final class BinaryPay_Variable {

	/**
	 *  API All Response Status
	 */
	const STATUS_DECLINED   = 'DECLINED';
	const STATUS_BLOCKED    = 'BLOCKED';
	const STATUS_FAILED     = 'FAILED';
	const STATUS_INPROGRESS = 'INPROGRESS';
	const STATUS_SUCCESSFUL = 'SUCCESSFUL';
	const STATUS_AUTHORISED = 'AUTHORISED';
	const STATUS_SUBMITTED  = 'SUBMITTED';
	const STATUS_REFUNDED   = 'REFUNDED';
	const STATUS_NEW        = 'NEW';
	const STATUS_EXPIRED    = 'EXPIRED';
	const STATUS_UNKNOWN    = 'UNKNOWN';
	const STATUS_APPROVED   = 'APPROVED';
	const STATUS_ERROR      = 'ERROR';
	const STATUS_PROCESSING = 'IN_PROGRESS';
	const STATUS_COMPLETED  = 'COMPLETED';

	/**
	 *  Account Infomation
	 */
	const ACCOUNTID   = 'accountId';
	const USERNAME    = 'username';
	const PASSWORD    = 'password';
	const ENVIRONMENT = 'environment';


	const GATEWAY_PATH = 'Gateways';

	const ERROR_SIGNAL = 'BinaryPay Notice';

	/**
	 * API key for binarypay
	 */
	const API_KEY = 'apiKey';

	/**
	 *  Purchase or Refund Amount
	 */
	const AMOUNT = 'amount';

	/**
	 * Currency Type
	 */
	const CURRENCY = 'currency';

	/**
	 * User Agent - user's browser
	 */
	const USER_AGENT = 'userAgent';

	/**
	 * Transaction - an array contains transaction details
	 */
	const TRANSACTION = 'transaction';

	/**
	 * Product Code - for qcard long term finance
	 */
	const PRODUCT_CODE = 'productCode';

	/**
	 * Direct to return URL after purchase
	 */
	const REDIRECT = 'redirecToUrlResponse';

	/**
	 * Process number returned by Qcard API
	 */
	const PROCESS_NO = 'processNo';

	/**
	 * Quantity of line items - qcard
	 */
	const QUANTITY = 'quantity';
	/**
	 * ID
	 */
	const IP = 'ip';

	/**
	 *  For all Callback Url
	 */
	const RETURN_URL = 'returnUrl';

	/**
	 *  Purchase or Refund Reference
	 */
	const REFERENCE = 'reference';

	/**
	 *  Purchase or Refund particular
	 */
	const PARTICULAR = 'particular';

	/**
	 *  Card Number
	 */
	const CARD_NUMBER = 'cardNumber';

	/**
	 *  Card Type
	 */
	const CARD_TYPE = 'cardType';

	/**
	 *  Card Expiry
	 */
	const CARD_EXPIRY = 'cardExpiry';

	/**
	 *  Card Holder
	 */
	const CARD_HOLDER = 'cardHolder';

	/**
	 *  Card CSC
	 */
	const CARD_CSC = 'cardCSC';

	/**
	 *  Card Token
	 */
	const CARD_TOKEN = 'cardToken';

	/**
	 *  Token Reference
	 */
	const CARD_TOKEN_REF = 'tokenReference';

	/**
	 *  Origin Transaction Id
	 */
	const ORIGIN_TRANSACTION_ID = 'originalTransactionId';

	/**
	 * Transaction Id
	 */
	const TRANSACTION_ID = 'transactionId';

	/**
	 *  Email
	 */
	const EMAIL = 'email';

	/**
	 * Merchat
	 */
	const MERCHANT = 'merchant';

	/**
	 * Merchant Code
	 */
	const MERCHANT_CODE = 'merchantCode';

	/**
	 * Merchant Url
	 */
	const MERCHANT_URL = 'merchantUrl';

	/**
	 *  Webpayment Merchant Token
	 */
	const MERCHANT_TOKEN = 'merchantToken';

	/**
	 *  Webpayment Store Card
	 */
	const STORE_CARD = 'storeCard';

	/**
	 *  Webpayment Force Store Card
	 */
	const FORCE_STORE_CARD = 'forceStoreCard';

	/**
	 *  Webpayment Display Email
	 */
	const DISPLAY_EMAIL = 'displayCustomerEmail';

	/**
	 *  WebPayment CMD Code
	 */
	const CMD = 'cmd';

	/**
	 * Bank Info
	 */
	const BANK = 'bank';

	const SKU = 'sku';

	/**
	 * OnlineEFTPOS PayerId
	 */
	const MOBILENUMBER = 'mobileNumber';

	/**
	 * OnlineEFTPOS Description
	 */
	const DESCRIPTION = 'description';

	/**
	 * PayerId Type
	 */
	const PAYMENT_TYPE = 'paymentType';

	/**
	 * Refund Id
	 */
	const REFUND_ID = 'refundId';

	/**
	 * Payment Id
	 */
	const ORDER_ID = 'orderId';

	/**
	 * Bank Id
	 */
	const BANK_ID = 'bankId';

	/**
	 * Term - financenow
	 */
	const TERM = 'term';

	/**
	 * Defterm - financenow
	 */
	const DEFTERM = 'defterm';

	/**
	 * Deposit - financenow
	 */
	const DEPOSIT = 'deposit';

	/**
	 * Rate - financenow
	 */
	const RATE = 'rate';

	/**
	 * Customer name - financenow
	 */
	const CUSTOMER_NAME = 'customerName';

	/**
	 *Customer email - financenow
	 */
	const CUSTOMER_EMAIL = 'customerEmail';

	/**
	 * GENOAPAY
	 */
	/**
	 * Firstname
	 */
	const FIRSTNAME = 'firstname';

	/**
	 * Surname
	 */
	const SURNAME = 'surname';

	/**
	 * Shipping Address
	 */
	const SHIPPING_ADDRESS = 'shippingAddress';

	/**
	 * Billing Address
	 */
	const BILLING_ADDRESS = 'billingAddress';

	/**
	 * Suburb
	 */
	const SHIPPING_SUBURB = 'shippingSuburb';

	/**
	 * City
	 */
	const SHIPPING_CITY = 'shippingCity';

	/**
	 * Postcode
	 */
	const SHIPPING_POSTCODE = 'shippingPostcode';

	/**
	 * Country Code
	 */
	const SHIPPING_COUNTRY_CODE = 'shippingCountryCode';

	/**
	 * Suburb
	 */
	const BILLING_SUBURB = 'billingSuburb';

	/**
	 * City
	 */
	const BILLING_CITY = 'billingCity';

	/**
	 * Postcode
	 */
	const BILLING_POSTCODE = 'billingPostcode';

	/**
	 * Country Code
	 */
	const BILLING_COUNTRY_CODE = 'billingCountryCode';

	/**
	 * Purchase Token
	 */
	const PURCHASE_TOKEN = 'purchaseToken';

	/**
	 * Tax amount
	 */
	const TAX_AMOUNT = 'taxAmount';

	/**
	 * Shipping Lines
	 */
	const SHIPPING_LINES = 'shippingLines';

	/**
	 * Product
	 */
	const PRODUCTS = 'products';

	/**
	 * Refund Reason
	 */
	const REASON = 'reason';

	/**
	 * FailureURL POLIPAY
	 */
	const FAILURE_URL = 'FailureURL';

	/**
	 * NotificationURL POLIPAY
	 */
	const NOTIFICATION_URL = 'NotificationURL';

	/**
	 * CancellationURL POLIPAY
	 */
	const CANCELLATION_URL = 'CancellationURL';

	/**
	 * MerchantReferenceFormat POLIPAY
	 */
	const REFERENCEFORMAT = 'ReferenceFormat';
}
