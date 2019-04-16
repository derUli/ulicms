<?php

// TODO: This is old code before the switch to MVC architecture
// This should be rewritten with MVC pattern
use UliCMS\Security\PermissionChecker;
use UliCMS\Security\ContentPermissionChecker;

$show_filters = Settings::get("user/" . get_user_id() . "/show_filters");

$permissionChecker = new PermissionChecker(get_user_id());

if ($permissionChecker->hasPermission("pages")) {
    ?>
    <?php
    if (!isset($_SESSION["filter_title"])) {
        $_SESSION["filter_title"] = "";
    }

    if (isset($_GET["filter_title"])) {
        $_SESSION["filter_title"] = $_GET["filter_title"];
    }
    if (!empty($_GET["filter_language"]) and faster_in_array($_GET["filter_language"], getAllLanguages(true))) {
        $_SESSION["filter_language"] = $_GET["filter_language"];
        $_SESSION["filter_parent"] = null;
    }

    if (!isset($_SESSION["filter_category"])) {
        $_SESSION["filter_category"] = 0;
    }

    if (isset($_GET["filter_active"])) {
        if ($_GET["filter_active"] === "null")
            $_SESSION["filter_active"] = null;
        else
            $_SESSION["filter_active"] = intval($_GET["filter_active"]);
    }

    if (isset($_GET["filter_approved"])) {
        if ($_GET["filter_approved"] === "null")
            $_SESSION["filter_approved"] = null;
        else
            $_SESSION["filter_approved"] = intval($_GET["filter_approved"]);
    }

    if (isset($_GET["filter_type"])) {
        if ($_GET["filter_type"] == "null") {
            $_SESSION["filter_type"] = null;
        } else {
            $_SESSION["filter_type"] = $_GET["filter_type"];
        }
    }

    if (isset($_GET["filter_menu"])) {
        if ($_GET["filter_menu"] == "null") {
            $_SESSION["filter_menu"] = null;
        } else {
            $_SESSION["filter_menu"] = $_GET["filter_menu"];
        }
    }

    if (isset($_GET["filter_parent"])) {
        if ($_GET["filter_parent"] == "null") {
            $_SESSION["filter_parent"] = null;
        } else {
            $_SESSION["filter_parent"] = $_GET["filter_parent"];
        }
    }

    if (!isset($_SESSION["filter_parent"])) {
        $_SESSION["filter_parent"] = null;
    }

    if (!isset($_SESSION["filter_menu"])) {
        $_SESSION["filter_menu"] = null;
    }
    if (!isset($_SESSION["filter_type"])) {
        $_SESSION["filter_type"] = null;
    }

    if (!isset($_SESSION["filter_active"])) {
        $_SESSION["filter_active"] = null;
    }

    if (!isset($_SESSION["filter_approved"])) {
        $_SESSION["filter_approved"] = null;
    }

    if (isset($_GET["filter_category"])) {
        $_SESSION["filter_category"] = intval($_GET["filter_category"]);
    }

    if (!empty($_GET["filter_status"]) and faster_in_array($_GET["filter_status"], array(
                "Standard",
                "standard",
                "trash"
            ))) {
        $_SESSION["filter_status"] = $_GET["filter_status"];
    }

    $menus = getAllMenus(true);

    array_unshift($menus, "null");
    // FIXME: Das SQL hier in einen Controller auslagern
    $sql = "select a.id as id, a.title as title from " . tbname("content") . " a inner join " . tbname("content") . " b on a.id = b.parent ";

    if (faster_in_array($_SESSION["filter_language"], getAllLanguages(true))) {
        $sql .= "where b.language='" . $_SESSION["filter_language"] . "' ";
    }

    $sql .= " group by a.title ";
    $sql .= " order by a.title";
    $parents = db_query($sql);
    ?>

    <?php echo Template::executeModuleTemplate("core_content", "icons.php"); ?>

    <h2><?php translate("pages"); ?></h2>

    <p><?php translate("pages_infotext"); ?></p>
    <div id="page-list">
        <?php
        if ($permissionChecker->hasPermission("pages_create")) {
            ?>
            <form action="#" method="get">
                <div class="checkbox">
                    <label><input type="checkbox" class="js-switch" name="show_filters" id="show_filters"
                                  value="1" data-url="<?php echo ModuleHelper::buildMethodCallUrl(PageController::class, "toggleFilters"); ?>"
                                  <?php if ($show_filters) echo "checked"; ?>> <?php translate("show_filters"); ?></label>
                </div>
            </form>
            <div class="row">
                <div class="col-xs-6">
                    <a href="index.php?action=pages_new" class="btn btn-default"><i
                            class="fa fa-plus"></i> <?php translate("create_page"); ?></a>
                </div>
                <div class="col-xs-6 text-right">
                    <div class="page-list-filters" style="<?php
                    if (!$show_filters)
                        echo "display:none";
                    ?>">
                        <a
                            href="<?php echo ModuleHelper::buildMethodCallUrl("PageController", "resetFilters"); ?>"
                            class="btn btn-default" id="btn-reset-filters"><i
                                class="fas fa-undo"></i> <?php translate("reset_filters") ?></a>
                    </div>
                </div>
            </div>

        <?php } ?>

        <form method="get" action="index.php" class="page-list-filters" style="<?php if (!$show_filters) echo "display:none"; ?>">
            <div class="row">
                <div class="col-xs-6">
                    <?php translate("title"); ?>
                    <input type="hidden" name="action" value="pages"> <input
                        type="text" name="filter_title"
                        value="<?php esc($_SESSION["filter_title"]); ?>">
                </div>

                <div class="col-xs-6">
                    <?php translate("filter_by_language"); ?>
                    <select name="filter_language" onchange="filterByLanguage(this)">
                        <option value="">
                            <?php translate("please_select"); ?>

                        </option>
                        <?php
                        $languages = getAllLanguages(true);
                        for ($j = 0; $j < count($languages); $j ++) {
                            if ($languages[$j] == $_SESSION["filter_language"]) {
                                echo "<option value='" . $languages[$j] . "' selected>" . getLanguageNameByCode($languages[$j]) . "</option>";
                            } else {
                                echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . "</option>";
                            }
                        }
                        ?>
                        <?php ?>

                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6">
                    <?php translate("type") ?>
                    <?php $types = get_used_post_types(); ?>
                    <select name="filter_type" onchange="filterByType(this);">
                        <option value="null"
                        <?php
                        if ("null" == $_SESSION["filter_type"])
                            echo "selected";
                        ?>>
                            [<?php translate("every") ?>]
                        </option>
                        <?php
                        foreach ($types as $type) {
                            if ($type == $_SESSION["filter_type"]) {
                                echo '<option value="' . $type . '" selected>' . get_translation($type) . "</option>";
                            } else {
                                echo '<option value="' . $type . '">' . get_translation($type) . "</option>";
                            }
                        }
                        ?>
                    </select>

                </div>
                <div class="col-xs-6">
                    <?php translate("status") ?>
                    <select name="filter_status" onchange="filterByStatus(this)">
                        <option value="Standard"
                        <?php
                        if ($_SESSION["filter_status"] == "standard") {
                            echo " selected";
                        }
                        ?>>
                                    <?php translate("standard"); ?>
                        </option>
                        <option value="trash"
                        <?php
                        if ($_SESSION["filter_status"] == "trash") {
                            echo " selected";
                        }
                        ?>>
                                    <?php translate("recycle_bin"); ?>
                        </option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6">
                    <?php translate("category"); ?>
                    <?php
                    echo Categories::getHTMLSelect($_SESSION["filter_category"], true);
                    ?>
                </div>
                <div class="col-xs-6">
                    <?php translate("menu"); ?>
                    <select name="filter_menu" onchange="filterByMenu(this);">

                        <?php
                        foreach ($menus as $menu) {
                            if ($menu == "null") {
                                $name = "[" . get_translation("every") . "]";
                            } else {
                                $name = $menu;
                            }

                            if ($menu == $_SESSION["filter_menu"]) {
                                echo '<option value="' . $menu . '" selected>' . get_translation($name) . "</option>";
                            } else {
                                echo '<option value="' . $menu . '">' . get_translation($name) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <?php translate("parent"); ?>
                    <select name="filter_parent" onchange="filterByParent(this);">
                        <option value="null"
                        <?php
                        if ("null" == $_SESSION["filter_parent"])
                            echo "selected";
                        ?>>
                            [<?php translate("every"); ?>]
                        </option>
                        <option value="-"
                        <?php
                        if ("-" == $_SESSION["filter_parent"])
                            echo "selected";
                        ?>>
                            [<?php translate("none"); ?>]
                        </option>
                        <?php
                        while ($parent = db_fetch_object($parents)) {
                            $parent_id = $parent->id;
                            $title = htmlspecialchars($parent->title);
                            if ($parent_id == $_SESSION["filter_parent"]) {
                                echo '<option value="' . $parent_id . '" selected>' . $title . "</option>";
                            } else {
                                echo '<option value="' . $parent_id . '">' . $title . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-xs-6">
                    <?php translate("enabled"); ?>
                    <select name="filter_active" onchange="filterByActive(this);">
                        <option value="null"
                        <?php
                        if (null == $_SESSION["filter_active"]) {
                            echo "selected";
                        }
                        ?>>
                            [<?php translate("every"); ?>]
                        </option>
                        <option value="1"
                        <?php
                        if (1 === $_SESSION["filter_active"]) {
                            echo "selected";
                        }
                        ?>><?php translate("enabled"); ?></option>
                        <option value="0"
                        <?php
                        if (0 === $_SESSION["filter_active"]) {
                            echo "selected";
                        }
                        ?>><?php translate("disabled"); ?></option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">

                    <?php
                    translate("approved");
                    ?>
                    <p>
                        <select name="filter_approved" onchange="filterByApproved(this);">
                            <option value="null"
                            <?php
                            if (null == $_SESSION["filter_approved"]) {
                                echo "selected";
                            }
                            ?>>
                                [<?php
                                translate("every");
                                ?>]</option>
                            <option value="1"
                            <?php
                            if (1 === $_SESSION["filter_approved"]) {
                                echo "selected";
                            }
                            ?>><?php
                                        translate("yes");
                                        ?></option>
                            <option value="0"
                            <?php
                            if (0 === $_SESSION["filter_approved"]) {
                                echo "selected";
                            }
                            ?>><?php
                                        translate("no");
                                        ?></option>
                        </select>
                    </p>
                </div>
            </div>
        </form>

        <?php
        if ($_SESSION["filter_status"] == "trash" and $permissionChecker->hasPermission("pages")) {
            ?>

            <a
                href="<?php echo ModuleHelper::buildMethodCallUrl("PageController", "emptyTrash"); ?>"
                onclick="return ajaxEmptyTrash(this.href);" class="btn btn-warning">
                <i class="fa fa-trash" aria-hidden="true"></i>
                <?php translate("empty_recycle_bin"); ?></a>
            <?php
        }
        ?>

        <?php
        if (faster_in_array($_GET["order"], array(
                    "title",
                    "menu",
                    "position",
                    "parent",
                    "active"
                )))
            $order = $_GET["order"];
        $filter_language = basename($_GET["filter_language"]);
        $filter_status = basename($_GET["filter_status"]);

        if (empty($filter_language)) {
            if (!empty($_SESSION["filter_language"])) {
                $filter_language = $_SESSION["filter_language"];
            } else {
                $filter_language = "";
            }
        }

        if ($_SESSION["filter_status"] == "trash") {
            $filter_status = "`deleted_at` IS NOT NULL";
        } else {
            $filter_status = "`deleted_at` IS NULL";
        }

        if (empty($order)) {
            $order = "menu";
        }

        if (!empty($filter_language)) {
            $filter_sql = "WHERE language = '" . $filter_language . "' ";
        } else {
            $filter_sql = "WHERE 1=1 ";
        }

        if ($_SESSION["filter_category"] != 0) {
            $filter_sql .= "AND category=" . intval($_SESSION["filter_category"]) . " ";
        }

        $filter_sql .= "AND " . $filter_status . " ";

        if ($_SESSION["filter_menu"] != null) {
            $filter_sql .= "AND menu = '" . db_escape($_SESSION["filter_menu"]) . "' ";
        }
        if ($_SESSION["filter_type"] != null) {
            $filter_sql .= "AND `type` = '" . db_escape($_SESSION["filter_type"]) . "' ";
        }

        if ($_SESSION["filter_active"] !== null) {
            $filter_sql .= "AND active = " . intval($_SESSION["filter_active"]) . " ";
        }

        if ($_SESSION["filter_approved"] !== null) {
            $filter_sql .= "AND approved = " . intval($_SESSION["filter_approved"]) . " ";
        }

        if ($_SESSION["filter_parent"] != null) {
            if ($_SESSION["filter_parent"] != "-") {
                $filter_sql .= "AND parent = '" . intval($_SESSION["filter_parent"]) . "' ";
            } else {
                $filter_sql .= "AND parent IS NULL ";
            }
        }

        if (isset($_SESSION["filter_title"]) and ! empty($_SESSION["filter_title"])) {
            $filter_sql .= "AND (title LIKE '" . db_escape($_SESSION["filter_title"]) . "%' or title LIKE '%" . db_escape($_SESSION["filter_title"]) . "' or title LIKE '%" . db_escape($_SESSION["filter_title"]) . "%' or title LIKE '" . db_escape($_SESSION["filter_title"]) . "' ) ";
        }


        $group = Group::getCurrentGroup();
        $userLanguage = $permissionChecker->getLanguages();
        $joined = "";
        foreach ($userLanguage as $lang) {
            $joined .= "'" . Database::escapeValue($lang->getLanguageCode()) . "',";
        }
        $joined = trim($joined, ",");
        if (count($userLanguage) > 0) {
            $filter_sql .= " AND language in (";
            $filter_sql .= $joined;
            $filter_sql .= ")";
        }

        $filter_sql .= " ";

        $query = db_query("SELECT * FROM " . tbname("content") . " " . $filter_sql . " ORDER BY $order,position, systemname ASC") or die(db_error());
        ?>
        <div class="x-results-found"><?php BackendHelper::formatDatasetCount(Database::getNumRows($query)); ?></div>
        <div class="scroll">
            <table class="tablesorter dataset-list">
                <thead>
                    <tr style="font-weight: bold;">
                        <th><?php translate("title"); ?>
                        </th>
                        <th class="hide-on-mobile"><?php translate("menu"); ?>
                        </th>
                        <th class="hide-on-mobile"><?php translate("position"); ?>
                        </th>
                        <th class="hide-on-mobile"><?php translate("parent"); ?>
                        </th>

                        <th class="hide-on-mobile"><?php translate("activated"); ?>
                        </th>
                        <td style="text-align: center"><?php translate("view"); ?>
                        </td>
                        <!--
                        <?php
                        if ($permissionChecker->hasPermission("pages_create")) {
                            ?>
                                                                            <td style="text-align: center"><?php translate("clone"); ?>
                                                                            </td> -->
                        <?php } ?>
                        <td style="text-align: center"><?php translate("edit"); ?>
                        </td>
                        <td style="text-align: center"><?php translate("delete"); ?>
                        </td>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (db_num_rows($query) > 0) {
                        while ($row = db_fetch_object($query)) {
                            echo '<tr id="dataset-' . $row->id . '">';
                            echo "<td>" . htmlspecialchars($row->title);
                            if (!empty($row->redirection) and ! is_null($row->redirection) and $row->type == "link") {
                                esc(" --> ") . htmlspecialchars($row->redirection);
                            }

                            echo "</td>";
                            echo "<td class=\"hide-on-mobile\">" . htmlspecialchars(get_translation($row->menu)) . "</td>";

                            echo "<td class=\"hide-on-mobile\">" . $row->position . "</td>";
                            echo "<td class=\"hide-on-mobile\">" . htmlspecialchars(getPageTitleByID($row->parent)) . "</td>";

                            if ($row->active) {
                                echo "<td class=\"hide-on-mobile\">" . get_translation("yes") . "</td>";
                            } else {
                                echo "<td class=\"hide-on-mobile\">" . get_translation("no") . "</td>";
                            }

                            if (startsWith($row->redirection, "#") or $row->type == "node" or $row->type == "snippet") {
                                echo "<td style='text-align:center'></td>";
                            } else {
                                $url = "../?goid={$row->id}";
                                echo "<td style='text-align:center'><a href=\"" . $url . "\"><img class=\"mobile-big-image\" src=\"gfx/preview.png\" alt=\"" . get_translation("view") . "\" title=\"" . get_translation("view") . "\"></a></td>";
                            }

                            $checker = new ContentPermissionChecker(get_user_id());
                            $can_edit_this = $checker->canWrite($row->id);

                            if (!$can_edit_this) {
                                echo "<td></td><td></td>";
                            } else {
                                echo "<td style='text-align:center'>" . '<a href="index.php?action=pages_edit&page=' . $row->id . '"><img class="mobile-big-image" src="gfx/edit.png" alt="' . get_translation("edit") . '" title="' . get_translation("edit") . '"></a></td>';

                                if ($_SESSION["filter_status"] == "trash") {
                                    echo "<td style='text-align:center'>";
                                    echo ModuleHelper::buildMethodCallForm("PageController", "undelete", array(
                                        "page" => $row->id
                                            ), "post", array(
                                        "class" => "undelete-form",
                                        "data->id" => $row->id
                                    ));

                                    get_csrf_token_html();
                                    echo '<input type="image" class="mobile-big-image" src="gfx/undelete.png" alt="' . get_translation("recover") . '" title="' . get_translation("recover") . '"></form></td>';
                                } else {
                                    echo "<td style='text-align:center'>";
                                    echo ModuleHelper::deleteButton("index.php", array(
                                        "page" => $row->id,
                                        "sClass" => "PageController",
                                        "sMethod" => "delete"
                                            ), array(
                                        "data-id" => $row->id,
                                        "class" => "page-delete-form"
                                    ));
                                    echo "</td>";
                                }
                            }
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    enqueueScriptFile("scripts/page.js");
    combinedScriptHtml();
    ?>
    <?php
    $translation = new JSTranslation();
    $translation->addKey("ask_for_delete");
    $translation->addKey("wanna_empty_trash");
    $translation->addKey("reset_filters");
    $translation->renderJS();
    ?>
    <br />
    <?php
} else {
    noPerms();
}
