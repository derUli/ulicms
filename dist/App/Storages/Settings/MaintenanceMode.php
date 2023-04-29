<?php

declare(strict_types=1);

namespace App\Storages\Settings;

use Settings;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * This class is for enabling/disabling the maintenance mode
 */
class MaintenanceMode implements ToggleInterface {
    // Maintenance Mode is a setting
    public const SETTING_NAME = 'maintenance_mode';

    // Maintenance mode enabled on this value
    public const VALUE_ON = '1';

    // Maintenance mode disabled on this value
    public const VALUE_OFF = '0';

    private static self $instance;

    /**
     * Get instance
     *
     * @return self
     */
    public static function getInstance(): self {
        self::$instance = self::$instance ?? new self();

        return self::$instance;
    }

    /**
     * Check if maintenance mode is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool {
        return (bool)Settings::get(self::SETTING_NAME);
    }

    /**
     * Enable maintenance mode
     *
     * @return void
     */
    public function enable(): void {
        Settings::set(self::SETTING_NAME, self::VALUE_ON);
    }

    /**
     * Disable maintenance mode
     *
     * @return void
     */
    public function disable(): void {
        Settings::set(self::SETTING_NAME, self::VALUE_OFF);
    }

    /**
     * Toggle maintenance mode
     *
     * @return bool
     */
    public function toggle(): bool {
        if($this->isEnabled()) {
            $this->disable();
        } else {
            $this->enable();
        }

        return $this->isEnabled();
    }
}
