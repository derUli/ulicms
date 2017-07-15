<?php if(ViewBag::get("done")){?>
<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	<?php
	$translation = "x_datasets_moved";
	if (ViewBag::get ( "affected_rows" ) == 1) {
		$translation = "x_dataset_moved";
	}
	?>
		<?php translate($translation, array("%count%" => ViewBag::get("affected_rows"))); ?>
		</div>
<?php }?>
<form method="post"
	action="<?php Template::escape(ModuleHelper::buildAdminURL("move_menu_items"));?>">
<?php 	$menus = getAllMenus ();?>

<p>			<?php translate("move_all_menu_items_from");?>
				<select name="move_from" size="1">
			<option value="" selected>-</option>
					<?php
					foreach ( $menus as $menu ) {
						?>
					<option value="<?php echo $menu?>">
					<?php echo $menu?>
					</option>
					<?php
					}
					?>
				</select>
				<?php translate("move_all_menu_items_to");?>
				<select name="move_to" size="1">
			<option value="" selected>-</option>
					<?php
					
					foreach ( $menus as $menu ) {
						?>
					<option value="<?php echo $menu?>">
					<?php echo $menu?>
					</option>
					<?php
					}
					?>
				</select>

	</p>
	<p>
		<input type="submit" value="<?php translate("move");?>">
	</p>
	<?php csrf_token_html();?>
</form>