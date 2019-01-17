<?php
use UliCMS\Security\PermissionChecker;

$sql = "";
$execute_select_statements = (Settings::get("sql_studio/table_name_onclick_action") == "generate_and_execute_select_statement");

$permissionChecker = new PermissionChecker(get_user_id());

echo ModuleHelper::buildMethodCallForm(SqlStudioController::class, "executeSql", array(), RequestMethod::POST, array(
    "class" => "sql-studio-ui"
));
?>
<div class="row">
	<div class="col-md-4">
		<div class="tables">
			<strong><?php translate("tables");?></strong>
			<ul class="list-unstyled">
			<?php foreach(ViewBag::get("tables") as $table){?>
		<li><a href="#"
					data-sql="select * from <?php esc(Database::escapeName($table));?>;"
					data-execute="<?php echo strbool($execute_select_statements);?>"
					class="btn btn-link"><i class="fa fa-table" aria-hidden="true"></i>
					 <?php esc($table);?></a></li>
		<?php }?>
		</ul>
		</div>
	</div>
	<div class="col-md-8">
		<p>
			<strong><?php translate("sql_editor");?></strong>
		</p>
		<p><?php
echo UliCMS\HTML\Input::TextArea("sql_code", $sql, 10, 80, array(
    "class" => "codemirror",
    "data-mimetype" => "text/sql",
    "id" => "sql_code"
));
?></p>
		<div class="voffset2">
			<button type="button" id="btn-execute" class="btn btn-primary">
				<i class="fa fa-bolt" aria-hidden="true"></i>
			 <?php translate("execute");?></button>
<?php if($permissionChecker->hasPermission("sql_studio_settings")){?>
			<a
				href="<?php echo ModuleHelper::buildActionURL("sql_studio_settings")?>"
				class="pull-right btn btn-default"> <i class="fa fa-wrench"
				aria-hidden="true"></i>
			 <?php translate("settings");?></a>
			 <?php }?>
		</div>
		<div class="scroll result-table voffset2">
			<label><?php translate("results")?></label> <i
				class="fa fa-spinner fa-spin" id="result-spinner"
				style="display: none;"></i>
		</div>
		<div id="result-data" class="scroll"></div>

	</div>
</div>
<?php echo ModuleHelper::endForm();?>
<?php
enqueueScriptFile(ModuleHelper::buildRessourcePath("sql_studio", "js/backend.js"));
combinedScriptHtml();
?>