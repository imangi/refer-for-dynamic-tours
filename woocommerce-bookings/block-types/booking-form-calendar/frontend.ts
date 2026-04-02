/**
 * WordPress dependencies
 */
import {
	store,
	getContext,
	getElement,
	getConfig,
	withSyncEvent,
} from '@wordpress/interactivity';

/**
 * Internal dependencies
 */
import {
	formatDateAsYmd,
	formatMonthKey,
	getMonthStartDate,
	addDays,
} from '@block-types/shared/utils';
import { buildCacheKey } from '@block-types/shared/availability';
import type { FormContext } from '@block-types/shared/types';

export type CalendarDay = {
	key: string;
	day: number;
	ymd: string;
	isInViewMonth: boolean;
};

/**
 * Context type for the booking-form-calendar namespace.
 * Represents the local context properties used within this calendar block.
 */
export type Context = {
	touchStartX: number;
	touchCurrentX: number;
	isDragging: boolean;
};

export type Config = {
	monthNames: string[];
};

/**
 * Get the previous month and year.
 *
 * @param month The month.
 * @param year  The year.
 * @return The previous month and year.
 */
const getPreviousMonth = ( month: number, year: number ) => {
	const previousMonth = month === 1 ? 12 : month - 1;
	const previousYear = month === 1 ? year - 1 : year;
	return { previousMonth, previousYear };
};

/**
 * Get the next month and year.
 *
 * @param month The month.
 * @param year  The year.
 * @return The next month and year.
 */
const getNextMonth = ( month: number, year: number ) => {
	const nextMonth = month === 12 ? 1 : month + 1;
	const nextYear = month === 12 ? year + 1 : year;
	return { nextMonth, nextYear };
};

// Get the booking-form store to read/write state
const { state: bookingFormState, actions: bookingFormActions } = store(
	'woocommerce-bookings/booking-form',
	{}
);

const { state, actions } = store(
	'woocommerce-bookings/booking-form-calendar',
	{
		state: {
			get visibleDates() {
				const bookingFormContext = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				const start = bookingFormState.gridStart;
				const end = bookingFormState.gridEnd;
				const dates: CalendarDay[] = [];
				// Calculate days between start and end (inclusive)
				const daysDiff =
					Math.floor(
						( end.getTime() - start.getTime() ) /
							( 1000 * 60 * 60 * 24 )
					) + 1;
				for ( let i = 0; i < daysDiff; i++ ) {
					const d = addDays( start, i );
					const viewMonthStart = getMonthStartDate(
						bookingFormContext.viewMonth
					);
					if ( ! viewMonthStart ) {
						continue;
					}
					const isInViewMonth =
						formatMonthKey( d ) ===
						formatMonthKey( viewMonthStart );

					let keyPrefix: string;
					if ( isInViewMonth ) {
						keyPrefix = 'current';
					} else if (
						formatMonthKey( d ) < formatMonthKey( viewMonthStart )
					) {
						keyPrefix = 'prev';
					} else {
						keyPrefix = 'next';
					}

					dates.push( {
						key: `${ keyPrefix }-${ d.getDate() }-${ i }`,
						ymd: formatDateAsYmd( d ),
						day: d.getDate(),
						isInViewMonth,
					} );
				}
				return dates;
			},

			get viewMonthName() {
				const bookingFormContext = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				// Parse viewMonth from "YYYY-MM" format
				const viewMonthStr = bookingFormContext.viewMonth || '';
				const month = parseInt(
					viewMonthStr.split( '-' )[ 1 ] || '1',
					10
				);
				const monthNames = getConfig< Config >()?.monthNames;
				return monthNames[ month - 1 ];
			},

			get viewYear() {
				const bookingFormContext = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				const viewMonthStr = bookingFormContext.viewMonth || '';
				const year = parseInt(
					viewMonthStr.split( '-' )[ 0 ] || '2024',
					10
				);
				return year;
			},

			get prevMonthLabel() {
				const bookingFormContext = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				const viewMonthStr =
					bookingFormContext.viewMonth ||
					formatMonthKey( new Date() );
				const parts = viewMonthStr.split( '-' );
				const viewMonth = parseInt( parts[ 1 ], 10 );
				const viewYear = parseInt( parts[ 0 ], 10 );
				const { previousMonth, previousYear } = getPreviousMonth(
					viewMonth,
					viewYear
				);
				const monthNames = getConfig< Config >()?.monthNames;
				return `Go to ${
					monthNames[ previousMonth - 1 ]
				} ${ previousYear }`;
			},

			get nextMonthLabel() {
				const bookingFormContext = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				const viewMonthStr =
					bookingFormContext.viewMonth ||
					formatMonthKey( new Date() );
				const parts = viewMonthStr.split( '-' );
				const viewMonth = parseInt( parts[ 1 ], 10 );
				const viewYear = parseInt( parts[ 0 ], 10 );
				const { nextMonth, nextYear } = getNextMonth(
					viewMonth,
					viewYear
				);
				const monthNames = getConfig< Config >()?.monthNames;
				return `Go to ${ monthNames[ nextMonth - 1 ] } ${ nextYear }`;
			},

			get dayAriaLabel() {
				const context = getContext< Context & { day: CalendarDay } >();
				const dateParts = context.day.ymd.split( '-' );
				const year = parseInt( dateParts[ 0 ], 10 );
				const month = parseInt( dateParts[ 1 ], 10 );
				const dayNumber = parseInt( dateParts[ 2 ], 10 );

				const monthNames = getConfig< Config >()?.monthNames;
				return `${ monthNames[ month - 1 ] } ${ dayNumber }, ${ year }`;
			},

			// Derived states for dynamic day properties
			get dayIsToday() {
				const context = getContext< Context & { day: CalendarDay } >();
				const today = new Date();
				const todayString = formatDateAsYmd( today );
				return context.day.ymd === todayString;
			},

			get dayIsSelected() {
				const context = getContext< Context & { day: CalendarDay } >();
				const bookingFormContext = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				return bookingFormContext.selectedDate === context.day.ymd;
			},

			get dayIsDisabled() {
				// Always enable today's date, even if there are no time slots
				if ( state.dayIsToday ) {
					return false;
				}

				// Create date objects at 00:00:00 for consistent day-based comparison
				const context = getContext< Context & { day: CalendarDay } >();
				const dateToCheck = new Date( context.day.ymd );
				dateToCheck.setHours( 0, 0, 0, 0 );

				// Get booking window from booking-form store
				// TODO: These 00;00 could be added as derived withtin the bookingWindow state.
				const bookingWindow = bookingFormState.bookingWindow;
				if ( bookingWindow ) {
					const windowStart = new Date( bookingWindow.start );
					const windowEnd = new Date( bookingWindow.end );
					windowStart.setHours( 0, 0, 0, 0 );
					windowEnd.setHours( 0, 0, 0, 0 );

					if (
						dateToCheck < windowStart ||
						dateToCheck > windowEnd
					) {
						return true;
					}
				}

				// Look up in cache.
				const bookingFormContext = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				const monthKey = formatMonthKey( dateToCheck );
				const cacheKey = buildCacheKey(
					bookingFormContext.selectedTeamId,
					monthKey
				);
				const cacheEntry = (
					bookingFormContext.availabilityCache as
						| Record<
								string,
								{
									monthKey: string;
									data: Record<
										string,
										Record< string, number >
									>;
								}
						  >
						| undefined
				 )?.[ cacheKey ];

				const daySlots = cacheEntry?.data?.[ context.day.ymd ] as
					| Record< string, unknown >
					| number
					| undefined;

				const hasSlots =
					daySlots && typeof daySlots === 'object'
						? Object.keys( daySlots ).length > 0
						: Boolean( daySlots );

				// Disabled when there are no slots for this date.
				return ! hasSlots;
			},

			get dayTabIndex() {
				return state.dayIsDisabled ? -1 : 0;
			},

			get calendarKey() {
				const bookingFormContext = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				const viewMonthStr = bookingFormContext.viewMonth || '';
				return viewMonthStr;
			},

			get isPreviousMonthDisabled() {
				const bookingFormContext = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				if ( bookingFormContext.isBusy ) {
					return true;
				}

				const bookingWindow = bookingFormState.bookingWindow;
				if ( ! bookingWindow ) {
					return false;
				}

				const viewMonthStr = bookingFormContext.viewMonth || '';
				const parts = viewMonthStr.split( '-' );
				const viewMonth = parseInt( parts[ 1 ], 10 );
				const viewYear = parseInt( parts[ 0 ], 10 );

				const { previousMonth, previousYear } = getPreviousMonth(
					viewMonth,
					viewYear
				);

				const minDate = new Date( bookingWindow.start );
				if (
					( previousMonth < minDate.getMonth() + 1 &&
						previousYear === minDate.getFullYear() ) ||
					previousYear < minDate.getFullYear()
				) {
					return true;
				}

				return false;
			},

			get isNextMonthDisabled() {
				const bookingFormContext = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				if ( bookingFormContext.isBusy ) {
					return true;
				}

				const bookingWindow = bookingFormState.bookingWindow;
				if ( ! bookingWindow ) {
					return false;
				}

				const viewMonthStr = bookingFormContext.viewMonth || '';
				const parts = viewMonthStr.split( '-' );
				const viewMonth = parseInt( parts[ 1 ], 10 );
				const viewYear = parseInt( parts[ 0 ], 10 );

				const { nextMonth, nextYear } = getNextMonth(
					viewMonth,
					viewYear
				);

				const maxDate = new Date( bookingWindow.end );
				if (
					( nextMonth > maxDate.getMonth() + 1 &&
						nextYear === maxDate.getFullYear() ) ||
					nextYear > maxDate.getFullYear()
				) {
					return true;
				}

				return false;
			},
		},

		actions: {
			async navigateToPreviousMonth() {
				const bookingFormContext = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				const viewMonthStr = bookingFormContext.viewMonth || '';
				const parts = viewMonthStr.split( '-' );
				const viewMonth = parseInt( parts[ 1 ], 10 );
				const viewYear = parseInt( parts[ 0 ], 10 );

				const { previousMonth, previousYear } = getPreviousMonth(
					viewMonth,
					viewYear
				);
				const nextViewMonth = `${ previousYear }-${ String(
					previousMonth
				).padStart( 2, '0' ) }`;

				bookingFormActions.setViewMonth( nextViewMonth );
			},
			async navigateToNextMonth() {
				const bookingFormContext = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				const viewMonthStr = bookingFormContext.viewMonth || '';
				const parts = viewMonthStr.split( '-' );
				const viewMonth = parseInt( parts[ 1 ], 10 );
				const viewYear = parseInt( parts[ 0 ], 10 );

				const { nextMonth, nextYear } = getNextMonth(
					viewMonth,
					viewYear
				);
				const nextViewMonth = `${ nextYear }-${ String(
					nextMonth
				).padStart( 2, '0' ) }`;

				bookingFormActions.setViewMonth( nextViewMonth );
			},
			handleSelectDate: withSyncEvent( ( event: Event ) => {
				const dayElement = getElement()?.ref as HTMLElement;
				if ( ! dayElement ) {
					return;
				}

				const isDisabled =
					dayElement.getAttribute( 'aria-disabled' ) === 'true';
				if ( isDisabled ) {
					event.preventDefault();
					return;
				}

				const dateString = dayElement.dataset.date;
				if ( ! dateString ) {
					return;
				}

				const bookingFormContext = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				// Do nothing if clicked on the same date.
				if ( bookingFormContext.selectedDate === dateString ) {
					return;
				}

				// Set the date in the booking-form context
				bookingFormActions.selectDate( dateString );

				// Focus the time slots block if config requires time selection.
				const ctx = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				if ( ctx?.requiresTimeSelection ) {
					const form = dayElement.closest( 'form' );
					const timeSlotsBlock = form?.querySelector(
						'.wc-bookings-time-slots__grid'
					);
					if ( timeSlotsBlock ) {
						setTimeout( () => {
							const firstButton = (
								timeSlotsBlock as HTMLElement
							 )?.querySelector( 'button' );
							if ( firstButton ) {
								( firstButton as HTMLElement )?.focus();
							}
						}, 0 );
					}
				}
			} ),
			onTouchStart( event: TouchEvent ) {
				const context = getContext< Context >();
				const { clientX } = event.touches[ 0 ];

				context.touchStartX = clientX;
				context.touchCurrentX = clientX;
				context.isDragging = true;
			},
			onTouchMove( event: TouchEvent ) {
				const context = getContext< Context >();
				if ( ! context.isDragging ) {
					return;
				}

				const { clientX } = event.touches[ 0 ];
				context.touchCurrentX = clientX;

				// Only prevent default if there's significant horizontal movement
				const delta = clientX - context.touchStartX;
				if ( Math.abs( delta ) > 10 ) {
					event.preventDefault();
				}
			},
			onTouchEnd: () => {
				const context = getContext< Context >();
				if ( ! context.isDragging ) {
					return;
				}

				const SNAP_THRESHOLD = 0.4;
				const delta = context.touchCurrentX - context.touchStartX;
				const element = getElement()?.ref;
				const calendarWidth = element?.offsetWidth || 0;

				// Only trigger swipe actions if there was significant movement
				if ( Math.abs( delta ) > calendarWidth * SNAP_THRESHOLD ) {
					if ( delta > 0 && ! state.isPreviousMonthDisabled ) {
						actions.navigateToPreviousMonth();
					} else if ( delta < 0 && ! state.isNextMonthDisabled ) {
						actions.navigateToNextMonth();
					}
				}

				// Reset touch state
				context.isDragging = false;
				context.touchStartX = 0;
				context.touchCurrentX = 0;
			},
		},
	}
);
