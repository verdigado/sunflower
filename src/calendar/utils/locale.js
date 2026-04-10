const DEFAULT_FIRST_DAY = 1;

function capitalizeLocaleLabel( label, locale ) {
	if ( ! label ) {
		return label;
	}

	return label.charAt( 0 ).toLocaleUpperCase( locale ) + label.slice( 1 );
}

/**
 * Normalize locale strings for FullCalendar file names and Intl APIs.
 *
 * @param {string} locale Raw locale code.
 * @return {string|undefined} Normalized locale code.
 */
export function normalizeLocaleCode( locale ) {
	if ( ! locale || 'string' !== typeof locale ) {
		return undefined;
	}

	return locale.toLowerCase().replace( /_/g, '-' );
}

/**
 * Prefer the browser locale and fall back to the document language.
 *
 * @return {string|undefined} Preferred locale code.
 */
export function getPreferredLocale() {
	if ( 'undefined' !== typeof window ) {
		const browserLocale =
			window.navigator.languages?.find( Boolean ) ||
			window.navigator.language;

		if ( browserLocale ) {
			return normalizeLocaleCode( browserLocale );
		}
	}

	if ( 'undefined' !== typeof document ) {
		return normalizeLocaleCode( document.documentElement.lang );
	}

	return undefined;
}

/**
 * Return locale candidates from most specific to least specific.
 *
 * @param {string} locale Locale code.
 * @return {string[]} Locale candidates.
 */
export function getLocaleCandidates( locale ) {
	const normalizedLocale = normalizeLocaleCode( locale );

	if ( ! normalizedLocale ) {
		return [];
	}

	const [ language ] = normalizedLocale.split( '-' );

	return language && language !== normalizedLocale
		? [ normalizedLocale, language ]
		: [ normalizedLocale ];
}

/**
 * Resolve the preferred first day of the week from the browser locale.
 *
 * @return {number} First day of week in FullCalendar format (0-6).
 */
export function getLocaleFirstDay() {
	const locale = getPreferredLocale();

	if (
		! locale ||
		'undefined' === typeof Intl ||
		'undefined' === typeof Intl.Locale
	) {
		return DEFAULT_FIRST_DAY;
	}

	try {
		const firstDay = new Intl.Locale( locale ).weekInfo?.firstDay;

		return 'number' === typeof firstDay ? firstDay % 7 : DEFAULT_FIRST_DAY;
	} catch ( error ) {
		return DEFAULT_FIRST_DAY;
	}
}

/**
 * Build localized weekday labels for the preview calendar.
 *
 * @return {string[]} Weekday labels in the preferred locale.
 */
export function getLocalizedWeekdayLabels() {
	const locale = getPreferredLocale();
	const formatter = new Intl.DateTimeFormat( locale, {
		weekday: 'short',
	} );
	const firstDay = getLocaleFirstDay();
	const baseSunday = new Date( Date.UTC( 2024, 0, 7 ) );

	return Array.from( { length: 7 }, ( _, index ) => {
		const date = new Date( baseSunday );
		date.setUTCDate(
			baseSunday.getUTCDate() + ( ( firstDay + index ) % 7 )
		);

		return formatter.format( date ).replace( /\.$/u, '' );
	} );
}

/**
 * Build a localized month name for the preview calendar.
 *
 * @param {Date} date Source date.
 * @return {string} Localized month name.
 */
export function getLocalizedMonthName( date ) {
	return new Intl.DateTimeFormat( getPreferredLocale(), {
		month: 'long',
	} ).format( date );
}

/**
 * Build a localized "today" label for the preview calendar.
 *
 * @return {string} Localized today label.
 */
export function getLocalizedTodayLabel() {
	if (
		'undefined' === typeof Intl ||
		'undefined' === typeof Intl.RelativeTimeFormat
	) {
		return 'Today';
	}

	const locale = getPreferredLocale();

	return capitalizeLocaleLabel(
		new Intl.RelativeTimeFormat( locale, {
			numeric: 'auto',
		} ).format( 0, 'day' ),
		locale
	);
}
