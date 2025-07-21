/* eslint-disable no-undef */
// get the sticky element
const stickyElement = document.querySelector( '.top-bar' );

let ticking = false;
let scrollThreshold = 1;

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

/* Parallax for Header Image */

document.addEventListener( 'DOMContentLoaded', function () {
	const covers = document.querySelectorAll( '.wp-block-cover' );

	covers.forEach( ( cover ) => {
		const image = cover.querySelector(
			'.wp-block-cover__image-background'
		);
		if ( image ) {
			image.style.transform = 'scale(1.2)';
			image.style.willChange = 'transform'; // Performance-Tweak
		}
	} );

	(() => {

		const covers = document.querySelectorAll('.wp-block-cover');

		if ('scrollRestoration' in history) {
			history.scrollRestoration = 'manual';
		}

		document.documentElement.style.overflowAnchor = 'none';
		const SPEED = 0.1; // Bewegungsgeschwindigkeit

		function update() {
			const scrollY      = window.scrollY;
			const viewportH    = window.innerHeight;

			covers.forEach((cover) => {
				const img = cover.querySelector('.wp-block-cover__image-background');
				if (!img) return;

				const { top, bottom } = cover.getBoundingClientRect();

				// Nur berechnen, wenn das Cover (teilweise) im Viewport ist
				if (top < viewportH && bottom > 0) {
					const offset = (scrollY - cover.offsetTop) * SPEED;
					img.style.transform = `translateY(${offset}px) scale(1.2)`;
				}
			});
		}

		window.addEventListener('load', update, { passive: true });

		window.addEventListener('scroll', () => requestAnimationFrame(update), {
			passive: true,
		});
	})();



/* Mehrere Columns in einer Group haben ein Bild am Anfang */
document.querySelectorAll( '.wp-block-columns' ).forEach( ( columns ) => {
	const allColumns = columns.querySelectorAll( ':scope > .wp-block-column' );

	const allStartWithImage = Array.from( allColumns ).every( ( col ) => {
		const firstChild = col.firstElementChild;
		return firstChild && firstChild.classList.contains( 'wp-block-image' );
	} );

	if ( allColumns.length > 1 && allStartWithImage ) {
		columns.classList.add( 'all-columns-start-with-image' );
	}
} );

/* Eine von 2 columns enthält nur headlines */

document.querySelectorAll( '.wp-block-group' ).forEach( ( group ) => {
	const cols = group.querySelectorAll(
		':scope > .wp-block-columns > .wp-block-column'
	);
	if ( cols.length !== 2 ) return;

	const headlineOnly = ( col ) => {
		return Array.from( col.children ).every( ( el ) =>
			/^H[1-6]$/.test( el.tagName )
		);
	};

	if ( headlineOnly( cols[ 0 ] ) || headlineOnly( cols[ 1 ] ) ) {
		group.classList.add( 'two-cols-headline-only' );
	}
} );



	/**
	 * Menu-Hamburger
	 */

	(function () {
		const BODY_CLASS         = 'hamburger-menu';
		const RIGHT_BAR_SELECTOR = '.right-bar';
		const CONTENT_SELECTOR   = '.right-bar__content';

		function hasOverflow(el) {
			return (
				el.scrollHeight > el.clientHeight ||
				el.scrollWidth  > el.clientWidth
			);
		}

		function updateBodyClass() {
			const rightBar = document.querySelector(RIGHT_BAR_SELECTOR);
			const content  = rightBar?.querySelector(CONTENT_SELECTOR);
			if (!rightBar || !content) return;

			const hadClass = document.body.classList.contains(BODY_CLASS);
			if (hadClass) document.body.classList.remove(BODY_CLASS);

			const overflow = hasOverflow(content);

			document.body.classList.toggle(BODY_CLASS, overflow);
		}

		updateBodyClass();

		document.addEventListener('DOMContentLoaded', () => {
			updateBodyClass();
			requestAnimationFrame(updateBodyClass);
		});

		window.addEventListener('load', updateBodyClass, { passive: true });
		window.addEventListener('resize', updateBodyClass, { passive: true });

		const rightBar = document.querySelector(RIGHT_BAR_SELECTOR);
		if (rightBar) {
			new MutationObserver(updateBodyClass).observe(rightBar, {
				attributes: true,
				childList : true,
				subtree   : true,
			});
		}
	})();


});


document.addEventListener('DOMContentLoaded', () => {
	const brandLeft = document.querySelector('.brand-left');
	if (!brandLeft) return;

	const setWidthVar = () => {
		const rect  = brandLeft.getBoundingClientRect();
		const style = window.getComputedStyle(brandLeft);
		const total =
			rect.width +
			parseFloat(style.marginLeft || 0) +
			parseFloat(style.marginRight || 0);

		document.documentElement.style.setProperty(
			'--width-brand-left',
			total + 'px'
		);
	};

	setWidthVar();

	window.addEventListener('resize', setWidthVar, { passive: true });

	new ResizeObserver(setWidthVar).observe(brandLeft);
});


/**
 * Hamburger ausklappen
 */

document.addEventListener('DOMContentLoaded', () => {
	const burger   = document.querySelector('.hamburger');  // Klasse!
	const rightBar = document.querySelector('.right-bar');

	if (!burger || !rightBar) return;                       // Sicherheits-Check

	burger.addEventListener('click', () => {
		rightBar.classList.toggle('unfold');

		const expanded = rightBar.classList.contains('unfold');
		burger.setAttribute('aria-expanded', expanded);
	});
});


/**
 * Menu-Dropdown für Touch devices
 */


