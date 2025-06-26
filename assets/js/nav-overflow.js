document.addEventListener( 'DOMContentLoaded', function () {
	const navWrapper = document.querySelector( '.right-bar' );
	const menuUl = document.querySelector( '#menu-neues-hauptmenue' );

	if ( ! navWrapper || ! menuUl ) {
		return;
	}

	// Create the "More" menu item if it doesn't exist
	let moreMenu = menuUl.querySelector( '.more-menu' );
	if ( ! moreMenu ) {
		moreMenu = document.createElement( 'li' );
		moreMenu.className = 'menu-item more-menu position-relative';
		moreMenu.innerHTML = `
      <a href="#">More</a>
      <ul class="sub-menu position-absolute bg-white border p-2 mt-2" style="display:none;"></ul>
    `;
		menuUl.appendChild( moreMenu );
	}

	const moreSubMenu = moreMenu.querySelector( '.sub-menu' );

	// Bind hover events only once
	let hoverBound = false;
	function bindHover() {
		if ( hoverBound ) {
			return;
		}
		moreMenu.addEventListener( 'mouseenter', () => {
			moreSubMenu.style.display = 'block';
		} );
		moreMenu.addEventListener( 'mouseleave', () => {
			moreSubMenu.style.display = 'none';
		} );
		hoverBound = true;
	}

	function adjustMenu() {
		// Move all items back from "More" submenu to main menu
		while ( moreSubMenu.firstChild ) {
			menuUl.insertBefore( moreSubMenu.firstChild, moreMenu );
		}

		moreMenu.style.display = 'none';
		moreSubMenu.style.display = 'none';

		const wrapperWidth = Math.max( 0, navWrapper.offsetWidth - 286 );
		let usedWidth = 0;
		let firstOverflowIndex = null;

		const menuItems = Array.from( menuUl.children ).filter(
			( li ) => li !== moreMenu
		);

		// Determine the first item that doesn't fit
		for ( let i = 0; i < menuItems.length; i++ ) {
			const itemWidth = menuItems[ i ].offsetWidth;
			if ( usedWidth + itemWidth + moreMenu.offsetWidth > wrapperWidth ) {
				firstOverflowIndex = i;
				break;
			}
			usedWidth += itemWidth;
		}

		// Move all overflowing items into the "More" submenu
		if ( firstOverflowIndex !== null ) {
			moreMenu.style.display = 'block';
			bindHover();
			for ( let i = firstOverflowIndex; i < menuItems.length; i++ ) {
				moreSubMenu.appendChild( menuItems[ i ] );
			}
		}

		// Hide "More" button if it's empty
		if ( moreSubMenu.children.length === 0 ) {
			moreMenu.style.display = 'none';
		}
	}

	window.addEventListener( 'resize', adjustMenu );
	adjustMenu();
} );
