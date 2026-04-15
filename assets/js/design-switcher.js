/* --------------------------------------------------------------
   Sunflower – Einheitlicher Front‑End‑Design‑Umschalter
   -------------------------------------------------------------- */
( () => {
	const STORAGE_KEY = 'sunflower_design';

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

	const setStored = ( obj ) =>
		localStorage.setItem( STORAGE_KEY, JSON.stringify( obj ) );

	const applyClasses = ( { formstyle, colorscheme, footer } ) => {
		const body = document.body;
		body.classList.remove(
			'formstyle-rounded',
			'formstyle-sharp',
			'colorscheme-light',
			'colorscheme-green',
			'footer-sand',
			'footer-green'
		);
		body.classList.add( `formstyle-${ formstyle }` );
		body.classList.add( `colorscheme-${ colorscheme }` );
		body.classList.add( `footer-${ footer }` );
	};

	const syncUiFromValues = ( { formstyle, colorscheme, footer } ) => {
		const setIfExists = ( id, value ) => {
			const el = document.getElementById( id );
			if ( el ) {
				el.value = value;
			}
		};
		setIfExists( 'formstyle-select', formstyle );
		setIfExists( 'colorscheme-select', colorscheme );
		setIfExists( 'footer-select', footer );
	};

	// Set active button in the panel based on current values.
	const setActiveButton = ( { formstyle, colorscheme } ) => {
		document
			.querySelectorAll( '.design-switcher-trigger' )
			.forEach( ( b ) => b.classList.remove( 'is-active' ) );

		const selector = `.design-switcher-trigger[data-formstyle="${ formstyle }"][data-colorscheme="${ colorscheme }"]`;
		const activeBtn = document.querySelector( selector );
		if ( activeBtn ) {
			activeBtn.classList.add( 'is-active' );
		}
	};

	const toggleBtn = document.getElementById( 'design-switcher-toggle' );
	const panel = document.getElementById( 'design-switcher-panel' );
	const closeBtn = document.getElementById( 'design-switcher-close' );
	const backdrop = document.getElementById( 'design-switcher-backdrop' );

	if ( toggleBtn && panel ) {
		const openPanel = () => {
			panel.removeAttribute( 'hidden' );
			panel.setAttribute( 'aria-hidden', 'false' );
			backdrop.classList.add( 'visible' );
			backdrop.removeAttribute( 'hidden' );

			// Fokus‑Trap: erstes fokussierbares Element im Panel
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
			toggleBtn.focus(); // zurück zum Icon‑Button
		};

		toggleBtn.addEventListener( 'click', openPanel );
		if ( closeBtn ) {
			closeBtn.addEventListener( 'click', closePanel );
		}
		backdrop.addEventListener( 'click', ( e ) => {
			if ( e.target === backdrop ) {
				closePanel();
			}
		} );
		document.addEventListener( 'keydown', ( e ) => {
			if ( e.key === 'Escape' && ! panel.hasAttribute( 'hidden' ) ) {
				closePanel();
			}
		} );
	}

	const handleGutenbergClick = ( e ) => {
		const btn = e.target.closest( '.design-switcher-trigger' );
		if ( ! btn ) {
			return;
		} // Klick nicht auf einem unserer Buttons

		const formstyle = btn.dataset.formstyle || 'rounded';
		const colorscheme = btn.dataset.colorscheme || 'light';
		// Footer wird in den Buttons **nicht** gesteuert → übernehmen den gespeicherten oder den Default‑Wert
		const stored = getStored() || {
			footer: 'sand', // Default‑Footer, falls nichts gespeichert ist
		};
		const payload = {
			formstyle,
			colorscheme,
			footer: stored.footer,
		};

		applyClasses( payload );
		setStored( payload );
		syncUiFromValues( payload );
		setActiveButton( payload );
	};

	document.body.addEventListener( 'click', handleGutenbergClick );

	const initPanelSelects = () => {
		const wrapper = document.getElementById( 'design-switcher' );
		if ( ! wrapper ) {
			return;
		}

		const readFromUI = () => ( {
			formstyle:
				document.getElementById( 'formstyle-select' )?.value ||
				'rounded',
			colorscheme:
				document.getElementById( 'colorscheme-select' )?.value ||
				'light',
			footer: document.getElementById( 'footer-select' )?.value || 'sand',
		} );

		// Beim Laden: ggf. gespeicherten Zustand übernehmen
		const stored = getStored();
		if ( stored ) {
			applyClasses( stored );
			syncUiFromValues( stored );
			setActiveButton( stored );
		} else {
			// Noch kein Eintrag → Standard‑Werte im Storage sichern
			const defaults = readFromUI();
			setStored( defaults );
		}

		// Auf jede Select‑Box hören
		wrapper.querySelectorAll( 'select' ).forEach( ( sel ) => {
			sel.addEventListener( 'change', () => {
				const values = readFromUI();
				applyClasses( values );
				setStored( values );
				setActiveButton( values ); // ggf. den zugehörigen Button markieren
			} );
		} );
	};

	const init = () => {
		initPanelSelects();
	};

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
