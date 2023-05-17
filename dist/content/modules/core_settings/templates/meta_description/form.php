<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Translations\JSTranslation;

$languages = getAllLanguages();
$meta_descriptions = [];
$languageCount = count($languages);

for ($i = 0; $i < $languageCount; $i++) {
    $lang = $languages[$i];
    $meta_descriptions[$lang] = Settings::get('meta_description_' . $lang);
    if (! $meta_descriptions[$lang]) {
        $meta_descriptions[$lang] = Settings::get('meta_description');
    }
}
?>
<p>
    <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('settings_simple'); ?>"
        class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate('back'); ?></a>
</p>
<h1><?php get_translation('meta_description'); ?></h1>
<?php
echo \App\Helpers\ModuleHelper::buildMethodCallForm('MetaDescriptionController', 'save', [], 'post', [
    'id' => 'meta_description_settings',
    'class' => 'ajax-form'
]);
?>
<table style="border: 0">
    <tr>
        <td style="min-width: 100px;"><strong><?php translate('language'); ?>
            </strong></td>
        <td><strong><?php translate('meta_description'); ?>
            </strong></td>
    </tr>
    <?php
    $languageCount = count($languages);
for ($n = 0; $n < $languageCount; $n++) {
    $lang = $languages[$n];
    ?>
        <tr>
            <td>
                <?php esc(getLanguageNameByCode($lang)); ?>
            </td>
            <td><input
                    name="meta_description_<?php esc($lang); ?>"
                    value="<?php esc($meta_descriptions[$lang]); ?>" class="form-control"></td>
        </tr>
    <?php }
?>
    <tr>
        <td></td>
        <td class="text-center">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i>
                <?php translate('save_changes'); ?>
            </button>
        </td>
    </tr>
</table>
<?php
echo \App\Helpers\ModuleHelper::endForm();

$translation = new JSTranslation();
$translation->addKey('changes_were_saved');
$translation->render();

enqueueScriptFile(\App\Helpers\ModuleHelper::buildRessourcePath('core_settings', 'js/ajax_form.js'));
combinedScriptHtml();
