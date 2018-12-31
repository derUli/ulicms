<div class="alert alert-info">
<?php echo nl2br(get_translation("engagespot_info"));?>
</div>
<p>
	<a
		href="<?php esc(PushNotificationsController::ENGAGESPOT_DASHBOARD_URL);?>"
		class="btn btn-success" target="_blank"><?php translate("send_push_notifications");?></a>
</p>

<?php echo ModuleHelper::buildMethodCallForm("PushNotificationsController", "saveSettings");?>
<div class="form-group">
	<label for="site_key"><?php translate("site_key");?></label> <input
		type="text" class="form-control" name="site_key" id="site_key"
		value="<?php esc(Settings::get("engagespot/site_key"));?>">
</div>
<button type="submit" class="btn btn-primary"><?php translate("save");?></button>
<?php echo ModuleHelper::endForm();?>
