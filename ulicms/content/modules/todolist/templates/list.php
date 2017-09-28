<?php $data = TodoListItem::getAllbyUser();?>
<?php csrf_token_html();?>
<p>
	<a href="#"
		data-url="<?php echo ModuleHelper::buildMethodCallURL("TodoListModule", "addItem");?>"
		class="btn btn-info" id="btn-new" role="button"><?php translate("new");?></a>
</p>
<table class="tablesorter" id="todolist">
	<thead>
		<th></th>
		<th><?php translate("title");?></th>
		<td class="text-center"><?php translate("edit");?></td>
		<td class="text-center"><?php translate("delete");?></td>
	</thead>
	<tbody>
	<?php
	foreach ( $data as $item ) {
		ViewBag::set ( "item", $item );
		echo Template::executeModuleTemplate ( "todolist", "item.php" );
	}
	?>
	</tbody>
</table>
<script type="text/javascript"
	src="<?php echo ModuleHelper::buildModuleRessourcePath("todolist", "js/list.js")?>"></script>