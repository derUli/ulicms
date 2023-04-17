<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;
use App\Translations\JSTranslation;

$permissionChecker = PermissionChecker::fromCurrentUser();

if ($permissionChecker->hasPermission('settings_simple')) {
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
        <a href="<?php echo ModuleHelper::buildActionURL('settings_simple'); ?>"
           class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate('back'); ?></a>
    </p>
    <h1><?php get_translation('meta_description'); ?></h1>
    <?php
    echo ModuleHelper::buildMethodCallForm('MetaDescriptionController', 'save', [], 'post', [
        'id' => 'meta_description_settings'
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
                        value="<?php esc($meta_descriptions[$lang]); ?>"></td>
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
    echo ModuleHelper::endForm();

    $translation = new JSTranslation();
    $translation->addKey('changes_was_saved');
    $translation->render();

    enqueueScriptFile(ModuleHelper::buildRessourcePath('core_settings', 'js/meta_description.js'));
    combinedScriptHtml();
} else {
    noPerms();
}
