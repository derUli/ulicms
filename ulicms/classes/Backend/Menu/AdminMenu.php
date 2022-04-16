<?php

declare(strict_types=1);

namespace UliCMS\Backend\Menu;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

/**
 * Administration area menu
 */
class AdminMenu {

    private $children = [];

    /**
     * Constructor
     * @param array $children Menu Items
     */
    public function __construct(array $children = []) {
        $this->children = $children;
    }

    /**
     * Get all menu items
     * @return array Menu entries
     */
    public function getChildren(): array {
        return $this->children;
    }

    /**
     * Set menu entries
     * @param array $value Menu entries
     * @return void
     */
    public function setChildren(array $value): void {
        $this->children = $value;
    }

    /**
     * Checks if the menu has entries
     * @return bool
     */
    public function hasChildren(): bool {
        return (count($this->children) > 0);
    }

    /**
     * Render the admin menu as <ul>
     * @return string HTML Menu
     */
    public function render(): string {
        $html = "<ul>";
        foreach ($this->children as $child) {
            // only render items for that the current user has permissions
            if ($child->userHasPermission()) {
                $html .= $child->render();
            }
        }
        $html .= "</ul>";
        return $html;
    }

}
