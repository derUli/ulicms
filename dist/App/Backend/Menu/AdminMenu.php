<?php

declare(strict_types=1);

namespace App\Backend\Menu;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

/**
 * This class renders the admin menu
 */
class AdminMenu
{
    private $children = [];

    /**
     * Constructor
     * @param array $children
     */
    public function __construct(array $children = [])
    {
        $this->children = $children;
    }

    /**
     * Get children
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Set children
     * @param array $value
     * @return void
     */
    public function setChildren(array $value): void
    {
        $this->children = $value;
    }

    /**
     * Check if the menu has children
     * @return bool
     */
    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    /**
     * Render the menu to HTML
     * @return string
     */
    public function render(): string
    {
        $html = '<ul>';

        foreach ($this->children as $child) {
            // only render items for that the current user has permissions
            if ($child->userHasPermission()) {
                $html .= $child->render();
            }
        }
        $html .= '</ul>';

        return $html;
    }
}
