<?php

use UliCMS\Models\Content\Advertisement\Banners;
use UliCMS\Models\Content\Categories;

$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("banners")) {
    if (!isset($_SESSION["filter_category"])) {
        $_SESSION["filter_category"] = 0;
    }
    if (isset($_GET["filter_category"])) {
        $_SESSION["filter_category"] = intval($_GET["filter_category"]);
    }
    if ($_SESSION["filter_category"] == 0) {
        $banners = Banners::getAll();
    } else {
        $banners = Banners::getByCategory($_SESSION["filter_category"]);
    }
    ?>
    <?php echo Template::executeModuleTemplate("core_content", "icons.php"); ?>

    <h2><?php translate("advertisements"); ?></h2>
    <p>
        <?php translate("advertisement_infotext"); ?>
        <?php
        if ($permissionChecker->hasPermission("banners_create")) {
            ?><br /> <br /> <a href="index.php?action=banner_new"
                             class="btn btn-default"><i class="fa fa-plus"></i> <?php translate("add_advertisement"); ?>
            </a><br />
        <?php } ?>
    </p>
    <p><?php translate("category"); ?>
        <?php
        echo Categories::getHTMLSelect($_SESSION["filter_category"], true);
        ?>
    </p>

    <p><?php BackendHelper::formatDatasetCount(count($banners)); ?></p>
    <div class="scroll">
        <table class="tablesorter">
            <thead>
                <tr style="font-weight: bold;">
                    <th><?php translate("advertisements"); ?>
                    </th>
                    <th><?php translate("language"); ?>
                    </th>
                    <?php if ($permissionChecker->hasPermission("banners_edit")) { ?>
                        <td class="no-sort text-center"><?php translate("edit"); ?>
                        </td>
                        <td class="no-sort text-center"><?php translate("delete"); ?>
                        </td>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($banners) > 0) {
                    foreach ($banners as $banner) {
                        ?>
                        <?php
                        echo '<tr id="dataset-' . $banner->getId() . '">';
                        if ($banner->getType() == "gif") {
                            $link_url = Template::getEscape($banner->getLinkUrl());
                            $image_url = Template::getEscape($banner->getImageUrl());
                            $name = Template::getEscape($banner->getName());
                            echo '<td><a href="' . $link_url . '" target="_blank"><img src="' . $image_url . '" title="' . $name . '" alt="' . $name . '" border=0></a></td>';
                        } else {
                            echo '<td>' . Template::getEscape($banner->render()) . '</td>';
                        }
                        if (!$banner->getLanguage()) {
                            echo '<td>' . get_translation("every") . '</td>';
                        } else {
                            echo '<td>' . getLanguageNameByCode($banner->getLanguage()) . "</td>";
                        }
                        if ($permissionChecker->hasPermission("banners_edit")) {
                            echo "<td class=\"text-center\">" . '<a href="index.php?action=banner_edit&banner=' . $banner->getId() . '"><img class="mobile-big-image" src="gfx/edit.png" alt="' . get_translation("edit") . '" title="' . get_translation("edit") . '"></a></td>';
                            echo "<td class=\"text-center\">" . '<form action="index.php?sClass=BannerController&sMethod=delete&banner=' . $banner->getId() . '" method="post" class="delete-form">' . get_csrf_token_html() . '<input type="image" class="mobile-big-image" src="gfx/delete.gif" title="' . get_translation("delete") . '"></form></td>';
                        }
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    enqueueScriptFile(ModuleHelper::buildRessourcePath("core_content", "js/banners.js"));
    combinedScriptHtml();
    ?>

    <?php
} else {
    noPerms();
}

$translation = new JSTranslation(array(
    "ask_for_delete"
        ));
$translation->render();
