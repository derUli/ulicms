<?php
$item = ViewBag::get ( "item" );
if ($item) {
	?>
<tr>
	<td><input class="checkbox-done" data-id="<?php echo $item->getID();?>"
		data-url="<?php echo ModuleHelper::buildMethodCallURL("TodoListModule", "checkItem");?>"
		type="checkbox" <?php if($item->isDone()){ echo "checked";}?>></td>
	<td><?php Template::escape($item->getTitle());?></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<?php }?>