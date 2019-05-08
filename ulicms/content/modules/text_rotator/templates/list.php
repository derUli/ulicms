<?php

use UliCMS\HTML\Input;
use UliCMS\Security\PermissionChecker;

$rotatingTexts = RotatingText::getAll();
$permissionChecker = new PermissionChecker(get_user_id());
?>
<?php
if ($permissionChecker->hasPermission("text_rotator_edit")) {
    ?>
    <div class="form-group">
        <a href="<?php esc(ModuleHelper::buildActionURL("text_rotator_create")); ?>" class="btn btn-primary">
            <i class="fa fa-plus"></i> <?php translate("new");
    ?></a>
    </div>
<?php } ?>
<div class="scroll">
    <table class="tablesorter">
        <thead>
            <tr>
                <th><?php translate("id"); ?></th>
                <th><?php translate("words"); ?></th>
                <th class="no-sort"><?php translate("shortcode"); ?></th>
                <?php
                if ($permissionChecker->hasPermission("text_rotator_edit")) {
                    ?>
                    <th class="no-sort text-center">
                        <?php translate("edit")
                        ?>
                    </th>
                    <th class="no-sort text-center">
                        <?php translate("delete") ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rotatingTexts as $text) {
                ?>
                <tr>
                    <td><?php esc($text->getID()); ?></td>
                    <td><?php esc($text->getWords()); ?></td>
                    <td>
                        <?php
                        echo Input::TextBox("word_{$text->getID()}",
                                $text->getShortcode(), "text",
                                array("readonly" => "readonly",
                                    "class" => "select-on-click"));
                        ?>
                    </td>
                    <?php
                    if ($permissionChecker->hasPermission("text_rotator_edit")) {
                        ?>
                        <td class="text-center"><a
                                href="<?php
                                esc(ModuleHelper::buildActionURL("text_rotator_edit", "id={$text->getID()}"));
                                ?>">
                                <img class="mobile-big-image" src="gfx/edit.png"
                                     alt="<?php translate("edit"); ?>"
                                     title="<?php translate("edit"); ?>">
                            </a></td>
                        <td class="text-center">
                            <?php echo ModuleHelper::deleteButton(ModuleHelper::buildMethodCallUrl(TextRotatorController::class, "delete", "id={$text->getID()}")); ?>

                        </td>
                    <?php } ?>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>
<?php
$translation = new JSTranslation();
$translation->addKey("ask_for_delete");
$translation->renderJS();
?>
<?php
