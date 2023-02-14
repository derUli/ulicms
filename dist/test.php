<?php

function getTime() {
    return microtime(true);
}

// Average page loading time
function doLoadCheck($cycles, $url) {
    $times = [];

    for ($i = 1; $i <= $cycles; $i++) {
        $startTime = getTime();
        
        system("curl -s -o nul $url");
        $endTime = getTime();
        
        $times[] = $endTime - $startTime;
            echo "URL: $url, Cycle: $i, Average Front page loading time: " . (array_sum($times) / count($times)) ."\n";

    }
    return (array_sum($times) / count($times));
}

// Average Test running time
function doTestCheck($cycles) {
    $times = [];

    for ($i = 1; $i <= $cycles; $i++) {
        echo "Test run $i\n\n";
        $startTime = getTime();
        $cmd = "vendor\\bin\\phpunit tests";
        
        system($cmd);
        $endTime = getTime();
        
        $times[] = $endTime - $startTime;
        echo "Average Unit Test running time: " . (array_sum($times) / count($times));
    }


}

$cycles = 2000;
 $loadCheckOld = doLoadCheck($cycles, 'http://localhost/ulicms-old/'); 
 $loadCheckNew = doLoadCheck($cycles, 'http://localhost/ulicms/'); 
 
 echo "$cycles Cycles\n";
 echo "Load check old: $loadCheckOld\n";
 echo "Load check new: $loadCheckNew\n";
 
 