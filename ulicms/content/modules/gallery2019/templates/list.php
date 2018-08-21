<?php $acl = new ACL();?>
<?php if($acl->hasPermission("gallery_create")){?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("gallery_create");?>"
		class="btn btn-primary"><?php translate("create_gallery");?></a>
</p>
<?php }?>

<table class="tablesorter">
	<thead>
		<tr>
			<th><?php translate("title");?></th>
			<th><?php translate("image_amount")?></th>
			<td></td>
			<td></td>
		</tr>
	</thead>
	<tbody></tbody>
</table>