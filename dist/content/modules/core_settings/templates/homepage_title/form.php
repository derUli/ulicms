<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;
use App\Translations\JSTranslation;

$permissionChecker = PermissionChecker::fromCurrentUser();

if ($permissionChecker->hasPermission('settings_simple')) {
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
        <a href="<?php echo ModuleHelper::buildActionURL('settings_simple'); ?>"
           class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate('back'); ?></a>
    </p>
    <h1><?php translate('homepage_title'); ?></h1>
    <?php
    echo ModuleHelper::buildMethodCallForm('HomepageTitleController', 'save', [], 'post', [
        'id' => 'homepage_title_settings'
    ]);
    ?>
    <table>
        <tr>
            <td style="min-width: 100px;"><strong><?php translate('language'); ?>
                </strong></td>
            <td><strong><?php translate('title'); ?>
                </strong></td>
        </tr>
        <?php
        $languageCount = count($languages);
    for ($n = 0; $n < $languageCount; $n++) {
        $lang = $languages[$n];
        ?>
            <tr>
                <td><?php esc(getLanguageNameByCode($lang)); ?></td>
                <td><input
                        name="homepage_title_<?php esc($lang); ?>"
                        value="<?php esc($homepage_titles[$lang]); ?>"></td>
            </tr>
        <?php }
    ?>
        <tr>
            <td></td>
            <td class="text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> <?php translate('save_changes'); ?></button>
            </td>
        </tr>
    </table>
    <?php
    echo ModuleHelper::endForm();

    $translation = new JSTranslation();
    $translation->addKey('changes_were_saved');
    $translation->render();

    enqueueScriptFile(ModuleHelper::buildRessourcePath('core_settings', 'js/homepage_title.js'));
    combinedScriptHtml();
} else {
    noPerms();
}
