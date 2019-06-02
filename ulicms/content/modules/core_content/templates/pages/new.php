<?php

use UliCMS\Models\Content\Categories;
use UliCMS\Models\Content\Language;
use UliCMS\Models\Content\Types\DefaultContentTypes;
use UliCMS\Helpers\NumberFormatHelper;

$permissionChecker = new ACL();
$groups = db_query("SELECT id, name from " . tbname("groups"));
if ($permissionChecker->hasPermission("pages") and $permissionChecker->hasPermission("pages_create")) {

    $editor = get_html_editor();

    $allThemes = getThemesList();
    $cols = Database::getColumnNames("content");

    // FIXME: No SQL in Views
    $sql = "SELECT id, name FROM " . tbname("videos");
    $videos = Database::query($sql);
    $sql = "SELECT id, name FROM " . tbname("audio");
    $audios = Database::query($sql);

    $pages_activate_own = $permissionChecker->hasPermission("pages_activate_own");

    $types = get_available_post_types();
    ?>
    <div class="loadspinner">
        <?php require "inc/loadspinner.php"; ?>
    </div>
    <?php
    echo ModuleHelper::buildMethodCallForm("PageController", "create", array(), "post",
            array(
                "name" => "newpageform",
                "id" => "pageform",
                "style" => "display:none",
                "class" => "pageform main-form",
                "data-get-content-types-url" => ModuleHelper::buildMethodCallUrl(PageController::class, "getContentTypes"),
                "data-slug-free-url" => ModuleHelper::buildMethodCallUrl(PageController::class, "checkSlugFree"),
                "data-parent-pages-url" => ModuleHelper::buildMethodCallUrl(PageController::class, "filterParentPages")
    ));
    ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("pages"); ?>"
           class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <input type="hidden" name="add" value="add">
    <div id="accordion-container">
        <h2 class="accordion-header"><?php translate("title_and_headline"); ?></h2>
        <div class="accordion-content">
            <strong><?php
                translate("permalink");
                ?>*
            </strong><br /> <input type="text" name="slug" id="slug"
                                   required="required" value=""> <br /> <strong><?php
                                       translate("page_title");
                                       ?>*
            </strong><br /> <input type="text" required="required"
                                   name="title" value="" onkeyup="suggestSlug(this.value)">
            <div class="typedep hide-on-snippet hide-on-non-regular">
                <br /> <strong><?php translate("alternate_title"); ?>
                </strong><br /> <input type="text" name="alternate_title" value=""> <small><?php translate("ALTERNATE_TITLE_INFO"); ?>
                </small> <br /> <br /> <strong><?php translate("show_headline"); ?></strong>
                <br /> <select name="show_headline">
                    <option value="1" selected><?php translate("yes"); ?></option>
                    <option value="0"><?php translate("no"); ?></option>
                </select>
            </div>
        </div>
        <h2 class="accordion-header"><?php translate("page_type"); ?></h2>
        <div class="accordion-content">
            <?php foreach ($types as $type) { ?>
                <input type="radio" name="type" id="type_<?php echo $type; ?>"
                       value="<?php echo $type; ?>"
                       <?php
                       if ($type == "page") {
                           echo "checked";
                       }
                       ?>> <label
                       for="type_<?php echo $type; ?>"><?php translate($type); ?></label> <br />
                   <?php } ?>

        </div>
        <h2 class="accordion-header"><?php translate("menu_entry"); ?></h2>
        <div class="accordion-content">
            <strong><?php translate("language"); ?>
            </strong> <br /> <select name="language">
                <?php
                $languages = getAllLanguages(true);
                if (!empty($_SESSION["filter_language"])) {
                    $default_language = $_SESSION["filter_language"];
                } else {
                    $default_language = Settings::get("default_language");
                }

                for ($j = 0; $j < count($languages); $j ++) {
                    if ($languages[$j] === $default_language) {
                        echo "<option value='" . $languages[$j] . "' selected>" . getLanguageNameByCode($languages[$j]) . "</option>";
                    } else {
                        echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . "</option>";
                    }
                }

                $pages = getAllPages($default_language, "title", false);
                ?>
            </select><br /> <br />
            <div class="typedep menu-stuff">
                <strong><?php translate("menu"); ?>
                </strong> <span style="cursor: help;"
                                onclick="$('div#menu_help').slideToggle()"><i class="fa fa-question-circle text-info" aria-hidden="true"></i></span><br /> <select
                    name="menu" size=1>
                        <?php
                        foreach (getAllMenus() as $menu) {
                            ?>
                        <option value="<?php echo $menu ?>"
                                <?php if ($menu == "top") echo "selected"; ?>>
                            <?php translate($menu); ?></option>
                        <?php
                    }
                    ?>
                </select>
                <div id="menu_help" class="help" style="display: none">
                    <?php echo nl2br(get_translation("help_menu")); ?>
                </div>
                <br /> <br /> <strong><?php translate("position"); ?>
                </strong> <span style="cursor: help;"
                                onclick="$('div#position_help').slideToggle()"><i class="fa fa-question-circle text-info" aria-hidden="true"></i></span><br /> <input
                    type="number" required="required" name="position" value="0" min="0"
                    step="1">
                <div id="position_help" class="help" style="display: none">
                    <?php echo nl2br(get_translation("help_position")); ?>
                </div>
                <br />
                <div id="parent-div">
                    <strong><?php translate("parent_id"); ?></strong><br /> <select
                        name="parent_id" size=1>
                        <option selected="selected" value="NULL">
                            [
                            <?php translate("none"); ?>
                            ]
                        </option>
                        <?php
                        foreach ($pages as $key => $page) {
                            ?>
                            <option value="<?php
                            echo $page["id"];
                            ?>">
                                        <?php
                                        esc($page["title"]);
                                        ?>
                                (ID:
                                <?php
                                echo $page["id"];
                                ?>
                                )
                            </option>
                            <?php
                        }
                        ?>
                    </select> <br /> <br />
                </div>
            </div>
            <div class="typedep" id="tab-target">
                <strong><?php
                    translate("open_in");
                    ?>
                </strong><br /> <select name="target" size=1>
                    <option value="_self">
                        <?php translate("target_self"); ?>
                    </option>
                    <option value="_blank">
                        <?php translate("target_blank"); ?>
                    </option>
                </select><br /> <br />
            </div>
            <strong><?php translate("activated"); ?>
            </strong><br /> <select name="active" size=1
                                    <?php if (!$pages_activate_own) echo "disabled"; ?>>
                <option value="1">
                    <?php translate("enabled"); ?>
                </option>
                <option value="0" <?php if (!$pages_activate_own) echo "selected"; ?>>
                    <?php translate("disabled"); ?>
                </option>
            </select> <br /> <br />
            <div class="typedep" id="hidden-attrib">
                <strong><?php translate("hidden"); ?>
                </strong><br /> <select name="hidden" size="1">
                    <option value="1">
                        <?php translate("yes"); ?>
                    </option>
                    <option value="0" selected>
                        <?php translate("no"); ?>
                    </option>
                </select> <br /> <br />
            </div>
            <strong><?php translate("category"); ?>
            </strong><br />
            <?php echo Categories :: getHTMLSelect(); ?>
            <div id="menu_image_div" class="voffset3">
                <strong><?php translate("menu_image"); ?>
                </strong><br />
                <input type="text" id="menu_image" name="menu_image"
                       readonly="readonly" class="kcfinder"
                       value="" style="cursor: pointer" /> <a href="#"
                       onclick="$('#menu_image').val('');return false;"
                       class="btn btn-default voffset2" class="btn btn-default"><i
                        class="fa fa-eraser"></i> <?php translate("clear"); ?>
                </a>
            </div>
        </div>
        <div class="typedep" id="tab-link" style="display: none;">
            <h2 class="accordion-header"><?php translate("link_url"); ?></h2>
            <div class="accordion-content">
                <strong><?php translate("link_url"); ?>
                </strong><br /> <input type="text" name="redirection" value="">
            </div>
        </div>
        <div class="typedep" id="tab-language-link" style="display: none;">
            <h2 class="accordion-header"><?php translate("language_link"); ?></h2>
            <div class="accordion-content">
                <strong><?php translate("language_link"); ?>
                </strong><br />
                <?php
                $languages = Language::getAllLanguages();
                ?>
                <select name="link_to_language">
                    <option value="">[<?php translate("none"); ?>]</option>
                    <?php foreach ($languages as $language) { ?>
                        <option value="<?php Template::escape($language->getID()); ?>"><?php Template::escape($language->getName()); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="typedep" id="tab-metadata">
            <h2 class="accordion-header"><?php translate("metadata"); ?></h2>
            <div class="accordion-content">
                <strong><?php translate("meta_description"); ?>
                </strong><br /> <input type="text" name="meta_description" value=''
                                       maxlength="200"> <br /> <strong><?php translate("meta_keywords"); ?>
                </strong><br /> <input type="text" name="meta_keywords" value=''
                                       maxlength="200">
                <div class="typedep" id="article-metadata">
                    <br /> <strong><?php translate("author_name"); ?></strong><br /> <input
                        type="text" name="article_author_name" value="" maxlength="80"> <br />
                    <strong><?php translate("author_email"); ?></strong><br /> <input
                        type="email" name="article_author_email" value="" maxlength="80"> <br />

                    <strong><?php translate("article_date"); ?></strong><br /> <input
                        name="article_date" type="datetime-local"
                        value="<?php echo NumberFormatHelper::timestampToHtml5Datetime(); ?>" step="any"> <br /> <strong><?php translate("excerpt"); ?></strong>
                    <textarea name="excerpt" id="excerpt" rows="5" cols="80" class="<?php esc($editor); ?>" data-mimetype="text/html"></textarea>
                </div>
                <div class="typedep" id="tab-og" style="display: none;">
                    <h3><?php translate("open_graph"); ?></h3>
                    <p><?php translate("og_help"); ?></p>
                    <strong><?php translate("title"); ?>
                    </strong><br /> <input type="text" name="og_title" value=""> <br /> <strong><?php translate("description"); ?>
                    </strong><br /> <input type="text" name="og_description" value=""> <br />
                    <strong><?php translate("type"); ?>
                    </strong><br /> <input type="text" name="og_type" value=""> <br /> <strong><?php translate("image"); ?></strong>
                    <br />
                    <input type="text" id="og_image" name="og_image" readonly="readonly"
                           class="kcfinder"
                           value="<?php esc($og_image); ?>"
                           style="cursor: pointer" /> <a href="#"
                           onclick="$('#og_image').val('');
                                   return false;"
                           class="btn btn-default voffset2"><i class="fa fa-eraser"></i> <?php translate("clear"); ?></a>
                </div>
            </div>
        </div>
        <div id="custom_fields_container">
            <?php
            foreach (DefaultContentTypes::getAll() as $name => $type) {
                $fields = $type->customFields;
                if (count($fields) > 0) {
                    ?>
                    <div class="custom-field-tab" data-type="<?php echo $name; ?>">
                        <h2 class="accordion-header"><?php translate($type->customFieldTabTitle ? $type->customFieldTabTitle : $name); ?></h2>
                        <div class="accordion-content">
                            <?php
                            foreach ($fields as $field) {
                                $field->name = "{$name}_{$field->name}";
                                ?>
                                <?php echo $field->render(null); ?>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <div class="typedep" id="tab-list" style="display: none">
            <h2 class="accordion-header"><?php translate("list_properties"); ?></h2>
            <div class="accordion-content">
                <strong><?php translate("type") ?></strong> <br />
                <?php $types = get_available_post_types(); ?>
                <select name="list_type">
                    <option value="null" selected>[<?php translate("every") ?>]
                    </option>
                    <?php
                    foreach ($types as $type) {
                        echo '<option value="' . $type . '">' . get_translation($type) . "</option>";
                    }
                    ?>
                </select> <br /> <br /> <strong><?php translate("language"); ?>
                </strong> <br /> <select name="list_language">
                    <option value="">[<?php translate("every"); ?>]</option>
                    <?php
                    $languages = getAllLanguages();

                    for ($j = 0; $j < count($languages); $j ++) {
                        echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . "</option>";
                    }
                    ?>
                </select> <br /> <br /> <strong><?php translate("category"); ?>
                </strong><br />
                <?php echo Categories :: getHTMLSelect(-1, true, "list_category") ?>
                <br /> <br /> <strong><?php
                    translate("menu");
                    ?>
                </strong><br /> <select name="list_menu" size=1>
                    <option value="">[<?php translate("every"); ?>]</option>
                    <?php
                    foreach (getAllMenus() as $menu) {
                        ?>
                        <option value="<?php echo $menu; ?>">
                            <?php
                            translate($menu);
                            ?></option>
                        <?php
                    }
                    ?>
                </select> <br /> <br /> <strong><?php translate("parent_id"); ?>
                </strong><br /> <select name="list_parent" size=1>
                    <option selected="selected" value="NULL">
                        [
                        <?php
                        translate("every");
                        ?>
                        ]
                    </option>
                    <?php
                    foreach ($pages as $key => $page) {
                        ?>
                        <option value="<?php
                        echo $page["id"];
                        ?>">
                                    <?php
                                    esc($page["title"]);
                                    ?>
                            (ID:
                            <?php
                            echo $page["id"];
                            ?>
                            )
                        </option>
                        <?php
                    }
                    ?>
                </select> <br /> <br /> <strong><?php
                    translate("order_by");
                    ?>
                </strong> <br /> <select name="list_order_by">
                    <?php foreach ($cols as $col) { ?>
                        <option value="<?php echo $col; ?>"
                        <?php
                        if ($col == "title") {
                            echo 'selected';
                        }
                        ?>><?php echo $col; ?></option>
                            <?php } ?>
                </select> <br /> <br /> <strong><?php
                    translate("order_direction");
                    ?>
                </strong> <select name="list_order_direction">
                    <option value="asc"><?php translate("asc"); ?></option>
                    <option value="desc"><?php translate("desc"); ?></option>
                </select> <br /> <br /> <strong><?php translate("limit"); ?></strong>
                <input type="number" min="0" name="limit" step="1" value="0"> <br />
                <strong><?php translate("use_pagination"); ?></strong><br /> <select
                    name="list_use_pagination">
                    <option value="1"><?php translate("yes") ?></option>
                    <option value="0" selected><?php translate("no") ?></option>
                </select>
            </div>
        </div>
        <div class="typedep" id="tab-module" style="display: none;">
            <h2 class="accordion-header"><?php translate("module"); ?></h2>
            <div class="accordion-content">
                <strong><?php translate("module"); ?></strong><br /> <select
                    name="module">
                    <option value="null">[<?php translate("none"); ?>]</option>
                    <?php foreach (ModuleHelper::getAllEmbedModules() as $module) { ?>
                        <option value="<?php echo $module; ?>"><?php echo $module; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="typedep" id="tab-video" style="display: none;">
            <h2 class="accordion-header"><?php translate("video"); ?></h2>
            <div class="accordion-content">
                <strong><?php translate("video"); ?></strong><br /> <select
                    name="video">
                    <option value="">[<?php translate("none"); ?>]</option>
                    <?php while ($row = Database::fetchObject($videos)) { ?>
                        <option value="<?php echo $row->id; ?>"><?php Template::escape($row->name); ?> (ID: <?php echo $row->id; ?>)</option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="typedep" id="tab-audio" style="display: none;">
            <h2 class="accordion-header"><?php translate("audio"); ?></h2>
            <div class="accordion-content">
                <strong><?php translate("audio"); ?></strong><br /> <select
                    name="audio">
                    <option value="">[<?php translate("none"); ?>]</option>
                    <?php while ($row = Database::fetchObject($audios)) { ?>
                        <option value="<?php echo $row->id; ?>"><?php Template::escape($row->name); ?> (ID: <?php echo $row->id; ?>)</option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="typedep" id="tab-image" style="display: none;">
            <h2 class="accordion-header"><?php translate("image"); ?></h2>
            <div class="accordion-content">
                <input type="text" id="image_url" name="image_url"
                       readonly="readonly" class="kcfinder"
                       value="" style="cursor: pointer" /> <a href="#"
                       onclick="$('#menu_image').val('');return false;"
                       class="btn btn-default voffset2"><i class="fa fa-eraser"></i> <?php
                           translate("clear");
                           ?>
                </a>
            </div>
        </div>
        <div class="typedep" id="tab-text-position" style="display: none">
            <h2 class="accordion-header"><?php translate("position_of_description"); ?></h2>
            <div class="accordion-content">
                <strong><?php translate("position_of_description"); ?>
                </strong> <br /> <select name="text_position">
                    <option value="before"><?php translate("description_before_content") ?></option>
                    <option value="after"><?php translate("description_after_content") ?></option>
                </select>
            </div>
        </div>
        <div class="typedep" id="article-image">
            <h2 class="accordion-header"><?php translate("article_image"); ?></h2>
            <div class="accordion-content">
                <strong><?php translate("article_image"); ?>
                </strong><br />
                <input type="text" id="article_image" name="article_image"
                       readonly="readonly" class="kcfinder"
                       value="" style="cursor: pointer" maxlength="255" /> <a href="#"
                       onclick="$('#article_image').val('');
                               return false;"
                       class="btn btn-default voffset2"><i class="fa fa-eraser"></i> <?php translate("clear"); ?></a>
            </div>
        </div>
        <div class="typedep" id="tab-comments">
            <h2 class="accordion-header"><?php translate("comments"); ?></h2>
            <div class="accordion-content">
                <strong><?php translate("comments_enabled"); ?></strong> <br /> <select
                    name="comments_enabled">
                    <option value="null" selected>[<?php translate("standard"); ?>]</option>
                    <option value="1"><?php translate("yes"); ?></option>
                    <option value="0"><?php translate("no"); ?></option>
                </select>
            </div>
        </div>
        <h2 class="accordion-header"><?php translate("other"); ?></h2>
        <div class="accordion-content">
            <div class="typedep" id="tab-cache-control" style="display: none;">
                <strong><?php translate("cache_control"); ?></strong> <br /> <select
                    name="cache_control">
                    <option value="auto" selected><?php translate("auto"); ?></option>
                    <option value="force"><?php translate("force"); ?></option>
                    <option value="no_cache"><?php translate("no_cache"); ?></option>
                </select> <br /> <br />
            </div>
            <div class="typedep" id="tab-menu-image">
                <strong><?php translate("design"); ?></strong><br /> <select
                    name="theme" size=1>
                    <option value="">
                        [
                        <?php translate("standard"); ?>
                        ]
                    </option>
                    <?php
                    foreach ($allThemes as $th) {
                        ?>
                        <option value="<?php
                        echo $th;
                        ?>">
                                    <?php
                                    echo $th;
                                    ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <br /> <strong><?php translate("visible_for"); ?>
            </strong><br /> <select name="access[]" size=4 multiple>
                <option value="all" selected>
                    <?php translate("everyone"); ?>
                </option>
                <option value="registered">
                    <?php
                    translate("registered_users");
                    ?>
                </option>
                <option value="mobile"><?php translate("mobile_devices"); ?></option>
                <option value="desktop"><?php translate("desktop_computers"); ?></option>
                <?php
                while ($row = db_fetch_object($groups)) {
                    echo '<option value="' . $row->id . '">' . _esc($row->name) . '</option>';
                }
                ?>
            </select> <br /> <br />
            <div class="typedep" id="custom_data_json">
                <?php do_event("before_custom_data_json"); ?>
                <strong><?php translate("custom_data_json"); ?></strong>
                <textarea name="custom_data" style="width: 100%; height: 200px;"
                          cols=80 rows=10
                          class="codemirror" data-mimetype="application/json" data-validate="json"><?php esc(CustomData::getDefaultJSON()); ?></textarea>
            </div>
        </div>
    </div>
    <br />
    <br />
    <?php
    do_event("page_option");
    ?>
    <div class="typedep" id="content-editor">
        <textarea name="content" id="content" cols=60 rows=20
                  class="<?php esc($editor); ?>" data-mimetype="text/html"></textarea>

    </div>
    <div class="inPageMessage"></div>
    <input type="hidden" name="add_page" value="add_page">
    <button type="submit" class="btn btn-primary">
        <i class="far fa-save"></i> <?php translate("save"); ?></button>
    <?php
    $translation = new JSTranslation(array(), "PageTranslation");
    $translation->addKey("confirm_exit_without_save");
    $translation->render();

    enqueueScriptFile("../node_modules/slug/slug.js");

    BackendHelper::enqueueEditorScripts();

    enqueueScriptFile("scripts/page.js");

    combinedScriptHtml();
    ?>
    <?php echo ModuleHelper::endForm(); ?>
    <?php
} else {
    noPerms();
}
