<?php
$permissionChecker = new ACL();
if (($permissionChecker->hasPermission("users") and $permissionChecker->hasPermission("users_edit")) or ( $_GET["admin"] == $_SESSION["login_id"])) {
    $id = intval($_GET["id"]);
    $languages = getAvailableBackendLanguages();
    $query = db_query("SELECT * FROM " . tbname("users") . " WHERE id='$id'");
    $user = new User($admin);
    $secondaryGroups = $user->getSecondaryGroups();
    $secondaryGroupIds = array();
    foreach ($secondaryGroups as $group) {
        $secondaryGroupIds[] = $group->getID();
    }
    while ($row = db_fetch_object($query)) {
        ?>
        <p>
            <a href="<?php echo ModuleHelper::buildActionURL("admins"); ?>"
               class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
        </p>
        <form action="index.php?sClass=UserController&sMethod=update"
              name="userdata_form" method="post" enctype="multipart/form-data"
              id="edit_user" autocomplete="off">
                  <?php csrf_token_html(); ?>
            <img
                src="<?php
                echo get_gravatar($row->email, 200);
                ?>"
                alt="Avatar Image" /> <br /> <input type="hidden" name="edit_admin"
                value="edit_admin"> <input type="hidden" name="id"
                value="<?php
                echo $row->id;
                ?>"> <br /> <strong><?php translate("username"); ?>*</strong><br />
            <input type="text" name="admin_username"
                   value="<?php echo real_htmlspecialchars($row->username); ?>" required
                   <?php
                   if (!$permissionChecker->hasPermission("users")) {
                       ?>
                       readonly="readonly" <?php
                   }
                   ?>> <br /> <strong><?php translate("lastname"); ?></strong><br />
            <input type="text" name="admin_lastname"
                   value="<?php
                   echo real_htmlspecialchars($row->lastname);
                   ?>"> <br /> <strong><?php translate("firstname"); ?></strong><br />
            <input type="text" name="admin_firstname"
                   value="<?php
                   echo real_htmlspecialchars($row->firstname);
                   ?>"
                   required="required"><br /> <strong><?php translate("email"); ?></strong><br />
            <input type="email" name="admin_email"
                   value="<?php
                   echo real_htmlspecialchars($row->email);
                   ?>"><br /> <strong><?php translate("last_login"); ?></strong><br />
                   <?php
                   if (is_null($row->last_login)) {
                       translate("never");
                   } else {
                       echo strftime("%x %X", $row->last_login);
                   }
                   ?><br /> <br /> <strong><?php translate("new_password"); ?></strong><br />
            <input type="password" name="admin_password" id="admin_password"
                   value="" autocomplete="off"><br /> <strong><?php translate("password_repeat"); ?></strong><br />
            <input type="password" name="admin_password_repeat"
                   id="admin_password_repeat" value="" autocomplete="off"> <br />
                   <?php
                   $permissionChecker = new ACL();
                   if ($permissionChecker->hasPermission("users")) {
                       $allGroups = $permissionChecker->getAllGroups();
                       asort($allGroups);
                       ?> <strong><?php translate("primary_group"); ?></strong> <br />
                <select name="group_id">
                    <option value="-"
                    <?php
                    if ($row->group_id === null) {
                        echo "selected";
                    }
                    ?>>[<?php translate("none"); ?>]</option>
                            <?php
                            foreach ($allGroups as $key => $value) {
                                ?>
                        <option
                            value="<?php
                            echo $key;
                            ?>"
                            <?php
                            if (intval($row->group_id) == $key) {
                                echo "selected";
                            }
                            ?>>
                                <?php esc($value) ?>
                        </option>
                        <?php
                    }
                    ?>
                </select> <br /> <br /> <strong><?php translate("secondary_groups"); ?></strong>
                <br /> <select name="secondary_groups[]" multiple>

                    <?php
                    foreach ($allGroups as $key => $value) {
                        ?>
                        <option
                            value="<?php
                            echo $key;
                            ?>"
                            <?php
                            echo in_array($key, $secondaryGroupIds) ? "selected" : "";
                            ?>>
                                <?php echo real_htmlspecialchars($value) ?>
                        </option>
                        <?php
                    }
                    ?>
                </select> <br /> <br />

                <?php
            }
            ?>
            <strong><?php
                translate("homepage");
                ?></strong> <br /> <input type="url" name="homepage"
                                    value="<?php echo real_htmlspecialchars($row->homepage); ?>"> <br />
            <strong><?php translate("html_editor"); ?></strong> <br /> <select
                name="html_editor">
                <option value="ckeditor"
                        <?php if (!$row->html_editor or $row->html_editor == "ckeditor") echo "selected" ?>>CKEditor</option>
                <option value="codemirror"
                        <?php if ($row->html_editor == "codemirror") echo "selected" ?>>CodeMirror</option>
            </select>
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="1"
                           class="js-switch"
                           <?php
                           if ($row->require_password_change) {
                               echo "checked";
                           }
                           ?>
                           name="require_password_change" id="require_password_change"><?php
                           translate("REQUIRE_PASSWORD_CHANGE_ON_NEXT_LOGIN");
                           ?> </label>
            </div>
            <?php
            if ($permissionChecker->hasPermission("users")) {
                ?>
                <div class="voffset2">
                    <div class="checkbox">
                        <label> <input type="checkbox" value="1" name="admin" id="admin"
                                       class="js-switch"
                                       <?php
                                       if ($row->admin) {
                                           echo "checked";
                                       }
                                       ?>> <?php translate("is_admin"); ?></label> <span style="cursor: help;"
                                                                        onclick="$('div#is_admin').slideToggle()">
                            <i class="fa fa-question-circle text-info" aria-hidden="true"></i>

                        </span>
                    </div>
                </div>
                <div id="is_admin" class="help" style="display: none">
                    <?php
                    echo nl2br(get_translation("HELP_IS_ADMIN"));
                    ?>
                </div>
                <div class="checkbox">
                    <label> <input type="checkbox" value="1" name="locked"
                                   id="locked"
                                   class="js-switch"
                                   <?php
                                   if ($row->locked) {
                                       echo "checked";
                                   }
                                   ?>> <?php
                                   translate("locked");
                                   ?> </label>
                </div>
                <?php
            } else {
                echo '<input type="hidden" name="admin" value="' . $row->admin . '">';
                if ($row->locked) {
                    echo '<input type="hidden" name="locked" value="' . $row->locked . '">';
                }
            }
            ?>
            <strong><?php translate("default_language"); ?></strong><br />
            <select name="default_language">
                <option value="" <?php if (!$row->default_language) echo " selected"; ?>>[<?php translate("standard"); ?>]</option>
                <?php
                for ($i = 0; $i < count($languages); $i ++) {
                    if ($row->default_language == $languages[$i]) {
                        echo '<option value="' . $languages[$i] . '" selected>' . getLanguageNameByCode($languages[$i]) . '</option>';
                    } else {
                        echo '<option value="' . $languages[$i] . '">' . getLanguageNameByCode($languages[$i]) . '</option>';
                    }
                }
                ?>
            </select> <br /> <br /> <strong><?php translate("about_me"); ?></strong><br />
            <textarea rows=10 cols=50 name="about_me"><?php echo htmlspecialchars($row->about_me) ?></textarea>
            <br />
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> <?php translate("OK"); ?></button>
        </form>
        <?php
        break;
    }
    ?>
    <?php
} else {
    noPerms();
}
