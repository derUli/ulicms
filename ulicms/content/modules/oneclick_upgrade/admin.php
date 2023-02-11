<?php

use App\HTML\Alert;

use function App\HTML\text;

define("MODULE_ADMIN_HEADLINE", get_translation("oneclick_upgrade") . " " . get_translation("settings"));

function oneclick_upgrade_admin()
{
    if (Request::isPost()) {
        Settings::set("oneclick_upgrade_channel", strval($_POST["oneclick_upgrade_channel"]));
    }
    $oneclick_upgrade_channel = strval(Settings::get("oneclick_upgrade_channel"));
    $channels = array(
        "fast",
        "slow",
    );

    $channelCount = count($channels);
    ?>

    <?php
    if (Request::isPost()) {
        ?>
        <div class="alert alert-success alert-dismissable fade in">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <?php translate("changes_was_saved") ?>
        </div>
    <?php }
    ?>
    <form action="<?php echo getModuleAdminSelfPath(); ?>"
          method="post">
              <?php csrf_token_html(); ?>
        <div>
            <label for="oneclick_upgrade_channel"><?php translate("channel") ?></label><br />
            <select
                name="oneclick_upgrade_channel"
                size=1
                id="oneclick_upgrade_channel">
                    <?php for ($i = 0; $i < $channelCount; $i++) { ?>
                    <option value="<?php Template::escape($channels[$i]) ?>"
                    <?php
                    if ($oneclick_upgrade_channel == $channels[$i]) {
                        echo " selected";
                    }
                        ?>><?php Template::escape(get_translation($channels[$i])) ?></option>
                        <?php } ?>

            </select>
        </div>
        <div id="help-texts" class="voffset3 alert alert-info">
            <div data-channel="fast">
                <?php echo text(get_translation("fast_description")); ?>
            </div>

            <div data-channel="slow">
                <?php echo text(get_translation("slow_description")); ?>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> <?php translate("save"); ?></button>
    </form>
    <?php
    enqueueScriptFile(
                            ModuleHelper::buildRessourcePath(
            "oneclick_upgrade",
            "js/settings.js"
        )
                        );
    combinedScriptHtml();
}
