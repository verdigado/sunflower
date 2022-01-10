<?php

$output = '<!DOCTYPE html>
<html lang="de-DE">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Changelog Sunflower Theme</title>
</head>

<body>
<h1>Changelogs</h1>';

exec('git tag', $tags);
$tags = array_reverse($tags);

for($i = 0; $i < count($tags); $i++){
    $release = "";
    $release .= sprintf("<h2>Neu in %s</h2>\n<ul>\n", $tags[$i]);
    
    exec(sprintf('git log --pretty=format:"%%s" %s...%s', $tags[$i], $tags[$i+1]), $commits);

    foreach($commits AS $commit){
        if(preg_match('/^publishing|^Bump|^Merge /', $commit)){
            continue;
        }

        $release .= sprintf("<li>%s</li>\n", $commit);
    }

    $release .= "</ul>\n\n";

    $output .= $release;

    
    if($tags[$i] == 'v1.0.0' ){
        break;
    }
}

$output .= '</body>
</html>
';

file_put_contents('changelog.html', $output);
echo "..done";