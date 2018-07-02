<?php
$acl = new ACL ();
if ($acl->hasPermission ( "expert_settings" )) {
	$data = Settings::getAll ();
	if ($acl->hasPermission ( "expert_settings_edit" )) {
		?>
		
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("settings_simple");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate("settings")?></h1>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("settings_edit");?>"
		class="btn btn-default"><?php translate("create_option");?></a>
</p>
<?php }?>
<p><?php BackendHelper::formatDatasetCount(count($data));?></p>
<?php
	
	if (count ( $data ) > 0) {
		?>
<div class="scroll">
	<table class="tablesorter">
		<thead>
			<tr style="font-weight: bold;">
				<th><?php translate("option");?></th>
				<th><?php translate("value");?></th>
			<?php if($acl->hasPermission("expert_settings_edit")){?>
			<td><?php translate("edit");?></td>
				<td><?php translate("delete");?></td>
			<?php }?>
		</tr>
		</thead>
		<tbody>
	<?php
		foreach ( $data as $row ) {
			?>
			<tr>
				<td><?php Template::escape($row->name);?></td>
				<td><?php Template::escape($row->value);?></td>
				<?php if($acl->hasPermission("expert_settings_edit")){?>
				<td class="text-center"><a
					href="<?php echo ModuleHelper::buildActionURL("settings_edit", "name=".Template::getEscape($row->name));?>"><img
						src="gfx/edit.png" alt="<?php translate("edit");?>"
						title="<?php translate("edit");?>"></a></td>
				<td>
				<?php
				
				echo ModuleHelper::deleteButton ( ModuleHelper::buildMethodCallUrl ( "ExpertSettingsController", "delete" ), array (
						"name" => $row->name 
				) );
				?>
				</td><?php }?>
			</tr>
		<?php }?>
		 </tbody>
	</table>
</div>
<?php }?>
<?php
	$translation = new JSTranslation ();
	$translation->addKey ( "ask_for_delete" );
	$translation->renderJS ();
	?>
<?php
} else {
	noPerms ();
}
