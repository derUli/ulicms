<?php
echo ModuleHelper::buildMethodCallForm(TelegramController::class, "save");
?>
<?php
if (Request::getVar("save")) {
    echo UliCMS\HTML\Alert::success(get_translation("changes_was_saved"));
}

if (Request::getVar("error")) {
    echo UliCMS\HTML\Alert::danger(get_translation(
                    Request::getVar("error")));
}
?>

<div class="checkbox">
    <label><?php
        echo UliCMS\HTML\Input::CheckBox("publish_articles_and_images", boolval(Settings::get("telegram/publish_articles_and_images")), "1",
                array("class" => "js-switch"));
        ?>
        <?php translate("telegram_publish_articles_and_images") ?></label>
</div>

<?php if (isModuleInstalled("blog")) { ?>
    <div class="checkbox voffset3">
        <label><?php
            echo UliCMS\HTML\Input::CheckBox("publish_blog_posts", boolval(Settings::get("telegram/publish_blog_posts")), "1",
                    array("class" => "js-switch"));
            ?>
            <?php translate("telegram_publish_blog_posts") ?></label>
    </div>
<?php } ?>
<hr/>
<div class="form-group">
    <label for="text"><?php translate("telegram_channel_name"); ?></label>
    <input type="text" class="form-control"
           name="channel_name" id="text"
           placeholder="<?php translate("telegram_channel_name"); ?>" autocomplete="new-password" value="<?php esc(Settings::get("telegram/channel_name")); ?>">
</div>
<div class="form-group">
    <label for="username"><?php translate("telegram_bot_token"); ?></label>
    <input type="text" class="form-control"
           name="bot_token" id="username"

           placeholder="<?php translate("telegram_bot_token"); ?>" autocomplete="new-password" value="<?php esc(Settings::get("telegram/bot_token")); ?>">
</div>
<div class="row">
    <div class="col-xs-6">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i>
            <?php translate("save"); ?>
        </button>
    </div>
    <div class="col-xs-6 text-right">
        <a href="<?php echo ModuleHelper::buildActionURL("telegram_help"); ?>"
           class="btn btn-info"><i class="fa fa-question-circle"></i>
            <?php translate("help"); ?></a>
    </div>
</div>
<?php
echo ModuleHelper::endForm();
