/**
 * WordPress dependencies
 */
import { store, getContext, getElement } from '@wordpress/interactivity';

/**
 * Internal dependencies
 */
import type { FormContext } from '@block-types/shared/types';

type Store = {
	actions: {
		handleSelectTeamMember: ( event: Event ) => void;
	};
	callbacks: {
		teamMemberInit: () => void;
	};
};

store< Store >( 'woocommerce-bookings/booking-team-member-selector', {
	actions: {
		handleSelectTeamMember: ( event: Event ) => {
			const target = event.target as HTMLSelectElement;
			const ctx = getContext< FormContext >(
				'woocommerce-bookings/booking-form'
			);
			const nextTeamId = target?.value
				? parseInt( target.value, 10 )
				: null;
			const normalizedNextTeamId = Number.isNaN( nextTeamId )
				? null
				: nextTeamId;

			if ( ctx.selectedTeamId !== normalizedNextTeamId ) {
				ctx.selectedTeamId = normalizedNextTeamId;
				ctx.needsInitialBuffer = true;
			}
		},
	},
	callbacks: {
		teamMemberInit: () => {
			const target = getElement()?.ref;
			if ( ! target?.value ) {
				return;
			}

			const ctx = getContext< FormContext >(
				'woocommerce-bookings/booking-form'
			);
			if ( ! ctx.selectedTeamId ) {
				ctx.selectedTeamId = parseInt( target.value, 10 );
			}
		},
	},
} );
