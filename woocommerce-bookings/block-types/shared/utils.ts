/**
 * Utility functions for booking blocks
 */

/**
 * Pad a number to two digits with leading zero
 *
 * @param n - Number to pad
 * @return Padded string
 */
export function padToTwoDigits( n: number ): string {
	return String( n ).padStart( 2, '0' );
}

/**
 * Format a date as YYYY-MM-DD
 *
 * @param date - Date to format
 * @return Formatted date string
 */
export function formatDateAsYmd( date: Date ): string {
	return `${ date.getFullYear() }-${ padToTwoDigits(
		date.getMonth() + 1
	) }-${ padToTwoDigits( date.getDate() ) }`;
}

/**
 * Format a date as YYYY-MM-DD HH:MM:SS
 *
 * @param date - Date to format
 * @return Formatted date-time string
 */
export function formatDateTimeAsYmdHis( date: Date ): string {
	return `${ formatDateAsYmd( date ) } ${ padToTwoDigits(
		date.getHours()
	) }:${ padToTwoDigits( date.getMinutes() ) }:${ padToTwoDigits(
		date.getSeconds()
	) }`;
}

/**
 * Format a date as YYYY-MM (month key)
 *
 * @param date - Date to format
 * @return Month key string (e.g., "2026-02")
 */
export function formatMonthKey( date: Date ): string {
	return `${ date.getFullYear() }-${ padToTwoDigits( date.getMonth() + 1 ) }`;
}

/**
 * Parse a date string in YYYY-MM-DD format
 *
 * @param s - Date string to parse
 * @return Parsed Date object
 */
export function parseDateString( s: string ): Date {
	const [ y, m, d ] = s.split( '-' ).map( Number );
	return new Date( y, m - 1, d );
}

/**
 * Add days to a date
 *
 * @param date - Base date
 * @param days - Number of days to add (can be negative)
 * @return New Date object
 */
export function addDays( date: Date, days: number ): Date {
	const d = new Date( date );
	d.setDate( d.getDate() + days );
	return d;
}

/**
 * Get the start date of a month from a month key
 *
 * @param monthKey - Month key in YYYY-MM format
 * @return Date object for the first day of the month, or null if invalid
 */
export function getMonthStartDate( monthKey: string ): Date | null {
	if ( ! monthKey ) {
		return null;
	}
	const [ y, m ] = monthKey.split( '-' ).map( Number );
	if ( ! y || ! m ) {
		return null;
	}
	return new Date( y, m - 1, 1 );
}

/**
 * Get the end date of a month from a month key
 *
 * @param monthKey - Month key in YYYY-MM format
 * @return Date object for the last day of the month, or null if invalid
 */
export function getMonthEndDate( monthKey: string ): Date | null {
	const d = getMonthStartDate( monthKey );
	if ( ! d ) {
		return null;
	}
	d.setMonth( d.getMonth() + 1 );
	d.setHours( 23, 59, 59 );
	return addDays( d, -1 );
}

/**
 * Get the start of the week for a given date
 *
 * @param {Date}   date         Date to get week start for
 * @param {number} weekStartsOn Day of week that week starts on (1 = Monday, 0 = Sunday)
 * @return {Date} Date object for the start of the week
 */
export function startOfWeek( date: Date, weekStartsOn: number = 1 ): Date {
	const d = new Date( date );
	const day = d.getDay(); // 0-6 (Sun-Sat)
	const delta = ( day - weekStartsOn + 7 ) % 7;
	return addDays( d, -delta );
}

/**
 * Get the end of the week for a given date
 *
 * @param {Date}   date         Date to get week end for
 * @param {number} weekStartsOn Day of week that week starts on (1 = Monday, 0 = Sunday)
 * @return {Date} Date object for the end of the week
 */
export function endOfWeek( date: Date, weekStartsOn: number = 1 ): Date {
	return addDays( startOfWeek( date, weekStartsOn ), 6 );
}

/**
 * Check if a date is within a window
 *
 * @param {string}                dateYmd        Date string in YYYY-MM-DD format
 * @param {string|null|undefined} windowStartYmd Window start date string in YYYY-MM-DD format
 * @param {string|null|undefined} windowEndYmd   Window end date string in YYYY-MM-DD format
 * @return {boolean} True if date is within window
 */
export function withinWindow(
	dateYmd: string,
	windowStartYmd: string | null | undefined,
	windowEndYmd: string | null | undefined
): boolean {
	if ( ! windowStartYmd || ! windowEndYmd ) {
		return true;
	}
	return dateYmd >= windowStartYmd && dateYmd <= windowEndYmd;
}

/**
 * Format a time string from HH:MM:SS to 12-hour format (e.g., "09:00 PM")
 *
 * @param timeString - Time string in HH:MM:SS format
 * @return Formatted time string (e.g., "09:00 PM")
 */
export function formatTimeString( timeString: string ): string {
	const [ hours, minutes ] = timeString.split( ':' ).map( Number );
	const ampm = hours >= 12 ? 'pm' : 'am';
	const hours12 = hours % 12 || 12;
	return `${ String( hours12 ).padStart( 2, '0' ) }:${ String(
		minutes
	).padStart( 2, '0' ) } ${ ampm }`;
}

/**
 * Lock body scroll while preserving scroll position.
 */
export function lockBodyScroll(): void {
	if ( typeof document === 'undefined' ) {
		return;
	}

	const body = document.body;
	if ( ! body || body.dataset.wcBookingsScrollLock ) {
		return;
	}

	const scrollY = window.scrollY || window.pageYOffset;
	body.dataset.wcBookingsScrollLock = '1';
	body.dataset.wcBookingsScrollY = String( scrollY );
	body.dataset.wcBookingsPrevOverflow = body.style.overflow || '';
	body.dataset.wcBookingsPrevPosition = body.style.position || '';
	body.dataset.wcBookingsPrevTop = body.style.top || '';
	body.dataset.wcBookingsPrevWidth = body.style.width || '';

	body.style.overflow = 'hidden';
	body.style.position = 'fixed';
	body.style.top = `-${ scrollY }px`;
	body.style.width = '100%';
}

/**
 * Unlock body scroll and restore previous scroll position.
 */
export function unlockBodyScroll(): void {
	if ( typeof document === 'undefined' ) {
		return;
	}

	const body = document.body;
	if ( ! body || ! body.dataset.wcBookingsScrollLock ) {
		return;
	}

	const scrollY = Number( body.dataset.wcBookingsScrollY || 0 );
	body.style.overflow = body.dataset.wcBookingsPrevOverflow || '';
	body.style.position = body.dataset.wcBookingsPrevPosition || '';
	body.style.top = body.dataset.wcBookingsPrevTop || '';
	body.style.width = body.dataset.wcBookingsPrevWidth || '';
	delete body.dataset.wcBookingsScrollLock;
	delete body.dataset.wcBookingsScrollY;
	delete body.dataset.wcBookingsPrevOverflow;
	delete body.dataset.wcBookingsPrevPosition;
	delete body.dataset.wcBookingsPrevTop;
	delete body.dataset.wcBookingsPrevWidth;
	window.scrollTo( 0, Number.isFinite( scrollY ) ? scrollY : 0 );
}
