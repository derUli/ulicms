<?php
$item = ViewBag::get ( "item" );
if ($item) {
	?>
<tr>
	<td><input class="checkbox-done" data-id="<?php echo $item->getID();?>"
		data-url="<?php echo ModuleHelper::buildMethodCallURL("TodoListModule", "checkItem");?>"
		type="checkbox" <?php if($item->isDone()){ echo "checked";}?>></td>
	<td><span class="title" data-id="<?php echo $item->getID();?>"><?php Template::escape($item->getTitle());?></span></td>
	<td class="text-center"><a href="#"
		data-id="<?php echo $item->getID();?>" class="btn-edit"
		data-url="<?php echo ModuleHelper::buildMethodCallURL("TodoListModule", "updateItem");?>"><img
			src="gfx/edit.png" alt="<?php translate("edit")?>"
			title="<?php translate("edit");?>"></a></td>
	<td class="text-center"><a href="#"
		data-id="<?php echo $item->getID();?>" class="btn-delete"
		data-url="<?php echo ModuleHelper::buildMethodCallURL("TodoListModule", "deleteItem");?>"><img
			src="gfx/delete.png" alt="<?php translate("delete")?>"
			title="<?php translate("delete");?>"></a></td>
</tr>
<?php }?>