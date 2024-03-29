<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Models\Content\Categories;

$id = (int)$_REQUEST['id'];
$result = Database::query('SELECT * FROM ' . Database::tableName('audio') . " WHERE id = {$id}");

if (Database::getNumRows($result) > 0) {
    $dataset = Database::fetchObject($result);
    ?><p>
        <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('audio'); ?>"
            class="btn btn-light btn-back is-not-ajax"><i class="fa fa-arrow-left"></i>
            <?php translate('back'); ?></a>
    </p>
    <h1><?php translate('UPLOAD_AUDIO'); ?></h1>
    <form action="index.php?sClass=AudioController&sMethod=update"
            method="post">
                <?php csrf_token_html(); ?>
        <input type="hidden" name="id" value="<?php echo $dataset->id; ?>">

        <input
            type="hidden" name="update" value="update"> 
        <div class="field">
            <strong class="field-label">
                <?php translate('name'); ?>*
            </strong>            
            <input type="text" name="name"
                    value="<?php echo _esc($dataset->name); ?>"
                    maxlength="255" required />

        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate('category'); ?>
            </strong>
            <?php echo Categories::getHTMLSelect($dataset->category_id); ?>
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate('audio_ogg'); ?>
            </strong>
            <input name="ogg_file" type="text"
                    value="<?php
                    echo _esc(
                        $dataset->ogg_file
                    );
    ?>">
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate('audio_mp3'); ?>
            </strong>
            <input name="mp3_file" type="text" readonly
                    value="<?php
    echo _esc(
        $dataset->mp3_file
    );
    ?>">
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate('insert_this_code_into_a_page'); ?>
            </strong>
            <input type="text" name="code" readonly
                    value="[audio id=<?php echo $dataset->id; ?>]"
                    class="select-on-click" readonly>
        </div>
        <div class="voffset2">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i>
                <?php translate('save'); ?>
        </div>
    </button>
    </form>
    <?php
} else {
    translate('audio_not_found');
}
