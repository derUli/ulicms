<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Helpers\DateTimeHelper;

if (! Settings::get('disable_ulicms_newsfeed')) {
    App\Utils\Session\sessionStart();
    $rss = new DOMDocument();
    $feeds = [];
    $feeds['de'] = 'https://www.ulicms.de/blog_rss.php?s=aktuelles&lang=de';
    $feeds['en'] = 'https://en.ulicms.de/blog_rss.php?s=aktuelles&lang=en';

    if (isset($_SESSION['system_language'], $feeds[$_SESSION['system_language']])
            ) {
        $feed_url = $feeds[$_SESSION['system_language']];
    } else {
        $feed_url = $feeds['en'];
    }

    $xml = file_get_contents_wrapper($feed_url, true);

    if ($xml && $rss->loadXML($xml)) {
        $feed = [];
        foreach ($rss->getElementsByTagName('item') as $node) {
            $item = [
                'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
                'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
            ];
            $feed[] = $item;
        }

        $limit = 5;

        send_header('Content-Type: text/html; charset=UTF-8');
        for ($x = 0; $x < $limit; $x++) {
            $title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
            $link = $feed[$x]['link'];
            $description = $feed[$x]['desc'];
            echo '<p><strong><a href="' . $link . '" target="_blank">' . $title . '</a></strong><br />';
            $date = strtotime($feed[$x]['date']);
            $datestr = DateTimeHelper::timestampToFormattedDateTime($date, IntlDateFormatter::LONG, IntlDateFormatter::NONE);
            $txt = get_translation('posted_on_date');
            $txt = str_replace('%s', $datestr, $txt);
            echo '<small><em>' . $txt . '</em></small></p>';
            echo '<p>' . $description . '</p>';
        }
    } else {
        translate('loading_feed_failed');
    }
}
