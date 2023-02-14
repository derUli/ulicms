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
            echo "Cycle $i, Average Front page loading time: " . (array_sum($times) / count($times)) ."\n";

    }

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
    }


    echo "Average Unit Test running time: " . (array_sum($times) / count($times));
}


 doLoadCheck(2000, 'http://localhost/ulicms-old/');