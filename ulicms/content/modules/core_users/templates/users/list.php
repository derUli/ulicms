<?php

use function UliCMS\HTML\imageTag;

$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("users")) {
	if (!isset($_SESSION["admins_filter_group"])) {
		$_SESSION["admins_filter_group"] = 0;
	}
	if (!is_null(Request::getVar("admins_filter_group"))) {
		$_SESSION["admins_filter_group"] = Request::getVar("admins_filter_group", 0, "int");
	}
	$manager = new UserManager();
	if ($_SESSION["admins_filter_group"] > 0) {
		$users = $manager->getUsersByGroupId($_SESSION["admins_filter_group"]);
	} else {
		$users = $manager->getAllUsers();
	}
	$groups = Group::getAll();
	?>
	<?php echo Template::executeModuleTemplate("core_users", "icons.php"); ?>
	<h2><?php translate("users"); ?></h2>
	<?php if ($permissionChecker->hasPermission("users_create")) { ?>
		<p>
			<?php translate("users_infotext"); ?>
			<br /> <br /> <a href="index.php?action=admin_new&ref=admins"
							 class="btn btn-default"><i class="fa fa-plus"></i> <?php translate("create_user"); ?></a><br />
		</p>
	<?php } ?>
	<form action="index.php" method="get" class="voffset3-5">    <strong><?php translate("primary_group"); ?></strong>
		<br />
		<input type="hidden" name="action" value="admins"> <select
			name="admins_filter_group" size="1"
			onchange="$(this).closest('form').submit();">
			<option value="0"
					<?php if ($_SESSION ["admins_filter_group"] <= 0) echo "selected"; ?>>[<?php translate("every"); ?>]</option>
					<?php foreach ($groups as $group) { ?>
				<option
				<?php if ($group->getId() == $_SESSION ["admins_filter_group"]) echo "selected "; ?>
					value="<?php Template::escape($group->getId()); ?>"><?php Template::escape($group->getName()); ?></option>
				<?php } ?>
		</select>
	</form>
	<br />
	<p><?php BackendHelper::formatDatasetCount(count($users)); ?></p>
	<?php if (count($users) > 0) { ?>
		<div class="scroll">
			<table class="tablesorter">
				<thead>
					<tr style="font-weight: bold;">
						<th class="no-sort"></th>
						<th><?php translate("username"); ?></th>
						<th class="hide-on-mobile"><?php translate("lastname"); ?></th>
						<th class="hide-on-mobile"><?php translate("firstname"); ?></th>
						<th class="hide-on-mobile"><?php translate("email"); ?></th>
						<th class="hide-on-mobile"><?php translate("primary_group"); ?></th>
						<?php if ($permissionChecker->hasPermission("users_edit")) { ?>
							<td class="no-sort text-center">
								<?php translate("edit"); ?>
							</td>
							<td class="no-sort text-center">
								<?php translate("delete"); ?>
							</td>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($users as $user) {
						$group = "[" . get_translation("none") . "]";
						if ($user->getGroupId()) {
							$group = $permissionChecker->getPermissionQueryResult($user->getGroupId());
							$group = $group["name"];
						}
						?>
						<?php
						$avatar = imageTag(
								$user->getAvatar(),
								[
									"class" => "avatar"
								]
						);
						echo '<tr id="dataset-' . $user->getId() . '">';
						echo "<td>" . $avatar . "</td>";
						echo "<td>";

						esc($user->getUsername()) . "</td>";
						echo "<td class=\"hide-on-mobile\">" .
						_esc($user->getLastName()) .
						"</td>";

						echo "<td class=\"hide-on-mobile\">" .
						_esc($user->getFirstname()) .
						"</td>";

						echo "<td class=\"hide-on-mobile\">" .
						_esc($user->getEmail()) . "</td>";
						echo "<td class=\"hide-on-mobile\">";
						$id = $user->getGroupId();
						if ($id and $permissionChecker->hasPermission("groups_edit")) {
							$url = ModuleHelper::buildActionURL("groups", "edit=$id");
							echo '<a href="' . Template::getEscape($url) . '">';
						}
						esc($group);

						if ($id and $permissionChecker->hasPermission("groups_edit")) {
							echo "</a>";
						}
						echo "</td>";
						if ($permissionChecker->hasPermission("users_edit")) {
							echo "<td class=\"text-center\">" . '<a href="index.php?action=admin_edit&id=' . $user->getId() . '"><img class="mobile-big-image" src="gfx/edit.png" alt="' . get_translation("edit") . '" title="' . get_translation("edit") . '"></a></td>';

							if ($user->getId() == $_SESSION["login_id"]) {
								echo "<td class=\"text-center\"></td>";
							} else {
								echo "<td class=\"text-center\">" . '<form action="index.php?sClass=UserController&sMethod=delete&id=' . $user->getId() . '" method="post" class="delete-form">' . get_csrf_token_html() . '<input type="image" class="mobile-big-image" src="gfx/delete.gif"></form></td>';
							}
						}
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
		<?php } ?>
	</div>
	<?php
} else {
	noPerms();
}

$translation = new JSTranslation(array(
	"ask_for_delete"
		));
$translation->render();
