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

	// Set active button based on current values.
	const setActiveButton = ( { formstyle, colorscheme } ) => {
		// Zuerst alle Wrapper zurücksetzen
		document
			.querySelectorAll( '.design-switcher-trigger' )
			.forEach( ( el ) => el.classList.remove( 'is-active' ) );

		// Dann die beiden passenden Wrapper aktivieren:
		const fsSelector = `.design-switcher-trigger.ds-fs-${ formstyle }`;
		const csSelector = `.design-switcher-trigger.ds-cs-${ colorscheme }`;

		const fsActive = document.querySelector( fsSelector );
		const csActive = document.querySelector( csSelector );

		if ( fsActive ) {
			fsActive.classList.add( 'is-active' );
		}
		if ( csActive ) {
			csActive.classList.add( 'is-active' );
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
		const wrapper = e.target.closest( '.design-switcher-trigger' );
		if ( ! wrapper ) {
			return;
		}

		const isFormStyle = [ ...wrapper.classList ].some( ( c ) =>
			c.startsWith( 'ds-fs-' )
		);
		const isColorScheme = [ ...wrapper.classList ].some( ( c ) =>
			c.startsWith( 'ds-cs-' )
		);

		if ( ! isFormStyle && ! isColorScheme ) {
			return;
		}

		const stored = getStored() || {
			formstyle: 'rounded',
			colorscheme: 'light',
			footer: 'sand',
		};

		const payload = { ...stored };

		if ( isFormStyle ) {
			const fsClass = [ ...wrapper.classList ].find( ( c ) =>
				c.startsWith( 'ds-fs-' )
			);
			payload.formstyle = fsClass.replace( 'ds-fs-', '' );
		}
		if ( isColorScheme ) {
			const csClass = [ ...wrapper.classList ].find( ( c ) =>
				c.startsWith( 'ds-cs-' )
			);
			payload.colorscheme = csClass.replace( 'ds-cs-', '' );
		}

		applyClasses( payload ); // <body>‑Klassen setzen
		setStored( payload ); // im localStorage sichern
		syncUiFromValues( payload ); // Panel‑Selects aktualisieren
		setActiveButton( payload ); // aktiven Button visuell markieren
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

		let currentSettings = getStored();
		if ( ! currentSettings ) {
			// First time visitors: read defaults and save to local storage.
			currentSettings = readFromUI();
			setStored( currentSettings );
		}

		applyClasses( currentSettings );
		syncUiFromValues( currentSettings );
		setActiveButton( currentSettings );

		// Listen to all select changes in the panel:
		wrapper.querySelectorAll( 'select' ).forEach( ( sel ) => {
			sel.addEventListener( 'change', () => {
				const values = readFromUI();
				applyClasses( values );
				setStored( values );
				setActiveButton( values );
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
