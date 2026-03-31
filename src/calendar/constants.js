/**
 * Calendar Block Constants
 *
 * Shared configuration values used across the calendar block components.
 */

/**
 * Fallback colours used by view.js when CSS custom properties are unavailable.
 * PRIMARY matches --calendar-color-primary (klee) — the event fill colour.
 * TEXT is always white for contrast.
 *
 * @type {Object}
 */
export const CALENDAR_COLORS = {
	PRIMARY: '#008939', // klee — matches event cards in the rest of the theme
	TEXT: '#ffffff',
};

/**
 * Empty array constant to maintain referential equality
 *
 * @type {Array}
 */
export const EMPTY_ARRAY = [];

/**
 * FullCalendar configuration defaults
 *
 * @type {Object}
 */
export const FULLCALENDAR_DEFAULTS = {
	INITIAL_VIEW: 'dayGridMonth',
	HEIGHT: 'auto',
	DISPLAY_EVENT_TIME: false,
};

/**
 * Data attribute names for calendar configuration
 *
 * @type {Object}
 */
export const DATA_ATTRIBUTES = {
	AJAX_URL: 'data-ajax-url',
	LOCALES_URL: 'data-locales-url',
	NONCE: 'data-nonce',
	TAGS: 'data-tags',
	TAG_COLORS: 'data-tag-colors',
};

/**
 * AJAX configuration
 *
 * @type {Object}
 */
export const AJAX_CONFIG = {
	ACTION: 'sunflower_get_calendar_events',
};

/**
 * WordPress entity configuration
 *
 * @type {Object}
 */
export const ENTITY_CONFIG = {
	TAXONOMY: 'taxonomy',
	EVENT_TAG: 'sunflower_event_tag',
	CONTEXT: 'view',
	PER_PAGE_ALL: -1,
};

/**
 * CSS class names
 *
 * @type {Object}
 */
export const CSS_CLASSES = {
	CALENDAR: 'sunflower-calendar',
	LOADING: 'calendar-loading',
	ERROR: 'calendar-error',
};

/**
 * Calendar preview configuration
 *
 * @type {Object}
 */
export const PREVIEW_CONFIG = {
	TOTAL_CELLS: 42,
	MOCK_EVENT_DAYS: [ 4, 12, 19 ],
};

/**
 * Error messages (German)
 *
 * @type {Object}
 */
export const ERROR_MESSAGES = {
	MISSING_CONFIG: 'Kalender-Konfiguration fehlt. Bitte Seite neu laden.',
	LIBRARY_NOT_LOADED: 'FullCalendar-Bibliothek wurde nicht geladen.',
	FETCH_FAILED:
		'Termine konnten nicht geladen werden. Bitte versuchen Sie es später erneut.',
	NETWORK_ERROR: 'Netzwerkfehler beim Laden der Termine.',
	INVALID_RESPONSE: 'Ungültige Serverantwort.',
};

/**
 * Color palette for tag colors
 * Predefined colors users can choose from for event tags
 *
 * @type {Array<Object>}
 */
export const TAG_COLOR_PALETTE = [
	{ name: 'Klee (Standard)', color: '#008939' },
	{ name: 'Tanne', color: '#005538' },
	{ name: 'Grashalm', color: '#8abd24' },
	{ name: 'Blau', color: '#3498db' },
	{ name: 'Dunkelblau', color: '#2c3e50' },
	{ name: 'Rot', color: '#e74c3c' },
	{ name: 'Orange', color: '#e67e22' },
	{ name: 'Gelb', color: '#f39c12' },
	{ name: 'Lila', color: '#9b59b6' },
	{ name: 'Pink', color: '#e91e63' },
	{ name: 'Türkis', color: '#1abc9c' },
	{ name: 'Grau', color: '#95a5a6' },
];
