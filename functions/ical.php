<?php
$_sunflower_event_from = @get_post_meta( $post->ID, '_sunflower_event_from')[0] ?: false;
$_sunflower_event_from = strToTime($_sunflower_event_from);
$_sunflower_event_until = @get_post_meta( $post->ID, '_sunflower_event_until')[0] ?: false;
$_sunflower_event_until = strToTime($_sunflower_event_until);
$_sunflower_event_whole_day = @get_post_meta( $post->ID, '_sunflower_event_whole_day')[0] ?: false;
$_sunflower_event_location_name = @get_post_meta( $post->ID, '_sunflower_event_location_name')[0] ?: false;
$_sunflower_event_location_street = @get_post_meta( $post->ID, '_sunflower_event_location_street')[0] ?: false;
$_sunflower_event_location_city = @get_post_meta( $post->ID, '_sunflower_event_location_city')[0] ?: false;





$from = getIcalDate($_sunflower_event_from, !$_sunflower_event_whole_day);
$until = ($_sunflower_event_until) ? getIcalDate($_sunflower_event_until, !$_sunflower_event_whole_day) : getIcalDate(3600 + $_sunflower_event_from, !$_sunflower_event_whole_day);

$now = getIcalDate(strToTime('now'), true);
$summary = get_the_title();
$proid = parse_url(get_bloginfo('url'), PHP_URL_HOST);
$uid = md5(uniqid(mt_rand(), true)) . '@' . $proid;
$description = get_the_excerpt();
$filename = preg_replace('/[^a-zA-Z0-9]/','-',$summary) . '.ics';
$location = join(', ', array_diff([
	$_sunflower_event_location_name, 
	$_sunflower_event_location_street, 
	$_sunflower_event_location_city	], 
	[false]));

$ical=<<<ICAL
BEGIN:VCALENDAR\r
VERSION:2.0\r
PRODID:$proid\r
METHOD:PUBLISH\r
BEGIN:VEVENT\r
UID:$uid\r
LOCATION:$location\r
SUMMARY:$summary\r
DESCRIPTION:$description\r
CLASS:PUBLIC\r
DTSTART:$from\r
DTEND:$until\r
DTSTAMP:$now\r
END:VEVENT\r
END:VCALENDAR\r
ICAL;

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=\"{$filename}\";");
header("Content-Description: File Transfer");
header("Content-Transfer-Encoding: binary");

echo $ical;
die();

function getIcalDate($timestamp, $withTime = false){
	return date('Ymd' . ($withTime ? '\THis' : ''), $timestamp);
}