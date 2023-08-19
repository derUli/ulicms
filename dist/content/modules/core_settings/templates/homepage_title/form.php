<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Translations\JSTranslation;

$languages = getAllLanguages();
$homepage_titles = [];
$languageCount = count($languages);
for ($i = 0; $i < $languageCount; $i++) {
    $lang = $languages[$i];
    $homepage_titles[$lang] = Settings::get('homepage_title_' . $lang);

    if (! $homepage_titles[$lang]) {
        $homepage_titles[$lang] = Settings::get('homepage_title');
    }
}
?>
<p>
    <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('settings_simple'); ?>"
        class="btn btn-light btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate('back'); ?></a>
</p>
<h1><?php translate('homepage_title'); ?></h1>
<?php
echo \App\Helpers\ModuleHelper::buildMethodCallForm('HomepageTitleController', 'save', [], 'post', [
    'id' => 'homepage_title_settings',
    'class' => 'ajax-form'
]);
?>

<div class="row">

    <?php
    $languageCount = count($languages);
for ($n = 0; $n < $languageCount; $n++) {
    $lang = $languages[$n];
    ?>

<div class="mb-3">
  <label for="<?php echo "title{$n}";?>" class="form-label"><?php esc(getLanguageNameByCode($lang)); ?></label>
  <input id="<?php echo "title{$n}";?>" name="homepage_title_<?php esc($lang); ?>" value="<?php esc($homepage_titles[$lang]); ?>" class="form-control">
</div>
    <?php }
?>

</div>
<button type="submit" class="btn btn-primary">
    <i class="fa fa-save"></i> <?php translate('save_changes'); ?>
</button>

<?php

echo \App\Helpers\ModuleHelper::endForm();

$translation = new JSTranslation();
$translation->addKey('changes_were_saved');
$translation->render();

enqueueScriptFile(\App\Helpers\ModuleHelper::buildRessourcePath('core_settings', 'js/ajax_form.js'));
combinedScriptHtml();
