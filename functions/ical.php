<?php

$from = getIcalDate(strToTime($_sunflower_event_from), !$_sunflower_event_whole_day);
$until = ($_sunflower_event_until) ? getIcalDate(strToTime($_sunflower_event_until), !$_sunflower_event_whole_day) : getIcalDate(3600 + strToTime($_sunflower_event_from), !$_sunflower_event_whole_day);

$now = getIcalDate(strToTime('now'), true);
$summary = get_the_title();
$proid = parse_url(get_bloginfo('url'), PHP_URL_HOST);
$uid = md5(uniqid(mt_rand(), true)) . '@' . $proid;
$description = get_the_excerpt();
$filename = preg_replace('/[^a-zA-Z0-9]/','-',$summary) . '.ics';

$ical=<<<ICAL
BEGIN:VCALENDAR\r
VERSION:2.0\r
PRODID:$proid\r
METHOD:PUBLISH\r
BEGIN:VEVENT\r
UID:$uid\r
LOCATION:$_sunflower_event_location_name\r
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
//header('Content-Length: '.$Groesse );

echo $ical;
die();

function getIcalDate($timestamp, $withTime = false){
	return date('Ymd' . ($withTime ? '\THis\Z' : ''), $timestamp);
}