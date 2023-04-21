<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\RequestMethod;
use App\Helpers\StringHelper;
use App\Translations\JSTranslation;

$types = get_available_post_types();
$typeSelection = [];
foreach ($types as $type) {
    $typeSelection[] = new App\HTML\ListItem($type, get_translation($type));
}

$commentableContentTypes = [];

$commentableContentTypeSettings = Settings::get('commentable_content_types');
if ($commentableContentTypeSettings) {
    $commentableContentTypes = StringHelper::splitAndTrim($commentableContentTypeSettings);
}
?>
<a
    href="<?php echo ModuleHelper::buildActionURL('settings_categories'); ?>"
    class="btn btn-default btn-back is-not-ajax">
    <i class="fa fa-arrow-left"></i> <?php translate('back'); ?>
</a>

<?php if (Request::getVar('save')) { ?>
    <div class="alert alert-success alert-dismissable fade in voffset3">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php translate('changes_was_saved'); ?>
    </div>
<?php } ?>
<?php
echo ModuleHelper::buildMethodCallForm(
    CommunitySettingsController::class,
    'save',
    [],
    RequestMethod::POST,
    [
        'id' => 'community_settings_form'
    ]
);
?>
<h1><?php translate('comments'); ?></h1>
<div class="field">
    <div class="checkbox">
        <label><?php
            echo App\HTML\Input::checkBox(
                'comments_enabled',
                (bool)Settings::get('comments_enabled'),
                '1',
                ['class' => 'js-switch']
            );
?><?php translate('comments_enabled'); ?></label>
    </div>
</div>
<div class="field">
    <div class="checkbox">
        <label><?php
echo App\HTML\Input::checkBox(
    'comments_must_be_approved',
    (bool)Settings::get('comments_must_be_approved'),
    '1',
    ['class' => 'js-switch']
);
?><?php translate('comments_must_be_approved'); ?></label>
    </div>
</div>
<div class="field">
    <label for="commentable_content_types[]"><?php translate('commentable_content_types'); ?></label>
    <?php
    echo App\HTML\Input::multiSelect('commentable_content_types[]', $commentableContentTypes, $typeSelection, 5);
?>
</div>
<div class="voffset2">
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> <?php translate('save'); ?></button>
</div>
<?php
echo ModuleHelper::endForm();

$translation = new JSTranslation();
$translation->addKey('changes_was_saved');
$translation->render();

enqueueScriptFile(
    ModuleHelper::buildRessourcePath(
        'core_settings',
        'js/community_settings.js'
    )
);
combinedScriptHtml();
