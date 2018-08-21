<?php
use Gallery2019\Gallery;

$acl = new ACL();
?>
<?php if($acl->hasPermission("gallery_create")){?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("gallery_create");?>"
		class="btn btn-primary"><?php translate("create_gallery");?></a>
</p>
<?php }?>

<?php
$galleries = Gallery::getAll();
?>

<table class="tablesorter">
	<thead>
		<tr>
			<th><?php translate("title");?></th>
			<th><?php translate("image_amount")?></th>
			<th><?php translate("shortcode");?></th>
			<td></td>
			<td></td>
		</tr>
	</thead>
	<tbody>
	<?php foreach($galleries as $gallery){?>
	<tr>
			<td><?php esc($gallery->getTitle());?></td>
			<td class="text-right"><?php esc(count($gallery->getImages()));?></td>
			<td><input type="text"
				value="[gallery=<?php echo $gallery->getID();?>]" readonly
				onclick="this.select();"></td>
			<td style="text-align: center;"><a
				href="<?php echo ModuleHelper::buildActionURL("gallery_edit", "id=".$gallery->getID());?>"><img
					src="gfx/edit.png" class="mobile-big-image"
					alt="<?php translate("edit");?>" title="<?php translate("edit");?>"></a></td>
			<td style="text-align: center;">
    				<?php
    echo ModuleHelper::deleteButton(ModuleHelper::buildMethodCallUrl("Gallery2019Controller", "delete"), array(
        "del" => $gallery->getID()
    ));
    ?>
    			</td>
		</tr>
	<?php }?>
	</tbody>
</table>