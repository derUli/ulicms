<?php

namespace UliCMS\CoreContent;

use App\HTML\ListItem;

class UIUtils {

    // get items for the "Robots" dropdown in the metadata section of
    // page edit form
    public static function getRobotsListItems() {
        $robotsListItems = [];
        $robotsListItems[] = new ListItem(
                null,
                "[" . get_translation("standard") . "]"
        );
        $robotsListItems[] = new ListItem(
                "index, follow",
                "index, follow"
        );
        $robotsListItems[] = new ListItem(
                "index, nofollow",
                "index, nofollow"
        );
        $robotsListItems[] = new ListItem(
                "noindex, follow",
                "noindex, follow"
        );
        $robotsListItems[] = new ListItem(
                "noindex, nofollow",
                "noindex, nofollow"
        );
        return $robotsListItems;
    }

}
