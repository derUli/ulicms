<?php

// TODO: This is old code before the switch to MVC architecture
// This should be rewritten with MVC pattern and using partial views
use UliCMS\Security\PermissionChecker;
use UliCMS\HTML\Alert;
use function UliCMS\HTML\icon;

$controller = ControllerRegistry::get(PageController::class);

$show_filters = Settings::get("user/" . get_user_id() . "/show_filters");

$permissionChecker = new PermissionChecker(get_user_id());

if ($permissionChecker->hasPermission("pages")) {
    echo Template::executeModuleTemplate("core_content", "icons.php"); ?>
    <h2><?php translate("pages"); ?></h2>
    <?php
    echo Alert::info(
        get_translation("pages_infotext")
    ); ?>

    <?php
    echo Template::executeModuleTemplate(
        "core_content",
        "pages/partials/filters/filters.php"
    ); ?>
    <div id="page-list">
        <?php if ($controller->_getPagesListView() === "default") { ?>
            <div class="row">
                <div class="col-xs-6">
                    <a href="index.php?action=pages_new" class="btn btn-primary"><i
                            class="fa fa-plus"></i> <?php translate("create_page"); ?></a>
                </div>
                <div class="col-xs-6 text-right">
                    <a href="<?php echo ModuleHelper::buildMethodCallUrl("PageController", "recycleBin"); ?>" class="btn btn-default"><i
                            class="fa fa-trash"></i> <?php translate("recycle_bin"); ?></a>
                </div>
            </div>
        <?php } elseif ($controller->_getPagesListView() === "recycle_bin") {
        ?>
            <div class="row">
                <div class="col-xs-6">
                    <a href="<?php
                    echo ModuleHelper::buildMethodCallUrl(
            PageController::class,
            "emptyTrash"
        ); ?>"
                       id="empty-trash"
                       class="btn btn-primary"><i
                            class="fas fa-broom"></i> <?php translate("empty_recycle_bin"); ?></a>
                </div>
                <div class="col-xs-6 text-right">
                    <a href="<?php echo ModuleHelper::buildMethodCallUrl("PageController", "pages"); ?>" class="btn btn-default"><i
                            class="fas fa-book"></i> <?php translate("pages"); ?></a>
                </div>
            </div>
        <?php
    } ?>
        <a
            href="#"
            class="btn btn-default voffset3"
            id="btn-go-up"
            style="display: none"
            data-url="<?php
            echo ModuleHelper::buildMethodCallUrl(
                        PageController::class,
                        "getParentPageId"
                    ); ?>">
                <?php echo icon("fas fa-arrow-up"); ?>
                <?php translate("go_up"); ?>
        </a>
        <div class="scroll voffset3">
            <table class="tablesorter dataset-list"
                   data-url="<?php echo ModuleHelper::buildMethodCallUrl("PageController", "getPages"); ?>">
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
                        <td class="no-sort text-center"><?php
                            translate(
                $controller->_getPagesListView() === "default" ? "delete" : "restore"
            ); ?>
                        </td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    enqueueScriptFile(
                                ModuleHelper::buildRessourcePath(
                "core_content",
                "js/pages/list.js"
            )
                            );
    combinedScriptHtml();
    $translation = new JSTranslation();
    $translation->addKey("ask_for_delete");
    $translation->addKey("wanna_empty_trash");
    $translation->addKey("reset_filters");
    $translation->renderJS();
} else {
    noPerms();
}
