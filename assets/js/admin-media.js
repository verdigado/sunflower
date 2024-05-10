/* eslint-disable no-undef */
jQuery( function ( $ ) {
	// Images
	let fileFrameFieldId;

	// limit to one image only
	const fileFrame = ( wp.media.frames.fileFrame = wp.media( {
		title: texts.select_image,
		multiple: false,
	} ) );

	fileFrame.on( 'select', function () {
		const image = fileFrame.state().get( 'selection' ).first().toJSON();
		$( '#' + fileFrameFieldId ).val( image.url );
	} );

	// open media library
	$( '#sunflower_open_graph_fallback_image_button' ).on(
		'click',
		function ( event ) {
			event.preventDefault();
			fileFrameFieldId = 'sunflower_open_graph_fallback_image';
			if ( fileFrame ) {
				fileFrame.open();
			}
		}
	);
} );
/* eslint-enable no-undef */
