<?php

// ajax_cmd abschaffen, stattdessen Actions verwenden
$ajax_cmd = $_REQUEST["ajax_cmd"];

switch ($ajax_cmd) {
    default:
        echo "Unknown Call";
        break;
}
