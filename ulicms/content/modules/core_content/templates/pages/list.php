<?php

use UliCMS\Models\Content\Categories;
// TODO: This is old code before the switch to MVC architecture
// This should be rewritten with MVC pattern and using partial views
use UliCMS\Security\PermissionChecker;

$show_filters = Settings::get("user/" . get_user_id() . "/show_filters");

$permissionChecker = new PermissionChecker(get_user_id());

if ($permissionChecker->hasPermission("pages")) {
    if (!isset($_SESSION["filter_title"])) {
        $_SESSION["filter_title"] = "";
    }

    if (isset($_GET["filter_title"])) {
        $_SESSION["filter_title"] = $_GET["filter_title"];
    }
    if (!empty($_GET["filter_language"]) and faster_in_array($_GET["filter_language"], getAllLanguages(true))) {
        $_SESSION["filter_language"] = $_GET["filter_language"];
        $_SESSION["filter_parent"] = "-";
    }

    if (!isset($_SESSION["filter_category"])) {
        $_SESSION["filter_category"] = 0;
    }

    if (isset($_GET["filter_active"])) {
        if ($_GET["filter_active"] === "null") {
            $_SESSION["filter_active"] = null;
        } else {
            $_SESSION["filter_active"] = intval($_GET["filter_active"]);
        }
    }

    if (isset($_GET["filter_approved"])) {
        if ($_GET["filter_approved"] === "null") {
            $_SESSION["filter_approved"] = null;
        } else {
            $_SESSION["filter_approved"] = intval($_GET["filter_approved"]);
        }
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
        $_SESSION["filter_parent"] = "-";
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
    $sql = "select a.id as id, a.title as title from " . tbname("content") . " a inner join " . tbname("content") . " b on a.id = b.parent_id ";

    if (faster_in_array($_SESSION["filter_language"], getAllLanguages(true))) {
        $sql .= "where b.language='" . $_SESSION["filter_language"] . "' ";
    }

    $sql .= " group by a.title, a.id ";
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
                    <a href="index.php?action=pagespages_new&parent_id=<?php echo $_SESSION["filter_parent"]; ?>" class="btn btn-default"><i
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
                    <?php translate("parent_id"); ?>
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
                            $title = _esc($parent->title);
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


        if ($_SESSION["filter_parent"] and $_SESSION["filter_parent"] != '-') {
            $parentPage = ContentFactory::getByID($_SESSION["filter_parent"]);
            $parentId = $parentPage->parent_id ? $parentPage : "-";
            ?>
            <div class="form-group">
                <a href="<?php
                echo ModuleHelper::buildActionUrl("pages",
                        "filter_parent={$parentId}");
                ?>" class="btn btn-default">
                    <?php echo UliCMS\HTML\icon("fa fa-arrow-up"); ?> <?php translate("go_up"); ?></a>
                <?php
            }
            ?>
        </div>
        <div class="scroll">
            <table class="tablesorter dataset-list"
                   data-url="<?php
                   echo ModuleHelper::buildMethodCallUrl("PageController",
                           "getPages");
                   ?>">
                <thead>
                    <tr style="font-weight: bold;">
                        <th><?php translate("title"); ?>
                        </th>
                        <th><?php translate("menu"); ?>
                        </th>
                        <th><?php translate("position"); ?>
                        </th>
                        <th><?php translate("parent_id"); ?>
                        </th>

                        <th><?php translate("activated"); ?>
                        </th>
                        <td class="no-sort text-center"><?php translate("view"); ?>
                        </td>
                        <td class="no-sort text-center"><?php translate("edit"); ?>
                        </td>
                        <td class="no-sort text-center"><?php translate("delete"); ?>
                        </td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    enqueueScriptFile(ModuleHelper::buildRessourcePath("core_content", "js/pages/page.js"));
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
