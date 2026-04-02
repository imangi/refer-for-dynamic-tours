/**
 * WordPress dependencies
 */
import { store, withSyncEvent, getContext } from '@wordpress/interactivity';

/**
 * Internal dependencies
 */
import setStyles from './set-styles';
import { lockBodyScroll, unlockBodyScroll } from '@block-types/shared/utils';
import type { FormContext } from '@block-types/shared/types';

type Store = {
	state: {
		ui: {
			openByProductId: number | null;
		};
		isModalOpen: boolean;
		shouldHideAddToCartButton: boolean;
	};
	actions: {
		handleTabNavigation: ( event: KeyboardEvent ) => void;
		onModalKeyDown: ( event: KeyboardEvent ) => void;
		openModal: () => void;
		closeModal: () => void;
	};
	callbacks: {
		focusModalWhenOpen: () => void;
	};
};

setStyles();

const { state: bookingFormState } = store(
	'woocommerce-bookings/booking-form',
	{}
);

const { state, actions: modalActions } = store< Store >(
	'woocommerce-bookings/booking-modal',
	{
		state: {
			// Global state enables cross-tree modal controls. (e.g., trigger block can be anywhere on the page).
			ui: {
				openByProductId: null as number | null,
			},
			get isModalOpen() {
				const productId = bookingFormState.currentProductId;
				return state.ui.openByProductId === productId;
			},
			get shouldHideAddToCartButton() {
				const ctx = getContext< FormContext >(
					'woocommerce-bookings/booking-form'
				);
				return ! ctx.selectedSlotKey || ! ctx.selectedDate;
			},
		},
		actions: {
			handleTabNavigation: ( event: KeyboardEvent ) => {
				// Only handle Tab when modal is open
				if ( ! state.isModalOpen || event.key !== 'Tab' ) {
					return;
				}

				const focusableElementsSelectors =
					'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), [tabindex]:not([tabindex="-1"])';

				const dialog = document.querySelector(
					'.wc-bookings-modal[open]'
				) as HTMLElement;

				if ( ! dialog ) {
					return;
				}

				// Query focusable elements within the dialog
				const focusableElements = Array.from(
					dialog.querySelectorAll( focusableElementsSelectors )
				).filter(
					( el ) => ! ( el as HTMLElement ).hasAttribute( 'inert' )
				) as HTMLElement[];

				if ( ! focusableElements.length ) {
					return;
				}

				const firstFocusableElement = focusableElements[ 0 ];
				const lastFocusableElement =
					focusableElements[ focusableElements.length - 1 ];
				const activeElement = dialog.ownerDocument
					.activeElement as HTMLElement;

				// If focus is outside the dialog, trap it and move to first element
				if ( ! dialog.contains( activeElement ) ) {
					event.preventDefault();
					event.stopPropagation();
					firstFocusableElement.focus();
					return;
				}

				// Handle wrapping: Tab from last element goes to first
				if (
					! event.shiftKey &&
					activeElement === lastFocusableElement
				) {
					event.preventDefault();
					event.stopPropagation();
					firstFocusableElement.focus();
					return;
				}

				// Handle wrapping: Shift+Tab from first element goes to last
				if (
					event.shiftKey &&
					activeElement === firstFocusableElement
				) {
					event.preventDefault();
					event.stopPropagation();
					lastFocusableElement.focus();
				}
			},
			onModalKeyDown: withSyncEvent( ( event: KeyboardEvent ) => {
				// Only handle keys when modal is open
				if ( ! state.isModalOpen ) {
					return;
				}

				if ( event.key === 'Escape' ) {
					event.preventDefault();
					modalActions.closeModal();
					return;
				}

				if ( event.key === 'Tab' ) {
					modalActions.handleTabNavigation( event );
				}
			} ),
			openModal: () => {
				const productId = bookingFormState.currentProductId;
				if ( productId ) {
					state.ui.openByProductId = productId;
				}

				// Lock page scroll.
				lockBodyScroll();
			},
			closeModal: () => {
				state.ui.openByProductId = null;

				// Unlock page scroll.
				unlockBodyScroll();
			},
		},
		callbacks: {
			focusModalWhenOpen() {
				if ( ! state.isModalOpen ) {
					return;
				}

				setTimeout( () => {
					const modal = document.querySelector(
						'.wc-bookings-modal[open]'
					) as HTMLElement;
					modal?.focus();
				}, 0 );
			},
		},
	}
);
