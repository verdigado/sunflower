<?php

$from = getIcalDate($_sunflower_event_from);
	$until = getIcalDate($_sunflower_event_until);
	$now = getIcalDate('now');
	$summary = get_the_title();
	$proid = parse_url(get_bloginfo('url'), PHP_URL_HOST);
	$uid = md5(uniqid(mt_rand(), true)) . '@' . $proid;
	$description = get_the_excerpt();
	$filename = preg_replace('/[^a-zA-Z0-9]/','-',$summary) . '.ics';

$ical=<<<ICAL
BEGIN:VCALENDAR
VERSION:2.0
PRODID:$proid
METHOD:PUBLISH
BEGIN:VEVENT
UID:$uid
LOCATION:$_sunflower_event_location_name
SUMMARY:$summary
DESCRIPTION:$description
CLASS:PUBLIC
DTSTART:$from
DTEND: $until
DTSTAMP: $now
END:VEVENT
END:VCALENDAR
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