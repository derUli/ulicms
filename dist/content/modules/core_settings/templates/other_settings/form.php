<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\EmailModes;
use App\HTML\Alert;
use App\Security\Permissions\PermissionChecker;
use App\Translations\JSTranslation;

$permissionChecker = PermissionChecker::fromCurrentUser();

$max_failed_logins_items = Settings::get('max_failed_logins_items');

$email_mode = Settings::get('email_mode');
$smtp_encryption = Settings::get('smtp_encryption');
$smtp_no_verify_certificate = Settings::get('smtp_no_verify_certificate');
$smtp_host = Settings::get('smtp_host') ?? '127.0.0.1';
$smtp_port = Settings::get('smtp_port') ?? '25';
$smtp_user = Settings::get('smtp_user');
$smtp_password = Settings::get('smtp_password');
$smtp_auth = Settings::get('smtp_auth');

$twofactor_authentication = Settings::get('twofactor_authentication');

$ga = new PHPGangsta_GoogleAuthenticator();
$ga_secret = Settings::get('ga_secret');
$qrCodeUrl = $ga->getQRCodeGoogleUrl(get_translation('ULICMS_LOGIN_AT') . ' ' . get_domain(), $ga_secret);
?>
<?php
echo \App\Helpers\ModuleHelper::buildMethodCallForm('OtherSettingsController', 'save', [], 'post', [
    'id' => 'other_settings',
    'autocomplete' => 'off'
]);
?>
<a
    href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('settings_categories'); ?>"
    class="btn btn-light btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate('back'); ?></a>
<div id="accordion-container" class="voffset3">
    <h2 class="accordion-header">
        <?php translate('DOMAIN2LANGUAGE_MAPPING'); ?>
    </h2>

    <div class="accordion-content">
        <?php
        echo Alert::info(
            get_translation('DOMAIN2LANGUAGE_MAPPING_INFO'),
            '',
            true
        );
?>
        <div class="field">
            <textarea name="domain_to_language" rows="10" cols="40"><?php echo _esc(Settings::get('domain_to_language')); ?></textarea>
        </div>
    </div>
    <h2 class="accordion-header">
        <?php translate('security'); ?>
    </h2>
    <div class="accordion-content">
        <div class="field">
            <div class="label">
                <label for="max_failed_logins_items"><?php translate('max_failed_login_items'); ?>
                </label>
            </div>
            <div class="inputWrapper">
                <input type="number" name="max_failed_logins_items" min="0" max="999"
                        value="<?php echo (int)$max_failed_logins_items; ?>" />
            </div>
        </div>
        <h2><?php translate('google_authenticator'); ?></h2>

        <div class="field">
            <div class="label">
                <label for="twofactor_authentication"><?php translate('2_FACTOR_AUTHENTICATION_ENABLED'); ?>
                </label>
            </div>
            <div class="inputWrapper">
                <input type="checkbox" id="twofactor_authentication"
                        name="twofactor_authentication"
                        class="js-switch"
                        <?php
                if ($twofactor_authentication) {
                    echo 'checked ';
                }
?>>
            </div>
        </div>
        <div class="voffset2">
            <img src="<?php echo $qrCodeUrl; ?>"
                    alt="QR-Codemit Google Authenticator scannen"
                    title="QR-Code mit Google Authenticator scannen" />
        </div>
        <a href="https://support.google.com/accounts/answer/1066447"
            target="_blank" class="btn btn-info voffset3"><i
                class="fa fa-question-circle" aria-hidden="true"></i>
            <?php translate('help'); ?></a>
        <?php
        if ($permissionChecker->hasPermission('default_access_restrictions_edit')) {
            ?>
            <h2><?php translate('DEFAULT_ACCESS_RESTRICTIONS'); ?></h2>
            <a
                href="<?php
                echo \App\Helpers\ModuleHelper::buildActionURL(
                    'default_access_restrictions'
                );
            ?>"
                class="btn btn-light is-not-ajax"
                > <i class="fas fa-tools"></i>
                <?php translate('view'); ?></a>
        <?php }
        ?>
    </div>
    <h2 class="accordion-header">
        <?php translate('EMAIL_DELIVERY'); ?>
    </h2>
    <div class="accordion-content">
        <div>
            <div class="label">Modus:</div>
            <div class="inputWrapper">
                <select id='email_mode' name="email_mode" size="1">
                    <option value="internal"
                    <?php
                    if ($email_mode == EmailModes::INTERNAL) {
                        echo ' selected="selected"';
                    }
?>>mail()</option>
                    <option value="phpmailer"
                    <?php
if ($email_mode == EmailModes::PHPMAILER) {
    echo ' selected="selected"';
}
?>>SMTP</option>
                </select>
            </div>
        </div>
        <div class="smtp_settings" id="smtp_settings" style="display: none">
            <h3>
                <?php translate('smtp_settings'); ?>
            </h3>

            <div class="field">
                <div class="label">
                    <?php translate('hostname'); ?>
                </div>
                <div class="inputWrapper">
                    <input type="text" name="smtp_host"
                            value="<?php esc($smtp_host); ?>">
                </div>
            </div>
            <div class="field">
                <div class="label">
                    <?php translate('port'); ?>
                </div>
                <div class="inputWrapper">
                    <input type="text" name="smtp_port"
                            value="<?php echo _esc($smtp_port); ?>">
                </div>
            </div>
            <div class="field">
                <div class="label">
                    <label for="smtp_auth"> <?php translate('smtp_encryption'); ?>
                    </label>
                </div>
                <div class="inputWrapper">
                    <select name="smtp_encryption">
                        <option value=""
                        <?php
    if (empty($smtp_encryption)) {
        echo 'selected';
    }
?>><?php translate('unencrypted'); ?></option>
                        <option value="ssl"
                        <?php
if ($smtp_encryption == 'ssl') {
    echo 'selected';
}
?>>SSL</option>
                        <option value="tls"
                        <?php
if ($smtp_encryption == 'tls') {
    echo 'selected';
}
?>>TLS</option>
                    </select>
                </div>
            </div>
            <div class="field">
                <div class="label">
                    <label for="smtp_no_verify_certificate"> <?php translate('smtp_no_verify_certificate'); ?>
                    </label>
                </div>
                <div class="inputWrapper">
                    <div class="field">
                        <input type="checkbox" id="smtp_no_verify_certificate"

                                class="js-switch"
                                name="smtp_no_verify_certificate"

                                <?php
        if ($smtp_no_verify_certificate) {
            echo ' checked="checked"';
        }
?>
                                value="smtp_no_verify_certificate"> <br /> <small><?php translate('smtp_no_verify_certificate_warning'); ?></small>
                    </div>
                </div>
            </div>
            <div>
                <div class="label">
                    <label for="smtp_auth"> <?php translate('AUTHENTIFACTION_REQUIRED'); ?>
                    </label>
                </div>
                <div class="inputWrapper">
                    <input type="checkbox" id="smtp_auth" name="smtp_auth"

                            class="js-switch"
                            <?php
                            if ($smtp_auth) {
                                echo ' checked="checked"';
                            }
?>
                            value="auth">
                </div>
            </div>
            <div id="smtp_auth_div" style="display: none">
                <div class="field">
                    <div class="label">
                        <?php translate('user'); ?>
                    </div>
                    <div class="inputWrapper">
                        <input type="text" name="smtp_user"
                                value="<?php echo _esc($smtp_user); ?>">
                    </div>
                </div>
                <div class="field">
                    <div class="label">
                        <?php translate('password'); ?>
                    </div>
                    <div class="inputWrapper">
                        <input type="password" name="smtp_password"
                                value="<?php echo _esc($smtp_password); ?>"
                                autocomplete="new-password">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<button type="submit" class="btn btn-primary voffset3">
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
        'js/other_settings.js'
    )
);
combinedScriptHtml();
