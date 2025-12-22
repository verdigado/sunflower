/* eslint-disable no-undef */
// get the sticky element
const stickyElement = document.querySelector( '.top-bar' );

let ticking = false;
const scrollThreshold = 40;

function updateStuckState() {
	if ( stickyElement ) {
		const shouldBeStuck = window.scrollY > scrollThreshold;
		const isCurrentlyStuck = stickyElement.classList.contains( 'stuck' );

		if ( shouldBeStuck && ! isCurrentlyStuck ) {
			stickyElement.classList.add( 'stuck' );
		} else if ( ! shouldBeStuck && isCurrentlyStuck ) {
			stickyElement.classList.remove( 'stuck' );
		}
	}
	ticking = false;
}

function onScroll() {
	if ( ! ticking ) {
		requestAnimationFrame( updateStuckState );
		ticking = true;
	}
}

if ( stickyElement ) {
	window.addEventListener( 'scroll', onScroll, { passive: true } );
	// Initialer Check
	updateStuckState();
}

jQuery( function () {
	jQuery( '.show-leaflet' ).on( 'click', function () {
		const lat = jQuery( '.show-leaflet' ).data( 'lat' );
		const lon = jQuery( '.show-leaflet' ).data( 'lon' );
		const zoom = jQuery( '.show-leaflet' ).data( 'zoom' );

		showLeaflet( lat, lon, zoom );
	} );

	jQuery( '#privacy_policy_url' ).attr(
		'href',
		sunflower.privacy_policy_url
	);

	jQuery( '.show-search' ).on( 'click', function () {
		jQuery( '.topmenu .search input' ).toggleClass( 'active' );
		jQuery( '.topmenu .search input' ).trigger( 'focus' );
	} );

	jQuery( '.show-contrast' ).on( 'click', function () {
		jQuery( 'html' ).toggleClass( 'theme--contrast' );
		jQuery( 'html' ).toggleClass( 'theme--default' );
		localStorage.setItem(
			'theme--contrast',
			jQuery( 'html' ).hasClass( 'theme--contrast' )
		);
	} );

	if ( localStorage.getItem( 'theme--contrast' ) === 'true' ) {
		jQuery( 'html' ).addClass( 'theme--contrast' );
		jQuery( 'html' ).removeClass( 'theme--default' );
	}

	addRssReadMore();

	// mailto unscrambler
	jQuery( '[data-unscramble]' ).on( 'click', function () {
		const text = jQuery( this )
			.data( 'unscramble' )
			.split( '' )
			.reverse()
			.join( '' );
		window.location.href = 'MAILTO:' + text;

		return false;
	} );

	jQuery( '.wp-block-gallery figure' ).each( function () {
		const caption = jQuery( 'figcaption', this ).text();
		jQuery( 'a', this )
			.first()
			.attr( 'data-lightbox', 'sunflower-gallery' );
		jQuery( 'a', this ).first().attr( 'data-title', caption );
	} );

	lightbox.option( {
		albumLabel: sunflower.texts.lightbox2.imageOneOf,
	} );
} );

function getIcon() {
	return L.icon( {
		iconUrl: sunflower.maps_marker,
		iconSize: [ 25, 41 ], // size of the icon
		shadowSize: [ 0, 0 ], // size of the shadow
		iconAnchor: [ 12, 41 ], // point of the icon which will correspond to marker's location
		shadowAnchor: [ 0, 0 ], // the same for the shadow
		popupAnchor: [ 0, -41 ], // point from which the popup should open relative to the iconAnchor
	} );
}

function showLeaflet( lat, lon, zoom ) {
	const leaflet = L.map( 'leaflet' ).setView( [ lat, lon ], zoom );
	L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		attribution:
			'&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>',
	} ).addTo( leaflet );

	L.marker( [ lat, lon ], { icon: getIcon() } ).addTo( leaflet );
}

jQuery( '.show-leaflet-all' ).on( 'click', function showLeafletAll() {
	const leaflet = L.map( 'leaflet' ).setView(
		[ map.center.lat, map.center.lon ],
		map.center.zoom
	);
	L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		attribution:
			'&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>',
	} ).addTo( leaflet );

	map.marker.forEach( ( marker ) =>
		L.marker( [ marker.lat, marker.lon ], { icon: getIcon() } )
			.addTo( leaflet )
			.bindPopup( marker.content )
	);
} );

jQuery( '#sunflower-contact-form' ).on( 'submit', function ( e ) {
	e.preventDefault();

	jQuery
		.ajax( {
			url: sunflower.ajaxurl,
			method: 'POST',
			data: {
				action: 'sunflower_contact_form',
				_wpnonce: jQuery( '#_wpnonce' ).val(),
				message: jQuery( '#message' ).val(),
				name: jQuery( '#name' ).val(),
				mail: jQuery( '#mail' ).val(),
				mailTo: jQuery( '#mail-to' ).val(),
				phone: jQuery( '#phone' ).val(),
				title: jQuery( '#contact-form-title' ).html(),
				captcha: jQuery( '#captcha' ).val(),
				sendCopy: jQuery( '#send-copy' ).val(),
			},
		} )
		.done( function ( response ) {
			response = JSON.parse( response );

			if ( response.code === 500 ) {
				const errorbox = jQuery(
					'#sunflower-contact-form #form-error'
				);
				errorbox.html( response.text );
				errorbox.show();

				const button = jQuery( '#submit' );
				button.hide();

				// show some progress
				const interval = setInterval( function () {
					errorbox.append( ' . ' );
				}, 500 );

				// show submit button after 5 seconds again and hide error message
				setTimeout( function () {
					clearInterval( interval );
					button.prop( 'disabled', false );
					button.css( 'opacity', 1 );
					button.show();
					errorbox.hide();
				}, 5000 );
				return;
			}

			jQuery( '#sunflower-contact-form' ).html( response.text );
		} );

	return false;
} );

/* add read more link to rss block items */
function addRssReadMore() {
	// loop over every title with link and add it as "read more" link
	jQuery( '.wp-block-rss .wp-block-rss__item' ).each( function () {
		const titlelink = jQuery( '.wp-block-rss__item-title a', this );
		const mydiv = jQuery( '<div class="d-flex flex-row-reverse">' );
		const moreLink = jQuery( '<a />', {
			href: titlelink.attr( 'href' ),
			class: 'continue-reading',
			rel: 'bookmark',
			text: sunflower.texts.readmore,
		} );
		jQuery( this ).append( mydiv.append( moreLink ) );

		// convert excerpt into link to have same behaviour as latest-posts
		const excerpt = jQuery( '.wp-block-rss__item-excerpt', this ).text();
		const newExcerpt = jQuery( '<div>', {
			class: 'wp-block-rss__item-excerpt entry-content',
		} );
		jQuery( '.wp-block-rss__item-excerpt', this ).replaceWith(
			newExcerpt.append( titlelink.clone().text( excerpt ) )
		);
	} );
}

// make parent item of dropdowm menu clickable which is not intended by Bootstrap
jQuery( '.dropdown .dropdown-toggle' ).on( 'click', function () {
	if ( jQuery( '.dropdown:hover' ).length !== 0 ) {
		window.location = jQuery( this ).attr( 'href' );
	}

	return false;
} );

jQuery( function () {
	jQuery( '.navbar-toggler' ).click( function () {
		if ( jQuery( '.navbar-toggler' ).hasClass( 'collapsed' ) ) {
			window.setTimeout( () => {
				jQuery( 'body' ).removeClass( 'navbar-open' );
			}, 100 );
		} else {
			jQuery( 'body' ).addClass( 'navbar-open' );
		}
	} );
} );

document.addEventListener( 'DOMContentLoaded', function () {
	// Add aria-labels to make lightbox2 WCAG2AA compliant.
	const observerLightbox = new MutationObserver( () => {
		const closeBtn = document.querySelector( ".lb-close[role='button']" );
		const cancelBtn = document.querySelector( ".lb-cancel[role='button']" );
		const nextBtn = document.querySelector( ".lb-next[role='button']" );
		const prevBtn = document.querySelector( ".lb-prev[role='button']" );

		if ( closeBtn ) {
			closeBtn.setAttribute(
				'aria-label',
				sunflower.texts.lightbox2.closeLightbox
			);
		}

		if ( cancelBtn ) {
			cancelBtn.setAttribute(
				'aria-label',
				sunflower.texts.lightbox2.cancelLoading
			);
		}

		if ( nextBtn ) {
			nextBtn.setAttribute(
				'aria-label',
				sunflower.texts.lightbox2.nextImage
			);
		}

		if ( prevBtn ) {
			prevBtn.setAttribute(
				'aria-label',
				sunflower.texts.lightbox2.previousImage
			);
		}
	} );

	observerLightbox.observe( document.body, {
		childList: true,
		subtree: true,
	} );
} );
/* eslint-enable no-undef */

/* -------------------------------------------
Columns, deren jede Spalte mit einem Bild beginnt
 * ------------------------------------------- */
document.querySelectorAll( '.wp-block-columns' ).forEach( ( columns ) => {
	const allColumns = columns.querySelectorAll( ':scope > .wp-block-column' );

	// Prüfen, ob jede Spalte mit einem Bild‑Block startet
	const allStartWithImage = Array.from( allColumns ).every( ( col ) => {
		const firstChild = col.firstElementChild;
		return firstChild && firstChild.classList.contains( 'wp-block-image' );
	} );

	if ( allColumns.length > 1 && allStartWithImage ) {
		columns.classList.add( 'all-columns-start-with-image' );
	}

	if ( allColumns.length >= 3 ) {
		columns.classList.add( 'more-than-two-columns' );
	}
} );

document.querySelectorAll( '.wp-block-group' ).forEach( ( group ) => {
	const cols = group.querySelectorAll(
		':scope > .wp-block-columns > .wp-block-column'
	);
	const numCols = cols.length;

	if ( numCols >= 3 ) {
		group.classList.add( 'more-than-two-columns' );
	}

	if ( numCols === 2 ) {
		const headlineOnly = ( col ) =>
			Array.from( col.children ).every( ( el ) =>
				/^H[1-6]$/.test( el.tagName )
			);

		if ( headlineOnly( cols[ 0 ] ) || headlineOnly( cols[ 1 ] ) ) {
			group.classList.add( 'two-cols-headline-only' );
		}
	}
} );

/**
 * Menu-Collapse
 */

( () => {
	const BODY_CLASS = 'hamburger-menu';
	const MEASURE_CLASS = 'js-measuring';
	const RIGHT_BAR_SELECTOR = '.right-bar';
	const CONTENT_SELECTOR = '.right-bar__content';

	const qs = ( sel ) => document.querySelector( sel );

	const hasOverflow = ( el ) =>
		el.scrollWidth > el.clientWidth || el.scrollHeight > el.clientHeight;

	function computeOverflow() {
		const rightBar = qs( RIGHT_BAR_SELECTOR );
		const content = rightBar?.querySelector( CONTENT_SELECTOR );
		if ( ! rightBar || ! content ) {
			return false;
		}

		rightBar.classList.add( MEASURE_CLASS );

		const body = document.body;
		const hadClass = body.classList.contains( BODY_CLASS );
		if ( hadClass ) {
			body.classList.remove( BODY_CLASS );
		}

		const overflow = hasOverflow( content );

		if ( hadClass ) {
			body.classList.add( BODY_CLASS );
		}
		rightBar.classList.remove( MEASURE_CLASS );

		return overflow;
	}

	let lastState = null;
	let pending = false;

	function scheduleUpdate() {
		if ( pending ) {
			return;
		}
		pending = true;
		requestAnimationFrame( () => {
			pending = false;
			const overflow = computeOverflow();
			if ( overflow !== lastState ) {
				lastState = overflow;
				document.body.classList.toggle( BODY_CLASS, overflow );
			}
		} );
	}

	scheduleUpdate();

	document.addEventListener( 'DOMContentLoaded', scheduleUpdate );
	window.addEventListener( 'load', scheduleUpdate, { passive: true } );
	window.addEventListener( 'resize', scheduleUpdate, { passive: true } );

	const rightBar = qs( RIGHT_BAR_SELECTOR );
	if ( rightBar ) {
		new MutationObserver( scheduleUpdate ).observe( rightBar, {
			childList: true,
			subtree: true,
		} );
	}
} )();

document.addEventListener( 'DOMContentLoaded', () => {
	const brandLeft = document.querySelector( '.brand-left' );
	if ( ! brandLeft ) {
		return;
	}

	const setWidthVar = () => {
		const rect = brandLeft.getBoundingClientRect();
		const style = window.getComputedStyle( brandLeft );
		const total =
			rect.width +
			parseFloat( style.marginLeft || 0 ) +
			parseFloat( style.marginRight || 0 );

		document.documentElement.style.setProperty(
			'--width-brand-left',
			total + 'px'
		);
	};

	setWidthVar();

	window.addEventListener( 'resize', setWidthVar, { passive: true } );

	new ResizeObserver( setWidthVar ).observe( brandLeft );
} );

/**
 * Hamburger ausklappen
 */

document.addEventListener( 'DOMContentLoaded', () => {
	const burger = document.querySelector( '.hamburger' ); // Klasse!
	const rightBar = document.querySelector( '.right-bar' );

	if ( ! burger || ! rightBar ) {
		return;
	} // Sicherheits-Check

	burger.addEventListener( 'click', () => {
		rightBar.classList.toggle( 'unfold' );

		const expanded = rightBar.classList.contains( 'unfold' );
		burger.setAttribute( 'aria-expanded', expanded );
	} );
} );

/**
 * Untermenü nach innen rücken, falls es aus Viewport herausragt
 */

document.addEventListener( 'DOMContentLoaded', () => {
	const ITEM_SELECTOR = '.main-menu .nav > li.menu-item-has-children';
	const items = document.querySelectorAll( ITEM_SELECTOR );

	items.forEach( ( item ) => {
		const submenu = item.querySelector( ':scope > ul.sub-menu' );
		if ( ! submenu ) {
			return;
		}

		const reposition = () => {
			submenu.style.left = '';
			submenu.style.right = '';
			submenu.style.transform = '';

			if ( getComputedStyle( submenu ).display === 'none' ) {
				return;
			}

			const rect = submenu.getBoundingClientRect();
			const overflowRight = rect.right - window.innerWidth;
			const overflowLeft = rect.left;

			if ( overflowRight > 0 ) {
				submenu.style.left = 'auto';
				submenu.style.right = '0';
			} else if ( overflowLeft < 0 ) {
				submenu.style.left = '0';
				submenu.style.right = 'auto';
			}
		};

		const openHandler = () => requestAnimationFrame( reposition );
		const closeHandler = () => {
			submenu.style.left = '';
			submenu.style.right = '';
			submenu.style.transform = '';
		};

		item.addEventListener( 'mouseenter', openHandler );
		item.addEventListener( 'focusin', openHandler ); // Tastatur­navigation
		item.addEventListener( 'mouseleave', closeHandler );
		item.addEventListener( 'focusout', closeHandler );
	} );

	window.addEventListener( 'resize', () => {
		document
			.querySelectorAll( `${ ITEM_SELECTOR }:hover > ul.sub-menu` )
			.forEach( ( submenu ) =>
				requestAnimationFrame( () => {
					submenu.style.left = '';
					submenu.style.right = '';
					const rect = submenu.getBoundingClientRect();
					const overflowRight = rect.right - window.innerWidth;
					const overflowLeft = rect.left;
					if ( overflowRight > 0 ) {
						submenu.style.left = 'auto';
						submenu.style.right = '0';
					} else if ( overflowLeft < 0 ) {
						submenu.style.left = '0';
						submenu.style.right = 'auto';
					}
				} )
			);
	} );
} );

/**
 * mark before & after bekommen die selbe linienfarbe wie mark background / border
 */

document.addEventListener( 'DOMContentLoaded', () => {
	document
		.querySelectorAll( 'h1 mark, h2 mark, h3 mark' )
		.forEach( ( mark ) => {
			const bg = getComputedStyle( mark ).backgroundColor;
			mark.style.setProperty( '--bg', bg );
		} );
} );

/*
 * Slider
 */

( () => {
	'use strict';

	const breakpoint = 950; // px (nur für Group-Slider)
	const dragThreshold = 35; // px
	const verticalScrollThreshold = 5; // px

	const instances = [];

	const prevent = ( e ) => e.preventDefault();

	const disableScroll = () => {
		document.addEventListener( 'wheel', prevent, { passive: false } );
		document.addEventListener( 'touchmove', prevent, { passive: false } );
	};

	const enableScroll = () => {
		document.removeEventListener( 'wheel', prevent, { passive: false } );
		document.removeEventListener( 'touchmove', prevent, {
			passive: false,
		} );
	};

	function bootstrap() {
		const viewportWidth = window.innerWidth;

		document
			.querySelectorAll(
				/* <<< GEÄNDERT: Latest-Posts-Track zusätzlich zulassen >>> */
				'.wp-block-columns.all-columns-start-with-image.more-than-two-columns, .latest-posts .row.posts-slider, .tarife'
			)
			.forEach( ( trackEl ) => {
				const isActive =
					trackEl.classList.contains( 'js-column-slider' );

				/* <<< NEU: Latest-Posts sollen IMMER sliden; Groups nur <= breakpoint >>> */
				const isLatestPostsTrack =
					trackEl.classList.contains( 'posts-slider' ) ||
					( trackEl.classList.contains( 'row' ) &&
						trackEl.closest( '.latest-posts' ) );

				if ( isLatestPostsTrack ) {
					if ( ! isActive ) {
						initSlider( trackEl );
					} else {
						const inst = instances.find(
							( ins ) => ins.track === trackEl
						);
						if ( inst ) {
							inst.recalc();
						}
					}
				} else if ( viewportWidth <= breakpoint && ! isActive ) {
					initSlider( trackEl );
				} else if ( viewportWidth > breakpoint && isActive ) {
					destroySlider( trackEl );
				} else if ( isActive ) {
					const inst = instances.find(
						( ins ) => ins.track === trackEl
					);
					if ( inst ) {
						inst.recalc();
					}
				}
			} );
	}

	function initSlider( track ) {
		track.classList.add( 'column-slider', 'js-column-slider' );
		track.style.flexWrap = 'nowrap';
		track.style.transition = 'transform .5s ease-in-out';

		const nav = document.createElement( 'div' );
		nav.className = 'navbuttons';
		nav.innerHTML = `
			<button class="slider__button slider__button--prev" aria-label="Vorherige Folie">
				<div class="button__direction">
					<svg class="button--direction" width="22" height="20" viewBox="0 0 22 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path d="M9.66585 19.7617L11.0408 18.4284L3.87418 11.2617L21.9575 11.2617L21.9575 9.38672L3.87419 9.38671L11.0825 2.17838L9.70752 0.886716L0.249183 10.3034L9.66585 19.7617Z"/>
					</svg>
				</div>
			</button>
			<button class="slider__button slider__button--next" aria-label="Nächste Folie">
				<div class="button__direction">
					<svg width="22" height="20" viewBox="0 0 22 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path d="M9.66585 19.7617L11.0408 18.4284L3.87418 11.2617L21.9575 11.2617L21.9575 9.38672L3.87419 9.38671L11.0825 2.17838L9.70752 0.886716L0.249183 10.3034L9.66585 19.7617Z"/>
					</svg>
				</div>
			</button>
		`;
		track.parentNode.insertBefore( nav, track.nextSibling );

		/* <<< GEÄNDERT: Slides je nach Track-Typ ermitteln >>> */
		const isLatestPosts =
			track.classList.contains( 'posts-slider' ) ||
			( track.classList.contains( 'row' ) &&
				track.closest( '.latest-posts' ) );

		const slides = Array.from( track.children ).filter( ( el ) => {
			if ( isLatestPosts ) {
				// Latest-Posts (Bootstrap-Grid): Spalten sind .col-*
				return el.matches( '.col-12, .col-md-6, .col-md-4' );
			}
			// Bestehende Group-Slider
			return el.classList.contains( 'wp-block-column' );
		} );

		const state = {
			track,
			slides,
			nav,
			prevBtn: nav.querySelector( '.slider__button--prev' ),
			nextBtn: nav.querySelector( '.slider__button--next' ),
			index: 0,
			total: slides.length,
			slideWidth: 0,
			gap: 0,
			isDragging: false,
			startX: 0,
			startY: 0,
			currentTranslate: 0,
			previousTranslate: 0,
			rafId: 0,
			/* <<< NEU: wie viele Karten pro „Seite“ bei Latest-Posts >>> */
			slidesPerView: 1,
			isLatestPosts,
		};

		function step() {
			return state.slideWidth + state.gap;
		}

		function computeGap() {
			const trackStyle = getComputedStyle( state.track );
			let g = parseFloat( trackStyle.columnGap );
			if ( Number.isNaN( g ) ) {
				const gapStr = trackStyle.gap || '';
				const parts = gapStr.trim().split( /\s+/ );
				const last = parts[ parts.length - 1 ];
				const val = parseFloat( last );
				g = Number.isNaN( val ) ? 0 : val;
			}
			if ( g > 0 ) {
				return g;
			}

			// Fallback: Abstand messen (funktioniert auch mit margin-/padding-basiertem „Gap“)
			if ( state.slides.length > 1 ) {
				const a = state.slides[ 0 ].getBoundingClientRect();
				const b = state.slides[ 1 ].getBoundingClientRect();
				return Math.max( 0, Math.round( b.left - a.right ) );
			}
			return 0;
		}

		function setByIndex( skipAnim = false ) {
			const x = -( state.index * step() );
			state.currentTranslate = x;
			state.previousTranslate = x;
			if ( skipAnim ) {
				state.track.style.transition = 'none';
			}
			state.track.style.transform = `translateX(${ x }px)`;
			if ( skipAnim ) {
				requestAnimationFrame( () => {
					state.track.style.transition = 'transform .5s ease-in-out';
				} );
			}
		}

		function recalc() {
			// Inhaltsbreite des Containers (clientWidth inkl. Padding -> Padding abziehen)
			const style = getComputedStyle( state.track );
			const padLeft = parseFloat( style.paddingLeft ) || 0;
			const padRight = parseFloat( style.paddingRight ) || 0;
			const contentWidth = Math.max(
				0,
				Math.round( state.track.clientWidth - padLeft - padRight )
			);

			// Gap zuerst ermitteln
			state.gap = computeGap();

			if ( state.isLatestPosts ) {
				/* <<< NEU: 3/2/1 Karten je nach Breite >>> */
				const vw = window.innerWidth;
				let spv = 3;
				if ( vw < 800 ) {
					spv = 1;
				} else if ( vw < 1200 ) {
					spv = 2;
				}
				state.slidesPerView = spv;

				const totalGap = state.gap * ( spv - 1 );
				state.slideWidth = Math.max(
					0,
					Math.floor( ( contentWidth - totalGap ) / spv )
				);

				state.slides.forEach( ( el ) => {
					el.style.flex = `0 0 ${ state.slideWidth }px`;
					el.style.width = `${ state.slideWidth }px`;
					el.style.minWidth = `${ state.slideWidth }px`;
					el.style.maxWidth = `${ state.slideWidth }px`;
				} );
			} else {
				/* Group-Slider: weiter 1 Karte pro „Seite“ <= 950px */
				state.slidesPerView = 1;
				state.slideWidth = contentWidth;
				state.slides.forEach( ( el ) => {
					el.style.flex = `0 0 ${ state.slideWidth }px`;
					el.style.width = `${ state.slideWidth }px`;
					el.style.minWidth = `${ state.slideWidth }px`;
					el.style.maxWidth = `${ state.slideWidth }px`;
				} );
			}

			// Index einklammern
			const maxIndex = Math.max( 0, state.total - state.slidesPerView );
			if ( state.index > maxIndex ) {
				state.index = maxIndex;
			}

			setByIndex( true );
		}

		function onStart( x, y ) {
			state.isDragging = true;
			state.startX = x;
			state.startY = y;
			state.track.style.transition = 'none';
			state.rafId = requestAnimationFrame( onAnim );
			disableScroll();
		}

		function onMove( x, y ) {
			if ( ! state.isDragging ) {
				return;
			}
			const dx = x - state.startX;
			const dy = y - state.startY;

			// Vertikales Scrollen zulassen, wenn Absicht klar ist
			if (
				Math.abs( dy ) > verticalScrollThreshold &&
				Math.abs( dx ) < dragThreshold * 10
			) {
				enableScroll();
			} else {
				disableScroll();
			}

			const maxTranslate = -(
				step() * Math.max( 0, state.total - state.slidesPerView )
			);
			state.currentTranslate = state.previousTranslate + dx;

			// Sanftes Einklammern
			const overshoot = 60;
			if ( state.currentTranslate > overshoot ) {
				state.currentTranslate = overshoot;
			}
			if ( state.currentTranslate < maxTranslate - overshoot ) {
				state.currentTranslate = maxTranslate - overshoot;
			}
		}

		function onEnd() {
			cancelAnimationFrame( state.rafId );
			if ( ! state.isDragging ) {
				return;
			}
			state.isDragging = false;

			const moved = state.currentTranslate - state.previousTranslate;
			const inc = state.isLatestPosts ? state.slidesPerView : 1;

			const maxIndex = Math.max( 0, state.total - state.slidesPerView );

			if ( moved < -dragThreshold ) {
				state.index = Math.min( maxIndex, state.index + inc );
			} else if ( moved > dragThreshold ) {
				state.index = Math.max( 0, state.index - inc );
			}

			setByIndex();
			state.track.style.transition = 'transform .5s ease-in-out';
			enableScroll();
		}

		function onAnim() {
			state.track.style.transform = `translateX(${ state.currentTranslate }px)`;
			if ( state.isDragging ) {
				state.rafId = requestAnimationFrame( onAnim );
			}
		}

		// Buttons
		const onNext = () => {
			const inc = state.isLatestPosts ? state.slidesPerView : 1;
			const maxIndex = Math.max( 0, state.total - state.slidesPerView );
			if ( state.index < maxIndex ) {
				state.index = Math.min( maxIndex, state.index + inc );
				setByIndex();
			}
		};
		const onPrev = () => {
			const inc = state.isLatestPosts ? state.slidesPerView : 1;
			if ( state.index > 0 ) {
				state.index = Math.max( 0, state.index - inc );
				setByIndex();
			}
		};

		// Touch-Events
		const onTouchStart = ( e ) =>
			onStart( e.touches[ 0 ].clientX, e.touches[ 0 ].clientY );
		const onTouchMove = ( e ) =>
			onMove( e.touches[ 0 ].clientX, e.touches[ 0 ].clientY );
		const onTouchEnd = () => onEnd();

		// Mouse-Events
		const onMouseDown = ( e ) => onStart( e.clientX, e.clientY );
		const onMouseMove = ( e ) => onMove( e.clientX, e.clientY );
		const onMouseUp = () => onEnd();
		const onMouseLeave = () => onEnd();

		track.addEventListener( 'touchstart', onTouchStart, { passive: true } );
		track.addEventListener( 'touchmove', onTouchMove, { passive: false } );
		track.addEventListener( 'touchend', onTouchEnd );
		track.addEventListener( 'touchcancel', onTouchEnd );

		track.addEventListener( 'mousedown', onMouseDown );
		track.addEventListener( 'mousemove', onMouseMove );
		track.addEventListener( 'mouseup', onMouseUp );
		track.addEventListener( 'mouseleave', onMouseLeave );

		state.prevBtn.addEventListener( 'click', onPrev );
		state.nextBtn.addEventListener( 'click', onNext );

		window.addEventListener( 'resize', recalc );

		recalc();

		instances.push( {
			track,
			nav,
			recalc,
			remove: () => {
				track.removeEventListener( 'touchstart', onTouchStart );
				track.removeEventListener( 'touchmove', onTouchMove );
				track.removeEventListener( 'touchend', onTouchEnd );
				track.removeEventListener( 'touchcancel', onTouchEnd );

				track.removeEventListener( 'mousedown', onMouseDown );
				track.removeEventListener( 'mousemove', onMouseMove );
				track.removeEventListener( 'mouseup', onMouseUp );
				track.removeEventListener( 'mouseleave', onMouseLeave );

				state.prevBtn.removeEventListener( 'click', onPrev );
				state.nextBtn.removeEventListener( 'click', onNext );

				window.removeEventListener( 'resize', recalc );

				// Inline-Styles zurücksetzen
				state.slides.forEach( ( el ) => {
					el.style.flex = '';
					el.style.width = '';
					el.style.minWidth = '';
					el.style.maxWidth = '';
				} );

				track.style.transform = '';
				track.style.flexWrap = '';
				track.style.transition = '';

				nav.remove();
			},
		} );
	}

	function destroySlider( track ) {
		const i = instances.findIndex( ( ins ) => ins.track === track );
		if ( i === -1 ) {
			return;
		}

		instances[ i ].remove();
		track.classList.remove( 'column-slider', 'js-column-slider' );
		instances.splice( i, 1 );
	}

	document.addEventListener( 'DOMContentLoaded', bootstrap );
	window.addEventListener( 'resize', bootstrap );
} )();

// Accordion: immer nur EIN Panel geöffnet
document.addEventListener( 'click', function ( e ) {
	const btn = e.target.closest( '.accordion-button' );
	if ( ! btn ) {
		return;
	}

	const targetSel =
		btn.getAttribute( 'data-bs-target' ) ||
		btn.getAttribute( 'data-target' );

	if ( ! targetSel ) {
		return;
	}

	const panel = document.querySelector( targetSel );
	if ( ! panel ) {
		return;
	}

	// Bootstrap-Collapse ausbremsen
	e.preventDefault();
	e.stopPropagation();

	const isOpen = panel.classList.contains( 'show' );

	// 1. Scope bestimmen: wenn es einen .accordion-Wrapper gibt → innerhalb,
	//    sonst global über die ganze Seite
	const accordion = btn.closest( '.accordion' );
	const allPanels = accordion
		? accordion.querySelectorAll( '.accordion-collapse' )
		: document.querySelectorAll( '.accordion-collapse' );

	// 2. Alle anderen Panels schließen
	allPanels.forEach( ( el ) => {
		if ( el === panel ) {
			return;
		}

		el.classList.remove( 'show' );

		const otherBtn = el
			.closest( '.accordion-item' )
			?.querySelector( '.accordion-button' );

		if ( otherBtn ) {
			otherBtn.classList.add( 'collapsed' );
			otherBtn.setAttribute( 'aria-expanded', 'false' );
		}
	} );

	// 3. Geklicktes Panel toggeln
	if ( isOpen ) {
		// war offen → schließen, dann ist ggf. gar kein Panel offen
		panel.classList.remove( 'show' );
		btn.classList.add( 'collapsed' );
		btn.setAttribute( 'aria-expanded', 'false' );
	} else {
		// war zu → öffnen
		panel.classList.add( 'show' );
		btn.classList.remove( 'collapsed' );
		btn.setAttribute( 'aria-expanded', 'true' );
	}
} );

// Bootstrap-Collapse an den Buttons deaktivieren, damit nichts doppelt toggelt
document.addEventListener( 'DOMContentLoaded', function () {
	document
		.querySelectorAll( '.accordion-button[data-bs-toggle="collapse"]' )
		.forEach( function ( btn ) {
			btn.removeAttribute( 'data-bs-toggle' );
		} );
} );

// Headline mit Hinterlegung bei Umbruch: Hinterlegung raus

( function () {
	let resizeTimeout;

	function isMultilineMark( mark ) {
		if ( ! mark || ! mark.firstChild ) {
			return false;
		}

		const range = document.createRange();
		range.selectNodeContents( mark );

		const rects = range.getClientRects();

		if ( typeof range.detach === 'function' ) {
			range.detach();
		}

		return rects.length > 1;
	}

	function getHeadlineColor( heading ) {
		const lightBgClasses = [
			'has-weiss-background-color',
			'has-sonne-background-color',
			'has-sand-background-color',
			'has-kalk-background-color',
			'has-white-background-color',
			'has-gruener-sand-background-color',
		];

		const darkBgClasses = [
			'has-gray-background-color',
			'has-tanne-background-color',
			'has-klee-background-color',
			'has-himmel-background-color',
		];

		const schwarzwaldClasses = [ 'has-schwarzwald-background-color' ];

		const grashalmClasses = [ 'has-grashalm-background-color' ];

		function findContextWithClasses( classNames ) {
			let el = heading;

			while ( el && el !== document.body ) {
				if (
					classNames.some( ( cls ) => el.classList.contains( cls ) )
				) {
					return el;
				}

				const bg = el.querySelector( '.wp-block-cover__background' );
				if (
					bg &&
					classNames.some( ( cls ) => bg.classList.contains( cls ) )
				) {
					return bg;
				}

				el = el.parentElement;
			}

			return null;
		}

		if ( findContextWithClasses( lightBgClasses ) ) {
			return '#005538';
		}
		if ( findContextWithClasses( darkBgClasses ) ) {
			return '#ffffff';
		}
		if ( findContextWithClasses( schwarzwaldClasses ) ) {
			return '#8abd24';
		}
		if ( findContextWithClasses( grashalmClasses ) ) {
			return '#002216';
		}

		const body = document.body;

		if ( body.classList.contains( 'colorscheme-light' ) ) {
			return '#005538';
		}
		if ( body.classList.contains( 'colorscheme-green' ) ) {
			return '#8abd24';
		}

		return null;
	}

	function processHeadings() {
		const headings = document.querySelectorAll( 'h1, h2, h3, h4, h5, h6' );

		headings.forEach( function ( heading ) {
			const hasMarkNow = !! heading.querySelector( 'mark' );

			if ( ! heading.dataset.markOriginalHtml && ! hasMarkNow ) {
				return;
			}

			if ( ! heading.dataset.markOriginalHtml && hasMarkNow ) {
				heading.dataset.markOriginalHtml = heading.innerHTML;
				heading.dataset.markOriginalStyle =
					heading.getAttribute( 'style' ) || '';
			}

			if ( ! heading.dataset.markOriginalHtml ) {
				return;
			}

			if ( heading.innerHTML !== heading.dataset.markOriginalHtml ) {
				heading.innerHTML = heading.dataset.markOriginalHtml;
			}

			const originalStyle = heading.dataset.markOriginalStyle || '';
			if ( originalStyle ) {
				heading.setAttribute( 'style', originalStyle );
			} else {
				heading.removeAttribute( 'style' );
			}

			// 2b. Sonderregel für erstes Cover → left:0 + margin-top:0
			const firstCoverInner = document.querySelector(
				'body .entry-content > .wp-block-cover:first-child .wp-block-cover__inner-container'
			);
			if ( firstCoverInner && firstCoverInner.contains( heading ) ) {
				heading.style.left = '0';
				heading.style.marginTop = '0';
			}

			const marks = heading.querySelectorAll( 'mark' );
			if ( ! marks.length ) {
				return;
			}

			let hasMultiline = false;

			marks.forEach( function ( mark ) {
				if ( ! hasMultiline && isMultilineMark( mark ) ) {
					hasMultiline = true;
				}
			} );

			if ( ! hasMultiline ) {
				return;
			}

			const newColor = getHeadlineColor( heading );
			if ( newColor ) {
				heading.style.color = newColor;
			}

			heading.querySelectorAll( 'mark' ).forEach( function ( mark ) {
				const parent = mark.parentNode;
				while ( mark.firstChild ) {
					parent.insertBefore( mark.firstChild, mark );
				}
				parent.removeChild( mark );
			} );
		} );
	}

	function debouncedProcess() {
		clearTimeout( resizeTimeout );
		resizeTimeout = setTimeout( processHeadings, 150 );
	}

	window.addEventListener( 'load', processHeadings );
	window.addEventListener( 'resize', debouncedProcess );
} )();
