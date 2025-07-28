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

	requestAnimationFrame( () =>
		requestAnimationFrame( () =>
			document.documentElement.classList.remove( 'preload' )
		)
	);

	if ( ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	const io = new IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				if ( entry.isIntersecting ) {
					entry.target.style.opacity = '1';
					io.unobserve( entry.target );
				}
			} );
		},
		{ rootMargin: '0px 0px -10% 0px' }
	);

	const observeIfBelowFold = ( el ) => {
		if ( el.getBoundingClientRect().top > window.innerHeight ) {
			el.style.opacity = '0';
			io.observe( el );
		}
	};

	SELECTORS.forEach( ( sel ) =>
		document.querySelectorAll( sel ).forEach( observeIfBelowFold )
	);

	document.addEventListener( 'visibilitychange', () => {
		if ( document.hidden ) {
			io.disconnect(); // aufräumen
		} else {
			/* verbliebene unsichtbare Elemente erneut beobachten */
			SELECTORS.forEach( ( sel ) =>
				document.querySelectorAll( sel ).forEach( ( el ) => {
					if ( el.style.opacity === '0' ) {
						io.observe( el );
					}
				} )
			);
		}
	} );

	const mo = new MutationObserver( ( muts ) => {
		muts.forEach( ( m ) => {
			m.addedNodes.forEach( ( node ) => {
				if ( ! ( node instanceof Element ) ) {
					return;
				}

				if ( SELECTORS.some( ( sel ) => node.matches( sel ) ) ) {
					observeIfBelowFold( node );
				}

				if ( typeof node.querySelectorAll === 'function' ) {
					SELECTORS.forEach( ( sel ) => {
						node.querySelectorAll( sel ).forEach(
							observeIfBelowFold
						);
					} );
				}
			} );
		} );
	} );
	mo.observe( document.body, { childList: true, subtree: true } );
} )();
