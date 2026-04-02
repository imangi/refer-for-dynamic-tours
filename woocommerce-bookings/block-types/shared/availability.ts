/**
 * Availability cache utility functions for booking blocks
 */

import {
	getMonthStartDate,
	getMonthEndDate,
	formatDateTimeAsYmdHis,
	parseDateString,
} from './utils';
import type { AvailabilityCacheEntry, CacheMetaEntry } from './types';

/**
 * Build a cache key for availability data
 *
 * @param {number|null} teamId   Team member ID (can be null)
 * @param {string}      monthKey Month key in YYYY-MM format
 * @return {string} Cache key string
 */
export function buildCacheKey(
	teamId: number | null,
	monthKey: string
): string {
	const teamPart = teamId ? `team=${ teamId }` : 'team=none';
	return `avail:${ teamPart }:month=${ monthKey }`;
}

/**
 * Group consecutive months together
 *
 * @param monthKeys Array of month keys in YYYY-MM format
 * @return Array of arrays, each containing consecutive months
 */
export function groupConsecutiveMonths( monthKeys: string[] ): string[][] {
	if ( monthKeys.length === 0 ) {
		return [];
	}

	// Sort months chronologically
	const sorted = [ ...monthKeys ].sort();

	const groups: string[][] = [];
	let currentGroup: string[] = [ sorted[ 0 ] ];

	for ( let i = 1; i < sorted.length; i++ ) {
		const prevMonth = getMonthStartDate( sorted[ i - 1 ] );
		const currMonth = getMonthStartDate( sorted[ i ] );

		if ( ! prevMonth || ! currMonth ) {
			// Invalid month key, start new group
			groups.push( currentGroup );
			currentGroup = [ sorted[ i ] ];
			continue;
		}

		// Check if consecutive (next month)
		const expectedNextMonth = new Date( prevMonth );
		expectedNextMonth.setMonth( expectedNextMonth.getMonth() + 1 );

		if (
			expectedNextMonth.getFullYear() === currMonth.getFullYear() &&
			expectedNextMonth.getMonth() === currMonth.getMonth()
		) {
			// Consecutive, add to current group
			currentGroup.push( sorted[ i ] );
		} else {
			// Not consecutive, start new group
			groups.push( currentGroup );
			currentGroup = [ sorted[ i ] ];
		}
	}

	// Don't forget the last group
	if ( currentGroup.length > 0 ) {
		groups.push( currentGroup );
	}

	return groups;
}

/**
 * Calculate date range for consecutive months
 *
 * @param {string[]} monthKeys    Array of consecutive month keys in YYYY-MM format
 * @param {Object}   window       Optional booking window with start and end date strings
 * @param {string}   window.start Start date string
 * @param {string}   window.end   End date string
 * @return {Object|null} Date range with start and end Date objects, or null if invalid
 */
export function calculateDateRangeForMonths(
	monthKeys: string[],
	window?: { start: string; end: string }
): { start: Date; end: Date } | null {
	if ( monthKeys.length === 0 ) {
		return null;
	}

	const today = new Date();
	today.setHours( 0, 0, 0, 0 );

	// Get start of first month
	let start = getMonthStartDate( monthKeys[ 0 ] );
	if ( ! start ) {
		return null;
	}

	// Get end of last month
	const lastMonthKey = monthKeys[ monthKeys.length - 1 ];
	let end = getMonthEndDate( lastMonthKey );
	if ( ! end ) {
		return null;
	}

	// Skip past months - adjust start to today if needed
	if ( start < today ) {
		start = new Date( today );
	}

	// Respect booking window boundaries
	if ( window?.start ) {
		const windowStart = parseDateString( window.start );
		windowStart.setHours( 0, 0, 0, 0 );
		if ( start < windowStart ) {
			start = windowStart;
		}
	}

	if ( window?.end ) {
		const windowEnd = parseDateString( window.end );
		windowEnd.setHours( 23, 59, 59, 999 );
		if ( end > windowEnd ) {
			end = windowEnd;
		}
	}

	// If start > end after adjustments, return null
	if ( start > end ) {
		return null;
	}

	return { start, end };
}

/**
 * Build availability fetch parameters from month keys
 *
 * @param {string[]} monthKeys    Array of consecutive month keys in YYYY-MM format
 * @param {Object}   window       Optional booking window with start and end date strings
 * @param {string}   window.start Start date string
 * @param {string}   window.end   End date string
 * @return {Object|null} Parameters object with start_date and end_date strings, or null if invalid
 */
export function buildAvailabilityFetchParams(
	monthKeys: string[],
	window?: { start: string; end: string }
): { start_date: string; end_date: string } | null {
	const dateRange = calculateDateRangeForMonths( monthKeys, window );
	if ( ! dateRange ) {
		return null;
	}

	return {
		start_date: formatDateTimeAsYmdHis( dateRange.start ),
		end_date: formatDateTimeAsYmdHis( dateRange.end ),
	};
}

/**
 * Parse availability API response
 *
 * @param {Object}   response           API response object
 * @param {string[]} requestedMonthKeys Array of month keys that were requested
 * @return {Object} Object keyed by monthKey with availability data
 */
export function parseAvailabilityResponse(
	response: any,
	requestedMonthKeys: string[]
): Record< string, Record< string, Record< string, number > > > {
	const availabilityResponse = response?.availability;
	if ( ! availabilityResponse ) {
		return {};
	}

	const result: Record<
		string,
		Record< string, Record< string, number > >
	> = {};

	for ( const monthKey of requestedMonthKeys ) {
		result[ monthKey ] = availabilityResponse[ monthKey ] || {};
	}

	return result;
}

/**
 * Determine which months need fetching
 *
 * @param {string[]}    monthKeys                    Array of month keys to check
 * @param {Object}      cacheState                   Cache state object with availabilityCache, cacheMeta, and inFlight properties
 * @param {Object}      cacheState.availabilityCache Availability cache entries
 * @param {Object}      cacheState.cacheMeta         Cache metadata entries
 * @param {Object}      cacheState.inFlight          In-flight request tracking
 * @param {Function}    buildCacheKeyFn              Function to build cache key
 * @param {number|null} teamId                       Team member ID
 * @return {string[]}   Array of month keys that need fetching
 */
export function getMonthsNeedingFetch(
	monthKeys: string[],
	cacheState: {
		availabilityCache?: Record< string, AvailabilityCacheEntry >;
		cacheMeta?: Record< string, CacheMetaEntry >;
		inFlight?: Record< string, boolean >;
	},
	buildCacheKeyFn: ( teamId: number | null, monthKey: string ) => string,
	teamId: number | null
): string[] {
	return monthKeys.filter( ( monthKey ) => {
		const cacheKey = buildCacheKeyFn( teamId, monthKey );
		const cached = cacheState.availabilityCache?.[ cacheKey ];
		const meta = cacheState.cacheMeta?.[ cacheKey ];
		const expired = ! meta || meta.expiresAt <= Date.now();
		const alreadyInFlight = cacheState.inFlight?.[ cacheKey ];

		// Return true if needs fetching: (not cached OR expired) AND not already in flight
		return ( ! cached || expired ) && ! alreadyInFlight;
	} );
}
