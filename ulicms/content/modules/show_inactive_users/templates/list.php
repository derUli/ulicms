<?php
$days = ViewBag::get("days");
$users = ViewBag::get("users");
?>
<form action="index.php" method="get">
	<input type="hidden" name="action" value="module_settings"> <input
		type="hidden" name="module" value="show_inactive_users">
	<p>
		<label for="days"><?php translate("not_logged_in_since_days");?></label><br />
		<input id="days" name="days" type="number" step="1"
			value="<?php esc($days)?>">
	</p>
	<p>
		<button type="submit" class="btn btn-primary"><?php translate("do_search");?></button>
	</p>
</form>
<?php echo ModuleHelper::buildMethodCallForm("ShowInactiveUsersController", "delete");?>
<div class="scroll">
	<table class="tablesorter">
		<thead>
			<tr style="font-weight: bold;">
				<td><input type="checkbox" class="select-all" data-target=".user"></td>
				<th><span><?php translate("username");?></span></th>
				<th><span><?php translate("last_login");?></span></th>

				<th class="hide-on-mobile"><?php translate("lastname");?></th>
				<th class="hide-on-mobile"><?php translate("firstname");?></th>
				<th class="hide-on-mobile"><?php translate("email");?></th>
			</tr>
		</thead>
		<tbody>
	<?php
foreach ($users as $user) {
    $date = $user->getLastLogin() ? date("Y-m-d H:i:s", $user->getLastLogin()) : get_translation("never");
    echo '
		<tr id="dataset-' . $user->getId() . '">';
    echo "<td><input type=\"checkbox\" name=\"users[]\" value=\"{$user->getId()}\" class=\"checkbox user\" data-select-all-checkbox=\".select-all\" data-checkbox-group=\".user\"></td>";
    echo "<td>";
    echo '<img src="' . get_gravatar($user->getEmail(), 26) . '"
				alt="Avatar von ' . real_htmlspecialchars($user->getUsername()) . '"
				style="width: 26px;"> ';
    echo real_htmlspecialchars($user->getUsername()) . "
			</td>";
    echo "
			<td>{$date}</td>";
    echo "
			<td class=\"hide-on-mobile\">" . real_htmlspecialchars($user->getLastName()) . "</td>";
    echo "
			<td class=\"hide-on-mobile\">" . real_htmlspecialchars($user->getFirstname()) . "</td>";
    echo "
			<td class=\"hide-on-mobile\">" . real_htmlspecialchars($user->getEmail()) . "</td>";
    echo "</tr>";
}
?>

</tbody>
	</table>
</div>
<div class="checkbox">
	<label> <input type="checkbox" name="confirm-delete" value="1" required><?php translate("ask_for_delete")?></label>
</div>
<p>
	<button type="submit" class="btn btn-danger"><?php translate("delete_selected_users");?></button>
</p>







<?php echo ModuleHelper::endForm();?>
