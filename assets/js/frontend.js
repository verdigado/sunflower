// get the sticky element
const stickyDetector = document.querySelector('#navbar-sticky-detector');
const stickyElement = document.querySelector('.navbar-main');


const observer = new IntersectionObserver(
  ([e]) => {
    stickyElement.classList.toggle('stuck', e.intersectionRatio < 1);

    let h = jQuery('.navbar-main').height();
    if (jQuery('body.admin-bar').length) {
      h += 32;
    }

    if (e.intersectionRatio < 1) {
      jQuery('#content').css('margin-top', h);
    } else {
      jQuery('#content').css('margin-top', 0);
    }

  },
  { threshold: [0, 1] }
);

observer.observe(stickyDetector);

jQuery(document).ready(function () {
  jQuery('.show-leaflet').click(function () {
    const lat = jQuery('.show-leaflet').data('lat');
    const lon = jQuery('.show-leaflet').data('lon');
    const zoom = jQuery('.show-leaflet').data('zoom');

    showLeaflet(lat, lon, zoom);
  })

  jQuery('#privacy_policy_url').attr('href', sunflower.privacy_policy_url);

  jQuery('.show-search').click(function () {
    jQuery('.topmenu .search input').toggleClass('active');
    jQuery('.topmenu .search input').focus();
  })

  jQuery('.show-contrast').click(function () {
    jQuery('html').toggleClass('theme--contrast');
    jQuery('html').toggleClass('theme--default');
    localStorage.setItem('theme--contrast', jQuery('html').hasClass('theme--contrast'));
  })
  if (localStorage.getItem('theme--contrast') === 'true') {
    jQuery('html').addClass('theme--contrast');
    jQuery('html').removeClass('theme--default');
  }


  adjustMetaboxHeight();

  jQuery('[data-unscramble]').click(function () {
    let text = jQuery(this).data('unscramble').split('').reverse().join('');
    window.location.href = "MAILTO:" + text;

    return false;
  })

  jQuery('.wp-block-gallery figure').each(function(){
    let caption = jQuery('figcaption', this).text();
    jQuery('a', this).first().attr('data-lightbox','sunflower-gallery');
    jQuery('a', this).first().attr('data-title', caption);
  })

    lightbox.option({
      'albumLabel': 'Bild %1 von %2'
    })


});

function getIcon() {
  return L.icon({
    iconUrl: sunflower.maps_marker,
    iconSize: [25, 41], // size of the icon
    shadowSize: [0, 0], // size of the shadow
    iconAnchor: [12, 41], // point of the icon which will correspond to marker's location
    shadowAnchor: [0, 0],  // the same for the shadow
    popupAnchor: [0, -41] // point from which the popup should open relative to the iconAnchor
  });
}

function showLeaflet(lat, lon, zoom) {
  const leaflet = L.map('leaflet').setView([lat, lon], zoom);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
  }).addTo(leaflet);

  const marker = L.marker([lat, lon], { icon: getIcon() }).addTo(leaflet);
};

jQuery('.show-leaflet-all').click(showLeafletAll);
function showLeafletAll() {
  const leaflet = L.map('leaflet').setView([map.center.lat, map.center.lon], map.center.zoom);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
  }).addTo(leaflet);

  map.marker.forEach(marker => L.marker([marker.lat, marker.lon], { icon: getIcon() }).addTo(leaflet).bindPopup(marker.content));

};

jQuery('#sunflower-contact-form').on('submit', function (e) {
  e.preventDefault();

  jQuery.ajax({
    url: sunflower.ajaxurl,
    method: "POST",
    data: {
      action: 'sunflower_contact_form',
      message: jQuery('#message').val(),
      name: jQuery('#name').val(),
      mail: jQuery('#mail').val(),
      captcha: jQuery('#captcha').val(),


    },
  }).done(function (response) {
    response = JSON.parse(response);

    if (response.code == 500) {
      jQuery('#sunflower-contact-form').append('<div class="bg-danger p-4 text-white">' + response.text + '</div>');
      return;
    }

    jQuery('#sunflower-contact-form').html(response.text);
  }

  );

  return false;
});

function adjustMetaboxHeight() {
  if (!jQuery(".metabox").length) {
    return;
  }

  const tooBig = jQuery('.metabox').outerHeight() - jQuery('.entry-header').outerHeight();

  if (tooBig <= 0) {
    return;
  }

  jQuery(".entry-content").prepend('<div class="metabox-spacer"></div>');

  jQuery(".metabox-spacer").height(tooBig + 'px');

}
