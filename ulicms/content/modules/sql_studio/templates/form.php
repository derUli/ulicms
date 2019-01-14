<?php $sql = "";?>
<?php

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