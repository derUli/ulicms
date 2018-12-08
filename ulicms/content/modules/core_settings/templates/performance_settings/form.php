<?php
use UliCMS\Security\PermissionChecker;

$permissionChecker = new PermissionChecker(get_user_id());

if (! $permissionChecker->hasPermission("performance_settings")) {
    noPerms();
} else {
    $cache_enabled = ! Settings::get("cache_disabled");
    $cache_period = round(Settings::get("cache_period") / 60);
    ?>
    <?php
    if (Request::getVar("clear_cache")) {
        ?>

<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<?php translate ( "cache_was_cleared" );?>
</div>
<?php }?>
<?php
    if (Request::getVar("save")) {
        ?>
<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	<?php translate("changes_was_saved");?>
	</div>
<?php }?>
<?php ModuleHelper::buildMethodCallForm($sClass, $sMethod);?>
<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("settings_categories");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a> <a
		href="<?php echo ModuleHelper::buildMethodCallUrl("PerformanceSettingsController", "clearCache");?>"
		class="btn btn-warning pull-right"><?php translate("clear_cache");?></a>
</p>
<h1><?php translate("performance");?></h1>
<?php echo ModuleHelper::buildMethodCallForm("PerformanceSettingsController", "save");?>
<h2><?php translate("page_cache");?></h2>
<div class="label">
	<label for="cache_enabled"><?php translate("cache_enabled");?>
				</label>
</div>
<div class="inputWrapper">
	<input type="checkbox" id="cache_enabled" name="cache_enabled"
		value="cache_enabled"
		<?php
    
    if ($cache_enabled)
        echo " checked=\"checked\"";
    ?>>
</div>
<div class="label">
			<?php
    
    translate("CACHE_VALIDATION_DURATION");
    ?>
			</div>
<div class="inputWrapper">
	<input type="number" name="cache_period" min="1" max="20160"
		value="<?php
    
    echo $cache_period;
    ?>">
	<?php translate("minutes");?>
			</div>
<p>
	<button type="submit" name="submit" class="btn btn-primary voffset3"><?php translate("save_changes");?></button>
</p>
<script type="text/javascript">
$("#form").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  },
  success:function(e){
  $("#loading").hide();
  $("#message").html("<span style=\"color:green;\"><?php translate("CHANGES_WAS_SAVED");?></span>");
  }
});
</script>
<?php echo ModuleHelper::endForm();?>
<?php }?>