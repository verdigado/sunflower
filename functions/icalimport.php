<?php

include WP_CONTENT_DIR . '/themes/sunflower/lib/vendor/autoload.php';

use Sabre\VObject\ParseException;
use Sabre\VObject\Reader;

function sunflower_icalimport($url = false, $auto_categories = false)
{
    try {
        $vcalendar = Reader::read(
            file_get_contents($url),
            Reader::OPTION_FORGIVING
        );
    } catch (ParseException $parseException) {
        // Rethrow the exception to abstract the type
        return $parseException->getMessage();
    }

    $timezone = new \DateTimeZone(get_option('timezone_string'));

    $time_range_history = sunflower_get_constant('SUNFLOWER_EVENT_TIME_RANGE_BACK') ?: '0 months';
    $time_range_future = sunflower_get_constant('SUNFLOWER_EVENT_TIME_RANGE') ?: '6 months';
    $recurring_events_max = (int) sunflower_get_constant('SUNFLOWER_EVENT_RECURRING_EVENTS') ?: 10;

    $timeRangeStart = new \DateTime();
    $timeRangeStart->setTimestamp(strtotime('-' . $time_range_history));

    $timeRangeStop = new \DateTime();
    $timeRangeStop->setTimestamp(strtotime((string) $time_range_future));

    // expand RRULE events to new vCalendar which has all events in the given time range
    $newVCalendar = $vcalendar->expand($timeRangeStart, $timeRangeStop);

    $allEvents = [];
    if (is_iterable($newVCalendar->VEVENT)) {
        foreach ($newVCalendar->VEVENT as $event) {
            // limit to events in the given time range
            if ($event->isInTimeRange($timeRangeStart, $timeRangeStop)) {
                $allEvents[] = $event;
            };
        }
    }

    $updated_events = 0;
    $ids_from_remote = [];
    $count_recurring_events = [];

    foreach ($allEvents as $allEvent) {
        $uid = $allEvent->UID->getValue();

        // modified instances of a recurring event, RECURRENCE-ID is set but no RRULE
        if (isset($allEvent->RRULE) || isset($allEvent->{'RECURRENCE-ID'})) {
            if (isset($count_recurring_events[$uid])) {
                ++$count_recurring_events[$uid];
            } else {
                $count_recurring_events[$uid] = 1;
            }

            if ($count_recurring_events[$uid] > $recurring_events_max) {
                continue;
            }

            $uid .= '_' . $allEvent->DTSTART->getValue();
        }

        // is this event already imported
        $is_imported = sunflower_get_event_by_uid($uid);
        $wp_id = 0;
        if ($is_imported->have_posts()) {
            $is_imported->the_post();
            $wp_id = get_the_ID();
            ++$updated_events;
        }

        $post_content = sprintf('<!-- wp:paragraph --><p>%s</p><!-- /wp:paragraph -->', nl2br((string) $allEvent->DESCRIPTION));

        if (isset($allEvent->URL) && filter_var((string) $allEvent->URL, FILTER_VALIDATE_URL)) {
            $post_content .= sprintf('<!-- wp:paragraph --><p>%1$s: <a href="%1$s" target="_blank">%1$s</a></p><!-- /wp:paragraph -->', __('More Information', 'sunflower'), (string) $allEvent->URL);
        }

        $post = [
            'ID' => $wp_id,
            'post_type' => 'sunflower_event',
            'post_title' => $allEvent->SUMMARY->getValue(),
            'post_content' => $post_content,
            'post_status' => 'publish',
        ];
        $id = wp_insert_post((array) $post, true);
        if (!is_int($id)) {
            echo 'Could not copy post';
            return false;
        }

        // save all event post ids from imported ics ressources
        $ids_from_remote[] = $id;

        $timezoneFix = null;
        if (sunflower_get_setting('sunflower_fix_time_zone_error')) {
            $timezoneFix = $timezone;
        }

        // write start and end time to event post metadata
        update_post_meta($id, '_sunflower_event_from', $allEvent->DTSTART->getDateTime($timezoneFix)->setTimezone($timezone)->format('Y-m-d H:i'));
        update_post_meta($id, '_sunflower_event_until', $allEvent->DTEND->getDateTime($timezoneFix)->setTimezone($timezone)->format('Y-m-d H:i'));
        update_post_meta($id, '_sunflower_event_uid', $uid);

        if (isset($allEvent->LOCATION)) {
            update_post_meta($id, '_sunflower_event_location_name', (string) $allEvent->LOCATION);

            if (!filter_var((string) $allEvent->LOCATION, FILTER_VALIDATE_URL)) {
                $coordinates = sunflower_geocode((string) $allEvent->LOCATION);
                if ($coordinates) {
                    [$lon, $lat] = $coordinates;
                    update_post_meta($id, '_sunflower_event_lat', $lat);
                    update_post_meta($id, '_sunflower_event_lon', $lon);
                    $zoom = sunflower_get_constant('SUNFLOWER_EVENT_IMPORTED_ZOOM') ?: 12;
                    update_post_meta($id, '_sunflower_event_zoom', $zoom);
                }
            }
        }

        $categories = $allEvent->CATEGORIES ?? '';
        $categories .= ($auto_categories) ? ',' . $auto_categories : '';
        if ($categories === '') {
            continue;
        }

        if ($categories === '0') {
            continue;
        }

        wp_set_post_terms($id, $categories, 'sunflower_event_tag');
    }

    return [$ids_from_remote, count($allEvents) - $updated_events, $updated_events];
}

function sunflower_get_event_by_uid($uid)
{
    return new WP_Query(
        [
            // 'paged' => $paged,
            // 'nopaging'        => true,
            'post_type' => 'sunflower_event',
            'meta_key' => '_sunflower_event_uid',
            'orderby' => 'meta_value',
            'meta_query' => [[
                'key' => '_sunflower_event_uid',
                'value' => $uid,
                'compare' => '=',
            ]],
        ]
    );
}

function sunflower_get_events_having_uid()
{
    $events_with_uid = new WP_Query(
        [
            // 'paged' => $paged,
            'nopaging' => true,
            'post_type' => 'sunflower_event',
            'meta_key' => '_sunflower_event_uid',
            'orderby' => 'meta_value',
            'meta_query' => [[
                'key' => '_sunflower_event_uid',
                'compare' => 'EXISTS',
            ]],
        ]
    );

    $ids = [];
    while ($events_with_uid->have_posts()) {
        $events_with_uid->the_post();
        $ids[] = get_the_ID();
    }

    return $ids;
}

add_action('init', 'sunflower_import_icals');
function sunflower_import_icals($force = false)
{
    if (!$force && get_transient('sunflower_ical_imported')) {
        return false;
    }

    if (!sunflower_get_setting('sunflower_ical_urls')) {
        return false;
    }

    $import_every_n_hour = sunflower_get_constant('SUNFLOWER_EVENT_IMPORT_EVERY_N_HOUR') ?: 3;
    set_transient('sunflower_ical_imported', 1, $import_every_n_hour * 3600);

    $lines = explode("\n", (string) sunflower_get_setting('sunflower_ical_urls'));

    $ids_from_remote = [];
    foreach ($lines as $line) {
        $info = explode(';', $line);

        $url = trim($info[0]);
        $auto_categories = $info[1] ?? false;

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            continue;
        }

        $response = sunflower_icalimport($url, $auto_categories);
        if (!empty($response)) {
            $ids_from_remote = array_merge($ids_from_remote, $response[0]);
        }
    }

    $deleted_on_remote = array_diff(sunflower_get_events_having_uid(), $ids_from_remote);

    foreach ($deleted_on_remote as $to_be_deleted) {
        wp_delete_post($to_be_deleted);
    }
}

function sunflower_geocode($location)
{
    static $i = 0;
    $transient = sprintf('sunflower_geocache_%s', $location);

    if ($cached = get_transient($transient)) {
        return $cached;
    }

    if ($i > 3) {
        // download 3 geodata per import
        return false;
    }

    $url = sprintf('https://nominatim.openstreetmap.org/search?q=%s&format=geocodejson', urlencode((string) $location));
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => "Accept-language: en\r\n" .
                            "user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.96 Safari/537.36\r\n",
        ],
    ];
    $context = stream_context_create($opts);

    $json = json_decode(file_get_contents($url, false, $context));

    $lonlat = isset($json->features[0]) ? $json->features[0]->geometry->coordinates : false;

    ++$i;

    set_transient($transient, $lonlat);

    return $lonlat;
}
