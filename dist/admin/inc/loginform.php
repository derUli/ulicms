<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\RequestMethod;

$ga = new PHPGangsta_GoogleAuthenticator();
$ga_secret = Settings::get('ga_secret');
$qrCodeUrl = $ga->getQRCodeGoogleUrl('UliCMS Login auf ' . get_domain(), $ga_secret);

$twofactor_authentication = Settings::get('twofactor_authentication');

$languages = getAvailableBackendLanguages();
$languagesCount = count($languages);

$default_language = getSystemLanguage();
if (isset($_SESSION['language']) && in_array($_SESSION['language'], $languages)) {
    $default_language = $_SESSION['language'];
}

$admin_logo = Settings::get('admin_logo');
if (! $admin_logo) {
    $admin_logo = 'gfx/logo.png';
}

$error = (isset($_REQUEST['error']) && ! empty($_REQUEST['error'])) ? $_REQUEST['error'] : null;

$login_welcome_text = Settings::getLang('login_welcome_text', $default_language);
?>
<?php
if ($login_welcome_text) {
    ?>
    <div id="login-welcome-text">
        <?php echo nl2br($login_welcome_text); ?>
    </div>
<?php }
?>
<h3 id="login-please-headline">
    <?php translate('please_authenticate'); ?>
</h3>
<?php
echo \App\Helpers\ModuleHelper::buildMethodCallForm(
    'SessionManager',
    'login',
    [],
    RequestMethod::POST,
    [
        'id' => 'login-form',
        'data-has-error' => $error !== null
    ]
);
?>
<?php
csrf_token_html();
?>
<?php
if (! empty($_REQUEST['go'])) {
    ?>
    <input type="hidden" name="go"
           value='<?php esc($_REQUEST['go']); ?>'>
           <?php
}
?>
<table>
    <tr>
        <td><strong><?php translate('username'); ?></strong></td>
        <td><input type="text" name="user" value="" autocomplete="username"></td>
    </tr>
    <tr>
        <td><strong><?php translate('password'); ?></strong></td>
        <td><input type="password" id="password" name="password" value="" autocomplete="current-password"></td>
    </tr>
    <tr>
        <td><label for="view_password"><?php translate('view_password'); ?></label></td>
        <td><input type="checkbox" id="view_password" /></td>
    </tr>
    <tr>
        <td><strong><?php translate('language'); ?></strong></td>
        <td><select name="system_language">
                <option value="" selected>[<?php translate('standard'); ?>]</option>
                <?php
         for ($i = 0; $i < $languagesCount; $i++) {
             echo '<option value="' . $languages[$i] . '">' . getLanguageNameByCode($languages[$i]) . '</option>';
         }
?>
            </select></td>
    </tr>
    <?php
    if ($twofactor_authentication) {
        ?>
        <tr>
            <td><strong><?php translate('confirmation_code'); ?></strong></td>
            <td><input type="text" name="confirmation_code" value=""
                       autocomplete="nope"></td>
        </tr>
        <?php
    }
?>
    <tr>
        <td></td>
        <td class="text-center">
            <div class="voffset2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> <?php translate('login'); ?></button>
            </div>
        </td>
    </tr>
</table>
<?php echo \App\Helpers\ModuleHelper::endForm(); ?>
<?php
if ($error) {
    ?>
    <div class="alert alert-danger voffset2">
        <?php esc($error); ?>
    </div>
    <?php
}
?>
<?php
if (Settings::get('visitors_can_register') === 'on' || Settings::get('visitors_can_register') === '1') {
    ?>
    <a
        href="?register=register&<?php
        if (! empty($_REQUEST['go'])) {
            echo 'go=' . _esc($_REQUEST['go']);
        }
    ?>"
        class="btn btn-light voffset2"><i class="fas fa-user-plus"></i> <?php translate('register'); ?></a>
        <?php
}
?>
    <?php
if (! Settings::get('disable_password_reset')) {
    ?>
    <a href="?reset_password" class="btn btn-light float-end voffset2"><i
            class="fa fa-lock"></i> <?php translate('reset_password'); ?></a>
    <?php
}
enqueueScriptFile('scripts/login.js');
enqueueScriptFile('../node_modules/password-strength-meter/dist/password.min.js');
combinedScriptHtml();
