<?php
if (isset($_GET["single"])) {
    $single = Database::escapeValue($_GET["single"]);
    $language = Database::escapeValue(getCurrentLanguage());
    $query = db_query("SELECT datum FROM `" . tbname("blog") . "` WHERE seo_shortname='$single' and language = '$language'");
    
    if ($query) {
        if (db_num_rows($query) > 0) {
            $result = db_fetch_assoc($query);
            $datum = $result["datum"];
            
            if (containsModule(get_requested_pagename(), "blog")) {
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $datum) . ' GMT');
            }
        } else {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        }
    }
}
