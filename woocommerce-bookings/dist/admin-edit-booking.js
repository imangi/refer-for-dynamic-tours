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

;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// external "jQuery"
const external_jQuery_namespaceObject = window["jQuery"];
var external_jQuery_default = /*#__PURE__*/__webpack_require__.n(external_jQuery_namespaceObject);
;// external "moment"
const external_moment_namespaceObject = window["moment"];
var external_moment_default = /*#__PURE__*/__webpack_require__.n(external_moment_namespaceObject);
;// ./src/js/admin-edit-booking.js
// External dependencies.



(function ($) {
  /**
   * When Document is ready.
   */
  $(document).ready(function () {
    // When a confirm link clicked from email, show dialog and trigger change.
    if ('true' === new URLSearchParams(window.location.href).get('confirm') && 'confirmed' !== $('#_booking_status').val() && confirm((0,external_wp_i18n_namespaceObject.__)('Confirm the booking?', 'woocommerce-bookings'))) {
      $(' #_booking_status').val('confirmed');
      $(' #post').submit();
    }

    // Handle attendance status configuration button click.
    $('#configure-attendance-status-btn').on('click', function (e) {
      e.preventDefault();
      const $prompt = $('.booking-attendance-status-prompt');
      const $field = $('.booking-attendance-status-field');
      $prompt.slideUp(300, function () {
        $prompt.hide();
        $field.slideDown(300, function () {
          $('#_booking_attendance_status').focus();
        });
      });
    });

    // Handle booking status change - hide/show attendance status field.
    function toggleAttendanceStatusField() {
      const bookingStatus = $('#_booking_status').val();
      const $attendanceField = $('.booking-attendance-status-field');
      const $attendancePrompt = $('.booking-attendance-status-prompt');
      if ('cancelled' === bookingStatus) {
        // Hide both prompt and field when cancelled.
        $attendancePrompt.slideUp(300);
        $attendanceField.slideUp(300);
      } else {
        // Show prompt if it exists, otherwise show field.
        if ($attendancePrompt.length) {
          $attendancePrompt.slideDown(300);
        } else if ($attendanceField.length) {
          $attendanceField.slideDown(300);
        }
      }
    }

    // Initial check on page load.
    toggleAttendanceStatusField();

    // Handle status changes.
    $('#_booking_status').on('change', toggleAttendanceStatusField);
  });

  /**
   * Returns true if the date string is of the format
   * yyyy-mm-dd, false otherwise.
   *
   * @param {string} dateString Date string.
   * @return {boolean} date string is of a valid format or not.
   */
  function isDateValidFormat(dateString = false) {
    if (false === dateString) {
      return false;
    }
    return external_moment_default()(dateString, 'YYYY-MM-DD', true).isValid();
  }

  /**
   * Returns true if the end date is before start date,
   * false otherwise.
   *
   * @param {string} startDate The start date string
   * @param {string} endDate The end date string
   * @return {boolean} the end date is before start date or not.
   */
  function isEndDateBeforeStartDate(startDate = '', endDate = '') {
    if (0 === startDate.length || 0 === endDate.length) {
      return false;
    }
    return !external_moment_default()(endDate).isBefore(startDate);
  }

  /**
   * Renders the error message.
   *
   * @param {string} errorMessage The error message to be rendered.
   */
  function renderError(errorMessage = '') {
    if (0 === errorMessage.length) {
      return;
    }
    alert(errorMessage); // eslint-disable-line
  }

  /**
   * Client-side validation to check if start and end dates in
   * Bookings > Add/Edit Booking is of the format yyyy-mm-dd.
   *
   * Prevents submission and displays error if the format is
   * otherwise.
   */
  const saveBookingBtn = $('#woocommerce-booking-save input[name="save"]');
  saveBookingBtn.on('click', function (e) {
    const startDateEl = $('#booking_start_date');
    const endDateEl = $('#booking_end_date');
    const startDateValue = startDateEl.val();
    const endDateValue = endDateEl.val();
    if (!(isDateValidFormat(startDateValue) && isDateValidFormat(endDateValue))) {
      e.preventDefault();
      renderError(wc_bookings_admin_edit_booking_params.invalid_start_end_date);
      return;
    }
    if (isDateValidFormat(startDateValue) && isDateValidFormat(endDateValue) && !isEndDateBeforeStartDate(startDateValue, endDateValue)) {
      e.preventDefault();
      renderError(wc_bookings_admin_edit_booking_params.date_range_invalid);
    }
  });
})((external_jQuery_default()));
/******/ })()
;
//# sourceMappingURL=admin-edit-booking.js.map