<?php
/**
 * Helper to generate a changelog.html
 *
 * @package sunflower
 */

$sunflower_output = '<!DOCTYPE html>
<html lang="de-DE">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Changelog Sunflower Theme</title>
</head>

<body>
<h1>Changelogs</h1>
';

exec( 'git tag -l --sort=-creatordate --format="%(creatordate:short);%(refname:short)" | head -n 10', $tags ); // phpcs:ignore

$sunflower_output .= sunflower_read_commits_between_tags( array( '', 'HEAD' ), explode( ';', $tags[0] ) );

$sunflower_count_tags = count( $tags );
for ( $sunflower_i = 0; $sunflower_i < $sunflower_count_tags - 1; ++$sunflower_i ) {
	$sunflower_output .= sunflower_read_commits_between_tags( explode( ';', $tags[ $sunflower_i ] ), explode( ';', $tags[ $sunflower_i + 1 ] ) );
}

$sunflower_output .= '</body>
</html>
';

file_put_contents( 'changelog.html', $sunflower_output ); // phpcs:ignore
echo '..done';

/**
 * Read the commit messages between two tags.
 *
 * @param string $from Read from tag.
 * @param string $to Read to tag.
 */
function sunflower_read_commits_between_tags( $from, $to ) {
	global $argv;
	exec( sprintf( 'git log --pretty=format:"%%s" %s...%s', $from[1], $to[1] ), $commits ); // phpcs:ignore

	if ( 'HEAD' === $from[1] ) {
		$from_str = $argv[1] ?? 'der neuesten Version';
		$time     = gmdate( 'Y-m-d' );
	} else {
		$from_str = $from[1];
		$time     = $from[0];
	}

	return sprintf( "<h2>Neu in %s (%s)</h2>\n<ul>\n%s</ul>\n\n", $from_str, $time, sunflower_filter_commit_messages( $commits ) );
}

/**
 * Filter the commit messages and remove e.g Merge commits.
 *
 * @param Array $commits Array with all commit messages.
 */
function sunflower_filter_commit_messages( $commits ) {
	$return = '';

	foreach ( $commits as $commit ) {
		if ( preg_match( '/^publishing|^Bump|^Merge /', (string) $commit ) ) {
			continue;
		}

		$return .= sprintf( "<li>%s</li>\n", $commit );
	}

	return $return;
}
