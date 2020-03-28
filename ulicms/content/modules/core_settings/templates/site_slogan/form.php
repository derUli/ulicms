<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("settings_simple")) {
    $languages = getAllLanguages();
    $site_slogans = [];
    for ($i = 0; $i < count($languages); $i ++) {
        $lang = $languages[$i];
        $site_slogans[$lang] = Settings::get("site_slogan_" . $lang);

        if (!$site_slogans[$lang]) {
            $site_slogans[$lang] = Settings::get("site_slogan");
        }
    }
    ?><p>
        <a href="<?php echo ModuleHelper::buildActionURL("settings_simple"); ?>"
           class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <h1>
        <?php translate("site_slogan"); ?>
    </h1>
    <?php
    echo ModuleHelper::buildMethodCallForm("SiteSloganController", "save", [], "post", array(
        "id" => "site_slogan_settings"
    ));
    ?>
    <table>
        <tr>
            <td style="min-width: 100px;"><strong><?php
                    translate("language");
                    ?>
                </strong></td>
            <td>
                <strong><?php translate("site_slogan"); ?></strong>
            </td>
        </tr>
        <?php
        for ($n = 0; $n < count($languages); $n ++) {
            $lang = $languages[$n];
            ?>
            <tr>
                <td><?php
                    esc(getLanguageNameByCode($lang));
                    ?></td>
                <td><input
                        name="site_slogan_<?php
                        esc($lang);
                        ?>"
                        value="<?php
                        esc($site_slogans[$lang]);
                        ?>"></td>
                    <?php
                }
                ?>
        </tr>
        <tr>
            <td></td>
            <td class="text-center">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> 
                    <?php translate("save_changes");?>
                </button>
            </td>
        </tr>
    </table>
    <?php echo ModuleHelper::endForm(); ?>
    <script>
        $("#site_slogan_settings").ajaxForm({beforeSubmit: function (e) {
                $("#message").html("");
                $("#loading").show();
            },
            success: function (e) {
                $("#loading").hide();
                $("#message").html("<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
            }
        });

    </script>
    <?php
} else {
    noPerms();
}
