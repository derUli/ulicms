<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;
use App\Translations\JSTranslation;

$permissionChecker = PermissionChecker::fromCurrentUser();

$languages = getAllLanguages();
$site_slogans = [];
$languageCount = count($languages);
for ($i = 0; $i < $languageCount; $i++) {
    $lang = $languages[$i];
    $site_slogans[$lang] = Settings::get('site_slogan_' . $lang);

    if (! $site_slogans[$lang]) {
        $site_slogans[$lang] = Settings::get('site_slogan');
    }
}
?><p>
    <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('settings_simple'); ?>"
        class="btn btn-light btn-back is-not-ajax">
        <i class="fa fa-arrow-left"></i> <?php translate('back'); ?>
    </a>
</p>
<h1>
    <?php translate('site_slogan'); ?>
</h1>
<?php
echo \App\Helpers\ModuleHelper::buildMethodCallForm('SiteSloganController', 'save', [], 'post', [
    'id' => 'site_slogan_settings',
    'class' => 'ajax-form'
]);
?>
    <?php
    for ($n = 0; $n < $languageCount; $n++) {
        $lang = $languages[$n];
        ?>
        
        <div class="mb-3">
  <label for="<?php echo "field_{$n}";?>" class="form-label"><?php esc(getLanguageNameByCode($lang)); ?></label>
  <input name="site_slogan_<?php esc($lang); ?>" id="<?php echo "field_{$n}";?>" value="<?php esc($site_slogans[$lang]); ?>" class="form-control">
</div>
    <?php } ?>

    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> 
        <?php translate('save_changes'); ?>
    </button>
        
<?php
echo \App\Helpers\ModuleHelper::endForm();

$translation = new JSTranslation();
$translation->addKey('changes_were_saved');
$translation->render();

enqueueScriptFile(
    \App\Helpers\ModuleHelper::buildRessourcePath(
        'core_settings',
        'js/ajax_form.js'
    )
);
combinedScriptHtml();
