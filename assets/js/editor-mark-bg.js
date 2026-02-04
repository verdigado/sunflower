/**
 * Editor Script: Setzt --bg CSS Variable für angeschrägte Headlines
 *
 * Im Frontend macht das frontend.js, aber das läuft nicht im Block Editor.
 * Dieses Script überwacht den Editor und setzt die Variable dynamisch.
 *
 * Wird über enqueue_block_assets geladen, läuft daher direkt im Editor-iFrame.
 */

( function () {
	'use strict';

	/**
	 * Setzt die --bg Variable für ein einzelnes mark-Element
	 * @param {HTMLElement} mark
	 */
	function updateSingleMark( mark ) {
		// Inline-style direkt lesen (nicht computed, da CSS !important hat)
		const inlineBg = mark.style.backgroundColor;
		if (
			inlineBg &&
			inlineBg !== 'transparent' &&
			inlineBg !== 'rgba(0, 0, 0, 0)'
		) {
			mark.style.setProperty( '--bg', inlineBg );
			return;
		}

		// Fallback: Aus style-Attribut parsen (für serverseitig gesetzte Styles)
		const styleAttr = mark.getAttribute( 'style' ) || '';
		const bgMatch = styleAttr.match( /background-color:\s*([^;]+)/i );
		if ( bgMatch && bgMatch[ 1 ] ) {
			const bgValue = bgMatch[ 1 ].trim();
			if ( bgValue !== 'transparent' && bgValue !== 'rgba(0, 0, 0, 0)' ) {
				mark.style.setProperty( '--bg', bgValue );
			}
		}
	}

	/**
	 * Setzt die --bg Variable für alle mark-Elemente in Headlines
	 */
	function updateAllMarks() {
		const marks = document.querySelectorAll( 'h1 mark, h2 mark, h3 mark' );
		marks.forEach( updateSingleMark );
	}

	/**
	 * Initialisiert den MutationObserver
	 */
	function setupObserver() {
		if ( document.body.__markBgObserverSetup ) {
			return;
		}

		document.body.__markBgObserverSetup = true;

		// Initial ausführen
		updateAllMarks();

		// MutationObserver für dynamische Änderungen
		const observer = new MutationObserver( function () {
			// Debounce: Nur einmal pro Frame ausführen
			if ( ! document.body.__markBgPending ) {
				document.body.__markBgPending = true;
				requestAnimationFrame( function () {
					updateAllMarks();
					document.body.__markBgPending = false;
				} );
			}
		} );

		observer.observe( document.body, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: [ 'style', 'class' ],
		} );
	}

	/**
	 * Hauptinitialisierung
	 */
	function init() {
		if ( document.body ) {
			setupObserver();
		} else {
			// Falls body noch nicht existiert, warten
			document.addEventListener( 'DOMContentLoaded', setupObserver );
		}
	}

	// Starten
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
