<?php

declare(strict_types=1);

namespace App\Security\Permissions;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * This class provides a ACL permission list
 */
abstract class ACL {
    /**
     * Returns default ACL permissions for a new user
     *
     * @param bool $admin default value
     *
     * @return array<int|string, bool|null>
     */
    public static function getDefaultACL(
        bool $admin = false,
    ) {
        $acl_data = [];

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

        $acl_data = apply_filter($acl_data, 'acl_list');

        ksort($acl_data);

        return $acl_data;
    }
}
