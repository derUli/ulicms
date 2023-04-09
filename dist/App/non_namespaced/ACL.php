<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use App\Constants\ModuleEventConstants;
use App\Security\PermissionChecker;

/**
 * Old permission checker class
 * @deprecated since version 2023.1
 */
class ACL
{
    /**
     * Checks if the current user has a permission
     * @param string $name
     * @return bool
     */
    public function hasPermission(string $name): bool
    {
        $checker = new PermissionChecker(get_user_id());
        return $checker->hasPermission($name);
    }

    /**
     * Creates a group
     * @param string $name
     * @param array|null $permissions
     * @return int
     */
    public function createGroup(string $name, ?array $permissions = null): int
    {
        $permissionData = $permissions === null ? $this->getDefaultACL() : json_encode($permissions);

        $sql = 'INSERT INTO `' . tbname('groups') .
                '` (`name`, `permissions`) ' .
                "VALUES('" . db_escape($name) . "','" .
                db_escape($permissionData) . "')";

        // Führe Query aus
        db_query($sql);

        // Gebe die letzte Insert-ID zurück, damit man gleich mit der erzeugten Gruppe arbeiten kann.
        return Database::getLastInsertID();
    }

    /**
     * Updates a group
     * @param int $id
     * @param string $name
     * @param array|null $permissions
     * @return int
     */
    public function updateGroup(
        int $id,
        string $name,
        ?array $permissions = null
    ): int {
        $permissionData = $permissions === null ? $this->getDefaultACL() : json_encode($permissions);

        $sql = 'UPDATE `' . tbname('groups') . "` SET name='" .
                db_escape($name) . "', permissions='" . db_escape($permissionData) . "' WHERE id=" . $id;

        // Führe Query aus
        db_query($sql);
        // Gebe die letzte Insert-ID zurück, damit man gleich mit der erzeugten Gruppe arbeiten kann.
        return $id;
    }

    /**
     * Deletes a group
     * @param int $id
     * @param int|null $move_users_to
     */
    public function deleteGroup(int $id, ?int $move_users_to = null)
    {
        $id = (int) $id;

        if ($move_users_to === null) {
            $updateUsers = 'UPDATE ' . tbname('users') .
                    " SET `group_id`=NULL where `group_id`={$id}";
        } else {
            $updateUsers = 'UPDATE ' . tbname('users') .
                    ' SET `group_id`=' . $move_users_to . " where `group_id`={$id}";
        }

        db_query($updateUsers);

        $deleteGroupSQL = 'DELETE FROM `' . tbname('groups') .
                '` WHERE id=' . $id;
        db_query($deleteGroupSQL);
    }

    /**
     * Fetches a group
     * @param int|null $id
     * @return array|null
     */
    public function getPermissionQueryResult(?int $id = null): ?array
    {
        $group_id = null;
        if ($id) {
            $group_id = $id;
        } elseif (isset($_SESSION['group_id'])) {
            $group_id = $_SESSION['group_id'];
        }
        if (! $group_id) {
            return null;
        }

        $sqlString = 'SELECT * FROM `' . tbname('groups') .
                '` WHERE id=' . $group_id;
        $result = db_query($sqlString);

        if (db_num_rows($result) == 0) {
            return null;
        }

        $dataset = db_fetch_assoc($result);

        return $dataset;
    }

    /**
     * Fetches all groups
     * @param string $order
     * @return array
     */
    public function getAllGroups(string $order = 'id DESC'): array
    {
        $list = [];
        $sql = 'SELECT id, name FROM `' . tbname('groups') . '` ORDER by ' . $order;
        $result = db_query($sql);
        while ($assoc = db_fetch_assoc($result)) {
            $list[$assoc['id']] = $assoc['name'];
        }
        return $list;
    }

    /**
     * Returns default ACL permissions for a new user
     * @global type $acl_array
     * @param bool $admin If "admin" every permission is true
     * @param bool $plain if true returns a JSON string
     * @return bool
     */
    public function getDefaultACLAsJSON(
        bool $admin = false,
        bool $plain = false
    ) {
        $acl_data = [];

        // Hook für das Erstellen eigener ACL Objekte
        // Temporäres globales Array zum hinzufügen eigener Objekte
        global $acl_array;
        $acl_array = $acl_data;
        do_event('custom_acl', ModuleEventConstants::RUNS_MULTIPLE);
        $acl_data = $acl_array;
        unset($acl_array);

        // read custom permissions from modules
        $modules = getAllModules();
        foreach ($modules as $module) {
            $acl_metadata = getModuleMeta($module, 'custom_acl');
            if ($acl_metadata && is_array($acl_metadata)) {
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

    /**
     * Alias for getDefaultACLAsJSON()
     * @param bool $admin
     * @param bool $plain
     * @return type
     */
    public function getDefaultACL(bool $admin = false, bool $plain = false)
    {
        return $this->getDefaultACLAsJSON($admin, $plain);
    }
}
