<?php

use UliCMS\HTML\Input;

$rotatingTexts = RotatingText::getAll();
?>
<div class="form-group">
    <a href="<?php esc(ModuleHelper::buildActionURL("text_rotator_create")); ?>" class="btn btn-primary">
        <i class="fa fa-plus"></i> <?php translate("new"); ?></a>
</div>
<div class="scrollable">
    <table class="tablesorter">
        <thead>
            <tr>
                <th><?php translate("id"); ?></th>
                <th><?php translate("words"); ?></th>
                <th class="no-sort"><?php translate("shortcode"); ?></th>
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
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>