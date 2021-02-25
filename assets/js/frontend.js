// get the sticky element
const stickyDetector = document.querySelector( '#navbar-sticky-detector' );
const stickyElement = document.querySelector( '.navbar-main' );


const observer = new IntersectionObserver(
  ( [e] ) => {
    stickyElement.classList.toggle( 'stuck', e.intersectionRatio < 1 );

    const h =  jQuery('.navbar-main').height();
    if(e.intersectionRatio < 1) {
      jQuery('#content').css('margin-top', h);
    }else{
      jQuery('#content').css('margin-top', 0);
    }

  },
  {threshold: [ 0, 1 ] }
);

observer.observe( stickyDetector );

jQuery(document).ready( function (){
  jQuery('.show-leaflet').click( function(){
    const lat = jQuery('.show-leaflet').data('lat');
    const lon = jQuery('.show-leaflet').data('lon');
    const zoom = jQuery('.show-leaflet').data('zoom');

    showLeaflet(lat, lon, zoom);
  })
});


function showLeaflet(lat, lon, zoom){
  const leaflet = L.map('leaflet').setView([lat, lon], zoom);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
  }).addTo(leaflet);

  const marker = L.marker([lat, lon]).addTo(leaflet);
};
