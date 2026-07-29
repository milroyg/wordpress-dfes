/**
 * Constants for Location Weather Design Library
 */

// API Endpoints
export const API_ENDPOINTS = {
	PATTERNS: '/spl-weather/v2/get_premade_patterns',
	WISHLIST: '/spl-weather/v2/save_wishlist_item',
	SINGLE_PATTERN:
		'https://demo.locationweather.io/wp-json/location-weather/v1/single-pattern',
	UPGRADE_URL: 'https://locationweather.io/pricing/',
};

// CSS Classes
export const CSS_CLASSES = {
	MODAL: 'splw-patterns-builder-modal',
	POPUP_OPEN: 'splw-patterns-popup-open',
	TOOLBAR_LIBRARY: 'splw-patterns-toolbar-design-library',
	PATTERN_GRID: 'splw-pattern-grid',
	PATTERN_COL2: 'splw-pattern-col2',
	PATTERN_COL3: 'splw-pattern-col3',
};

// Default Values
export const DEFAULTS = {
	COLUMN: '3',
	SEARCH_QUERY: '',
	TREND: 'default',
	FREE_PRO: 'all',
	DEBOUNCE_DELAY: 200,
	SKELETON_COUNT: 25,
};

// Keyboard Keys
export const KEYBOARD_KEYS = {
	ESCAPE: 27,
	ENTER: 'Enter',
	SPACE: ' ',
};

// Filter Options
export const FILTER_OPTIONS = {
	TREND: [
		{ value: 'default', label: 'Sort By' },
		{ value: 'popular', label: 'Popular' },
		{ value: 'latest', label: 'Latest' },
	],
};
