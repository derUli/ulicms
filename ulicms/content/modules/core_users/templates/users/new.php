<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("users") and $permissionChecker->hasPermission("users_create")) {
    $languages = getAvailableBackendLanguages();
    $default_language = getSystemLanguage();
    $ref = _esc(Request::getVar("ref", "home"));
    ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("admins"); ?>"
           class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <form action="index.php?sClass=UserController&sMethod=create"
          method="post" autocomplete="off" id="edit_user">
              <?php csrf_token_html(); ?>
        <input type="hidden" name="add_admin" value="add_admin"> <strong><?php translate("username"); ?>*</strong><br />
        <input type="text" required="required" name="admin_username" value="">
        <br /> <strong><?php translate("lastname"); ?></strong><br /> <input
            type="text" name="admin_lastname" value=""> <br /> <strong><?php translate("firstname"); ?></strong><br />
        <input type="text" name="admin_firstname" value=""><br /> <strong><?php translate("email"); ?></strong><br />
        <input type="email" name="admin_email" value=""><br /> <strong><?php translate("password"); ?>*</strong><br />
        <input type="password" required="required" name="admin_password"
               id="admin_password" value="" autocomplete="off"> <br /> <strong><?php translate("password_repeat"); ?>*</strong><br />
        <input type="password" required="required" name="admin_password_repeat"
               id="admin_password_repeat" value="" autocomplete="off">
               <?php
               $permissionChecker = new ACL();
               $allGroups = $permissionChecker->getAllGroups();
               asort($allGroups);
               ?>
        <br /> <strong><?php translate("primary_group"); ?></strong> <br /> <select
            name="group_id">
            <option value="-"
            <?php
            if ($row->group_id === null) {
                echo "selected";
            }
            ?>>[<?php translate("none"); ?>]</option>
                    <?php
                    foreach ($allGroups as $key => $value) {
                        ?>
                <option value="<?php
                echo $key;
                ?>"
                        <?php
                        if (Settings::get("default_acl_group") == $key) {
                            echo "selected";
                        }
                        ?>>
                            <?php echo real_htmlspecialchars($value) ?>
                </option>
                <?php
            }
            ?>
        </select> <br /> <br /> <strong><?php translate("secondary_groups"); ?></strong>
        <br /> <select name="secondary_groups[]" multiple>

            <?php
            foreach ($allGroups as $key => $value) {
                ?>
                <option value="<?php
                echo $key;
                ?>">
                            <?php echo real_htmlspecialchars($value) ?>
                </option>
                <?php
            }
            ?>
        </select>

        <div class="checkbox block voffset3">
            <label>
                <input type="checkbox" value="1"
                       name="require_password_change"
                       id="require_password_change"
                       class="js-switch"><?php translate("REQUIRE_PASSWORD_CHANGE_ON_NEXT_LOGIN"); ?> </label>
        </div>

        <div class="checkbox block voffset3">
            <label><input type="checkbox" id="send_mail" name="send_mail"
                          value="sendmail"
                          class="js-switch">
                <?php translate("SEND_LOGINDATA_BY_MAIL"); ?></label>
        </div>
        <div class="checkbox block voffset3">
            <label><input type="checkbox" value="1" name="admin" id="admin"
                          class="js-switch">

                <?php translate("is_admin"); ?> </label>

            <span
                style="cursor: help;" onclick="$('div#is_admin').slideToggle()">
                <i class="fa fa-question-circle text-info" aria-hidden="true"></i>
            </span>
        </div>
        <div id="is_admin" class="help" style="display: none">
            <?php
            echo nl2br(get_translation("HELP_IS_ADMIN"));
            ?>
        </div>

        <div class="checkbox block voffset3">
            <label><input type="checkbox" value="1" name="locked" id="locked"
                          class="js-switch">
                <?php translate("locked"); ?> </label>
        </div>

        <div class="voffset3">
            <strong><?php translate("default_language"); ?></strong><br />
            <select name="default_language">
                <option value="" selected>[<?php translate("standard"); ?>]</option>
                <?php
                for ($i = 0; $i < count($languages); $i ++) {
                    echo '<option value="' . $languages[$i] . '">' . getLanguageNameByCode($languages[$i]) . '</option>';
                }
                ?>
            </select></div><br />
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> <?php translate("create_user"); ?></button>
    </form>
    <?php
} else {
    noPerms();
}
