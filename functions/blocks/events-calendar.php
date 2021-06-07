<?php

function sunflower_events_calendar_render( $block_attributes, $content ) {
	$events = array();
	foreach ( sunflower_get_next_events()->get_posts() as $post ) {
		$postMeta = get_post_meta( $post->ID );

		$event          = array();
		$event['title'] = $post->post_title;
		if ( $postMeta['_sunflower_event_from'][0] ) {
			$event['start'] = $postMeta['_sunflower_event_from'][0];
		}
		if ( $postMeta['_sunflower_event_until'][0] ) {
			$event['end'] = $postMeta['_sunflower_event_until'][0];
		}
		$event['url'] = get_post_permalink( $post );
		$events[]     = $event;
	}

	$classes    = ( isset( $block_attributes['className'] ) ) ? $block_attributes['className'] : '';
	$calendarId = 'calendar_' . mt_rand();

	return "
    <div id='$calendarId' class='$classes'></div>
    <script>

    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('$calendarId');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'de',
        themeSystem: 'bootstrap',
        validRange: function(nowDate) {
            var newStart = new Date(nowDate.getTime());
            newStart.setDate(1);
            return {
              start: newStart
            };
          },
        eventSources: [
          {
            events: " . json_encode( $events ) . ",
            backgroundColor: 'var(--bs-primary)',
          }
        ]
      });
      calendar.render();
    });
  </script>
  ";
}
