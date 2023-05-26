<?php

declare(strict_types=1);

namespace App\Security\Permissions;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\ModuleEvent;

/**
 * This class provides a ACL permission list
 */
abstract class ACL {
    /**
     * Returns default ACL permissions for a new user
     *
     * @global array<string, bool> $acl_array
     * @param bool $admin default value
     *
     * @return array<int|string, bool|null>
     */
    public static function getDefaultACL(
        bool $admin = false,
    ) {
        $acl_data = [];

        // Hook für das Erstellen eigener ACL Objekte
        // Temporäres globales Array zum hinzufügen eigener Objekte
        global $acl_array;
        $acl_array = $acl_data;
        do_event('custom_acl', ModuleEvent::RUNS_MULTIPLE);
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

        // Default value
        foreach ($acl_data as $key => $value) {
            $acl_data[$key] = $admin;
        }

        ksort($acl_data);

        return $acl_data;
    }
}
