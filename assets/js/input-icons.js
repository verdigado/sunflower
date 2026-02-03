document.addEventListener( 'DOMContentLoaded', function () {
	const iconMap = {
		email: 'fa-solid fa-envelope',
		url: 'fa-solid fa-globe',
		author: 'fa-solid fa-user',
	};

	const inputs = document.querySelectorAll(
		'input[type="email"], input[type="url"], input#author'
	);

	inputs.forEach( function ( input ) {
		const type = input.getAttribute( 'type' );
		const id = input.getAttribute( 'id' );
		// Für das Author-Feld (Name) verwenden wir die ID, da type="text" zu generisch ist
		const iconClass = id === 'author' ? iconMap.author : iconMap[ type ];

		if ( ! iconClass ) {
			return;
		}

		const wrapper = document.createElement( 'div' );
		wrapper.classList.add( 'input-with-icon' );

		const icon = document.createElement( 'i' );
		icon.className = `forkawesome ${ iconClass }`;

		input.parentNode.insertBefore( wrapper, input );
		wrapper.appendChild( icon );
		wrapper.appendChild( input );
	} );
} );
