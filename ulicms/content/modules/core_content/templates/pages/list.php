<?php

use UliCMS\Models\Content\Categories;
// TODO: This is old code before the switch to MVC architecture
// This should be rewritten with MVC pattern and using partial views
use UliCMS\Security\PermissionChecker;

$show_filters = Settings::get("user/" . get_user_id() . "/show_filters");

$permissionChecker = new PermissionChecker(get_user_id());

if ($permissionChecker->hasPermission("pages")) {
    echo Template::executeModuleTemplate("core_content", "icons.php");
    ?>
    <h2><?php translate("pages"); ?></h2>
    <p><?php translate("pages_infotext"); ?></p>
    <div id="page-list">
        <div class="row">
            <div class="col-xs-6">
                <a href="index.php?action=pagespages_new&parent_id=<?php echo $_SESSION["filter_parent"]; ?>" class="btn btn-default"><i
                        class="fa fa-plus"></i> <?php translate("create_page"); ?></a>
            </div>
        </div>
        <div class="scroll voffset3">
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
