<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\RequestMethod;
use App\Translations\JSTranslation;

$only_admins_can_edit = (int)(Settings::get('only_admins_can_edit'));
$only_group_can_edit = (int)(Settings::get('only_group_can_edit'));
$only_owner_can_edit = (int)(Settings::get('only_owner_can_edit'));
$only_others_can_edit = (int)(Settings::get('only_others_can_edit'));
?>
<p>
    <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('other_settings'); ?>"
        class="btn btn-light btn-back is-not-ajax">
        <i class="fa fa-arrow-left"></i> <?php translate('back'); ?>
    </a>
</p>
<h1><?php translate('DEFAULT_ACCESS_RESTRICTIONS'); ?></h1>
<?=
\App\Helpers\ModuleHelper::buildMethodCallForm(
    'DefaultAccessRestrictionsController',
    'save',
    [],
    RequestMethod::POST,
    [
        'id' => 'default_edit_restrictions',
        'class' => 'ajax-form'
    ]
);
?>
<div class="checkbox">
    <label>
        <input type="checkbox" name="only_admins_can_edit"
                id="only_admins_can_edit" value="1"
                class="js-switch"
                <?php
                if ($only_admins_can_edit) {
                    echo 'checked';
                }
?>><?php translate('admins'); ?></label>
</div>
<div class="checkbox">
    <label>
        <input type="checkbox" name="only_group_can_edit"
                id="only_group_can_edit" value="1"
                class="js-switch"
                <?php
if ($only_group_can_edit) {
    echo 'checked';
}
?>>
        <?php translate('group'); ?></label>
</div>
<div class="checkbox">
    <label>
        <input type="checkbox" name="only_owner_can_edit"
                id="only_owner_can_edit" value="1"
                class="js-switch"
                <?php
if ($only_owner_can_edit) {
    echo 'checked';
}
?>>
                <?php translate('owner'); ?>
    </label>
</div>
<div class="checkbox">
    <label>
        <input type="checkbox" name="only_others_can_edit"
                id="only_others_can_edit" value="1"
                class="js-switch"
                <?php
if ($only_others_can_edit) {
    echo 'checked';
}
?>>
                <?php translate('others'); ?>
    </label>
</div>
<div class="voffset2">
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> <?php translate('save_changes'); ?></button>
</div>
<?php
echo \App\Helpers\ModuleHelper::endForm();
$translation = new JSTranslation();
$translation->addKey('changes_were_saved');
$translation->render();
enqueueScriptFile(\App\Helpers\ModuleHelper::buildRessourcePath('core_settings', 'js/ajax_form.js'));
combinedScriptHtml();
