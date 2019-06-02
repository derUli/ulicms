<?php

function jumbotron_get_menu($name = "top", $parent_id = null, $recursive = true, $order = "position") {
    $html = "";
    $name = db_escape($name);
    $language = $_SESSION["language"];
    $sql = "SELECT id, slug, access, redirection, title, alternate_title, menu_image, target, type, position FROM " . tbname("content") . " WHERE menu='$name' AND language = '$language' AND active = 1 AND `deleted_at` IS NULL AND parent_id ";

    if (is_null($parent_id)) {
        $sql .= " IS NULL ";
    } else {
        $sql .= " = " . intval($parent_id) . " ";
    }
    $sql .= " ORDER by " . $order;
    $query = db_query($sql);

    if (db_num_rows($query) == 0) {
        return $html;
    }

    if (is_null($parent_id)) {
        $html .= "<ul class='nav nav-pills pull-right menu_top'>\n";
    } else {
        $containsCurrentItem = parent_item_contains_current_page($parent_id);

        $classes = "sub_menu";

        if ($containsCurrentItem) {
            $classes .= " contains-current-page";
        }
        $html .= "<ul class='" . $classes . "'>\n";
    }

    while ($row = db_fetch_object($query)) {
        if (checkAccess($row->access)) {
            $containsCurrentItem = parent_item_contains_current_page($row->id);

            $additional_classes = " menu-link-to-" . $row->id . " ";
            if ($containsCurrentItem) {
                $additional_classes .= "active ";
            }

            if (get_requested_pagename() != $row->slug) {
                $html .= "  <li class='" . trim($additional_classes) . "'>";
            } else {
                $html .= "  <li class='active" . rtrim($additional_classes) . "'>";
            }

            $title = $row->title;
            // Show page positions in menu if user has the "pages_show_positions" permission.
            if (is_logged_in()) {
                $acl = new ACL();
                $settingsName = "user/" . get_user_id() . "/show_positions";

                if ($acl->hasPermission("pages_show_positions") and Settings::get($settingsName)) {
                    $title .= " ({$row->position})";
                }
            }

            // if content has type link or node url is the target url else build seo url
            $url = ($row->type == "link" or $row->type == "node") ? $row->redirection : buildSEOUrl($row->slug);
            $url = Template::getEscape($url);

            if (get_requested_pagename() != $row->slug) {
                $html .= "<a href='" . $url . "' target='" . $row->target . "' class='" . trim($additional_classes) . "'>";
            } else {
                $html .= "<a class='menu_active_link" . rtrim($additional_classes) . "' href='" . $url . "' target='" . $row->target . "'>";
            }

            if (!is_null($row->menu_image) and ! empty($row->menu_image)) {
                $html .= '<img src="' . $row->menu_image . '" alt="' . _esc($title) . '"/>';
            } else {
                $html .= _esc($title);
            }
            $html .= "</a>\n";

            if ($recursive) {
                $html .= get_menu($name, $row->id, true, $order);
            }

            $html .= "</li>";
        }
    }
    $html .= "</ul>";
    return $html;
}
