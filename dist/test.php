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

    while ($currentMaxEquals > 1) {
        $i += 1;

        $startTime = getTime();

        system("curl -s -o nul $url");
        $endTime = getTime();

        $newTime = $endTime - $startTime;
        $times[] = $newTime;

        $newMinMax = "Min: " . min($times) . ', Max: ' . max($times);

        if ($newMinMax === $oldMinMax) {
            $currentMaxEquals -= 1;
        } else {
            $currentMaxEquals = $maxEquals;
        }

        $oldMinMax = $newMinMax;

        echo "URL: $url, Cycle: " . str_pad($i, 6, "0", STR_PAD_LEFT) . ", " . $newMinMax . "\n";
    }

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

        echo "Cycle: " . str_pad($i, 6, "0", STR_PAD_LEFT) . ", " . $newMinMax . "\n";
    }

    return $times;
}

$maxEquals = 4000;

//$timesOld = doLoadCheck('http://localhost/ulicms-old/', $maxEquals);
$timesNew = doLoadCheck('http://localhost/ulicms/', $maxEquals);
//$timesUnitTest = doTestCheck(10);

echo "http://localhost/ulicms-old/:\n";

// echo "Count: " . count($timesOld) . "\n";
// echo "Min Time: " . min($timesOld) . "\n";
//echo "Max Time: " . max($timesOld) . "\n\n";

echo "http://localhost/ulicms/:\n";
echo "Count: " . count($timesNew) . "\n";
echo "Min Time: " . min($timesNew) . "\n";
echo "Max Time: " . max($timesNew) . "\n\n";

//echo "Unit Test:\n";
//echo "Count: " . count($timesUnitTest) . "\n";
//echo "Min Time: " . min($timesUnitTest) . "\n";
//echo "Max Time: " . max($timesUnitTest) . "\n";
