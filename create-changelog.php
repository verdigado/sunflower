<?php

$output = '<!DOCTYPE html>
<html lang="de-DE">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Changelog Sunflower Theme</title>
</head>

<body>
<h1>Changelogs</h1>
';

exec( 'git tag -l --sort=-creatordate --format="%(creatordate:short);%(refname:short)" | head -n 10', $tags );

$output .= read_commits_between_tags( array( '', 'HEAD' ), explode( ';', $tags[0] ) );

for ( $i = 0; $i < count( $tags ) - 1; ++$i ) {
	$output .= read_commits_between_tags( explode( ';', $tags[ $i ] ), explode( ';', $tags[ $i + 1 ] ) );
}

$output .= '</body>
</html>
';

file_put_contents( 'changelog.html', $output );
echo '..done';

function read_commits_between_tags( $from, $to ) {
	global $argv;
	exec( sprintf( 'git log --pretty=format:"%%s" %s...%s', $from[1], $to[1] ), $commits );

	if ( $from[1] === 'HEAD' ) {
		$from_str = $argv[1] ?? 'der neuesten Version';
		$time     = date( 'Y-m-d' );
	} else {
		$from_str = $from[1];
		$time     = $from[0];
	}

	return sprintf( "<h2>Neu in %s (%s)</h2>\n<ul>\n%s</ul>\n\n", $from_str, $time, add_commit_messages( $commits ) );
}

function add_commit_messages( $commits ) {
	$return = '';

	foreach ( $commits as $commit ) {
		if ( preg_match( '/^publishing|^Bump|^Merge /', (string) $commit ) ) {
			continue;
		}

		$return .= sprintf( "<li>%s</li>\n", $commit );
	}

	return $return;
}
