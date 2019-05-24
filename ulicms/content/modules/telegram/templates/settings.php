<?php
echo ModuleHelper::buildMethodCallForm(TelegramController::class, "save");

if (Request::getVar("save")) {
    echo UliCMS\HTML\Alert::success(get_translation("changes_was_saved"));
}

if (Request::getVar("error")) {
    echo UliCMS\HTML\Alert::danger(get_translation(
                    Request::getVar("error")));
}
?>
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

           placeholder="<?php translate("telegram_bot_token_placeholder"); ?>" autocomplete="new-password" value="<?php esc(Settings::get("telegram/bot_token")); ?>">
</div>

<button type="submit" class="btn btn-primary">
    <i class="fa fa-save"></i>
    <?php translate("save"); ?>
</button>
<?php
echo ModuleHelper::endForm();
