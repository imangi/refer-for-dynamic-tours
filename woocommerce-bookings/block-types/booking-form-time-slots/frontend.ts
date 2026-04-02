/**
 * WordPress dependencies
 */
import { store, getContext, getElement } from '@wordpress/interactivity';

/**
 * Internal dependencies
 */
import { formatTimeString } from '@block-types/shared/utils';
import type { FormContext } from '@block-types/shared/types';

export type Context = {
	slotsPerPage: number;
	touchStartX: number;
	touchCurrentX: number;
	isDragging: boolean;
};

export type BookingTimeSlotsStore = {
	state: {
		isVisible: boolean;
		shouldShowPlaceholder: boolean;
		shouldShowPagination: boolean;
		totalPages: number;
		pages: {
			pageNumber: number;
			ariaLabel: string;
			isSelected: boolean;
		}[];
	};
	actions: {
		nextPage: () => void;
		prevPage: () => void;
		handleGoToPage: () => void;
		handleSelectTime: () => void;
		findNextAvailable: () => void;
		onTouchStart: ( event: TouchEvent ) => void;
		onTouchMove: ( event: TouchEvent ) => void;
		onTouchEnd: () => void;
	};
};

const { state: bookingFormState } = store(
	'woocommerce-bookings/booking-form',
	{}
);

const { state, actions } = store< BookingTimeSlotsStore >(
	'woocommerce-bookings/booking-time-slots',
	{
		state: {
			get isVisible() {
				return !! getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				)?.requiresTimeSelection;
			},
			get shouldShowPlaceholder() {
				const slots = bookingFormState.slotsForSelectedDate;
				if ( ! slots && ! bookingFormState.isBusy ) {
					return true;
				}
				return slots.length === 0;
			},
			get totalPages() {
				const slotsPerPage = getContext< Context >().slotsPerPage;
				const slots = bookingFormState.slotsForSelectedDate;
				return Math.ceil( slots.length / slotsPerPage );
			},
			get pages() {
				return Array.from(
					{ length: state.totalPages },
					( _, index ) => ( {
						pageNumber: index + 1,
						ariaLabel: `Go to page ${ index + 1 }`,
						isSelected:
							index + 1 === bookingFormState.slotsCurrentPage,
					} )
				);
			},
			get shouldShowPagination() {
				return state.totalPages > 1;
			},
			get slotsForPage() {
				const currentPage = bookingFormState.slotsCurrentPage;
				const slots = bookingFormState.slotsForSelectedDate;
				if ( ! slots ) {
					return [];
				}
				const ctx = getContext< Context >();
				return slots.slice(
					( currentPage - 1 ) * ctx.slotsPerPage,
					currentPage * ctx.slotsPerPage
				);
			},
			get isPreviousPageDisabled() {
				return bookingFormState.slotsCurrentPage === 1;
			},
			get isNextPageDisabled() {
				return bookingFormState.slotsCurrentPage === state.totalPages;
			},
			// Derived states for dynamic slot properties
			get slotIsSelected() {
				const context = getContext<
					Context & { slot: { time: string; capacity: number } }
				>();
				const ctx = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				const selectedTime = ctx.selectedSlotKey;
				return selectedTime && selectedTime === context.slot.time;
			},
			get slotAriaLabel() {
				const context = getContext<
					Context & { slot: { time: string; capacity: number } }
				>();
				return `Select ${ formatTimeString( context.slot.time ) }`;
			},
			get slotTimeString() {
				const context = getContext<
					Context & { slot: { time: string; capacity: number } }
				>();
				return formatTimeString( context.slot.time );
			},
		},
		actions: {
			nextPage() {
				const ctx = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				ctx.slotsCurrentPage = ctx.slotsCurrentPage + 1;
			},
			prevPage() {
				const ctx = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				ctx.slotsCurrentPage = ctx.slotsCurrentPage - 1;
			},
			handleGoToPage() {
				const elementButton = getElement()?.ref as HTMLElement;
				if ( ! elementButton ) {
					return;
				}
				const pageNumber = parseInt(
					elementButton.getAttribute( 'data-pageNumber' ) as string,
					10
				);
				if ( pageNumber < 1 || pageNumber > state.totalPages ) {
					return;
				}

				const ctx = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				ctx.slotsCurrentPage = pageNumber;
			},

			handleSelectTime() {
				const timeElement = getElement()?.ref as HTMLElement;
				if ( ! timeElement ) {
					return;
				}

				const time = timeElement.dataset.time;

				// Do nothing if clicked on the same time.
				const ctx = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				if ( ctx.selectedSlotKey === time ) {
					return;
				}
				ctx.selectedSlotKey = time;

				// Focus confirm button.
				setTimeout( () => {
					const button = document.querySelector(
						'.wc-bookings-modal[open] .wc-bookings-modal-buttons .wp-element-button'
					);
					if ( button ) {
						( button as HTMLElement )?.focus();
					}
				}, 0 );
			},

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

				const SNAP_THRESHOLD = 0.3;
				const delta = context.touchCurrentX - context.touchStartX;
				const element = getElement()?.ref;
				const calendarWidth = element?.offsetWidth || 0;

				// Only trigger swipe actions if there was significant movement
				if ( Math.abs( delta ) > calendarWidth * SNAP_THRESHOLD ) {
					if ( delta > 0 ) {
						actions.prevPage();
					} else if ( delta < 0 ) {
						actions.nextPage();
					}
				}

				// Reset touch state
				context.isDragging = false;
				context.touchStartX = 0;
				context.touchCurrentX = 0;
			},
		},
		callbacks: {
			selectFirstSlotWhenDateSelected() {
				// pick first slot if no slot is selected
				const ctx = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				const slots = bookingFormState.slotsForSelectedDate;
				if ( slots && slots.length > 0 ) {
					ctx.selectedSlotKey = slots[ 0 ].time;
					ctx.slotsCurrentPage = 1;
				} else {
					ctx.selectedSlotKey = null;
					ctx.slotsCurrentPage = 1;
				}
			},
		},
	}
);
