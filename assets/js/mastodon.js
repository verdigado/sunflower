/* eslint-disable no-undef */
'use strict';

// based on https://github.com/Aly-ve/Mastodon-share-button

const COOKIE_NAME = 'mastodon-instance-address';
const URL_REGEX = /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([\/\w .-]*)*\/?$/;

const msbConfig = {
	openModal() {
		jQuery( '#mastodonShareModal' ).modal( 'show' );
	},
	closeModal() {
		jQuery( '#mastodonShareModal' ).modal( 'hide' );
	},
	addressFieldSelector: '#msb-address',
	buttonModalSelector: '#msb-share',
	memorizeFieldId: 'msb-memorize-instance',
};

/* Mastodon Share Modal */
jQuery( '#mastodonShareModal' ).on( 'shown.bs.modal', function () {
	jQuery( '#msb-address' ).trigger( 'focus' );
} );

function msbShareButtonAction( name, target ) {
	let msbInstanceAddress = '';

	msbInstanceAddress = msbGetCookie( 'mastodon-instance-address' );
	if ( msbInstanceAddress.length > 0 ) {
		window.open(
			`${ msbInstanceAddress }/share?text=${ name }%20${ target }`,
			`__blank`
		);
	} else if (
		msbConfig &&
		msbConfig.openModal &&
		msbConfig.addressFieldSelector
	) {
		if ( document.querySelector( msbConfig.buttonModalSelector ) ) {
			const bms = document.querySelector( msbConfig.buttonModalSelector );
			bms.data = { target, name };
			bms.addEventListener( 'click', () => msbOnShare(), false );
		}
		msbConfig.openModal( name, target );
	}
}

function msbOnShare( _name, _target ) {
	if (
		msbConfig &&
		msbConfig.addressFieldSelector &&
		msbConfig.buttonModalSelector
	) {
		const name = !! _name
			? _name
			: document.querySelector( msbConfig.buttonModalSelector ).data.name;
		const target = !! _target
			? _target
			: document.querySelector( msbConfig.buttonModalSelector ).data
					.target;
		let msbInstanceAddress = document.querySelector(
			`${ msbConfig.addressFieldSelector }`
		).value;

		if ( ! msbInstanceAddress.startsWith( 'http' ) ) {
			msbInstanceAddress = 'https://' + msbInstanceAddress;
		}
		if ( msbInstanceAddress.match( URL_REGEX ) ) {
			if ( msbConfig.memorizeFieldId ) {
				const msbMemorizeIsChecked = document.querySelector(
					`#${ msbConfig.memorizeFieldId }`
				).checked;
				if (
					msbConfig.memorizeFieldId &&
					! msbGetCookie( COOKIE_NAME ).length > 0 &&
					msbMemorizeIsChecked
				) {
					msbSetCookie( COOKIE_NAME, msbInstanceAddress, 7 );
				}
			}

			window.open(
				`${ msbInstanceAddress }/share?text=${ name }%20${ target }`,
				`__blank`
			);
			if ( msbConfig && msbConfig.openModal && msbConfig.closeModal ) {
				msbConfig.closeModal();
			}
		}
	}
}

function msbGetCookie( cname ) {
	const name = cname + '=';
	const ca = document.cookie.split( ';' );
	for ( let i = 0; i < ca.length; i++ ) {
		let c = ca[ i ];
		while ( c.charAt( 0 ) === ' ' ) {
			c = c.substring( 1 );
		}
		if ( c.indexOf( name ) === 0 ) {
			return c.substring( name.length, c.length );
		}
	}
	return '';
}

function msbSetCookie( name, value, days ) {
	const d = new Date();
	d.setTime( d.getTime() + days * 86400000 );
	const expires = 'expires=' + d.toUTCString();
	document.cookie = `${ name }=${ value }; ${ expires }; path=/`;
}

( function () {
	const msbButtons = document.querySelectorAll( '.mastodon-share-button' );

	for ( let i = 0; i < msbButtons.length; i++ ) {
		( function ( j ) {
			const msbTarget = msbButtons[ j ].dataset.target;
			let msbName = msbButtons[ j ].dataset.name;

			// Replace hashtab by html code
			msbName = msbName.replace( /#/g, '%23' );

			/**
			 * Set the listener in each button
			 */
			msbButtons[ j ].addEventListener(
				'click',
				() => {
					msbShareButtonAction( msbName, msbTarget );
				},
				true
			);
		} )( i );
	}
} )();
/* eslint-enable no-undef */
