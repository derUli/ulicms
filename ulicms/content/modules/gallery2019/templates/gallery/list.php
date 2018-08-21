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
			<?php if($acl->hasPermission("galleries_edit")){?>
			<td></td>
			<td></td>
			<?php }?>
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
				
			<?php if($acl->hasPermission("galleries_edit")){?>
			<td class="text-center"><a
				href="<?php echo ModuleHelper::buildActionURL("gallery_edit", "id=".$gallery->getID());?>"><img
					src="gfx/edit.png" class="mobile-big-image"
					alt="<?php translate("edit");?>" title="<?php translate("edit");?>"></a></td>
			<td class="text-center">
    				<?php
        echo ModuleHelper::deleteButton(ModuleHelper::buildMethodCallUrl("GalleryController", "delete"), array(
            "id" => $gallery->getID()
        ));
        ?>
    			</td>
    			<?php }?>
		</tr>
	<?php }?>
	</tbody>
</table>
<?php 
$translation = new JSTranslation();
$translation->addKey("ask_for_delete");
$translation->render();
?>