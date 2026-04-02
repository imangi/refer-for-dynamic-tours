/**
 * Shared TypeScript types for booking blocks
 */

/**
 * Duration data structure (value + unit)
 */
export type DurationData = {
	value: string;
	unit: string;
};

/**
 * Availability cache entry structure
 */
export type AvailabilityCacheEntry = {
	monthKey: string;
	data: Record< string, Record< string, number > >;
};

/**
 * Cache metadata entry
 */
export type CacheMetaEntry = {
	fetchedAt: number;
	expiresAt: number;
};

/**
 * FormContext type for the booking-form namespace.
 * Represents the context properties accessed from the booking-form block.
 * Used across multiple block types that interact with the booking-form store.
 */
export type FormContext = {
	// Calendar view state
	viewMonth: string; // YYYY-MM format

	// Selections
	selectedTeamId: number | null;
	selectedDate: string | null; // YYYY-MM-DD format
	selectedSlotKey: string | null;
	requiresTimeSelection: boolean;

	// Slots pagination
	slotsCurrentPage: number;

	// Availability cache
	availabilityCache?: Record< string, AvailabilityCacheEntry >;

	// Request lifecycle
	isBusy: boolean;
};
