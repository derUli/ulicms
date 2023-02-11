<p>
    <a
        href="<?php echo ModuleHelper::buildActionURL("settings_categories"); ?>"
        class="btn btn-default btn-back is-not-ajax"><i class="fas fa-arrow-left"></i> <?php translate("back") ?></a>
</p>
<h1><?php translate("spamfilter"); ?></h1>
<?php
$permissionChecker = new ACL();
        if ($permissionChecker->hasPermission("spam_filter")) {
            ?>
    <form id="spamfilter_settings" name="?action=spam_filter" method="post">
        <?php echo ModuleHelper::buildMethodCallForm("SpamFilterController", "save"); ?>
        <div class="checkbox">
            <label for="spamfilter_enabled"> <input type="checkbox"
                                                    id="spamfilter_enabled" name="spamfilter_enabled"
                                                    class="js-switch"
                                                    <?php
                                                            if (Settings::get("spamfilter_enabled") == "yes") {
                                                                echo " checked";
                                                            }
            ?>
                                                    value="yes">
                                                    <?php translate("spamfilter_enabled"); ?>
            </label>
        </div>
        <div id="country_filter_settings"
        <?php
        if (Settings::get("spamfilter_enabled") != "yes") {
            echo " style='display:none;'";
        }
            ?>>
            <p>
                <label for="spamfilter_words_blacklist"><?php translate("blacklist"); ?></label><br />
                <textarea name="spamfilter_words_blacklist"
                          id="spamfilter_words_blacklist" rows=10 cols=40><?php esc(Settings::get("spamfilter_words_blacklist")); ?></textarea>
                <small><?php translate("min_time_to_fill_form_help"); ?></small>
            </p>
            <label for="country_blacklist"><?php translate("spam_countries"); ?></label>
            <input type="text" name="country_blacklist" id="country_blacklist"
                   value="<?php esc(Settings::get("country_blacklist")); ?>">
            <div class="checkbox">
                <label for="disallow_chinese_chars"> <input type="checkbox"
                                                            name="disallow_chinese_chars" id="disallow_chinese_chars"
                                                            class="js-switch"
                                                            <?php
                                                                if (Settings::get("disallow_chinese_chars")) {
                                                                    echo " checked=\"checked\"";
                                                                }
            ?>> <?php translate("disallow_chinese_chars"); ?>
                </label>
            </div>
            <div class="checkbox">
                <label for="disallow_cyrillic_chars"> <input type="checkbox"
                                                             class="js-switch"

                                                             name="disallow_cyrillic_chars" id="disallow_cyrillic_chars"
                                                             <?php
             if (Settings::get("disallow_cyrillic_chars")) {
                 echo " checked=\"checked\"";
             }
            ?>> <?php translate("disallow_cyrillic_chars"); ?>
                </label>
            </div>
            <div class="checkbox">
                <label for="disallow_rtl_chars"> <input type="checkbox"
                                                        class="js-switch"
                                                        name="disallow_rtl_chars" id="disallow_rtl_chars"
                                                        <?php
                                                        if (Settings::get("disallow_rtl_chars")) {
                                                            echo " checked=\"checked\"";
                                                        }
            ?>> <?php translate("disallow_rtl_chars"); ?>
                </label>
            </div>
            <div class="checkbox">
                <label><input name="reject_requests_from_bots" type="checkbox"
                              value=""
                              class="js-switch"
                              <?php echo Settings::get("reject_requests_from_bots") ? "checked" : ""; ?>> <?php translate("reject_requests_from_bots"); ?></label>
            </div>
            <div class="checkbox">
                <label for="check_mx_of_mail_address"> <input type="checkbox"
                                                              class="js-switch"
                                                              name="check_mx_of_mail_address" id="check_mx_of_mail_address"
                                                              <?php
                  if (Settings::get("check_mx_of_mail_address")) {
                      echo " checked=\"checked\"";
                  }
            ?>> <?php translate("check_mx_of_mail_address"); ?>
                </label>
            </div>
            <p>
                <label for="min_time_to_fill_form"><?php translate("min_time_to_fill_form"); ?></label><br />
                <input type="number" name="min_time_to_fill_form"
                       id="min_time_to_fill_form" step="any" min="0"
                       max="<?php esc(PHP_INT_MAX); ?>"
                       value="<?php esc(Settings::get("min_time_to_fill_form", "int")); ?>">
            </p>
        </div>
        <p class="voffset2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?php translate("save_changes"); ?></button>
        </p>
    </form>

    <?php
    $jsTranslation = new JSTranslation([], "SettingsTranslation");
            $jsTranslation->addKey("changes_was_saved");
            $jsTranslation->render();
            enqueueScriptFile(ModuleHelper::buildRessourcePath("core_settings", "js/spam_filter.js"));
            combinedScriptHtml();
        } else {
            noPerms();
        }
