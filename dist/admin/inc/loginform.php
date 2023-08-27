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
<?php
if ($error) {
    ?>
    <div class="alert alert-danger mb-2">
        <?php esc($error); ?>
    </div>
    <?php
}
?>


<div class="form-floating mb-2">
    <input name="user" id="user" autocomplete="username" type="text" class="form-control" placeholder="<?php translate('username'); ?>">
    <label for="user"><?php translate('username'); ?></label>
</div>


<div class="form-floating mb-2">
    <input name="password" id="password" type="password" class="form-control" value=""autocomplete="current-password"  placeholder="<?php translate('password'); ?>">
    <label for="password"><?php translate('password'); ?></label>
</div>

<div class="form-floating mb-2">
    <select name="system_language" id="system_language" placeholder="<?php translate('language');?>" class="form-control no-select2">
        <option value="" selected>[<?php translate('standard'); ?>]</option>
        <?php
        for ($i = 0; $i < $languagesCount; $i++) {
            echo '<option value="' . $languages[$i] . '">' . getLanguageNameByCode($languages[$i]) . '</option>';
        }
?>
    </select>
    <label for="system_language"><?php translate('language'); ?></label>
</div>

<?php if ($twofactor_authentication) {?>
    <div class="form-floating mb-2">
        <input name="confirmation_code" id="confirmation_code" type="text"  value="" autocomplete="nope" placeholder="<?php translate('confirmation_code'); ?>">
        <label for="confirmation_code"><?php translate('confirmation_code'); ?></label>
    </div>
    <?php }?>
    
<div class="btn-group flex-wrap">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-sign-in-alt"></i> 
        <?php translate('login'); ?>
    </button>

    <?php if (Settings::get('visitors_can_register') === 'on' || Settings::get('visitors_can_register') === '1') {?>
        <a href="?register=register&<?php
        if (! empty($_REQUEST['go'])) {
            echo 'go=' . _esc($_REQUEST['go']);
        }
        ?>" class="btn btn-light">
            <i class="fas fa-user-plus"></i> 
            <?php translate('register'); ?>
        </a>
    <?php } ?>

    <?php if (! Settings::get('disable_password_reset')) { ?>
        <a href="?reset_password" class="btn btn-light">
            <i class="fa fa-lock"></i>
            <?php translate('reset_password'); ?>
        </a>
    <?php } ?>
</div>

<?php echo \App\Helpers\ModuleHelper::endForm(); ?>

<?php
enqueueScriptFile('scripts/login.js');
enqueueScriptFile('../node_modules/password-strength-meter/dist/password.min.js');
combinedScriptHtml();
