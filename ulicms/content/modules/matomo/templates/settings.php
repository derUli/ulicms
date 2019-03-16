<?php
$matomo_url = Settings::get("matomo_url", "str");
$matomo_site_id = Settings::get("matomo_site_id", "int");
?>
<?php
if (Request::isPost()) {
    ?>
    <div class="alert alert-success alert-dismissable fade in">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php translate("changes_was_saved") ?>
    </div>
    <?php
}
?>
<form
    action="<?php Template::escape(ModuleHelper::buildAdminURL("matomo")) ?>"
    method="post">
        <?php csrf_token_html() ?>
    <p>
        <strong><?php translate("matomo_url"); ?></strong><br /> <input
            type="text" name="matomo_url"
            value="<?php Template::escape($matomo_url); ?>">
    </p>
    <p>
        <strong><?php translate("matomo_site_id"); ?></strong><br /> <input
            type="number" step="1" name="matomo_site_id"
            value="<?php Template::escape($matomo_site_id); ?>">
    </p>
    <p class="voffset3">
        <button type="submit" class="btn btn-default"><i class="fa fa-save"></i> <?php translate("save"); ?></button>
    </p>
</form>