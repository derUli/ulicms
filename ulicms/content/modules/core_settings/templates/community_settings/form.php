<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("settings_categories");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<?php if(Request::getVar("save")){?>
<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<?php translate("changes_was_saved")?>
		</div>
<?php }?>
<?php echo ModuleHelper::buildMethodCallForm(CommunitySettingsController::class, "save");?>
<h1><?php translate("community")?></h1>
<h2><?php translate("comments");?></h2>
<div class="checkbox">
	<label><?php echo UliCMS\HTML\Input::CheckBox("comments_enabled", boolval(Settings::get("comments_enabled")));?><?php translate("comments_enabled")?></label>
</div>
<div class="checkbox">
	<label><?php echo UliCMS\HTML\Input::CheckBox("comments_must_be_approved", boolval(Settings::get("comments_must_be_approved")));?><?php translate("comments_must_be_approved")?></label>
</div>
<p>
	<button type="submit" class="btn btn-primary"><?php translate("save");?></button>
</p>
<?php echo ModuleHelper::endForm();?>