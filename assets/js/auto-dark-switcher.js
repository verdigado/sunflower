/* --------------------------------------------------------------
   Sunflower – Automatisch dunkles Design
   -------------------------------------------------------------- */
( () => {

	const updateAutoDark = () => {
		const body = document.body;
		const darkModeMq = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)');
		const colorscheme = darkModeMq && darkModeMq.matches && 'green' || 'light';
		body.classList.remove( 'colorscheme-light', 'colorscheme-green', 'colorscheme-auto' );
		body.classList.add( `colorscheme-${ colorscheme }` );
	};

	const initAutoDark = () => {
		updateAutoDark();
		const darkModeMq = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)');
		if (darkModeMq) {
			darkModeMq.addEventListener('change', event => { updateAutoDark(); } );
		}
	};

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAutoDark );
	} else {
		initAutoDark();
	}
} )();
