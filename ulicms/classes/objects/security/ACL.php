<?php

declare(strict_types=1);

use UliCMS\Security\PermissionChecker;
use UliCMS\Constants\ModuleEventConstants;

// old permission check class
// please use PermissionChecker instead
class ACL {

    public function hasPermission(string $name): bool {
        $checker = new PermissionChecker(get_user_id());
        return $checker->hasPermission($name);
    }

    public function setPermission(
            string $name,
            bool $value,
            int $group_id
    ): void {
        $result = $this->getPermissionQueryResult();

        if (!$result) {
            return;
        }

        // JSON holen
        $json = $result["permissions"];
        if (is_null($json) or strlen($json) < 2) {
            return;
        }

        $permissionData = json_decode($json, true);

        $permissionData[$name] = $value;

        $newJSON = json_encode(
                $permissionData,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );

        $updateSQLString = "UPDATE `" . tbname("groups") .
                "` SET `permissions`='" . db_escape($newJSON)
                . "' WHERE id=" . $group_id;

        Database::query($updateSQLString);
    }

    public function createGroup(string $name, ?array $permissions = null): int {
        $permissionData = is_null($permissions) ? $this->getDefaultACL() : json_encode($permissions);

        $sql = "INSERT INTO `" . tbname("groups") .
                "` (`name`, `permissions`) " .
                "VALUES('" . db_escape($name) . "','" .
                db_escape($permissionData) . "')";

        // Führe Query aus
        db_query($sql);

        // Gebe die letzte Insert-ID zurück, damit man gleich mit der erzeugten Gruppe arbeiten kann.
        return db_last_insert_id();
    }

    public function updateGroup(
            int $id,
            string $name,
            ?array $permissions = null
    ): int {
        $permissionData = is_null($permissions) ? $this->getDefaultACL() : json_encode($permissions);

        $sql = "UPDATE `" . tbname("groups") . "` SET name='" .
                db_escape($name) . "', permissions='" . db_escape($permissionData) . "' WHERE id=" . $id;

        // Führe Query aus
        db_query($sql);
        // Gebe die letzte Insert-ID zurück, damit man gleich mit der erzeugten Gruppe arbeiten kann.
        return $id;
    }

    public function deleteGroup(int $id, ?int $move_users_to = null) {
        $id = intval($id);

        if (is_null($move_users_to)) {
            $updateUsers = "UPDATE " . tbname("users") .
                    " SET `group_id`=NULL where `group_id`=$id";
        } else {
            $updateUsers = "UPDATE " . tbname("users") .
                    " SET `group_id`=" . $move_users_to . " where `group_id`=$id";
        }

        db_query($updateUsers);

        $deleteGroupSQL = "DELETE FROM `" . tbname("groups") .
                "` WHERE id=" . $id;
        db_query($deleteGroupSQL);
    }

    public function getPermissionQueryResult(?int $id = null): ?array {
        if ($id) {
            $group_id = $id;
        } else {
            $group_id = $_SESSION["group_id"];
        }
        if (!$group_id) {
            return null;
        }

        $sqlString = "SELECT * FROM `" . tbname("groups") .
                "` WHERE id=" . $group_id;
        $result = db_query($sqlString);

        if (db_num_rows($result) == 0) {
            return null;
        }

        $dataset = db_fetch_assoc($result);

        return $dataset;
    }

    public function getAllGroups(string $order = 'id DESC'): array {
        $list = [];
        $sql = "SELECT * FROM `" . tbname("groups") . "` ORDER by " . $order;
        $result = db_query($sql);
        while ($assoc = db_fetch_assoc($result)) {
            $list[$assoc["id"]] = $assoc["name"];
        }
        return $list;
    }

    // initializes a json object with default permissions
    public function getDefaultACLAsJSON(
            bool $admin = false,
            bool $plain = false
    ) {
        $acl_data = [];

        // Willkommen
        $acl_data["dashboard"] = null;

        // Inhalte
        $acl_data["pages"] = null;
        $acl_data["banners"] = null;
        $acl_data["banners_create"] = null;
        $acl_data["banners_edit"] = null;
        $acl_data["categories"] = null;
        $acl_data["categories_create"] = null;
        $acl_data["categories_edit"] = null;
        $acl_data["forms"] = null;
        $acl_data["forms_create"] = null;
        $acl_data["forms_edit"] = null;

        // Medien
        $acl_data["images"] = null;
        $acl_data["files"] = null;
        $acl_data["videos"] = null;
        $acl_data["videos_create"] = null;
        $acl_data["videos_edit"] = null;
        $acl_data["audio"] = null;
        $acl_data["audio_create"] = null;
        $acl_data["audio_edit"] = null;

        // Benutzer
        $acl_data["users"] = null;
        $acl_data["users_create"] = null;
        $acl_data["users_edit"] = null;
        $acl_data["groups"] = null;
        $acl_data["groups_create"] = null;
        $acl_data["groups_edit"] = null;

        // Package Manager
        $acl_data["list_packages"] = null;
        $acl_data["install_packages"] = null;
        $acl_data["upload_patches"] = null;
        $acl_data["remove_packages"] = null;
        $acl_data["module_settings"] = null;

        // Updates durchführen
        $acl_data["update_system"] = null;
        $acl_data["patch_management"] = null;
        // Einstellungen
        $acl_data["settings_simple"] = null;
        $acl_data["design"] = null;
        $acl_data["spam_filter"] = null;
        $acl_data["cache"] = null;
        $acl_data["motd"] = null;
        $acl_data["languages"] = null;
        $acl_data["logo"] = null;
        $acl_data["favicon"] = null;
        $acl_data["other"] = null;
        $acl_data["expert_settings"] = null;
        $acl_data["expert_settings_edit"] = null;
        $acl_data["open_graph"] = null;
        $acl_data["info"] = null;

        // Workflows
        $acl_data["pages_activate_own"] = null;
        $acl_data["pages_activate_others"] = null;
        $acl_data["pages_edit_own"] = null;
        $acl_data["pages_edit_others"] = null;
        $acl_data["pages_change_owner"] = null;
        $acl_data["pages_create"] = null;
        $acl_data["pages_show_positions"] = null;
        $acl_data["pages_edit_permissions"] = null;

        $acl_data["default_access_restrictions_edit"] = null;

        // Hook für das Erstellen eigener ACL Objekte
        // Temporäres globales Array zum hinzufügen eigener Objekte
        global $acl_array;
        $acl_array = $acl_data;
        do_event("custom_acl", ModuleEventConstants::RUNS_MULTIPLE);
        $acl_data = $acl_array;
        unset($acl_array);

        // read custom permissions from modules
        $modules = getAllModules();
        foreach ($modules as $module) {
            $acl_metadata = getModuleMeta($module, "custom_acl");
            if ($acl_metadata and is_array($acl_metadata)) {
                foreach ($acl_metadata as $permission) {
                    $acl_data[$permission] = null;
                }
            }
        }

        // Admin has all rights
        $default_value = $admin;

        foreach ($acl_data as $key => $value) {
            $acl_data[$key] = $default_value;
        }

        ksort($acl_data);
        if ($plain) {
            return $acl_data;
        }

        $json = json_encode($acl_data);
        return $json;
    }

    public function getDefaultACL(bool $admin = false, bool $plain = false) {
        return $this->getDefaultACLAsJSON($admin, $plain);
    }

}
