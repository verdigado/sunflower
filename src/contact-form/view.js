const sunflowerForm = document.getElementById( 'sunflower-contact-form' );
sunflowerForm.addEventListener( 'submit', disableButton );

/**
 * Disable submit button after first submit
 */
function disableButton() {
	const button = document.getElementById( 'submit' );
	button.disabled = true;
	button.style.opacity = 0.5;
}
