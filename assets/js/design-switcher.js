/* ----------------------------------------------------------------------
 * Sunflower – reiner Front‑End‑Design‑Umschalter
 * ---------------------------------------------------------------------- */
( () => {
	const STORAGE_KEY = 'sunflower_design'; // Schlüssel im localStorage

	// ------------------------------------------------------------------
	// 1. Hilfsfunktionen
	// ------------------------------------------------------------------
	const getStored = () => {
		const raw = localStorage.getItem( STORAGE_KEY );
		if ( ! raw ) {
			return null;
		}
		try {
			return JSON.parse( raw );
		} catch ( e ) {
			return null;
		}
	};

	const setStored = ( obj ) => {
		localStorage.setItem( STORAGE_KEY, JSON.stringify( obj ) );
	};

	const applyClasses = ( values ) => {
		const body = document.body;
		// 1️⃣ Alle bisherigen Design‑Klassen entfernen
		body.classList.remove(
			'formstyle-rounded',
			'formstyle-sharp',
			'colorscheme-light',
			'colorscheme-green',
			'footer-sand',
			'footer-green',
			'post-image-flexible',
			'post-image-modern'
		);

		// 2️⃣ Neue Klassen hinzufügen
		body.classList.add( `formstyle-${ values.formstyle }` );
		body.classList.add( `colorscheme-${ values.colorscheme }` );
		body.classList.add( `footer-${ values.footer }` );
		body.classList.add( `post-image-${ values.postimage }` );
	};

	const readFromUI = () => ( {
		formstyle: document.getElementById( 'formstyle-select' ).value,
		colorscheme: document.getElementById( 'colorscheme-select' ).value,
		footer: document.getElementById( 'footer-select' ).value,
		postimage: document.getElementById( 'postimage-select' ).value,
	} );

	// ------------------------------------------------------------------
	// 2. Beim Laden: gespeicherte Werte übernehmen (falls vorhanden)
	// ------------------------------------------------------------------
	const init = () => {
		const wrapper = document.getElementById( 'design-switcher' );
		if ( ! wrapper ) {
			return;
		} // Umschalter nicht aktiviert → nichts tun

		// 2a) gespeicherte Variante holen und anwenden
		const stored = getStored();
		if ( stored ) {
			applyClasses( stored );
			// UI‑Felder auf gespeicherte Werte setzen
			document.getElementById( 'formstyle-select' ).value =
				stored.formstyle;
			document.getElementById( 'colorscheme-select' ).value =
				stored.colorscheme;
			document.getElementById( 'footer-select' ).value = stored.footer;
			document.getElementById( 'postimage-select' ).value =
				stored.postimage;
		} else {
			// no change needed, but setStored with default values for future reference
		}

		// 2b) Event‑Listener für jede Auswahl
		wrapper.querySelectorAll( 'select' ).forEach( ( sel ) => {
			sel.addEventListener( 'change', () => {
				const values = readFromUI(); // neue Werte aus UI
				applyClasses( values ); // sofort im DOM anwenden
				setStored( values ); // persistieren
			} );
		} );
	};

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();

/* --------------------------------------------------------------
   Design‑Switch‑Panel – UI‑Logik
   -------------------------------------------------------------- */
( () => {
	const toggleBtn = document.getElementById( 'design-switcher-toggle' );
	const panel = document.getElementById( 'design-switcher-panel' );

	if ( ! toggleBtn || ! panel ) {
		return;
	}

	// -----------------------------------------------------------------
	// Hilfsfunktion: (de)aktiviert das Panel + Overlay
	// -----------------------------------------------------------------
	const backdrop = document.getElementById( 'design-switcher-backdrop' );
	const openPanel = () => {
		panel.removeAttribute( 'hidden' );
		panel.setAttribute( 'aria-hidden', 'false' );
		backdrop.classList.add( 'visible' );
		backdrop.removeAttribute( 'hidden' );

		// Fokus‑Trap: erster Fokus‑able Element im Panel
		const firstFocusable = panel.querySelector(
			'select, button, [href], input, textarea, [tabindex]:not([tabindex="-1"])'
		);
		if ( firstFocusable ) {
			firstFocusable.focus();
		}
	};

	const closePanel = () => {
		panel.setAttribute( 'hidden', '' );
		panel.setAttribute( 'aria-hidden', 'true' );
		backdrop.classList.remove( 'visible' );
		backdrop.setAttribute( 'hidden', '' );

		// zurück zum Icon‑Button
		toggleBtn.focus();
	};

	// -----------------------------------------------------------------
	// Event‑Handler
	// -----------------------------------------------------------------
	const closeBtn = document.getElementById( 'design-switcher-close' );
	toggleBtn.addEventListener( 'click', openPanel );
	if ( closeBtn ) {
		closeBtn.addEventListener( 'click', closePanel );
	}
	backdrop.addEventListener( 'click', ( e ) => {
		if ( e.target === backdrop ) {
			closePanel();
		}
	} );

	// ESC‑Taste schließt das Panel
	document.addEventListener( 'keydown', ( e ) => {
		if ( e.key === 'Escape' && ! panel.hasAttribute( 'hidden' ) ) {
			closePanel();
		}
	} );

	// -----------------------------------------------------------------
	// Optional: Beim Laden prüfen, ob bereits ein Design‑Wert im
	//          localStorage ist – dann das Panel **nicht** öffnen,
	//          aber die UI‑Selects werden bereits auf die gespeicherten
	//          Werte gesetzt (das erledigt das alte Script bereits).
	// -----------------------------------------------------------------
	// (Kein zusätzlicher Code nötig – das alte Script läuft weiter.)
} )();
