<?php
$replace_placeholders = Settings::get("sql_studio/replace_placeholders");
$table_name_onclick_action = Settings::get("sql_studio/table_name_onclick_action");

$tableNameOnClickOptions = array(
    new UliCMS\HTML\ListItem("generate_select_statement", get_translation("generate_select_statement")),
    new UliCMS\HTML\ListItem("generate_and_execute_select_statement", get_translation("generate_and_execute_select_statement"))
);
?>
<h1><?php translate("sql_studio_settings");?></h1>
<div class="form-group">
	<a href="<?php echo ModuleHelper::buildAdminURL("sql_studio");?>"
		class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back");?></a>
</div>
<?php echo ModuleHelper::buildMethodCallForm(SqlStudioController::class, "saveSettings");?>
<div class="form-group">
	<label for="table_name_onclick_action"><?php translate("table_name_onclick_action");?></label> 
<?php

echo UliCMS\HTML\Input::SingleSelect("table_name_onclick_action", $table_name_onclick_action, $tableNameOnClickOptions, 1, array(
    "id" => "table_name_onclick_action"
));
?>
</div>
<div class="form-group">
	<div class="checkbox">
		<label>
<?php echo UliCMS\HTML\Input::CheckBox("replace_placeholders", $replace_placeholders, "1");?>
<?php translate("replace_placeholders");?></label>
	</div>
</div>
<button type="submit" class="btn btn-primary">
	<i class="fa fa-save"></i> <?php translate("save");?></button>
<?php echo ModuleHelper::endForm();?>