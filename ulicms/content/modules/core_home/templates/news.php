<?php

if (!Settings::get("disable_ulicms_newsfeed")) {
    @session_start();
    $rss = new DOMDocument();
    $feeds = array();
    $feeds["de"] = "https://www.ulicms.de/blog_rss.php?s=aktuelles&lang=de";
    $feeds["en"] = "https://en.ulicms.de/blog_rss.php?s=aktuelles&lang=en";

    if (isset($feeds[$_SESSION["system_language"]])) {
        $feed_url = $feeds[$_SESSION["system_language"]];
    } else {
        $feed_url = $feeds["en"];
    }

    $xml = file_get_contents_wrapper($feed_url, true);

    if ($xml and $rss->loadXML($xml)) {

        $feed = array();
        foreach ($rss->getElementsByTagName('item') as $node) {
            $item = array(
                'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
                'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
            );
            array_push($feed, $item);
        }

        $limit = 5;

        header("Content-Type: text/html; charset=UTF-8");
        for ($x = 0; $x < $limit; $x ++) {
            $title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
            $link = $feed[$x]['link'];
            $description = $feed[$x]['desc'];
            echo '<p><strong><a href="' . $link . '" title="' . $title . '" target="_blank">' . $title . '</a></strong><br />';
            $date = strtotime($feed[$x]['date']);
            $datestr = strftime("%x, %A", $date);
            $txt = get_translation("posted_on_date");
            $txt = str_replace("%s", $datestr, $txt);
            echo '<small><em>' . $txt . '</em></small></p>';
            echo '<p>' . $description . '</p>';
        }
    } else {
        translate("loading_feed_failed");
    }
}
