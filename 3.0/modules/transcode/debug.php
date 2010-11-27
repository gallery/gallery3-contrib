<?php

if (!isset($_GET['key']) || !isset($_GET['pwd']) || md5($_GET['pwd']) != "afa6151ebfef30bdf73c10b7fd35453f")
    exit("no access");

switch ($_GET['key']) {
    case "phpinfo": phpinfo(); break;
    case "ffmpeg": ffmpeg(); break;
    default: exit("don't know..");
}

function ffmpeg() {
    echo "<h1>ffmpeg info</h1>";
    $ffmpegPath = whereis("ffmpeg");
    echo "path: " . $ffmpegPath . "<br />";
    $version = @shell_exec($ffmpegPath . " -version"); $version = explode("\n", $version); $version = $version[0]; $version = explode(" ", $version); $version = $version[1];
    echo "version: " . $version . "<br />";
    echo "codecs:<pre>" . @shell_exec($ffmpegPath . " -codecs 2>&1") . "</pre><br />";
    echo "formats:<pre>" . @shell_exec($ffmpegPath . " -formats") . "</pre><br />";
}

function whereis($app) {
    $op = @shell_exec("whereis " . $app);
    if ($op != "") {
        $op = explode(" ", $op);
        for ($i = 1; $i < count($op); $i++) {
            if (file_exists($op[$i]) && !is_dir($op[$i]))
                return $op[$i];
        }
    }
    return false;
}

/*
stdClass Object
(
    [video] => stdClass Object
        (
            [codec] => h264,
            [height] => 576
            [width] => 640
        )

    [audio] => stdClass Object
        (
        )

)

 */