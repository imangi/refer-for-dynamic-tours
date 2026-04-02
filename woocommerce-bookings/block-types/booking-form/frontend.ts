/**
 * woocommerce-bookings/booking-form — Interactivity API store
 *
 * This store manages booking form state and availability caching.
 *
 * Caching Architecture:
 * - Cache keys uniquely identify availability data based on: teamId, monthKey, windowStart, windowEnd
 * - Data is stored in availabilityCache[cacheKey] = { monthKey, data: {...}, meta: {...} }
 * - Lookups use helper functions that build cache keys from current context
 * - This ensures consistent cache usage and prevents mixing data from different team/window combinations
 *
 * Notes:
 * - Multiple forms per page (all booking-specific data lives in local context per form instance)
 * - Race-safe caching with request versioning
 */
/**
 * External dependencies
 */
import { store, getContext, getConfig } from '@wordpress/interactivity';

/**
 * Internal dependencies
 */
import {
	formatDateAsYmd,
	formatMonthKey,
	getMonthStartDate,
	getMonthEndDate,
	addDays,
	startOfWeek,
	endOfWeek,
	parseDateString,
	withinWindow,
} from '@block-types/shared/utils';
import {
	buildCacheKey,
	groupConsecutiveMonths,
	buildAvailabilityFetchParams,
	parseAvailabilityResponse,
	getMonthsNeedingFetch,
} from '@block-types/shared/availability';
import type {
	DurationData,
	AvailabilityCacheEntry,
	CacheMetaEntry,
} from '@block-types/shared/types';

export type Context = {
	// Identity
	formId: string;

	// Selections
	selectedTeamId: number | null;
	selectedDate: string | null; // YYYY-MM-DD format
	selectedSlotKey: string | null;
	requiresTimeSelection: boolean;

	// Calendar view state
	viewMonth: string; // YYYY-MM format

	// Slots pagination
	slotsCurrentPage: number;

	// Constraints
	bookingWindowData: {
		start: DurationData;
		end: DurationData;
	} | null;

	// Caches (keyed by cacheKey, not monthKey)
	availabilityCache: Record< string, AvailabilityCacheEntry > | undefined;
	cacheMeta: Record< string, CacheMetaEntry > | undefined;

	// Request lifecycle (keyed by cacheKey)
	inFlight: Record< string, boolean > | undefined;
	requestVersion: Record< string, number > | undefined;

	// Misc
	lastError: string | null;
	isBusy: boolean;

	// Dynamically added properties
	cursorDateYmd?: string; // YYYY-MM-DD format
	isAddingBookingToCart?: boolean;
	needsInitialBuffer?: boolean;
};

export type Config = {
	isPermalinksPlain: boolean;
	weekStartsOn: number;
};

function calculateFutureDate( durationData: DurationData ) {
	if (
		! durationData ||
		typeof durationData.value === 'undefined' ||
		! durationData.unit
	) {
		return null;
	}

	const fromDate = new Date();
	const value = parseInt( durationData.value, 10 );
	if ( isNaN( value ) ) {
		return null;
	}

	if ( 0 === value ) {
		return fromDate;
	}

	switch ( durationData.unit ) {
		case 'minute':
			fromDate.setMinutes( fromDate.getMinutes() + value );
			break;
		case 'hour':
			fromDate.setHours( fromDate.getHours() + value );
			break;
		case 'day':
			fromDate.setDate( fromDate.getDate() + value );
			break;
		case 'week':
			fromDate.setDate( fromDate.getDate() + value * 7 );
			break;
		case 'month':
			fromDate.setMonth( fromDate.getMonth() + value );
			break;
		case 'year':
			fromDate.setFullYear( fromDate.getFullYear() + value );
			break;
		default:
			return null;
	}

	return fromDate;
}

const universalLock =
	'I acknowledge that using a private store means my plugin will inevitably break on the next store release.';

const { state: wooState } = store( 'woocommerce', {}, { lock: universalLock } );

const { state: productDataState } = store(
	'woocommerce/product-data',
	{},
	{ lock: universalLock }
);

const { state: modalState, actions: modalActions } = store(
	'woocommerce-bookings/booking-modal',
	{}
);

const { state, actions } = store( 'woocommerce-bookings/booking-form', {
	state: {
		// -------------------------
		// Booking window (derived from value+unit data)
		// -------------------------
		get bookingWindow() {
			const ctx = getContext< Context >();
			const windowData = ctx.bookingWindowData;
			if ( ! windowData ) {
				return null;
			}

			const startDate = calculateFutureDate( windowData.start );
			const endDate = calculateFutureDate( windowData.end );

			if ( ! startDate || ! endDate ) {
				return null;
			}

			return {
				start: formatDateAsYmd( startDate ),
				end: formatDateAsYmd( endDate ),
			};
		},

		// -------------------------
		// Calendar geometry
		// -------------------------
		get gridStart() {
			const ctx = getContext< Context >();
			const vm = getMonthStartDate( ctx.viewMonth );
			const weekStartsOn = getConfig< Config >()?.weekStartsOn ?? 1;
			return startOfWeek( vm, weekStartsOn );
		},
		get gridEnd() {
			const ctx = getContext< Context >();
			const last = getMonthEndDate( ctx.viewMonth );
			const weekStartsOn = getConfig< Config >()?.weekStartsOn ?? 1;
			return endOfWeek( last, weekStartsOn );
		},
		get monthsNeededForGrid() {
			const ctx = getContext< Context >();
			const start = state.gridStart;
			const end = state.gridEnd;

			const out = new Set();
			out.add( formatMonthKey( start ) );
			out.add( formatMonthKey( end ) );
			out.add( formatMonthKey( getMonthStartDate( ctx.viewMonth ) ) );

			return Array.from( out );
		},

		// -------------------------
		// Slots
		// -------------------------
		get slotsCurrentPage() {
			const ctx = getContext< Context >();
			return ctx.slotsCurrentPage;
		},

		// -------------------------
		// Availability interpretation
		// -------------------------
		get slotsForSelectedDate() {
			const ctx = getContext< Context >();
			if ( ! ctx.selectedDate ) {
				return null;
			}

			const cacheKey = buildCacheKey(
				ctx.selectedTeamId ?? null,
				formatMonthKey( parseDateString( ctx.selectedDate ) )
			);
			const cacheEntry = ctx.availabilityCache?.[ cacheKey ];
			if ( ! cacheEntry ) {
				return null;
			}
			const slots = cacheEntry.data?.[ ctx.selectedDate ] || null;
			if ( slots && typeof slots === 'object' ) {
				return Object.keys( slots ).map( ( time ) => ( {
					time,
					capacity: slots[ time ],
				} ) );
			}
			return slots;
		},

		get nextAvailableDateFrom() {
			const ctx = getContext< Context >();
			const cursor =
				ctx.cursorDateYmd ||
				ctx.selectedDate ||
				formatDateAsYmd( new Date() );
			const window = state.bookingWindow;
			const end = window?.end;

			// Scan forward day-by-day within loaded cache only (minimal baseline).
			// You can extend this later to fetch-forward month by month when needed.
			const cursorD = parseDateString( cursor );

			for ( let i = 0; i < 366; i++ ) {
				const d = addDays( cursorD, i );
				const date = formatDateAsYmd( d );

				if ( end && date > end ) {
					return null;
				}
				if ( ! withinWindow( date, window?.start, end ) ) {
					continue;
				}

				const cacheKey = buildCacheKey(
					ctx.selectedTeamId ?? null,
					formatMonthKey( parseDateString( date ) )
				);
				const cacheEntry = ctx.availabilityCache?.[ cacheKey ];
				if ( ! cacheEntry ) {
					continue; // not loaded
				}

				const slotsMap = cacheEntry.data?.[ date ];
				if ( ! slotsMap ) {
					continue;
				}

				// slotsMap is Record<string, number>
				const counts = Object.values( slotsMap ) as number[];
				for ( const count of counts ) {
					if ( count > 0 ) {
						return date;
					}
				}
			}
			return null;
		},

		/**
		 * Get the current product ID.
		 * This is the main source of truth for the product ID.
		 *
		 * It uses global state(s) to parse the "current" product ID in the following namespaces:
		 * - woocommerce/product-collection
		 * - woocommerce/product-data
		 */
		get currentProductId() {
			const ctx = getContext( 'woocommerce/product-collection' );
			if ( ctx?.productId ) {
				return ctx.productId;
			}

			if ( productDataState?.productId ) {
				return productDataState.productId;
			}

			return null;
		},

		/**
		 * Get the busy state from context.
		 * Indicates if the form is currently loading data.
		 */
		get isBusy() {
			const ctx = getContext< Context >();

			return (
				ctx.isBusy ||
				Object.keys( ctx.inFlight || {} ).length > 0 ||
				false
			);
		},
	},

	actions: {
		// -------------------------
		// Form data controls side effects
		// -------------------------
		setViewMonth( nextViewMonth: string ) {
			const ctx = getContext< Context >();
			ctx.viewMonth = nextViewMonth;

			// optimistic: view updates immediately, then we fetch what we need
			actions.ensureAvailability();
		},

		selectDate( dateYmd: string ) {
			// Prevent selecting outside booking window.
			const window = state.bookingWindow;
			if ( ! withinWindow( dateYmd, window?.start, window?.end ) ) {
				return;
			}

			const ctx = getContext< Context >();
			ctx.selectedDate = dateYmd;

			// Navigate to the next view month if needed.
			const nextViewMonth = formatMonthKey( parseDateString( dateYmd ) );
			if ( nextViewMonth !== ctx.viewMonth ) {
				actions.setViewMonth( nextViewMonth );
			}
		},

		// -------------------------
		// Add-to-Cart Actions
		// -------------------------
		*addBookingToCart() {
			const ctx = getContext< Context >();
			try {
				if ( ! ctx.selectedDate ) {
					throw new Error( 'No date selected' );
				}

				if ( ! ctx.selectedSlotKey ) {
					throw new Error( 'No time selected' );
				}

				ctx.isAddingBookingToCart = true;

				const bookingConfiguration = {
					date: `${ ctx.selectedDate } ${ ctx.selectedSlotKey }`,
				} as Record< string, string >;
				if ( ctx.selectedTeamId ) {
					bookingConfiguration.resource_id =
						ctx.selectedTeamId.toString();
				}

				const res: Response = yield fetch(
					`${ wooState.restUrl }wc/store/v1/cart/add-item`,
					{
						method: 'POST',
						headers: {
							Nonce: wooState.nonce,
							'Content-Type': 'application/json',
						},
						body: JSON.stringify( {
							id: state.currentProductId,
							booking_configuration: bookingConfiguration,
						} ),
					}
				);
				const json = yield res.json();
				ctx.isAddingBookingToCart = false;

				if ( json.data?.status < 200 || json.data?.status >= 300 ) {
					throw new Error( json.data?.message );
				}

				wooState.cart = json;

				return true;
			} catch ( error ) {
				// eslint-disable-next-line no-console
				console.error( error );

				return false;
			}
		},
		*addToCartAndContinueShopping( event: MouseEvent ) {
			if ( ! ( event.target instanceof HTMLAnchorElement ) ) {
				return;
			}
			event.preventDefault();

			const ctx = getContext< Context >();
			// Capture selected date and time before resetting
			const selectedDate = ctx.selectedDate;
			const selectedSlotKey = ctx.selectedSlotKey;

			const success = yield actions.addBookingToCart();
			if ( success ) {
				// Remove the slot from availability cache
				if (
					selectedDate &&
					selectedSlotKey &&
					ctx.availabilityCache
				) {
					const cacheKey = buildCacheKey(
						ctx.selectedTeamId ?? null,
						formatMonthKey( parseDateString( selectedDate ) )
					);
					const cacheEntry = ctx.availabilityCache[ cacheKey ];

					if ( cacheEntry && cacheEntry.data[ selectedDate ] ) {
						// Create immutable copy of the date's slots
						const dateSlots = {
							...cacheEntry.data[ selectedDate ],
						};

						// Remove the booked slot
						delete dateSlots[ selectedSlotKey ];

						// Check if there are any remaining slots for this date
						const hasRemainingSlots =
							Object.keys( dateSlots ).length > 0;

						// Create immutable copy of cache entry data
						const newData = { ...cacheEntry.data };

						if ( hasRemainingSlots ) {
							// Update the date's slots with the removed slot
							newData[ selectedDate ] = dateSlots;
						} else {
							// Remove the date key entirely if no slots remain
							delete newData[ selectedDate ];
						}

						// Update cache with immutable copy
						ctx.availabilityCache = {
							...ctx.availabilityCache,
							[ cacheKey ]: {
								monthKey: cacheEntry.monthKey,
								data: newData,
							},
						};
					}
				}

				// Reset the selected date and time.
				ctx.selectedDate = null;
				ctx.selectedSlotKey = null;
				ctx.slotsCurrentPage = 1;
				modalActions.closeModal();
			}
		},
		*addToCartAndCompleteBooking( event: MouseEvent ) {
			if ( ! ( event.target instanceof HTMLAnchorElement ) ) {
				return;
			}
			event.preventDefault();

			const success = yield actions.addBookingToCart();
			if ( success ) {
				window.location.href = event.target.href;
			}
		},

		// -------------------------
		// Fetch orchestration
		// -------------------------
		ensureAvailability() {
			const ctx = getContext< Context >();
			const monthsForGrid = state.monthsNeededForGrid;
			const monthsToRequest = [ ...monthsForGrid ];

			// Add forward buffer months only on first call to avoid frequent loading states
			const needsBuffer = ctx.needsInitialBuffer !== false; // Default to true if undefined
			if ( needsBuffer ) {
				const MONTH_BUFFER = 3; // Number of months to buffer after grid end
				const end = state.gridEnd;
				const endMonth = getMonthStartDate( formatMonthKey( end ) );

				if ( endMonth ) {
					// Add buffer months after grid end
					for ( let i = 1; i <= MONTH_BUFFER; i++ ) {
						const bufferDate = new Date( endMonth );
						bufferDate.setMonth( bufferDate.getMonth() + i );
						monthsToRequest.push( formatMonthKey( bufferDate ) );
					}
				}

				// Mark that initial buffer has been applied
				ctx.needsInitialBuffer = false;
			}

			actions.ensureAvailabilityData( monthsToRequest );
		},

		*ensureAvailabilityData( monthKeys: string[] ) {
			const ctx = getContext< Context >();

			// Step 1: Filter to only months that actually need fetching
			// This must happen BEFORE grouping to minimize API usage
			const monthsNeedingFetch = getMonthsNeedingFetch(
				monthKeys,
				{
					availabilityCache: ctx.availabilityCache,
					cacheMeta: ctx.cacheMeta,
					inFlight: ctx.inFlight,
				},
				buildCacheKey,
				ctx.selectedTeamId ?? null
			);

			if ( monthsNeedingFetch.length === 0 ) {
				return;
			}

			// Step 2: Group consecutive months that need fetching
			// Only group months that we actually need to fetch
			const groups = groupConsecutiveMonths( monthsNeedingFetch );

			for ( const group of groups ) {
				if ( group.length === 0 ) {
					continue;
				}

				// Step 3: Mark all months in this group as in-flight + bump versions atomically
				const versions: Record< string, number > = {};
				const inFlightUpdates: Record< string, boolean > = {};

				for ( const monthKey of group ) {
					const cacheKey = buildCacheKey(
						ctx.selectedTeamId ?? null,
						monthKey
					);
					const nextVersion =
						( ctx.requestVersion?.[ cacheKey ] || 0 ) + 1;
					versions[ cacheKey ] = nextVersion;
					inFlightUpdates[ cacheKey ] = true;
				}

				// Atomic update of all in-flight flags and versions
				ctx.inFlight = {
					...( ctx.inFlight || {} ),
					...inFlightUpdates,
				};
				ctx.requestVersion = {
					...( ctx.requestVersion || {} ),
					...versions,
				};

				// Step 4: Make batched API request for this consecutive group
				try {
					ctx.isBusy = true;

					// Build fetch parameters using shared utility
					const params = buildAvailabilityFetchParams(
						group,
						window || undefined
					);

					if ( ! params ) {
						// Invalid date range, skip this group
						continue;
					}

					// Build URL (same pattern as current fetchAvailabilityMonth)
					const urlParams = new URLSearchParams();
					urlParams.set( 'start_date', params.start_date );
					urlParams.set( 'end_date', params.end_date );
					if ( ctx.selectedTeamId ) {
						urlParams.set(
							'resource_id',
							String( ctx.selectedTeamId )
						);
					}

					const { isPermalinksPlain } = getConfig< Config >();
					const urlParamsSeparator = isPermalinksPlain ? '&' : '?';

					const url = `${ wooState.restUrl }wc-bookings/v2/products/${
						state.currentProductId
					}/availability${ urlParamsSeparator }${ urlParams.toString() }`;

					// eslint-disable-next-line no-console
					console.log( '[BookingForm] Fetching availability:', {
						group,
						start_date: params.start_date,
						end_date: params.end_date,
						teamId: ctx.selectedTeamId,
					} );

					// Make API request
					const res: Response = yield fetch( url, {
						method: 'GET',
						headers: {
							'Content-Type': 'application/json',
						},
					} );

					const json = yield res.json();

					// Check for API errors
					if ( json.data?.status < 200 || json.data?.status >= 300 ) {
						throw new Error(
							json.data?.message || 'Availability request failed'
						);
					}

					// Parse response using shared utility
					const parsed = parseAvailabilityResponse( json, group );

					// Step 5: Update cache for each month in this group (with version check)
					const cachedMonths: string[] = [];
					for ( const monthKey of group ) {
						const cacheKey = buildCacheKey(
							ctx.selectedTeamId ?? null,
							monthKey
						);
						const myVersion = versions[ cacheKey ];

						// Ignore if version changed (stale response)
						if (
							( ctx.requestVersion?.[ cacheKey ] || 0 ) !==
							myVersion
						) {
							continue;
						}

						// Update cache and meta
						ctx.availabilityCache = {
							...( ctx.availabilityCache || {} ),
							[ cacheKey ]: {
								monthKey,
								data: parsed[ monthKey ] || {},
							},
						};

						ctx.cacheMeta = {
							...( ctx.cacheMeta || {} ),
							[ cacheKey ]: {
								fetchedAt: Date.now(),
								expiresAt: Date.now() + 3 * 60 * 1000, // 3 minutes per month
							},
						};

						cachedMonths.push( monthKey );
					}

					ctx.lastError = null;
				} catch ( e ) {
					// Handle errors (check versions to avoid overwriting with stale errors)
					for ( const monthKey of group ) {
						const cacheKey = buildCacheKey(
							ctx.selectedTeamId ?? null,
							monthKey
						);
						const myVersion = versions[ cacheKey ];

						if (
							( ctx.requestVersion?.[ cacheKey ] || 0 ) ===
							myVersion
						) {
							// Cache empty entry with short TTL to prevent infinite retry loops
							ctx.availabilityCache = {
								...( ctx.availabilityCache || {} ),
								[ cacheKey ]: {
									monthKey,
									data: {},
								},
							};

							ctx.cacheMeta = {
								...( ctx.cacheMeta || {} ),
								[ cacheKey ]: {
									fetchedAt: Date.now(),
									expiresAt: Date.now() + 30 * 1000, // 30 seconds for error entries
								},
							};

							ctx.lastError =
								e?.message || 'Failed to load availability';
						}
					}
					// eslint-disable-next-line no-console
					console.error( e );
				} finally {
					// Step 6: Clear in-flight flags (only if versions match)
					for ( const monthKey of group ) {
						const cacheKey = buildCacheKey(
							ctx.selectedTeamId ?? null,
							monthKey
						);
						const myVersion = versions[ cacheKey ];

						if (
							( ctx.requestVersion?.[ cacheKey ] || 0 ) ===
							myVersion
						) {
							const next = { ...( ctx.inFlight || {} ) };
							delete next[ cacheKey ];
							ctx.inFlight = next;
						}
					}
					ctx.isBusy = false;
				}
			}
		},

		// -------------------------
		// Find next available
		// -------------------------
		findNextAvailable() {
			const ctx = getContext< Context >();

			// cursor: from selectedDate if set, else today (or bookingWindow.start)
			const window = state.bookingWindow;
			const cursor =
				ctx.selectedDate ||
				window?.start ||
				formatDateAsYmd( new Date() );

			// Set cursor in context for the getter to use
			ctx.cursorDateYmd = cursor;
			const next = state.nextAvailableDateFrom;

			if ( next ) {
				actions.selectDate( next );
			} else {
				// optionally: advance by month + fetch forward until bookingWindow.end
				// keep minimal: you can add "scan-forward fetch loop" later
			}
		},
	},
	callbacks: {
		preSelectToday() {
			const ctx = getContext< Context >();
			if ( ! ctx.selectedDate ) {
				const todayYmd = formatDateAsYmd( new Date() );
				actions.selectDate( todayYmd );
			}
		},
		fetchWhenModalOpen() {
			const isModalOpen =
				modalState.ui.openByProductId === state.currentProductId;
			if ( isModalOpen ) {
				actions.ensureAvailability();
			}
		},
	},
} );
