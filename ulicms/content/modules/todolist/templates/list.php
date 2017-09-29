<?php $data = TodoListItem::getAllbyUser();?>
<?php csrf_token_html();?>
<p>
	<a href="#"
		data-url="<?php echo ModuleHelper::buildMethodCallURL("TodoListModule", "addItem");?>"
		class="btn btn-info" id="btn-new" role="button"><?php translate("new");?></a>
</p>
<div class="scroll">
	<table id="todolist">
		<thead>
			<th></th>
			<th><?php translate("title");?></th>
			<td class="text-center"></td>
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
</div>
<div class="voffset3">
	<div>
		Icons made by <a href="https://www.flaticon.com/authors/dave-gandy"
			title="Dave Gandy">Dave Gandy</a> from <a
			href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a>
		is licensed by <a href="http://creativecommons.org/licenses/by/3.0/"
			title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a>
	</div>
</div>
<script type="text/javascript"
	src="<?php echo ModuleHelper::buildModuleRessourcePath("todolist", "js/list.js")?>"></script>
<?php
$translation = new JSTranslation ();
$translation->addKey ( "title" );
$translation->addKey ( "ask_for_delete" );
$translation->renderJS ();
?>