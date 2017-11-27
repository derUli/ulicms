<?php
$manager = new ModStarterProjectManager ();
$projects = $manager->getAllProjects ();
?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("modstarter_new");?>"
		class="btn btn-default"><?php translate("new");?></a>
</p>
<table class="tablesorter">
	<thead>
		<tr>
			<th><?php translate("name");?></th>
			<td></td>
		</tr>
	</thead>
	<tbody>
<?php foreach($projects as $project){?>
<tr>
			<td><?php esc($project);?></td>
			<td class="text-center"><a
				href="<?php echo ModuleHelper::buildMethodCallUrl("ModStarter", "edit", "name=".$project."&action=modstarter_edit");?>"><img
					src="gfx/edit.png" alt="<?php translate("edit");?>"
					title="<?php translate("edit");?>"></a></td>
		</tr>
<?php }?>
</tbody>
</table>