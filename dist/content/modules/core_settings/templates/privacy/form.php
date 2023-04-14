<?php

use App\Constants\RequestMethod;
use App\HTML\Alert;
use App\Translations\JSTranslation;

$permissionChecker = new \App\Security\ACL();
if ($permissionChecker->hasPermission('privacy_settings')) {
    $currentLanguage = Request::getVar('language');
    if (! $currentLanguage) {
        $currentLanguage = Settings::get('default_language');
    }
    $privacy_policy_checkbox_enable = $currentLanguage ? Settings::get("privacy_policy_checkbox_enable_{$currentLanguage}") : Settings::get('privacy_policy_checkbox_enable');
    $log_ip = Settings::get('log_ip');
    $delete_ips_after_48_hours = Settings::get('delete_ips_after_48_hours');
    $keep_spam_ips = Settings::get('keep_spam_ips');

    $languages = getAllLanguages(true);
    ?>
    <div class="field">
        <a
            href="<?php echo ModuleHelper::buildActionURL('settings_categories'); ?>"
            class="btn btn-default btn-back is-not-ajax">
            <i class="fa fa-arrow-left"></i>
            <?php translate('back'); ?>
        </a>
    </div>
    <?php
    if (Request::getVar('save')) {
        echo Alert::success(
            get_translation('changes_was_saved'),
            'voffset2'
        );
    }
    ?>
    <h2><?php translate('privacy'); ?></h2>
    <?php
    echo ModuleHelper::buildMethodCallForm(
        'PrivacyController',
        'save',
        [],
        RequestMethod::POST,
        [
            'id' => 'privacy-form'
        ]
    );
    ?>
    <div id="accordion-container">
        <h2 class="accordion-header"><?php translate('dsgvo_checkbox'); ?></h2>
        <div class="accordion-content">

            <div class="field">
                <strong><?php translate('language'); ?></strong> <br /> <select
                    name="language" id="language">
                        <?php
                        foreach ($languages as $language) {
                            ?>
                        <option value="<?php Template::escape($language); ?>"
                        <?php
                        if ($currentLanguage == $language) {
                            echo 'selected';
                        }
                            ?>><?php Template::escape(getLanguageNameByCode($language)); ?></option>
                            <?php }
                        ?>

                </select>
            </div>
            <?php csrf_token_html(); ?>
            <div class="field">
                <input type="checkbox" id="privacy_policy_checkbox_enable"
                       name="privacy_policy_checkbox_enable" value="1"
                       class="js-switch"
                       <?php
                       if ($privacy_policy_checkbox_enable) {
                           echo 'checked';
                       }
    ?>> <label
                       for="privacy_policy_checkbox_enable"><?php translate('privacy_policy_checkbox_enable'); ?></label>
            </div>
            <?php $editor = get_html_editor(); ?>
            <div id="privacy_policy_checkbox_text_container"
                 style="<?php echo $privacy_policy_checkbox_enable ? 'display:block' : 'display:none'; ?>">
                <strong><?php translate('privacy_policy_checkbox_text'); ?></strong><br />
                <textarea name="privacy_policy_checkbox_text"
                          class="<?php esc($editor); ?>" data-mimetype="text/html"
                          id="privacy_policy_checkbox_text" cols=60 rows=15><?php esc(Settings::get("privacy_policy_checkbox_text_{$currentLanguage}")); ?></textarea>
            </div>
        </div>
        <h2 class="accordion-header">
            <?php translate('IP_ADDRESSES'); ?>
        </h2>

        <div class="accordion-content">
            <div class="field">
                <div class="field-label">
                    <label for="log_ip"> <?php translate('LOG_IP_ADDRESSES'); ?>
                    </label>
                </div>
                <div class="inputWrapper">
                    <input type="checkbox" id="log_ip" name="log_ip"
                           class="js-switch"
                           <?php
        if ($log_ip) {
            echo 'checked ';
        }
    ?>>
                </div>
                <small>
                    <?php translate('LOG_IP_ADDRESSES_NOTICE'); ?>
                </small>
            </div>
            <hr />
            <div class="field">
                <div class="field-label">
                    <label for="delete_ips_after_48_hours">
                        <?php translate('DELETE_IPS_AFTER_48_HOURS'); ?>
                    </label>
                </div>
                <div class="inputWrapper">
                    <input type="checkbox" id="delete_ips_after_48_hours"
                           name="delete_ips_after_48_hours"
                           class="js-switch"
                           <?php
    if ($delete_ips_after_48_hours) {
        echo 'checked ';
    }
    ?>>
                </div>
            </div>
            <div class="field">
                <div class="field-label">
                    <label for="keep_spam_ips">
                        <?php translate('keep_spam_ips'); ?>
                    </label>
                </div>
                <div class="inputWrapper">
                    <input type="checkbox" id="keep_spam_ips" name="keep_spam_ips"
                           class="js-switch"
                           <?php
    if ($keep_spam_ips) {
        echo 'checked ';
    }
    ?>>
                </div>
            </div>
        </div>
    </div>
    <div class="voffset2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i>
            <?php translate('save_changes'); ?></button>
    </div>
    <?php
    $translation = new JSTranslation();
    $translation->addKey('changes_was_saved');
    $translation->render();

    BackendHelper::enqueueEditorScripts();
    enqueueScriptFile(ModuleHelper::buildRessourcePath('core_settings', 'js/privacy.js'));
    combinedScriptHtml();
    echo ModuleHelper::endForm();
    ?>
    <?php
} else {
    noPerms();
}
