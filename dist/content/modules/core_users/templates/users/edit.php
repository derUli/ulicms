<?php

use App\Constants\RequestMethod;
use App\Constants\HtmlEditor;
use App\HTML\Input;
use App\Helpers\DateTimeHelper;
use App\Translations\JSTranslation;

use function App\HTML\imageTag;

$permissionChecker = new ACL();
if (($permissionChecker->hasPermission('users') && $permissionChecker->hasPermission('users_edit')) || ($_GET['id'] == $_SESSION['login_id'])) {
    $id = (int)$_GET['id'];
    $languages = getAvailableBackendLanguages();
    $result = db_query('SELECT * FROM ' . tbname('users') . " WHERE id='$id'");
    $user = new User($id);
    $secondaryGroups = $user->getSecondaryGroups();
    $secondaryGroupIds = [];
    foreach ($secondaryGroups as $group) {
        $secondaryGroupIds[] = $group->getID();
    }

    $backUrl = $permissionChecker->hasPermission('users_edit') ? ModuleHelper::buildActionURL('admins') : ModuleHelper::buildActionURL('home');
    ?>
    <div class="btn-toolbar">
        <a href="<?php echo $backUrl; ?>"
           class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate('back') ?></a>
    </div>
    <?php
    while ($row = db_fetch_object($result)) {
        ?>
        <div class="field voffset2">
            <?php
            echo imageTag(
                $user->getAvatar(),
                [
                                                                                    'alt' => get_translation('avatar_image')
                                                                                ]
            );
        ?>
        </div>
        <?php
        echo ModuleHelper::buildMethodCallUploadForm(
            UserController::class,
            'update',
            [],
            RequestMethod::POST,
            [
                'id' => 'edit-user',
                'class' => 'field'
            ]
        );
        ?>
        <div class="field">
            <label for="avatar">
                <?php t('upload_new_avatar'); ?>
            </label>
            <?php
            echo Input::file(
                'avatar',
                false,
                'image/*'
            );
        ?>
            <?php if ($user->hasProcessedAvatar()) {
                ?>
                <div class="checkbox field voffset1">
                    <label>
                        <?php
                        echo App\HTML\Input::checkBox(
                            'delete_avatar',
                            false,
                            '1',
                            ['class' => 'js-switch']
                        );
                ?><?php translate('delete_avatar') ?>
                    </label>
                </div>
            <?php }
            ?>
        </div>
        <input type="hidden" name="edit_admin"
               value="edit_admin"> <input type="hidden" name="id"
               value="<?php echo $row->id; ?>">
        <div class="field voffset1">
            <strong class="field-label">
                <?php translate('username'); ?>*
            </strong>
            <input type="text" name="username"
                   value="<?php echo _esc($row->username); ?>" required disabled
                   <?php
                   if (! $permissionChecker->hasPermission('users')) {
                       ?>
                       readonly="readonly" <?php }
                   ?>>
        </div>
        <div class="row field">
            <div class="col-xs-12 col-md-6">
                <strong class="field-label">
                    <?php translate('firstname'); ?>
                </strong>
                <input type="text" name="firstname"
                       value="<?php echo _esc($row->firstname); ?>">
            </div>
            <div class="col-xs-12 col-md-6"> <strong class="field-label">
                    <?php translate('lastname'); ?>
                </strong>
                <input type="text" name="lastname"
                       value="<?php echo _esc($row->lastname); ?>">
            </div>
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate('email'); ?>
            </strong>
            <input type="email" name="email"
                   value="<?php echo _esc($row->email); ?>">
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate('last_login'); ?>
            </strong>
            <?php
            if ($row->last_login === null) {
                translate('never');
            } else {
                echo DateTimeHelper::timestampToFormattedDateTime($row->last_login);
            }
        ?>
        </div>
        <div class="row field">
            <div class="col-xs-12 col-md-6">
                <strong class="field-label">
                    <?php translate('new_password'); ?>
                </strong>
                <input type="password" name="password" id="password"
                       class="password-security-check"
                       value="" autocomplete="new-password"> </div>
            <div class="col-xs-12 col-md-6">
                <strong class="field-label">
                    <?php translate('password_repeat'); ?>
                </strong>
                <input type="password" name="password_repeat"
                       id="password_repeat" value="" autocomplete="new-password">
            </div>
        </div>
        <?php
        $permissionChecker = new ACL();
        if ($permissionChecker->hasPermission('users')) {
            $allGroups = $permissionChecker->getAllGroups();
            asort($allGroups);
            ?>
            <div class="field">
                <strong class="field-label">
                    <?php translate('primary_group'); ?>
                </strong>
                <select name="group_id">
                    <option value="-"
                    <?php
                    if ($row->group_id === null) {
                        echo 'selected';
                    }
            ?>>[<?php translate('none'); ?>]</option>
                            <?php
                    foreach ($allGroups as $key => $value) {
                        ?>
                        <option
                            value="<?php echo $key; ?>"
                            <?php
                            if ((int) ($row->group_id) == $key) {
                                echo 'selected';
                            }
                        ?>>
                                <?php esc($value) ?>
                        </option>
                    <?php }
                    ?>
                </select>
            </div>
            <div class="field"
                 <strong class="field-label">
                     <?php translate('secondary_groups'); ?>
            </strong>
            <select name="secondary_groups[]" multiple>
                <?php
                foreach ($allGroups as $key => $value) {
                    ?>
                    <option
                        value="<?php echo $key; ?>"
                        <?php echo in_array($key, $secondaryGroupIds) ? 'selected' : ''; ?>>
                            <?php echo _esc($value) ?>
                    </option>
                <?php }
                ?>
            </select>
            </div>
        <?php }
        ?>
        <div class="field">
            <strong class="field-label">
                <?php translate('homepage'); ?>
            </strong>
            <input type="url" name="homepage"
                   value="<?php echo _esc($row->homepage); ?>">
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate('html_editor'); ?>
            </strong><select
                name="html_editor">
                <option value="ckeditor"
                <?php
                if (! $row->html_editor || $row->html_editor == HtmlEditor::CKEDITOR) {
                    echo 'selected';
                }
        ?>>CKEditor</option>
                <option value="codemirror"
                <?php
        if ($row->html_editor == HtmlEditor::CODEMIRROR) {
            echo 'selected';
        }
        ?>>CodeMirror</option>
            </select>
        </div>
        <div class="checkbox block">
            <label>
                <input type="checkbox" value="1"
                       class="js-switch"
                       <?php
               if ($row->require_password_change) {
                   echo 'checked';
               }
        ?>
                       name="require_password_change" id="require_password_change"><?php translate('REQUIRE_PASSWORD_CHANGE_ON_NEXT_LOGIN'); ?> </label>
        </div>
        <?php
        if ($permissionChecker->hasPermission('users')) {
            ?>
            <div class="checkbox block field">
                <label> <input type="checkbox" value="1" name="admin" id="admin"
                               class="js-switch"
                               <?php
                               if ($row->admin) {
                                   echo 'checked';
                               }
            ?>> <?php translate('is_admin'); ?></label>
                <span class="has-help"
                      onclick="$('div#is_admin').slideToggle()">
                    <i class="fa fa-question-circle text-info" aria-hidden="true"></i>
                </span>
            </div>
            <div id="is_admin" class="help" style="display: none">
                <?php echo nl2br(get_translation('HELP_IS_ADMIN')); ?>
            </div>
            <div class="checkbox block">
                <label> <input type="checkbox" value="1" name="locked"
                               id="locked"
                               class="js-switch"
                               <?php
            if ($row->locked) {
                echo 'checked';
            }
            ?>> <?php translate('locked'); ?> </label>
            </div>
            <?php
        } else {
            echo '<input type="hidden" name="admin" value="' . $row->admin . '">';
            if ($row->locked) {
                echo '<input type="hidden" name="locked" value="' . $row->locked . '">';
            }
        }
        ?>
        <div class="field">
            <strong class="field-label">
                <?php translate('default_language'); ?>
            </strong>
            <select name="default_language">
                <option value="" <?php
                if (! $row->default_language) {
                    echo ' selected';
                }
        ?>>[<?php translate('standard'); ?>]</option>
                        <?php
                $languageCount = count($languages);
        for ($i = 0; $i < $languageCount; $i++) {
            if ($row->default_language == $languages[$i]) {
                echo '<option value="' . $languages[$i] . '" selected>' . getLanguageNameByCode($languages[$i]) . '</option>';
            } else {
                echo '<option value="' . $languages[$i] . '">' . getLanguageNameByCode($languages[$i]) . '</option>';
            }
        }
        ?>
            </select>
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate('about_me'); ?>
            </strong>
            <textarea rows=10 cols=50 name="about_me"><?php esc($row->about_me) ?></textarea>
        </div>
        <div class="voffset2">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i>
                <?php translate('save'); ?></button>
        </div>
        <?php
        echo ModuleHelper::endForm();
        break;
    }
    ?>
    <?php
    $translation = new JSTranslation([], 'UserTranslation');
    $translation->addKey('passwords_not_equal');
    $translation->render();

    enqueueScriptFile(
        ModuleHelper::buildRessourcePath(
            'core_users',
            'js/form.js'
        )
    );
    enqueueScriptFile('../node_modules/password-strength-meter/dist/password.min.js');
    combinedScriptHtml();
} else {
    noPerms();
}
