/* --------------------------------------------------------------
   Sunflower – Automatisch dunkles Design
   -------------------------------------------------------------- */
( () => {
	// Inhibit automatic dark mode when design switcher sets colorscheme to a value other than "auto"
	const inhibitAutoDark = () => {
		const raw = localStorage.getItem( 'sunflower_design' );
		try {
			return JSON.parse( raw ).colorscheme !== 'auto';
		} catch ( e ) {
			return false;
		}
	};

	const updateAutoDark = () => {
		if ( inhibitAutoDark() ) {
			return;
		}
		const body = document.body;
		const darkModeMq =
			window.matchMedia &&
			window.matchMedia( '(prefers-color-scheme: dark)' );
		const colorscheme =
			( darkModeMq && darkModeMq.matches && 'green' ) || 'light';
		body.classList.remove( 'colorscheme-light', 'colorscheme-green' );
		body.classList.add( `colorscheme-${ colorscheme }` );
	};

	const initAutoDark = () => {
		updateAutoDark();
		const darkModeMq =
			window.matchMedia &&
			window.matchMedia( '(prefers-color-scheme: dark)' );
		if ( darkModeMq ) {
			darkModeMq.addEventListener( 'change', updateAutoDark );
		}
	};

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAutoDark );
	} else {
		initAutoDark();
	}
} )();
