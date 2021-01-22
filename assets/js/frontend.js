// get the sticky element
const stickyDetector = document.querySelector( '#navbar-sticky-detector' );
const stickyElement = document.querySelector( '.navbar-main' );


const observer = new IntersectionObserver(
  ( [e] ) => stickyElement.classList.toggle( 'stuck', e.intersectionRatio < 1 ),
  {threshold: [ 0, 1 ] }
);

observer.observe( stickyDetector );
