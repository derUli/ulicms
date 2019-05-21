<?php echo ModuleHelper::buildMethodCallForm(InstagramController::class, "save"); ?>


<div class="form-group">
    <label for="username"><?php translate("username"); ?></label>
    <input type="text" class="form-control"
           name="username" id="username"
           placeholder="<?php translate("instagram_username"); ?>" autocomplete="new-password" value="<?php esc(Settings::get("instagram/username")); ?>">
</div>

<div class="form-group">
    <label for="password"><?php translate("password"); ?></label>
    <input type="password" class="form-control"
           name="password" id="password"
           placeholder="<?php translate("instagram_password"); ?>" autocomplete="new-password" value="<?php esc(Settings::get("instagram/password")); ?>">
</div>
<button type="submit" class="btn btn-primary">
    <i class="fa fa-save"></i>
    <?php translate("save"); ?>
</button>
<?php
echo ModuleHelper::endForm();
