<?php
// @FIXME: Diese beiden Includes nach core_history verschieben.
include_once ULICMS_ROOT . "/classes/3rdparty/finediff.php";
include_once ULICMS_ROOT . "/classes/objects/content/vcs.php";

$acl = new ACL ();
if ($acl->hasPermission ( "pages" )) {
	$content_id = intval ( $_GET ["content_id"] );
	$revisions = VCS::getRevisionsByContentID ( $content_id );
	?>
<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("pages_edit", "page=".$content_id);?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate("versions");?></h1>
<div class="scroll">
	<table class="tablesorter">
		<thead>
			<tr>
				<th><?php translate("id");?></th>
				<th><?php translate("content");?></th>
				<th><?php translate("user");?></th>
				<th><?php translate("date");?></th>
				<th><?php translate("restore");?></th>
			</tr>
		</thead>
		<tbody>
<?php
	foreach ( $revisions as $revision ) {
		$view_diff_link = "index.php?action=view_diff&content_id=" . $revision->content_id . "&history_id=" . $revision->id;
		?>
<tr>
				<td><?php echo intval($revision->id);?></td>
				<td><a href="<?php echo $view_diff_link;?>" class="btn btn-info"
					target="_blank"><?php translate("view_diff");?></a></td>
				<td><?php
		
		$user = getUserById ( $revision->user_id );
		if ($user and isset ( $user ["username"] )) {
			echo htmlspecialchars ( $user ["username"] );
		}
		?></td>
				<td><?php echo $revision->date;?></td>
				<td><a
					href="<?php echo ModuleHelper::buildMethodCallUrl("HistoryController", "doRestore", "version_id=".$revision->id);?>"
					class="btn btn-danger"
					onclick="return confirm('<?php translate("ask_for_restore");?>');"><?php translate("restore");?></a>
				</td>
			</tr>
<?php }?>
</tbody>
	</table>
</div>
<?php
} else {
	noperms ();
}

?>
