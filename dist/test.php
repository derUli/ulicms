<?php

function getTime() {
    return microtime(true);
}

// Average page loading time
function doLoadCheck($url, $maxEquals = 100) {
    $times = [];

    $oldMinMax = "";

    $currentMaxEquals = $maxEquals;

    $i = 0;

    $repeat = 1;
    $maxRepeat = 0;

    while ($currentMaxEquals > 1) {
        $i += 1;

        $startTime = getTime();

        system("curl -s -o nul $url");
        $endTime = getTime();

        $newTime = (floatval(substr(strval($endTime - $startTime), 0, 7)));

        $times[] = $newTime;

        $newMinMax = "Min: " . min($times) . ', Max: ' . max($times);

        if ($newMinMax === $oldMinMax) {
            $currentMaxEquals -= 1;
            $repeat += 1;

            if ($repeat > $maxRepeat) {
                $maxRepeat = $repeat;
            }
        } else {
            $currentMaxEquals = $maxEquals;
            $repeat = 1;
        }

        $oldMinMax = $newMinMax;

        echo "Max Repeat: $maxRepeat, ";
        echo "URL: $url, Cycle: " . str_pad($i, 6, "0", STR_PAD_LEFT) . ", " . $newMinMax . "\n";
    }

    echo $maxRepeat . "\n";

    return $times;
}

function doTestCheck($maxEquals = 100) {
    $times = [];

    $oldMinMax = "";

    $i = 0;

    $currentMaxEquals = $maxEquals;

    while ($currentMaxEquals > 1) {
        $i += 1;

        $startTime = time();

        system("vendor\\bin\\phpunit tests");
        $endTime = time();

        $newTime = $endTime - $startTime;
        $times[] = $newTime;

        $newMinMax = "Min: " . min($times) . ', Max: ' . max($times);

        if ($newMinMax === $oldMinMax) {
            $currentMaxEquals -= 1;
        } else {
            $currentMaxEquals = $maxEquals;
        }

        $oldMinMax = $newMinMax;

        echo "Cycle: " . str_pad($i, 4, "0", STR_PAD_LEFT) . ", " . $newMinMax . "\n";
    }

    return $times;
}

$maxEquals = 60;
$urls = [
    'http://localhost/ulicms-old/',
    'http://localhost/ulicms/',
];

$results = [];

foreach ($urls as $url) {
    $results[$url] = doLoadCheck($url, $maxEquals);
}


foreach ($results as $url => $times) {
    echo "$url:\n";
    $count = count($times);
    $min = min($times);
    $max = max($times);

    echo "Count: " . $count . "\n";
    echo "Min Time: " . $min . "\n";
    echo "Max Time: " . $max . "\n\n";
}