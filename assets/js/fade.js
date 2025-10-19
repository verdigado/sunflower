( () => {
	'use strict';

	const SELECTORS = [
		'header',
		'main',
		'section',
		'article',
		'aside',
		'nav',
		'figure',
		'img',
		'video',
		'picture',
		'.wp-block-buttons',
		'.right-bar',
		'.logo-background',
		'p',
		'a',
		'.wp-block-group',
		'blockquote',
		'ul',
		'ol',
		'li',
		'table',
		'form',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
	];

	// Doppelte rAF: Klasse 'preload' nach dem ersten Paint entfernen
	requestAnimationFrame( () =>
		requestAnimationFrame( () =>
			document.documentElement.classList.remove( 'preload' )
		)
	);

	// Respect reduced motion: sofort sichtbar, keine Beobachtung
	const REDUCED = window.matchMedia(
		'(prefers-reduced-motion: reduce)'
	).matches;
	if ( ! ( 'IntersectionObserver' in window ) || REDUCED ) {
		SELECTORS.forEach( ( sel ) =>
			document.querySelectorAll( sel ).forEach( ( el ) => {
				el.classList.remove( 'u-fade', 'is-visible' );
			} )
		);
		return;
	}

	const reveal = ( el ) => {
		el.classList.add( 'is-visible' );
		io.unobserve( el );
	};

	const io = new IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				if ( entry.isIntersecting ) {
					reveal( entry.target );
				}
			} );
		},
		{ rootMargin: '0px 0px -10% 0px' }
	);

	const prepareIfBelowFold = ( el ) => {
		const rect = el.getBoundingClientRect();
		// Nur "unter der Falz" vorbereiten, Above-the-fold bleibt sofort sichtbar
		if ( rect.top > window.innerHeight ) {
			// Nur einmal hinzufügen, um Reflow zu minimieren
			if ( ! el.classList.contains( 'u-fade' ) ) {
				el.classList.add( 'u-fade' );
			}
			// Sichtbar-Status sicher entfernen, falls vorhanden
			el.classList.remove( 'is-visible' );
			io.observe( el );
		} else {
			// Above-the-fold sofort sichtbar
			el.classList.remove( 'u-fade' );
			el.classList.add( 'is-visible' );
		}
	};

	// Initial: Kandidaten sammeln
	SELECTORS.forEach( ( sel ) =>
		document.querySelectorAll( sel ).forEach( prepareIfBelowFold )
	);

	// Tab-Wechsel: sauber aufräumen / reaktivieren
	document.addEventListener( 'visibilitychange', () => {
		if ( document.hidden ) {
			io.disconnect();
		} else {
			SELECTORS.forEach( ( sel ) =>
				document.querySelectorAll( sel ).forEach( ( el ) => {
					if (
						el.classList.contains( 'u-fade' ) &&
						! el.classList.contains( 'is-visible' )
					) {
						io.observe( el );
					}
				} )
			);
		}
	} );

	// Dynamisch hinzugefügte Nodes behandeln
	const mo = new MutationObserver( ( muts ) => {
		muts.forEach( ( m ) => {
			m.addedNodes.forEach( ( node ) => {
				if ( ! ( node instanceof Element ) ) {
					return;
				}

				if ( SELECTORS.some( ( sel ) => node.matches( sel ) ) ) {
					prepareIfBelowFold( node );
				}
				if ( typeof node.querySelectorAll === 'function' ) {
					SELECTORS.forEach( ( sel ) => {
						node.querySelectorAll( sel ).forEach(
							prepareIfBelowFold
						);
					} );
				}
			} );
		} );
	} );
	mo.observe( document.body, { childList: true, subtree: true } );
} )();
