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
        class="btn btn-light btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate('back'); ?></a>
</p>
<h1><?php get_translation('meta_description'); ?></h1>
<?php
echo \App\Helpers\ModuleHelper::buildMethodCallForm('MetaDescriptionController', 'save', [], 'post', [
    'id' => 'meta_description_settings',
    'class' => 'ajax-form'
]);
?>
</p>
<h1>
    <?php translate('meta_description'); ?>
</h1>
<?php
    $languageCount = count($languages);
for ($n = 0; $n < $languageCount; $n++) {
    $lang = $languages[$n];
    ?>
        <div class="mb-3">
  <label for="<?php echo "field_{$n}";?>" class="form-label"><?php esc(getLanguageNameByCode($lang)); ?></label>
  <input name="meta_description_<?php esc($lang); ?>" id="<?php echo "field_{$n}";?>" value="<?php esc($meta_descriptions[$lang]); ?>" class="form-control">
</div>


    <?php }
?>
<button type="submit" class="btn btn-primary">
<i class="fa fa-save"></i>
<?php translate('save_changes'); ?>
        
<?php
echo \App\Helpers\ModuleHelper::endForm();

$translation = new JSTranslation();
$translation->addKey('changes_were_saved');
$translation->render();

enqueueScriptFile(\App\Helpers\ModuleHelper::buildRessourcePath('core_settings', 'js/ajax_form.js'));
combinedScriptHtml();
