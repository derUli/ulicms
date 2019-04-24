<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("settings_simple")) {
    $controller = ControllerRegistry::get();
    $allSettings = Settings::getAll();
    $settings = array();
    foreach ($allSettings as $option) {
        $settings[$option->name] = Template::getEscape($option->value);
    }
    ?>
    <p>
        <a
            href="<?php echo ModuleHelper::buildActionURL("settings_categories"); ?>"
            class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <h2><?php translate("general_settings"); ?></h2>
    <p>Hier können Sie die Einstellungen für Ihre Internetseite verändern.</p>
    <?php
    echo ModuleHelper::buildMethodCallForm("SimpleSettingsController", "save", array(), "post", array(
        "id" => "settings_simple"
    ))
    ?>
    <table>
        <tr>
            <td><strong><?php translate("homepage_title"); ?></strong></td>
            <td><a href="index.php?action=homepage_title" class="btn btn-default"><i
                        class="fa fa-edit"></i> <?php translate("edit"); ?></a></td>
        </tr>
        <tr>
            <td><strong><?php translate("motto"); ?></strong></td>
            <td><a href="index.php?action=motto" class="btn btn-default"><i
                        class="fa fa-edit"></i> <?php translate("edit"); ?></a></td>
        </tr>
        <tr>
            <td><strong><?php translate("homepage_owner"); ?></strong></td>
            <td><input type="text" name="homepage_owner"
                       value="<?php
                       echo $settings["homepage_owner"];
                       ?>"></td>
        </tr>

        <tr>
            <td><strong><?php translate("OWNER_MAILADRESS"); ?></strong></td>
            <td><input type="email" name="email"
                       value="<?php
                       echo $settings["email"];
                       ?>"></td>
        </tr>
        <tr>
            <td><strong><?php translate("frontpage"); ?></strong></td>
            <td><a href="index.php?action=frontpage_settings"
                   class="btn btn-default"><i class="fa fa-edit"></i> <?php
                       translate("edit");
                       ?></a></td>
        </tr>
        <?php if ($permissionChecker->hasPermission("error_pages")) {
            ?>
            <tr>
                <td><strong><?php translate("error_pages"); ?></strong></td>
                <td><a href="index.php?action=error_pages"
                       class="btn btn-default"><i class="fa fa-edit"></i> <?php
                           translate("edit");
                           ?></a></td>
            </tr>
        <?php } ?>
        <tr>
            <td><strong><?php translate("MAINTENANCE_MODE_ENABLED"); ?></strong></td>
            <td><input type="checkbox" name="maintenance_mode"
                       class="js-switch"
                       <?php
                       if (strtolower($settings["maintenance_mode"] == "on") || $settings["maintenance_mode"] == "1" || strtolower($settings["maintenance_mode"]) == "true") {
                           echo " checked";
                       }
                       ?>></td>
        </tr>
        <tr>
            <td><strong><?php translate("GUEST_MAY_REGISTER"); ?></strong></td>
            <td><input type="checkbox" name="visitors_can_register"
                       class="js-switch"
                       <?php
                       if (strtolower($settings["visitors_can_register"] == "on") || $settings["visitors_can_register"] == "1" || strtolower($settings["visitors_can_register"]) == "true") {
                           echo " checked";
                       }
                       ?>></td>
        </tr>
        <tr>
            <td><strong><?php
                    translate("enable_password_reset");
                    ?></strong>

            <td><input type="checkbox" name="disable_password_reset"
                       value="enable"
                       class="js-switch"

                       <?php if (!isset($settings["disable_password_reset"])) echo " checked" ?>>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <strong><?php translate("metadata"); ?></strong></strong></td>
        </tr>
        <tr>
            <td><strong><?php
                    translate("description");
                    ?></strong></td>
            <td><a href="index.php?action=meta_description"
                   class="btn btn-default"><i class="fa fa-edit"></i> <?php
                       translate("edit");
                       ?></a></td>
        </tr>
        <tr>
            <td><strong><?php translate("keywords"); ?></strong></td>
            <td><a href="index.php?action=meta_keywords" class="btn btn-default"><i
                        class="fa fa-edit"></i>  <?php
                        translate("edit");
                        ?></a></td>
        </tr>
        <?php
        if ($permissionChecker->hasPermission("open_graph")) {
            ?>
            <tr>
                <td><strong><?php translate("open_graph"); ?>
                    </strong></td>
                <td><a href="index.php?action=open_graph" class="btn btn-default"><i
                            class="fa fa-edit"></i> <?php
                            translate("edit");
                            ?></a></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td><strong><?php translate("timezone"); ?></strong></td>
            <td><select name="timezone" size="1">
                    <?php
                    $timezones = $controller->getTimezones();
                    $current_timezone = Settings::get("timezone");
                    $current_timezone = trim($current_timezone);
                    sort($timezones);
                    for ($i = 0; $i < count($timezones); $i ++) {
                        $thisTimezone = $timezones[$i];
                        $thisTimezone = trim($thisTimezone);
                        if ($thisTimezone === $current_timezone) {
                            echo '<option value="' . $thisTimezone . '" selected>' . $thisTimezone . '</option>';
                        } else {
                            echo '<option value="' . $thisTimezone . '">' . $thisTimezone . '</option>';
                        }
                    }
                    ?>
                </select></td>
        </tr>
        <tr>
            <td><strong><?php translate("search_engines"); ?></strong></td>
            <td><select name="robots" size=1>
                    <?php
                    if (Settings::get("robots") == "noindex,nofollow") {
                        ?>

                        <option value="index,follow"><?php translate("EARCH_ENGINES_INDEX"); ?></option>
                        <option value="noindex,nofollow" selected><?php
                            translate("SEARCH_ENGINES_NOINDEX");
                            ?></option>

                        <?php
                    } else {
                        ?>
                        <option value="index,follow" selected><?php
                            translate("SEARCH_ENGINES_INDEX");
                            ?></option>
                        <option value="noindex,nofollow"><?php
                            translate("SEARCH_ENGINES_NOINDEX");
                            ?></option>
                        <?php
                    }
                    ?>
                </select></td>
        </tr>
        <?php
        do_event("settings_simple");
        ?>
        <tr>
            <td>

            <td align="center">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> <?php translate("save_changes") ?></button>
            </td>
        </tr>
    </table>
    <input type="hidden" name="save_settings" value="save_settings">
    <?php echo ModuleHelper::endForm(); ?>
    <?php
    enqueueScriptFile(ModuleHelper::buildRessourcePath("core_settings", "js/settings_simple.js"));
    combinedScriptHtml();
    ?>
    <?php
} else {
    noPerms();
}
