<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Translations\JSTranslation;

$groups = Group::getAll('name');

$languages = getAvailableBackendLanguages();
$default_language = getSystemLanguage();
$ref = _esc(Request::getVar('ref', 'home'));
?>
<div class="btn-toolbar">
    <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('admins'); ?>"
        class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate('back'); ?></a>
</div>
<form action="index.php?sClass=UserController&sMethod=create"
        method="post" id="edit-user" class="voffset2">
            <?php csrf_token_html(); ?>
    <input type="hidden" name="add_admin" value="add_admin">
    <div class="field">
        <strong class="field-label">
            <?php translate('username'); ?>*
        </strong>
        <input type="text" required="required" name="username" value="">
    </div>
    <div class="row field">
        <div class="col col-12 col-md-6">
            <strong class="field-label">
                <?php translate('firstname'); ?>
            </strong>
            <input type="text" name="firstname" value="">
        </div>
        <div class="col col-12 col-md-6">
            <strong class="field-label">
                <?php translate('lastname'); ?>
            </strong> <input
                type="text" name="lastname" value="">
        </div>
    </div>
    <div class="field">
        <strong class="field-label">
            <?php translate('email'); ?>
        </strong>
        <input type="email" name="email" value="">
    </div>
    <div class="row field">
        <div class="col col-12 col-md-6">
            <strong class="field-label">
                <?php translate('password'); ?>*
            </strong>
            <input type="password" required="required" name="password"
                    class="password-security-check"
                    id="password" value="" autocomplete="new-password">
        </div>
        <div class="col col-12 col-md-6">
            <strong class="field-label">
                <?php translate('password_repeat'); ?>*
            </strong>
            <input type="password" required="required" name="password_repeat"
                    id="password_repeat" value="" autocomplete="new-password">

        </div>
    </div>
    <?php

?>
    <div class="field">
        <strong class="field-label">
            <?php translate('primary_group'); ?>
        </strong>
        <select
            name="group_id">
            <option value="-">[<?php translate('none'); ?>]</option>
            <?php
        foreach ($groups as $group) {
            ?>
                <option value="<?php echo $group->getId(); ?>"
                <?php
            if (Settings::get('default_acl_group') == $group->getId()) {
                echo 'selected';
            }
            ?>>
                            <?php esc($group->getName()); ?>
                </option>
            <?php }
        ?>
        </select>
    </div>
    <div class="field">
        <strong class="field-label">
            <?php translate('secondary_groups'); ?>
        </strong>
        <select name="secondary_groups[]" multiple>
            <?php
        foreach ($groups as $group) {
            ?>
                <option value="<?php echo $group->getId(); ?>">
                    <?php esc($group->getName()); ?>
                </option>
            <?php }
        ?>
        </select>
    </div>
    <div class="checkbox block">
        <label>
            <input type="checkbox" value="1"
                    name="require_password_change"
                    id="require_password_change"
                    class="js-switch"><?php translate('REQUIRE_PASSWORD_CHANGE_ON_NEXT_LOGIN'); ?> </label>
    </div>
    <div class="checkbox block">
        <label><input type="checkbox" id="send_mail" name="send_mail"
                        value="sendmail"
                        class="js-switch">
            <?php translate('SEND_LOGINDATA_BY_MAIL'); ?></label>
    </div>
    <div class="checkbox block">
        <label><input type="checkbox" value="1" name="admin" id="admin"
                        class="js-switch">
            <?php translate('is_admin'); ?> </label>
        <span class="has-help"
                onclick="$('div#is_admin').slideToggle()">
            <i class="fa fa-question-circle text-info" aria-hidden="true"></i>
        </span>
    </div>
    <div id="is_admin" class="help" style="display: none">
        <?php echo nl2br(get_translation('HELP_IS_ADMIN')); ?>
    </div>
    <div class="checkbox block">
        <div class="field">
            <label><input type="checkbox" value="1" name="locked" id="locked"
                            class="js-switch">
                <?php translate('locked'); ?> </label>
        </div>
    </div>
    <div class="field">
        <strong class="field-label">
            <?php translate('default_language'); ?>
        </strong>
        <select name="default_language">
            <option value="" selected>[<?php translate('standard'); ?>]</option>
            <?php
        $languageCount = count($languages);
for ($i = 0; $i < $languageCount; $i++) {
    echo '<option value="' . $languages[$i] . '">' . getLanguageNameByCode($languages[$i]) . '</option>';
}
?>
        </select>
    </div>
    <div class="voffset2">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i>
            <?php translate('save'); ?></button>
    </div>
</form>
<?php
$translation = new JSTranslation([], 'UserTranslation');
$translation->addKey('passwords_not_equal');
$translation->render();
enqueueScriptFile(
    \App\Helpers\ModuleHelper::buildRessourcePath(
        'core_users',
        'js/users.js'
    )
);
enqueueScriptFile('../node_modules/password-strength-meter/dist/password.min.js');
combinedScriptHtml();
