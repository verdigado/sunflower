<?php
// phpcs:disable Generic.Arrays.DisallowLongArraySyntax

require_once ABSPATH . '/wp-content/themes/sunflower/assets/vndr/johngrogg/ics-parser/src/ICal/Event.php';
require_once ABSPATH . '/wp-content/themes/sunflower/assets/vndr/johngrogg/ics-parser/src/ICal/ICal.php';

use ICal\ICal;

function sunflower_icalimport(){
    try {
        $ical = new ICal('ICal.ics', array(
            'defaultSpan'                 => 2,     // Default value
            'defaultTimeZone'             => 'CET',
            'defaultWeekStart'            => 'MO',  // Default value
            'disableCharacterReplacement' => false, // Default value
            'filterDaysAfter'             => null,  // Default value
            'filterDaysBefore'            => null,  // Default value
            'skipRecurrence'              => false, // Default value
        ));
         $ical->initFile(ABSPATH . '/wp-content/themes/sunflower/functions/ical-test2.ics');
        //$ical->initUrl('https://raw.githubusercontent.com/u01jmg3/ics-parser/master/examples/ICal.ics', $username = null, $password = null, $userAgent = null);
    } catch (\Exception $e) {
        die($e);
    }

    $time_range = '100 week'; 
    $events = $ical->eventsFromInterval($time_range);

    if ($events) {
        echo '<h4>Events in the next ' . $time_range .':</h4>';
    }

    printf('%d %s', count($events), __(' events were imported', 'sunflower'));

    foreach ($events as $event){
    
        $post = array(
            'post_type'     => 'sunflower_event',
            'post_title'    => $event->summary,
            'post_content'  => sprintf('<!-- wp:paragraph -->%s<!-- /wp:paragraph -->', nl2br($event->description)),
            'post_status'   => 'publish'

        );
        $id = wp_insert_post((array) $post, true);
        if(!is_int($id)){
            echo "Could not copy post";
            return false;
        }

        update_post_meta( $id, '_sunflower_event_from', date('Y-m-d H:i', $ical->iCalDateToUnixTimestamp($event->dtstart_tz )));
        update_post_meta( $id, '_sunflower_event_until', date('Y-m-d H:i', $ical->iCalDateToUnixTimestamp($event->dtend_tz )));
        update_post_meta( $id, '_sunflower_event_location_name', $event->location);

    }
}
