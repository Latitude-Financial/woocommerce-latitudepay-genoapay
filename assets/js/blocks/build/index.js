/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@woocommerce/settings/build-module/index.js":
/*!******************************************************************!*\
  !*** ./node_modules/@woocommerce/settings/build-module/index.js ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "ADMIN_URL": function() { return /* binding */ ADMIN_URL; },
/* harmony export */   "COUNTRIES": function() { return /* binding */ COUNTRIES; },
/* harmony export */   "CURRENCY": function() { return /* binding */ CURRENCY; },
/* harmony export */   "LOCALE": function() { return /* binding */ LOCALE; },
/* harmony export */   "ORDER_STATUSES": function() { return /* binding */ ORDER_STATUSES; },
/* harmony export */   "SITE_TITLE": function() { return /* binding */ SITE_TITLE; },
/* harmony export */   "WC_ASSET_URL": function() { return /* binding */ WC_ASSET_URL; },
/* harmony export */   "DEFAULT_DATE_RANGE": function() { return /* binding */ DEFAULT_DATE_RANGE; },
/* harmony export */   "getSetting": function() { return /* binding */ getSetting; }
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/typeof */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");



function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(source, true).forEach(function (key) { (0,_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__.default)(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(source).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

var defaults = {
  adminUrl: '',
  countries: [],
  currency: {
    code: 'USD',
    precision: 2,
    symbol: '$',
    symbolPosition: 'left',
    decimalSeparator: '.',
    priceFormat: '%1$s%2$s',
    thousandSeparator: ','
  },
  defaultDateRange: 'period=month&compare=previous_year',
  locale: {
    siteLocale: 'en_US',
    userLocale: 'en_US',
    weekdaysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
  },
  orderStatuses: [],
  siteTitle: '',
  wcAssetUrl: ''
};
var globalSharedSettings = (typeof wcSharedSettings === "undefined" ? "undefined" : (0,_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_1__.default)(wcSharedSettings)) === 'object' ? wcSharedSettings : {}; // Use defaults or global settings, depending on what is set.

var allSettings = _objectSpread({}, defaults, {}, globalSharedSettings);

allSettings.currency = _objectSpread({}, defaults.currency, {}, allSettings.currency);
allSettings.locale = _objectSpread({}, defaults.locale, {}, allSettings.locale);
var ADMIN_URL = allSettings.adminUrl;
var COUNTRIES = allSettings.countries;
var CURRENCY = allSettings.currency;
var LOCALE = allSettings.locale;
var ORDER_STATUSES = allSettings.orderStatuses;
var SITE_TITLE = allSettings.siteTitle;
var WC_ASSET_URL = allSettings.wcAssetUrl;
var DEFAULT_DATE_RANGE = allSettings.defaultDateRange;
function getSetting(name) {
  var fallback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

  if (allSettings.hasOwnProperty(name)) {
    return allSettings[name];
  }

  return fallback;
}
//# sourceMappingURL=index.js.map

/***/ }),

/***/ "./src/constants.js":
/*!**************************!*\
  !*** ./src/constants.js ***!
  \**************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "TRANSLATION_TEXT_DOMAIN": function() { return /* binding */ TRANSLATION_TEXT_DOMAIN; }
/* harmony export */ });
const TRANSLATION_TEXT_DOMAIN = 'latitudepay-genoapay-integrations-for-woocommerce';

/***/ }),

/***/ "./src/utils.js":
/*!**********************!*\
  !*** ./src/utils.js ***!
  \**********************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getData": function() { return /* binding */ getData; }
/* harmony export */ });
/* harmony import */ var _woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @woocommerce/settings */ "./node_modules/@woocommerce/settings/build-module/index.js");
/**
 * External dependencies
 */

/**
 * Eway data comes form the server passed on a global object.
 */

const getData = () => {
  const data = (0,_woocommerce_settings__WEBPACK_IMPORTED_MODULE_0__.getSetting)('latitudepay_data', null);

  if (!data || typeof data !== 'object') {
    if (window.wcSettings && window.wcSettings['latitudepay_data']) {
      return window.wcSettings['latitudepay_data'];
    }

    throw new Error('Latitudepay initialization data is not available');
  }

  return data;
};

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ (function(module) {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/html-entities":
/*!**************************************!*\
  !*** external ["wp","htmlEntities"] ***!
  \**************************************/
/***/ (function(module) {

module.exports = window["wp"]["htmlEntities"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \*******************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _defineProperty; }
/* harmony export */ });
function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/typeof.js":
/*!***********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/typeof.js ***!
  \***********************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _typeof; }
/* harmony export */ });
function _typeof(obj) {
  "@babel/helpers - typeof";

  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function _typeof(obj) {
      return typeof obj;
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
!function() {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./src/utils.js");
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/html-entities */ "@wordpress/html-entities");
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _constants__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./constants */ "./src/constants.js");
var _settings$supports;



/**
 * External dependencies
 */




const {
  registerPaymentMethod
} = wc.wcBlocksRegistry;
const settings = (0,_utils__WEBPACK_IMPORTED_MODULE_2__.getData)();

const Label = props => {
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: `${settings.icon}`,
    alt: (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__.decodeEntities)(settings.title || (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('LatitudePay', _constants__WEBPACK_IMPORTED_MODULE_4__.TRANSLATION_TEXT_DOMAIN)),
    width: "140px"
  });
};

const Content = () => {
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: `${settings.snippet_url || "#"}`,
    alt: (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__.decodeEntities)(settings.title || (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('LatitudePay', _constants__WEBPACK_IMPORTED_MODULE_4__.TRANSLATION_TEXT_DOMAIN)),
    style: {
      cursor: 'pointer'
    }
  }));
};

registerPaymentMethod({
  name: settings.id,
  label: (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Label, null),
  ariaLabel: (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__.decodeEntities)(settings.title || (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('LatitudePay', _constants__WEBPACK_IMPORTED_MODULE_4__.TRANSLATION_TEXT_DOMAIN)),
  placeOrderButtonLabel: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Proceed to ' + settings.title || 0, _constants__WEBPACK_IMPORTED_MODULE_4__.TRANSLATION_TEXT_DOMAIN),
  canMakePayment: () => true,
  content: (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Content, null),
  edit: (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Content, null),
  supports: {
    features: (_settings$supports = settings === null || settings === void 0 ? void 0 : settings.supports) !== null && _settings$supports !== void 0 ? _settings$supports : []
  }
});
}();
/******/ })()
;
//# sourceMappingURL=index.js.map