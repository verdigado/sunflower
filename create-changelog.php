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

exec( 'git tag', $tags );
$tags = array_reverse( $tags );

$output .= read_commits_between_tags( 'HEAD', $tags[0] );

for ( $i = 0; $i < count( $tags ); $i++ ) {

	$output .= read_commits_between_tags( $tags[ $i ], $tags[ $i + 1 ] );

	if ( $tags[ $i ] == 'v1.0.0' ) {
		break;
	}
}

$output .= '</body>
</html>
';

file_put_contents( 'changelog.html', $output );
echo '..done';


function read_commits_between_tags( $from, $to ) {
	global $argv;
	exec( sprintf( 'git log --pretty=format:"%%s" %s...%s', $from, $to ), $commits );

	if ( $from === 'HEAD' ) {
		$from = ( isset( $argv[1] ) ) ? $argv[1] : 'der neuesten Version';
	}

	return sprintf( "<h2>Neu in %s</h2>\n<ul>%s</ul>\n\n", $from, add_commit_messages( $commits ) );
}


function add_commit_messages( $commits ) {
	$return = '';

	foreach ( $commits as $commit ) {
		if ( preg_match( '/^publishing|^Bump|^Merge /', $commit ) ) {
			continue;
		}

		$return .= sprintf( "<li>%s</li>\n", $commit );
	}
	return $return;
}
