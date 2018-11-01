<?php
if (Request::getVar("save")) {
    ?>
<div class="alert alert-success lert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	<?php translate("changes_was_saved");?>
	</div>
<?php }?>
<?php
echo ModuleHelper::buildMethodCallForm("LockInactiveUsersController", "save");
?>
<div class="checkbox">
	<label class="form-check-label" for="enable"><input
		class="form-check-input" type="checkbox" value="1" id="enable"
		<?php if(Settings::get("lock_inactive_users/enable")) echo "checked";?>
		name="enable"> 
		<?php translate("enabled");?> </label>
</div>
<div class="form-group">
	<label for="days"><?php translate("lock_users_after_x_days_of_inactivity")?></label>
	<input type="number" step="1" min="3" max="999" class="form-control"
		name="days" id="days"
		value="<?php esc(intval(Settings::get("lock_inactive_users/days")));?>">
</div>
<button type="submit" class="btn btn-primary"><?php translate("save");?></button>
<?php
echo ModuleHelper::endForm();
?>