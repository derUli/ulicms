<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Translations\JSTranslation;

$languages = getAllLanguages();
$frontpages = [];
$languagesCount = count($languages);

for ($i = 0; $i < $languagesCount; $i++) {
    $lang = $languages[$i];
    $frontpages[$lang] = Settings::get('frontpage_' . $lang);

    if (! $frontpages[$lang]) {
        $frontpages[$lang] = Settings::get('frontpage');
    }
}
?>
<p>
    <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('settings_simple'); ?>"
        class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate('back'); ?></a>
</p>
<h1>
    <?php translate('frontpage'); ?>
</h1>
<?php
echo \App\Helpers\ModuleHelper::buildMethodCallForm('FrontPageSettingsController', 'save', [], 'post', [
    'id' => 'frontpage_settings',
    'class' => 'ajax-form'
]);
?>
<table>
    <tr>
        <td><strong><?php translate('language'); ?>
            </strong></td>
        <td><strong><?php translate('frontpage'); ?>
            </strong></td>
    </tr>
    <?php
    for ($n = 0; $n < $languagesCount; $n++) {
        $lang = $languages[$n];
        ?>
        <tr>
            <td>
                <?php esc(getLanguageNameByCode($lang)); ?>
            </td>
            <td><select
                    name = "frontpage_<?php esc($lang); ?>"
                    size = "1">
                        <?php
                        $pages = getAllPages($lang, 'title', true);
        $pageCount = count($pages);
        for ($i = 0; $i < $pageCount; $i++) {
            if ($pages[$i]['slug'] == $frontpages[$lang]) {
                echo "<option value='" . _esc($pages[$i]['slug']) . "' selected='selected'>" . _esc($pages[$i]['title']) . ' (ID: ' . $pages[$i]['id'] . ')</option>';
            } else {
                echo "<option value='" . _esc($pages[$i]['slug']) . "'>" . _esc($pages[$i]['title']) . ' (ID: ' . $pages[$i]['id'] . ')</option>';
            }
        }
        ?>
                </select></td>
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
echo \App\Helpers\ModuleHelper::endForm();

$translation = new JSTranslation();
$translation->addKey('changes_were_saved');
$translation->render();

enqueueScriptFile(\App\Helpers\ModuleHelper::buildRessourcePath('core_settings', 'js/ajax_form.js'));
combinedScriptHtml();
