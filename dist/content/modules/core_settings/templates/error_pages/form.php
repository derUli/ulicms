<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\RequestMethod;
use App\HTML\Input;
use App\HTML\ListItem;
use App\Translations\JSTranslation;

$languages = getAllLanguages();
$errorCodes = [
    403 => get_translation('forbidden'),
    404 => get_translation('not_found')
];
?>
<p>
    <a
        href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('settings_simple'); ?>"
        class="btn btn-light btn-back is-not-ajax"><i class= "fa fa-arrow-left"></i>
            <?php translate('back');
?></a>
</p>

<h1><?php translate('error_pages'); ?></h1>
<?php
echo \App\Helpers\ModuleHelper::buildMethodCallForm(
    ErrorPagesController::class,
    'save',
    [],
    RequestMethod::POST,
    [
        'id' => 'error_pages_form',
        'class' => 'ajax-form'
    ]
);
?>
<?php foreach ($errorCodes as $code => $error) {
    ?>
    
    <div class="mb-3">
    <h3><?php esc("{$error} (Status {$code})"); ?></h3>
    
            <?php
            foreach (getAllLanguages() as $language) {
                $pages = getAllPages($language, 'title', true);
                $items = [new ListItem('-1', '[' . get_translation('standard') . ']')];
                foreach ($pages as $page) {
                    $items[] = new ListItem(
                        $page['id'],
                        $page['title']
                    );
                }
                ?>
              <div class="mb-3">
                    <label for="<?php echo "error_page[{$code}][{$language}]";?>" class="form-label"><?php esc(getLanguageNameByCode($language)); ?></label>
                        <?php
                        echo Input::singleSelect(
                            "error_page[{$code}][{$language}]",
                            Settings::getLanguageSetting("error_page_{$code}", $language),
                            $items,
                            1,
                            [
                                'id' => "error_page[{$code}][{$language}]"
                            ]
                        );
                ?>
                        </div>
                        <?php } ?>
    </div>
    <?php
}
?>
<button type="submit" class="btn btn-primary">
    <i class="fa fa-save"></i>
    <?php translate('save'); ?>
</button>
<?php
echo \App\Helpers\ModuleHelper::endForm();

$translation = new JSTranslation();
$translation->addKey('changes_were_saved');
$translation->render();

enqueueScriptFile(\App\Helpers\ModuleHelper::buildRessourcePath('core_settings', 'js/ajax_form.js'));
combinedScriptHtml();
