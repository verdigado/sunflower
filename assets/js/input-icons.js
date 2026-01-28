document.addEventListener( 'DOMContentLoaded', function () {
	const iconMap = {
		email: 'fa-solid fa-envelope',
	};

	const inputs = document.querySelectorAll( 'input[type="email"]' );

	inputs.forEach( function ( input ) {
		const type = input.getAttribute( 'type' );
		const iconClass = iconMap[ type ];

		const wrapper = document.createElement( 'div' );
		wrapper.classList.add( 'input-with-icon' );

		const icon = document.createElement( 'i' );
		icon.className = `forkawesome ${ iconClass }`;

		input.parentNode.insertBefore( wrapper, input );
		wrapper.appendChild( icon );
		wrapper.appendChild( input );
	} );
} );
