<?php
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
                        <th style="width: 40px;">ID</th>
                        <th><?php translate("username"); ?></th>
                        <th class="hide-on-mobile"><?php translate("lastname"); ?></th>
                        <th class="hide-on-mobile"><?php translate("firstname"); ?></th>
                        <th class="hide-on-mobile"><?php translate("email"); ?></th>
                        <th class="hide-on-mobile"><?php translate("primary_group"); ?></th>
                            <?php if ($permissionChecker->hasPermission("users_edit")) { ?>
                            <td><?php translate("edit"); ?></td>
                            <td><span><?php translate("delete"); ?> </span></td>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($users as $row) {
                        $group = "[" . get_translation("none") . "]";
                        if ($row->getGroupId()) {
                            $group = $permissionChecker->getPermissionQueryResult($row->getGroupId());
                            $group = $group["name"];
                        }
                        ?>
                        <?php
                        echo '<tr id="dataset-' . $row->getId() . '">';
                        echo "<td style=\"width:40px;\">" . $row->getId() . "</td>";
                        echo "<td>";
                        echo '<img src="' . get_gravatar($row->getEmail(), 26) . '" alt="Avatar von ' . real_htmlspecialchars($row->getUsername()) . '" style="width:26px;"> ';
                        esc($row->getUsername()) . "</td>";
                        echo "<td class=\"hide-on-mobile\">" . real_htmlspecialchars($row->getLastName()) . "</td>";
                        echo "<td class=\"hide-on-mobile\">" . real_htmlspecialchars($row->getFirstname()) . "</td>";
                        echo "<td class=\"hide-on-mobile\">" . real_htmlspecialchars($row->getEmail()) . "</td>";
                        echo "<td class=\"hide-on-mobile\">";
                        $id = $row->getGroupId();
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
                            echo "<td style='text-align:center;'>" . '<a href="index.php?action=admin_edit&id=' . $row->getId() . '"><img class="mobile-big-image" src="gfx/edit.png" alt="' . get_translation("edit") . '" title="' . get_translation("edit") . '"></a></td>';

                            if ($row->getId() == $_SESSION["login_id"]) {
                                echo "<td style='text-align:center;'></td>";
                            } else {
                                echo "<td style='text-align:center;'>" . '<form action="index.php?sClass=UserController&sMethod=delete&id=' . $row->getId() . '" method="post" class="delete-form">' . get_csrf_token_html() . '<input type="image" class="mobile-big-image" src="gfx/delete.gif"></form></td>';
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
