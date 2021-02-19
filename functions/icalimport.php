<?php
// phpcs:disable Generic.Arrays.DisallowLongArraySyntax

require_once ABSPATH . '/wp-content/themes/sunflower/assets/vndr/johngrogg/ics-parser/src/ICal/Event.php';
require_once ABSPATH . '/wp-content/themes/sunflower/assets/vndr/johngrogg/ics-parser/src/ICal/ICal.php';

use ICal\ICal;

function sunflower_icalimport( $url = false){
    try {
        $ical = new ICal('ICal.ics', array(
            'defaultSpan'                 => 2,     // Default value
            'defaultTimeZone'             => 'GMT',
            'defaultWeekStart'            => 'MO',  // Default value
            'disableCharacterReplacement' => false, // Default value
            'filterDaysAfter'             => null,  // Default value
            'filterDaysBefore'            => null,  // Default value
            'skipRecurrence'              => false, // Default value
        ));
         //$ical->initFile(ABSPATH . '/wp-content/themes/sunflower/functions/ical-test2.ics');

        $ical->initUrl($url, $username = null, $password = null, $userAgent = null);
    } catch (\Exception $e) {
        die($e);
    }

    $time_range = '6 months'; 
    $events = $ical->eventsFromInterval($time_range);

    $updated_events = 0;
    $ids_from_remote = array();
    foreach ($events as $event){

        // is this event already imported
        $is_imported = sunflower_get_event_by_uid( $event->uid );
        $wp_id = 0;
        if ( $is_imported->have_posts() ){
            $is_imported->the_post();
            $wp_id = get_the_ID();
            $updated_events++;
        }

        $post = array(
            'ID'            => $wp_id,
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

        $ids_from_remote[] = $id;

        update_post_meta( $id, '_sunflower_event_from', date('Y-m-d H:i', $ical->iCalDateToUnixTimestamp($event->dtstart_tz )));
        update_post_meta( $id, '_sunflower_event_until', date('Y-m-d H:i', $ical->iCalDateToUnixTimestamp($event->dtend_tz )));
        update_post_meta( $id, '_sunflower_event_location_name', $event->location);
        update_post_meta( $id, '_sunflower_event_uid', $event->uid);
    }

    return [$ids_from_remote, count($events) - $updated_events, $updated_events];

}

function sunflower_get_event_by_uid( $uid ){
    return new WP_Query(array(
        //'paged' => $paged,
        //'nopaging'		=> true,
        'post_type'     => 'sunflower_event',
        'meta_key' 	    => '_sunflower_event_uid', 
        'orderby'       => 'meta_value',
        'meta_query'    => array(
                array(
                    'key' => '_sunflower_event_uid',
                    'value' => $uid,
                    'compare' => '='
                ),
            )
    ));
}

function sunflower_get_events_having_uid( ){
    $events_with_uid = new WP_Query(array(
        //'paged' => $paged,
        'nopaging'		=> true,
        'post_type'     => 'sunflower_event',
        'meta_key' 	    => '_sunflower_event_uid', 
        'orderby'       => 'meta_value',
        'meta_query'    => array(
                array(
                    'key' => '_sunflower_event_uid',
                    'compare' => 'EXISTS'
                ),
            )
    ));

    $ids = array();
    while ( $events_with_uid->have_posts() ){
        $events_with_uid->the_post();
        $ids[] = get_the_ID();
    }

    return $ids;
}

add_action('init', 'sunflower_import_icals');
function sunflower_import_icals() {

    if( get_transient( 'sunflower_ical_imported' ) ){
        return false;
    }

    if( !get_sunflower_setting('sunflower_ical_urls') ){
        return false;
    }

    set_transient( 'sunflower_ical_imported', 1, 3 * 3600 );

    $urls = explode("\n", get_sunflower_setting('sunflower_ical_urls'));
    $ids_from_remote = array();
    foreach($urls AS $url){
        $url = trim($url);
        if(!filter_var($url, FILTER_VALIDATE_URL)){
            continue;
        }
    
       $response = sunflower_icalimport($url);
       $ids_from_remote = array_merge($ids_from_remote, $response[0]);
    }

    $deleted_on_remote = array_diff(sunflower_get_events_having_uid(), $ids_from_remote);

    foreach($deleted_on_remote AS $to_be_deleted){
        wp_delete_post($to_be_deleted);
    }
}
