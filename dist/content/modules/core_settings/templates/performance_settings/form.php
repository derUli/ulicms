<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

$canIuseLazyLoadingUrl = 'https://caniuse.com/#feat=loading-lazy-attr';

use App\Constants\RequestMethod;
use App\HTML\Input;
use App\HTML\ListItem;
use App\Security\Permissions\PermissionChecker;
use App\Translations\JSTranslation;

$lazy_loading = [];

if (Settings::get('lazy_loading_img', 'bool')) {
    $lazy_loading[] = 'img';
}

if (Settings::get('lazy_loading_iframe', 'bool')) {
    $lazy_loading[] = 'iframe';
}

$permissionChecker = new PermissionChecker(get_user_id());

$cache_enabled = ! Settings::get('cache_disabled');
$cache_period = round(Settings::get('cache_period') / 60);
?>
<?php
if (Request::getVar('clear_cache')) {
    ?>
    <div class="alert alert-success alert-dismissable fade in">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php translate('cache_was_cleared'); ?>
    </div>
<?php }
?>
<?php
if (Request::getVar('save')) {
    ?>
    <div class="alert alert-success alert-dismissable fade in">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">
            &times;</a>
        <?php translate('changes_were_saved'); ?>
    </div>
<?php }
?>

<a
    href="<?php
    echo \App\Helpers\ModuleHelper::buildActionURL(
        'settings_categories'
    );
?>"
    class="btn btn-light btn-back is-not-ajax">
    <i class="fas fa-arrow-left"></i>
    <?php translate('back'); ?></a>
<a
    href="<?php
echo \App\Helpers\ModuleHelper::buildMethodCallUrl(
    'PerformanceSettingsController',
    'clearCache'
);
?>"
    class="btn btn-warning float-end ">
    <i class="fas fa-broom"></i>
    <?php translate('clear_cache'); ?></a>

<h2><?php translate('performance'); ?></h2>
<?php
echo \App\Helpers\ModuleHelper::buildMethodCallForm(
    'PerformanceSettingsController',
    'save',
    [],
    RequestMethod::POST,
    [
        'id' => 'form',
        'class' => 'ajax-form'
    ]
);
?>
<div id="accordion-container">
    <h2 class="accordion-header">
        <?php translate('page_cache'); ?>
    </h2>
    <div class="accordion-content">
        <div class="field">
            <div class="label">
                <label for="cache_enabled">
                    <?php translate('cache_enabled'); ?>
                </label>
            </div>
            <div class="inputWrapper">
                <input type="checkbox" id="cache_enabled" name="cache_enabled"
                        class="js-switch"
                        value="cache_enabled"
                        <?php
                        if ($cache_enabled) {
                            echo ' checked="checked"';
                        }
?>>
            </div>
        </div>
        <div class="field">
            <div class="label">
                <?php translate('CACHE_VALIDATION_DURATION'); ?>
            </div>
            <div class="inputWrapper">
                <input type="number" name="cache_period" min="0" max="20160" class="form-control"
                        value="<?php echo $cache_period; ?>">
                        <?php translate('minutes'); ?>
            </div>
        </div>
    </div>
    <h2 class="accordion-header">
        <?php translate('lazy_loading'); ?>
    </h2>
    <div class="accordion-content">
        <div class="field">
            <div class="label">
                <?php translate('lazy_loading'); ?>
            </div>
            <div class="inputWrapper">
                <?php
                echo Input::multiSelect(
                    'lazy_loading[]',
                    $lazy_loading,
                    [
                        new ListItem(
                            'img',
                            get_translation('lazy_loading_img')
                        ),
                        new ListItem(
                            'iframe',
                            get_translation('lazy_loading_iframe')
                        ),
                    ],
                    1
                );
?>
            </div>
            <div class="alert alert-info voffset2">
                <p>
                    <?php translate('lazy_loading_help1'); ?>
                </p>
                <p>
                    <?php translate('lazy_loading_help2'); ?>
                    <br/>

                    <?php translate('lazy_loading_help3'); ?>
                    <br/>
                    <?php
    echo \App\Helpers\StringHelper::makeLinksClickable(
        $canIuseLazyLoadingUrl
    );
?>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="voffset2">
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i>
        <?php translate('save_changes'); ?>
    </button>
</div>
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
