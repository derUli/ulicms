<?php

function getTime() {
    return microtime(true);
}

// Average page loading time
function doLoadCheck($url, $maxRepeat = 100) {
    $times = [];
    $oldMinMax = "";
    $i = 0;
    $repeat = 1;

    $prevMax = 0;

    while ($repeat < $maxRepeat) {
    //while (true) {
        $i += 1;

        $startTime = getTime();

        system("curl -s -o nul $url");
        $endTime = getTime();

        $newTime = (floatval(substr(strval($endTime - $startTime), 0, 7)));

        $times[] = $newTime;

        $newMinMax = "Min: " . min($times) . ', Max: ' . max($times);

        if ($newMinMax === $oldMinMax) {
            $repeat += 1;
        } else {

            if ($repeat > $prevMax) {
                $prevMax = $repeat;
            }

            $repeat = 1;
        }

        $oldMinMax = $newMinMax;

        echo "Prev Max: $prevMax, ";
        echo "Repeat: $repeat, ";
        echo "URL: $url, Cycle: " . str_pad($i, 6, "0", STR_PAD_LEFT) . ", " . $newMinMax . "\n";
    }

    echo $maxRepeat . "\n";

    return $times;
}

$maxRepeat = 600;
$urls = [
    'http://localhost/ulicms-old/',
    'http://localhost/ulicms/',
];

$results = [];

foreach ($urls as $url) {
    $results[$url] = doLoadCheck($url, $maxRepeat);
}


foreach ($results as $url => $times) {
    echo "$url:\n";
    $count = count($times);
    $min = min($times);
    $max = max($times);

    // echo "Count: " . $count . "\n";
    echo "Min Time: " . $min . "\n";
    echo "\n";
}