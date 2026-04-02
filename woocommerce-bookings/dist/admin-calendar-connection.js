/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/************************************************************************/

;// external "jQuery"
const external_jQuery_namespaceObject = window["jQuery"];
var external_jQuery_default = /*#__PURE__*/__webpack_require__.n(external_jQuery_namespaceObject);
;// ./src/js/admin-calendar-connection.js

external_jQuery_default()(document).ready(function ($) {
  $('#wc_bookings_google_calendar_redirect').removeAttr('value');
  $('.wc-bookings-calendar-connect').on('click', function () {
    $('#wc_bookings_google_calendar_redirect').val('1');
    $('#bookings_settings').trigger('submit');
  });

  // Open WooConnect Google auth in a popup (single handler via enqueued script).
  $('#wc_bookings_google_connect_btn').on('click', function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    if (!url || $(this).hasClass('disabled')) {
      return;
    }
    var width = 600;
    var height = 600;
    var left = screen.width / 2 - width / 2;
    var top = screen.height / 2 - height / 2;
    // Intentionally omit noopener: OAuth callback relies on window.opener to refresh the parent and close the popup.
    var features = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=no,width=' + width + ',height=' + height + ',top=' + top + ',left=' + left;
    window.open(url, 'wc_bookings_google_auth', features);
  });
});
/******/ })()
;
//# sourceMappingURL=admin-calendar-connection.js.map