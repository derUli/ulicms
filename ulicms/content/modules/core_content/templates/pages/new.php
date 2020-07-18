<?php

// TODO: Rewrite this view, move logic to PageController
// Join new and edit views to one form
use UliCMS\Models\Content\TypeMapper;
use UliCMS\Models\Content\Categories;
use UliCMS\Models\Content\Language;
use UliCMS\Models\Content\Types\DefaultContentTypes;
use UliCMS\Helpers\NumberFormatHelper;
use UliCMS\CoreContent\UIUtils;
use function UliCMS\HTML\icon;
use UliCMS\HTML\Input;

$parent_id = Request::getVar("parent_id", null, "int");

$permissionChecker = new ACL();
$groups = db_query("SELECT id, name from " . tbname("groups"));
if ($permissionChecker->hasPermission("pages")
        and $permissionChecker->hasPermission("pages_create")) {
    $editor = get_html_editor();

    $allThemes = getAllThemes();
    $cols = Database::getColumnNames("content");

    // FIXME: No SQL in Views
    $sql = "SELECT id, name FROM " . tbname("videos");
    $videos = Database::query($sql);
    $sql = "SELECT id, name FROM " . tbname("audio");
    $audios = Database::query($sql);

    $pages_approve_own = $permissionChecker->hasPermission("pages_approve_own");

    $types = get_available_post_types();
    ?>
    <div class="loadspinner">
    <?php require "inc/loadspinner.php"; ?>
    </div>
    <?php
    echo ModuleHelper::buildMethodCallForm(
            "PageController",
            "create",
            [],
            "post",
            array(
                "name" => "newpageform",
                "id" => "pageform",
                "style" => "display:none",
                "class" => "pageform main-form new-page-form",
                "data-get-content-types-url" =>
                ModuleHelper::buildMethodCallUrl(
                        PageController::class,
                        "getContentTypes"
                ),
                "data-slug-free-url" =>
                ModuleHelper::buildMethodCallUrl(
                        PageController::class,
                        "nextFreeSlug"
                ),
                "data-parent-pages-url" =>
                ModuleHelper::buildMethodCallUrl(
                        PageController::class,
                        "filterParentPages"
                )
            )
    );
    ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("pages"); ?>"
           class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i>
    <?php translate("back") ?></a>
    </p>
    <input type="hidden" name="add" value="add">
    <div id="accordion-container">
        <h2 class="accordion-header"><?php translate("title_and_headline"); ?></h2>
        <div class="accordion-content">
            <div class="field">
                <strong class="field-label"><?php translate("permalink"); ?>*
                </strong>
                <input type="text" name="slug" id="slug"
                       required="required" value="">
                <small>
    <?php translate("auto_generated_from_title"); ?>
                </small>
            </div>

            <div class="field">
                <strong class="field-label">
                    <?php translate("page_title"); ?>*
                </strong>
                <input type="text" required="required"
                       name="title" value="" onkeyup="suggestSlug(this.value)">
            </div>
            <div class="typedep hide-on-snippet hide-on-non-regular">
                <div class="field">
                    <strong class="field-label">
                        <?php translate("alternate_title"); ?>
                    </strong>
                    <input type="text" name="alternate_title" value="">
                    <small>
                        <?php translate("ALTERNATE_TITLE_INFO"); ?>
                    </small>
                </div>
                <div class="field">
                    <strong class="field-label">
                        <?php translate("show_headline"); ?>
                    </strong>
                    <select name="show_headline">
                        <option value="1" selected><?php translate("yes"); ?></option>
                        <option value="0"><?php translate("no"); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <h2 class="accordion-header"><?php translate("page_type"); ?></h2>
        <div class="accordion-content">
            <?php foreach ($types as $type) {
                $model = TypeMapper::getModel($type);
                ?>
                <div>
                    <input type="radio" name="type" id="type_<?php echo $type; ?>"
                           value="<?php echo $type; ?>"
                           <?php
                           if ($type == DEFAULT_CONTENT_TYPE) {
                               echo "checked";
                           }
                           ?>> 

                    <label
                        for="type_<?php echo $type; ?>">
                            <?php
                                echo icon(
                                        $model->getIcon(),
                                        ["class" => "type-icon"]
                                );
                            ?>
        <?php translate($type); ?>
                    </label>
                </div>
    <?php } ?>
        </div>
        <h2 class="accordion-header"><?php translate("menu_entry"); ?></h2>
        <div class="accordion-content">
            <div class="field">
                <strong class="field-label">
    <?php translate("language"); ?>
                </strong>
                <select name="language">
                    <?php
                    $languages = getAllLanguages(true);
                    if (!empty($_SESSION["filter_language"])) {
                        $default_language = $_SESSION["filter_language"];
                    } else {
                        $default_language = Settings::get("default_language");
                    }

                    for ($j = 0; $j < count($languages); $j++) {
                        if ($languages[$j] === $default_language) {
                            echo "<option value='" . $languages[$j] . "' selected>" . getLanguageNameByCode($languages[$j]) . "</option>";
                        } else {
                            echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . "</option>";
                        }
                    }

                    $pages = getAllPages($default_language, "title", false);
                    ?>
                </select>
            </div>
            <div class="typedep menu-stuff">
                <div class="field">
                    <strong class="field-label">
    <?php translate("menu"); ?>
                        <span class="has-help"
                              onclick="$('div#menu_help').slideToggle()"><i class="fa fa-question-circle text-info" aria-hidden="true"></i></span>
                    </strong>
                    <select
                        name="menu" size=1>
                            <?php
                            foreach (getAllMenus() as $menu) {
                                ?>
                            <option value="<?php echo $menu ?>"
                                    <?php
                                if ($menu == DEFAULT_MENU) {
                                    echo "selected";
                                }
                                ?>>
        <?php translate($menu); ?></option>
        <?php }
    ?>
                    </select>
                </div>

                <div id="menu_help" class="help" style="display: none">
                        <?php echo nl2br(get_translation("help_menu")); ?>
                </div>
                <div class="field">
                    <strong class="field-label">
    <?php translate("position"); ?>
                        <span class="has-help"
                              onclick="$('div#position_help').slideToggle()">
                            <i class="fa fa-question-circle text-info" aria-hidden="true"></i></span>
                    </strong>
                    <input
                        type="number" required="required" name="position" value="0" min="0"
                        step="1">
                    <div id="position_help" class="help" style="display: none">
                        <?php echo nl2br(get_translation("help_position")); ?>
                    </div>
                </div>
                <div id="parent-div" class="field">
                    <strong class="field-label">
                        <?php translate("parent_id"); ?>
                    </strong>
                    <select
                        name="parent_id" size=1>
                        <option <?php
                            if (!$parent_id) {
                                echo "selected";
                            }
                            ?> value="NULL">
                            [
                        <?php translate("none"); ?>
                            ]
                        </option>
                                <?php
                                foreach ($pages as $key => $page) {
                                    ?>
                            <option value="<?php echo $page["id"]; ?>"
                                        <?php
                                        if ($parent_id == $page["id"]) {
                                            echo "selected";
                                        }
                                        ?>>
        <?php esc($page["title"]); ?>
                                (ID:
                            <?php echo $page["id"]; ?>
                                )
                            </option>
        <?php }
    ?>
                    </select>
                </div>
            </div>
            <div class="typedep" id="tab-target">
                <div class="field">
                    <strong class="field-label"><?php translate("open_in"); ?>
                    </strong>
                    <select name="target" size=1>
                        <option value="_self">
                            <?php translate("target_self"); ?>
                        </option>
                        <option value="_blank">
    <?php translate("target_blank"); ?>
                        </option>
                    </select>
                </div>
            </div>
            <div class="field">
                <strong class="field-label">
                <?php translate("activated"); ?>
                </strong>
                <select name="active" size=1
                        <?php
                        if (!$pages_approve_own) {
                            echo "disabled";
                        }
                        ?>>
                    <option value="1">
                            <?php translate("enabled"); ?>
                    </option>
                    <option value="0" <?php
                        if (!$pages_approve_own) {
                            echo "selected";
                        }
                        ?>>
                        <?php translate("disabled"); ?>
                    </option>
                </select>
            </div>
            <div class="typedep" id="hidden-attrib">
                <div class="field">
                    <strong class="field-label">
                            <?php translate("hidden"); ?>
                    </strong>
                    <select name="hidden" size="1">
                        <option value="1">
    <?php translate("yes"); ?>
                        </option>
                        <option value="0" selected>
                    <?php translate("no"); ?>
                        </option>
                    </select>
                </div>
            </div>
            <div class="field">
                <strong class="field-label">
                    <?php translate("category"); ?>
                </strong>
    <?php echo Categories :: getHTMLSelect(); ?>
            </div>

            <div id="menu_image_div" class="field">
                <strong class="field-label">
    <?php translate("menu_image"); ?>
                </strong>

                <input type="text" id="menu_image" name="menu_image"
                       readonly="readonly" class="fm"
                       value="" style="cursor: pointer" /> <a href="#"
                       onclick="$('#menu_image').val('');return false;"
                       class="btn btn-default voffset2" class="btn btn-default"><i
                        class="fa fa-eraser"></i> <?php translate("clear"); ?>
                </a>
            </div>
        </div>
        <div class="typedep" id="tab-link" style="display: none;">
            <div class="field">
                <h2 class="accordion-header"><?php translate("link_url"); ?></h2>
                <div class="accordion-content">
                    <strong class="field-label">
    <?php translate("link_url"); ?>
                    </strong>
                    <input type="text" name="link_url" value="">
                </div>
            </div>
        </div>
        <div class="typedep" id="tab-language-link" style="display: none;">
            <h2 class="accordion-header"><?php translate("language_link"); ?></h2>
            <div class="accordion-content">
                <strong class="field-label">
                    <?php translate("language_link"); ?>
                </strong>

                <select name="link_to_language">
                    <option value="">[<?php translate("none"); ?>]</option>
    <?php foreach (Language::getAllLanguages() as $language) { ?>
                        <option value="<?php Template::escape($language->getID()); ?>"><?php Template::escape($language->getName()); ?></option>
    <?php } ?>
                </select>
            </div>
        </div>
        <div class="typedep" id="tab-metadata">
            <h2 class="accordion-header"><?php translate("metadata"); ?></h2>
            <div class="accordion-content">
                <div class="field">
                    <strong class="field-label">
                        <?php translate("meta_description"); ?>
                    </strong>
                    <input type="text" name="meta_description" value=''
                           maxlength="200">
                </div>
                <div class="field">
                    <strong class="field-label">
                        <?php translate("meta_keywords"); ?>
                    </strong>
                    <input type="text" name="meta_keywords" value='' maxlength="200" placeholder="<?php translate("comma_separated"); ?>">
                </div>
                <div class="field">
                    <strong class="field-label">
                    <?php translate("robots"); ?>
                    </strong>
    <?php
    echo Input::singleSelect(
            "robots",
            null,
            UIUtils::getRobotsListItems()
    );
    ?>
                </div>
                <div class="typedep" id="article-metadata">

                    <div class="field">
                        <strong class="field-label">
                            <?php translate("author_name"); ?>
                        </strong>
                        <input type="text" name="article_author_name" value="" maxlength="80">
                    </div>

                    <div class="field">
                        <strong class="field-label">
                            <?php translate("author_email"); ?>
                        </strong>
                        <input type="email" name="article_author_email" value="" maxlength="80">
                    </div>

                    <div class="field">
                        <strong class="field-label">
    <?php translate("article_date"); ?>
                        </strong>
                        <input
                            name="article_date" type="text"
                            class="datetimepicker"
                            value="<?php echo NumberFormatHelper::timestampToSqlDate(); ?>" step="any"
                            >
                    </div>

                    <div class="field">
                        <strong class="field-label">
    <?php translate("excerpt"); ?>
                        </strong>
                        <textarea name="excerpt" id="excerpt" rows="5" cols="80" class="<?php esc($editor); ?>" data-mimetype="text/html"></textarea>
                    </div>
                </div>
                <div class="typedep" id="tab-og" style="display: none;">
                    <h3><?php translate("open_graph"); ?></h3>
                    <p><?php translate("og_help"); ?></p>
                    <div class="field">
                        <strong class="field-label">
    <?php translate("title"); ?>
                        </strong>
                        <input type="text" name="og_title" value="">
                    </div>
                    <div class="field">
                        <strong class="field-label">
    <?php translate("description"); ?>
                        </strong>
                        <input type="text" name="og_description" value="">
                    </div>
                    <div class="field">
                        <strong class="field-label"><?php translate("image"); ?></strong>
                        <input type="text" id="og_image" name="og_image" readonly="readonly"
                               class="fm"
                               value="<?php esc($og_image); ?>"
                               style="cursor: pointer" /> <a href="#"
                               onclick="$('#og_image').val('');
                                           return false;"
                               class="btn btn-default voffset2"><i class="fa fa-eraser"></i> <?php translate("clear"); ?></a>
                    </div>
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
                <?php }
            ?>
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
                <div class="field">
                    <strong class="field-label">
                        <?php translate("type") ?>
                    </strong>

                    <select name="list_type">
                        <option value="null" selected>[<?php translate("every") ?>]
                        </option>
                        <?php
                        foreach ($types as $type) {
                            echo '<option value="' . $type . '">' . get_translation($type) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="field">
                    <strong class="field-label">
    <?php translate("language"); ?>
                    </strong>
                    <select name="list_language">
                        <option value="">[<?php translate("every"); ?>]</option>
                        <?php
                        $languages = getAllLanguages();

                        for ($j = 0; $j < count($languages); $j++) {
                            echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="field">
                    <strong class="field-label">
                        <?php translate("category"); ?>
                    </strong>
                        <?php echo Categories :: getHTMLSelect(-1, true, "list_category") ?>
                </div>

                <div class="field">
                    <strong class="field-label">
                        <?php translate("menu"); ?>
                    </strong>
                    <select name="list_menu" size="1">
                        <option value="">[<?php translate("every"); ?>]</option>
    <?php
    foreach (getAllMenus() as $menu) {
        ?>
                            <option value="<?php echo $menu; ?>">
        <?php translate($menu); ?></option>
        <?php }
    ?>
                    </select>
                </div>

                <div class="field">
                    <strong class="field-label">
                        <?php translate("parent_id"); ?>
                    </strong>
                    <select name="list_parent" size=1>
                        <option selected="selected" value="">
                            [
                                    <?php translate("every"); ?>
                            ]
                        </option>
                            <?php
                            foreach ($pages as $key => $page) {
                                ?>
                            <option value="<?php echo $page["id"]; ?>">
                            <?php esc($page["title"]); ?>
                                (ID:
        <?php echo $page["id"]; ?>
                                )
                            </option>
                            <?php }
                        ?>
                    </select>
                </div>

                <div class="field">
                    <strong class="field-label">
                        <?php translate("order_by"); ?>
                    </strong>
                    <select name="list_order_by">
                                <?php foreach ($cols as $col) { ?>
                            <option value="<?php echo $col; ?>"
        <?php
        if ($col == "title") {
            echo 'selected';
        }
        ?>><?php echo $col; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="field">
                    <strong class="field-label">
    <?php translate("order_direction"); ?>
                    </strong>
                    <select name="list_order_direction">
                        <option value="asc"><?php translate("asc"); ?></option>
                        <option value="desc"><?php translate("desc"); ?></option>
                    </select>
                </div>

                <div class="field">
                    <strong class="field-label">
                        <?php translate("entries_per_page"); ?>
                    </strong>
                    <input type="number" min="0" name="limit" step="1" value="0">
                </div>

                <div class="field">
                    <strong class="field-label">
    <?php translate("use_pagination"); ?>
                    </strong>

                    <select
                        name="list_use_pagination">
                        <option value="1"><?php translate("yes") ?></option>
                        <option value="0" selected><?php translate("no") ?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="typedep" id="tab-module" style="display: none;">
            <h2 class="accordion-header"><?php translate("module"); ?></h2>
            <div class="accordion-content">
                <div class="field">
                    <strong class="field-label">
                        <?php translate("module"); ?>
                    </strong>
                    <select
                        name="module">
                        <option value="null">[<?php translate("none"); ?>]</option>
    <?php foreach (ModuleHelper::getAllEmbedModules() as $module) { ?>
                            <option value="<?php echo $module; ?>"><?php echo $module; ?></option>
    <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="typedep" id="tab-video" style="display: none;">
            <h2 class="accordion-header"><?php translate("video"); ?></h2>
            <div class="accordion-content">
                <div class="field">
                    <strong class="field-label">
                        <?php translate("video"); ?>
                    </strong>
                    <select
                        name="video">
                        <option value="">[<?php translate("none"); ?>]</option>
    <?php while ($row = Database::fetchObject($videos)) { ?>
                            <option value="<?php echo $row->id; ?>"><?php Template::escape($row->name); ?> (ID: <?php echo $row->id; ?>)</option>
    <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="typedep" id="tab-audio" style="display: none;">
            <div class="field">
                <h2 class="accordion-header"><?php translate("audio"); ?></h2>
                <div class="accordion-content">
                    <strong class="field-label">
                        <?php translate("audio"); ?>
                    </strong>
                    <select
                        name="audio">
                        <option value="">[<?php translate("none"); ?>]</option>
    <?php while ($row = Database::fetchObject($audios)) { ?>
                            <option value="<?php echo $row->id; ?>"><?php Template::escape($row->name); ?> (ID: <?php echo $row->id; ?>)</option>
    <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="typedep" id="tab-image" style="display: none;">
            <h2 class="accordion-header"><?php translate("image"); ?></h2>
            <div class="accordion-content">
                <input type="text" id="image_url" name="image_url"
                       readonly="readonly" class="fm"
                       value="" style="cursor: pointer" /> <a href="#"
                       onclick="$('#menu_image').val('');return false;"
                       class="btn btn-default voffset2"><i class="fa fa-eraser"></i> <?php translate("clear"); ?>
                </a>
            </div>
        </div>
        <div class="typedep" id="tab-text-position" style="display: none">
            <h2 class="accordion-header"><?php translate("position_of_description"); ?></h2>
            <div class="accordion-content">
                <div class="field">
                    <strong class="field-label">
    <?php translate("position_of_description"); ?>
                    </strong>
                    <select name="text_position">
                        <option value="before"><?php translate("description_before_content") ?></option>
                        <option value="after"><?php translate("description_after_content") ?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="typedep" id="article-image">
            <h2 class="accordion-header"><?php translate("article_image"); ?></h2>
            <div class="accordion-content">
                <div class="field">
                    <strong class="field-label">
    <?php translate("article_image"); ?>
                    </strong>

                    <input type="text" id="article_image" name="article_image"
                           readonly="readonly" class="fm"
                           value="" style="cursor: pointer" maxlength="255" /> <a href="#"
                           onclick="$('#article_image').val('');
                                       return false;"
                           class="btn btn-default voffset2"><i class="fa fa-eraser"></i> <?php translate("clear"); ?></a>
                </div>
            </div>
        </div>
        <div class="typedep" id="tab-comments">

            <h2 class="accordion-header"><?php translate("comments"); ?></h2>
            <div class="accordion-content">
                <div class="field">
                    <strong class="field-label">
    <?php translate("comments_enabled"); ?>
                    </strong>
                    <select
                        name="comments_enabled">
                        <option value="null" selected>[<?php translate("standard"); ?>]</option>
                        <option value="1"><?php translate("yes"); ?></option>
                        <option value="0"><?php translate("no"); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <h2 class="accordion-header"><?php translate("other"); ?></h2>
        <div class="accordion-content">
            <div class="typedep" id="tab-cache-control" style="display: none;">
                <div class="field">
                    <strong class="field-label">
    <?php translate("cache_control"); ?>
                    </strong>
                    <select
                        name="cache_control">
                        <option value="auto" selected><?php translate("auto"); ?></option>
                        <option value="force"><?php translate("force"); ?></option>
                        <option value="no_cache"><?php translate("no_cache"); ?></option>
                    </select>
                </div>
            </div>
            <div class="typedep" id="tab-menu-image">

                <div class="field">
                    <strong class="field-label">
                            <?php translate("design"); ?>
                    </strong>
                    <select
                        name="theme" size=1>
                        <option value="">
                            [
                        <?php translate("standard"); ?>
                            ]
                        </option>
                                    <?php
                            foreach ($allThemes as $th) {
                                ?>
                            <option value="<?php echo $th; ?>">
        <?php echo $th; ?></option>
        <?php }
    ?>
                    </select>
                </div>
            </div>
            <div class="field">
                <strong class="field-label">
                        <?php translate("visible_for"); ?>
                </strong>
                <select name="access[]" size=4 multiple>
                    <option value="all" selected>
                        <?php translate("everyone"); ?>
                    </option>
                    <option value="registered">
    <?php translate("registered_users"); ?>
                    </option>
                    <option value="mobile"><?php translate("mobile_devices"); ?></option>
                    <option value="desktop"><?php translate("desktop_computers"); ?></option>
                    <?php
                    while ($row = db_fetch_object($groups)) {
                        echo '<option value="' . $row->id . '">' . _esc($row->name) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="typedep" id="custom_data_json">
    <?php do_event("before_custom_data_json"); ?>
                <div class="field">
                    <strong class="field-label"><?php translate("custom_data_json"); ?></strong>
                    <textarea name="custom_data" style="width: 100%; height: 200px;"
                              cols=80 rows=10
                              class="codemirror" data-mimetype="application/json" data-validate="json"><?php esc(CustomData::getDefaultJSON()); ?></textarea>
                </div>
            </div>
        </div>
    </div>
    <?php do_event("page_option"); ?>
    <div class="typedep" id="content-editor">
        <div class="field">
            <textarea name="content" id="content" cols=60 rows=20
                      class="<?php esc($editor); ?>" data-mimetype="text/html"></textarea>
        </div>
    </div>
    <div class="inPageMessage"></div>
    <input type="hidden" name="add_page" value="add_page">
    <button type="submit" class="btn btn-primary btn-new" id="btn-submit">
        <i class="far fa-save"></i> <?php translate("save"); ?>
    </button>
    <?php
    $translation = new JSTranslation([], "PageTranslation");
    $translation->addKey("confirm_exit_without_save");
    $translation->addKey("fill_all_required_fields");
    $translation->render();

    enqueueScriptFile("../node_modules/slug/slug.js");

    BackendHelper::enqueueEditorScripts();

    enqueueScriptFile(ModuleHelper::buildRessourcePath("core_content", "js/pages/form.js"));

    combinedScriptHtml();

    echo ModuleHelper::endForm();
} else {
    noPerms();
}
