<div class="form-group">
	<a href="<?php echo ModuleHelper::buildAdminURL("sql_studio");?>"
		class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back");?></a></a>
</div>
<?php echo ModuleHelper::buildMethodCallForm(SqlStudioController::class, "saveSettings");?>
<p>Coming Soon!</p>
<?php echo ModuleHelper::endForm();?>