/* eslint-disable no-undef */
// get the sticky element
const stickyElement = document.querySelector( '.top-bar' );

let ticking = false;
const scrollThreshold = 1;

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

	adjustMetaboxHeight();

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
				jQuery( '#sunflower-contact-form' ).append(
					'<div class="bg-danger p-4 text-white">' +
						response.text +
						'</div>'
				);
				return;
			}

			jQuery( '#sunflower-contact-form' ).html( response.text );
		} );

	return false;
} );

function adjustMetaboxHeight() {
	if ( ! jQuery( '.metabox' ).length ) {
		return;
	}

	const tooBig =
		jQuery( '.metabox' ).outerHeight() -
		jQuery( '.entry-header' ).outerHeight();

	if ( tooBig <= 0 ) {
		return;
	}

	jQuery( '.entry-content' ).prepend( '<div class="metabox-spacer"></div>' );

	jQuery( '.metabox-spacer' ).height( tooBig + 'px' );
}

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
 * mark before & after bekommen die selbe linienfarbe wie mark background
 */

function applyMarkBorders( root = document ) {
	root.querySelectorAll( 'h1 mark' ).forEach( ( mark ) => {
		const bg = getComputedStyle( mark ).backgroundColor;
		mark.style.setProperty( '--bg', bg );
	} );
}

document.addEventListener( 'DOMContentLoaded', () => {
	applyMarkBorders();

	const observer = new MutationObserver( ( muts ) => {
		muts.forEach( ( m ) => {
			m.addedNodes.forEach( ( node ) => {
				if ( node.nodeType !== 1 ) {
					return;
				} // nur Elemente
				if ( node.matches?.( 'h1 mark' ) ) {
					applyMarkBorders( node.parentNode );
				} else {
					applyMarkBorders( node );
				} // falls <mark> tiefer liegt
			} );
		} );
	} );
	observer.observe( document.body, { childList: true, subtree: true } );
} );

/**
 * Menü hat weniger als 7 Einträge
 */

document.addEventListener( 'DOMContentLoaded', function () {
	const menu = document.querySelector( '.right-bar nav > ul' );
	if ( menu ) {
		const items = menu.querySelectorAll( ':scope > li' );
		if ( items.length <= 6 ) {
			document.body.classList.add( 'smallmenu' );
		}
	}
} );

/**
 * wechselnde Farbe H2
 */

document.addEventListener( 'DOMContentLoaded', function () {
	const headlines = document.querySelectorAll( '.colorscheme-light h2' );
	headlines.forEach( ( el, index ) => {
		if ( ( index + 1 ) % 2 === 0 ) {
			el.classList.add( 'even' );
		} else {
			el.classList.add( 'odd' );
		}
	} );
} );

/*
 * Slider
 */

( () => {
	'use strict';

	const BREAKPOINT = 950; // px
	const DRAG_THRESHOLD = 35; // px
	const V_SCROLL_THRESHOLD = 5; // px

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
		const vw = window.innerWidth;

		document
			.querySelectorAll(
				'.wp-block-columns.all-columns-start-with-image.more-than-two-columns'
			)
			.forEach( ( colBlock ) => {
				const active =
					colBlock.classList.contains( 'js-column-slider' );

				if ( vw <= BREAKPOINT && ! active ) {
					initSlider( colBlock );
				} else if ( vw > BREAKPOINT && active ) {
					destroySlider( colBlock );
				}
			} );
	}

	function initSlider( track ) {
		track.classList.add( 'column-slider', 'js-column-slider' );
		track.style.flexWrap = 'nowrap'; // Kein Zeilenumbruch
		track.style.transition = 'transform .5s ease-in-out';

		const nav = document.createElement( 'div' );
		nav.className = 'navbuttons';
		nav.innerHTML = `
            <button class="slider__button slider__button--prev" aria-label="Vorherige Folie">
                <div class="button__direction">
                	<svg class="button--direction"  width="22" height="20" viewBox="0 0 22 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
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

		const slides = Array.from( track.children ).filter( ( el ) =>
			el.classList.contains( 'wp-block-column' )
		);

		const st = {
			track,
			slides,
			nav,
			prevBtn: nav.querySelector( '.slider__button--prev' ),
			nextBtn: nav.querySelector( '.slider__button--next' ),
			idx: 0,
			total: slides.length,
			slideW: 0,
			gap: 0,
			trackW: 0,
			isDrag: false,
			sx: 0,
			sy: 0,
			curTrans: 0,
			prevTrans: 0,
			rafID: 0,
		};

		/* ------- Größen berechnen ------- */
		function recalc() {
			const first = st.slides[ 0 ];
			const slideStyle = getComputedStyle( first );
			const trackStyle = getComputedStyle( st.track );
			st.slideW = first.clientWidth;
			const marginR = parseFloat( slideStyle.marginRight ) || 0;
			const flexGap =
				parseFloat( trackStyle.columnGap || trackStyle.gap ) || 0;
			st.gap = marginR + flexGap; // beide Abstände addieren
			st.trackW = ( st.slideW + st.gap ) * st.total;
			st.track.style.width = `${ st.trackW }px`;
			updatePos();
		}

		/* ------- Position ------- */
		const slideWGap = () => st.slideW + st.gap;

		function updatePos() {
			const x = -( st.idx * slideWGap() );
			st.track.style.transform = `translateX(${ x }px)`;
			st.prevTrans = st.curTrans = x;
		}

		function setByIndex() {
			st.curTrans = -( st.idx * slideWGap() );
			st.prevTrans = st.curTrans;
			st.track.style.transform = `translateX(${ st.curTrans }px)`;
		}

		/* ------- Buttons ------- */
		st.nextBtn.addEventListener( 'click', () => {
			if ( st.idx < st.total - 1 ) {
				st.idx += 1;
				updatePos();
			}
		} );

		st.prevBtn.addEventListener( 'click', () => {
			if ( st.idx > 0 ) {
				st.idx -= 1;
				updatePos();
			}
		} );

		/* ------- Drag / Swipe Handlers ------- */
		function onStart( x, y ) {
			st.isDrag = true;
			st.sx = x;
			st.sy = y;
			st.track.style.transition = 'none';
			st.rafID = requestAnimationFrame( onAnim );
			disableScroll();
		}

		function onMove( x, y ) {
			if ( ! st.isDrag ) {
				return;
			}
			const dx = x - st.sx;
			const dy = y - st.sy;
			st.curTrans = st.prevTrans + dx;

			if (
				Math.abs( dy ) > V_SCROLL_THRESHOLD &&
				Math.abs( dx ) < DRAG_THRESHOLD * 10
			) {
				enableScroll();
			} else {
				disableScroll();
			}
		}

		function onEnd() {
			cancelAnimationFrame( st.rafID );
			st.isDrag = false;
			const moved = st.curTrans - st.prevTrans;

			if ( moved < -DRAG_THRESHOLD && st.idx < st.total - 1 ) {
				st.idx += 1;
			}
			if ( moved > DRAG_THRESHOLD && st.idx > 0 ) {
				st.idx -= 1;
			}

			setByIndex();
			st.track.style.transition = 'transform .5s ease-in-out';
			enableScroll();
		}

		function onAnim() {
			st.track.style.transform = `translateX(${ st.curTrans }px)`;
			if ( st.isDrag ) {
				st.rafID = requestAnimationFrame( onAnim );
			}
		}

		track.addEventListener(
			'touchstart',
			( e ) => onStart( e.touches[ 0 ].clientX, e.touches[ 0 ].clientY ),
			{ passive: true }
		);

		track.addEventListener(
			'touchmove',
			( e ) => onMove( e.touches[ 0 ].clientX, e.touches[ 0 ].clientY ),
			{ passive: false }
		);

		track.addEventListener( 'touchend', onEnd );

		track.addEventListener( 'mousedown', ( e ) =>
			onStart( e.clientX, e.clientY )
		);
		track.addEventListener( 'mousemove', ( e ) =>
			onMove( e.clientX, e.clientY )
		);
		track.addEventListener( 'mouseup', onEnd );
		track.addEventListener( 'mouseleave', onEnd );

		window.addEventListener( 'resize', recalc );

		recalc();

		instances.push( { track, nav, recalc } );
	}

	function destroySlider( track ) {
		const i = instances.findIndex( ( ins ) => ins.track === track );
		if ( i === -1 ) {
			return;
		}

		const { nav, recalc } = instances[ i ];

		track.style.transform = '';
		track.style.width = '';
		track.style.flexWrap = '';

		track.classList.remove( 'column-slider', 'js-column-slider' );
		nav.remove();

		window.removeEventListener( 'resize', recalc );
		instances.splice( i, 1 );
	}

	/* ---------------- Bootstrap ---------------- */
	document.addEventListener( 'DOMContentLoaded', bootstrap );
	window.addEventListener( 'resize', bootstrap );
} )();
