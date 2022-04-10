<?php

// TODO: refactor this file into multiple files
use UliCMS\Models\Content\Categories;
use UliCMS\HTML\Alert;
use UliCMS\Localization\JSTranslation;

$permissionChecker = new ACL();
if (!$permissionChecker->hasPermission("categories")) {
    noPerms();
} else {
    if (isset($_GET["order"]) and faster_in_array($_GET["order"], array(
                "id",
                "name",
                "description",
                "created",
                "updated"
            ))) {
        $order = db_escape($_GET["order"]);
    } else {
        $order = "id";
    }
    $categories = Categories::getAllCategories($order);
    ?>
    <?php
    if (!isset($_GET["add"]) && !isset($_GET["edit"])
            and $permissionChecker->hasPermission("categories_create")) {
        ?>
        <?php
        echo Template::executeModuleTemplate(
                "core_content",
                "icons.php"
        );
        ?>
        <h2><?php translate("categories"); ?></h2>
        <?php
        echo Alert::info(
                get_translation("categories_infotext")
        );
        ?>
        <div class="field">
            <a href="?action=categories&add"
               class="btn btn-default is-ajax"
               ><i
                    class="fa fa-plus"></i>
                <?php translate("create_category"); ?></a>
        </div>
    <?php }
    ?>
    <?php
    if (count($categories) > 0 && !isset($_GET["add"]) && !isset($_GET["edit"])) {
        ?>
        <div class="scroll">
            <table class="tablesorter">
                <thead>
                    <tr>
                        <th style="min-width: 50px;">
                            <?php translate("id"); ?>
                        </th>
                        <th style="min-width: 200px;">
                            <?php translate("name"); ?>
                        </th>
                        <th style="min-width: 200px;" class="hide-on-mobile">
                            <?php translate("description"); ?>
                        </th>
                        <?php
                        if ($permissionChecker->hasPermission("categories_edit")) {
                            ?>
                            <td class="no-sort"></td>
                            <td class="no-sort"></td>
                        <?php }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($categories as $category) {
                        ?>
                        <tr id="dataset-<?php echo $category->getID(); ?>">
                            <td><?php echo $category->getId(); ?></td>
                            <td style="padding-right: 20px;"><?php esc($category->getName()); ?></td>
                            <td style="padding-right: 20px;"
                                class="hide-on-mobile">
                                    <?php echo nl2br(_esc($category->getDescription())); ?>
                            </td>
                            <?php
                            if ($permissionChecker->hasPermission(
                                            "categories_edit"
                                    )) {
                                ?>
                                <td class="text-center"><a
                                        href="?action=categories&edit=<?php echo $category->getID(); ?>"
                                        class="is-ajax">
                                        <img
                                            src="gfx/edit.png"
                                            class="mobile-big-image"
                                            alt="<?php translate("edit"); ?>"
                                            title="<?php translate("edit"); ?>"></a>
                                </td>
                                <?php
                                if ($category->getId() != 1) {
                                    ?>
                                    <td class="text-center"><form
                                            action="?sClass=CategoryController&sMethod=delete&del=<?php echo $category->getId(); ?>"
                                            method="post"
                                            class="delete-form"><?php csrf_token_html(); ?><input
                                                type="image"
                                                class="mobile-big-image" src="gfx/delete.png"
                                                alt="<?php translate("delete"); ?>"
                                                title="<?php translate("delete"); ?>">
                                        </form></td>
                                    <?php
                                } else {
                                    ?>
                                    <td class="text-center">
                                        <a href="#"
                                           onclick="alert('<?php
                                           translate(
                                                   "CANT_DELETE_CATEGORY_GENERAL"
                                           );
                                           ?>')"><img
                                                class="mobile-big-image" src="gfx/delete.png"
                                                alt="<?php translate("delete"); ?>"
                                                title="<?php translate("delete"); ?>"> </a>
                                    </td>
                                <?php }
                                ?>
                            <?php }
                            ?>
                        </tr>
                    <?php }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        enqueueScriptFile(ModuleHelper::buildRessourcePath("core_content", "js/categories.js"));
        combinedScriptHtml();
        ?>
        <?php
    } elseif (isset($_GET["add"])) {
        if ($permissionChecker->hasPermission("categories_create")) {
            ?>
            <div class="btn-toolbar">
                <a href="<?php echo ModuleHelper::buildActionURL("categories"); ?>"
                   class="btn btn-default btn-back is-not-ajax">
                    <i class="fa fa-arrow-left"></i>
                    <?php translate("back") ?></a>
            </div>
            <h2><?php translate("create_category"); ?></h2>
            <?php
            echo ModuleHelper::buildMethodCallForm(
                    "CategoryController",
                    "create"
            );
            ?>
            <div class="field">
                <strong><?php translate("name"); ?>*</strong>
                <input
                    type="text"
                    name="name"
                    value=""
                    required
                    class="form-control"
                    >
            </div>
            <div class="field">
                <strong><?php translate("description"); ?></strong>
                <br />
                <textarea
                    cols="50"
                    name="description"
                    rows="5"
                    maxlength="255"
                    class="form-control"
                    ></textarea>
            </div>
            <div class="voffset2">
                <button type="submit" name="create" class="btn btn-primary">
                    <i class="fa fa-save"></i>
                    <?php translate("save"); ?>
                </button>
            </div>
            <?php echo ModuleHelper::endForm(); ?><?php
        } else {
            noPerms();
        }
    } elseif (isset($_GET["edit"])) {
        if ($permissionChecker->hasPermission("categories_edit")) {
            ?><div class="btn-toolbar">
                <a href="<?php echo ModuleHelper::buildActionURL("categories"); ?>"
                   class="btn btn-default btn-back is-not-ajax">
                    <i class="fa fa-arrow-left"></i>
                    <?php translate("back") ?></a>
            </div>
            <h2><?php translate("edit_category"); ?></h2>
            <?php
            echo ModuleHelper::buildMethodCallForm(
                    "CategoryController",
                    "update"
            );
            ?>
            <input type="hidden" name="id"
                   value="<?php echo intval($_GET["edit"]) ?>">
            <div class="field">
                <strong><?php translate("name"); ?>*</strong>
                <input
                    type="text"
                    name="name"
                    required
                    class="form-control"
                    value="<?php echo Categories::getCategoryById(intval($_GET["edit"])); ?>">
            </div>
            <div class="field">
                <strong><?php translate("description"); ?></strong> <br />
                <textarea
                    cols="50"
                    name="description"
                    rows="5"
                    class="form-control"
                    maxlength="255"><?php
                        esc(
                                Categories::getCategoryDescriptionById(
                                        intval($_GET["edit"])
                                )
                        );
                        ?></textarea>
            </div>
            <div class="voffset2">
                <button type="submit" name="update" class="btn btn-primary">
                    <i class="fa fa-save"></i> <?php translate("save"); ?></button>
            </div>
            </form>
            <?php
        } else {
            noPerms();
        }
    }
}
$translation = new JSTranslation(array(
    "ask_for_delete"
        ));
$translation->render();
