<?php

declare(strict_types=1);

namespace App\Backend\Menu;

use ACL;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

/**
 * Backend menu entry
 */
class MenuEntry
{
    private $title;
    private $link;
    private $identifier;
    private $permissions;
    private $children = [];
    private $newWindow = false;
    private $isAjax = false;

    /**
     * Constructor
     * @param string $title
     * @param string $link
     * @param string $identifier
     * @param type $permissions
     * @param array $children
     * @param bool $newWindow
     * @param bool $isAjax
     */
    public function __construct(
        string $title,
        string $link,
        string $identifier,
        $permissions = null,
        array $children = [],
        bool $newWindow = false,
        bool $isAjax = false
    ) {
        $this->title = $title;
        $this->link = $link;
        $this->identifier = $identifier;
        $this->permissions = $permissions;
        $this->children = $children;
        $this->newWindow = $newWindow;
        $this->isAjax = $isAjax;
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get link
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * Get identifier
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Set title
     * @param string $value
     * @return void
     */
    public function setTitle(string $value): void
    {
        $this->title = $value;
    }

    /**
     * Set link
     * @param string $value
     * @return void
     */
    public function setLink(string $value): void
    {
        $this->link = $value;
    }

    /**
     * Set identifier
     * @param string $value
     * @return void
     */
    public function setIdentifier(string $value): void
    {
        $this->identifier = $value;
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
     * Has children
     * @return bool
     */
    public function hasChildren(): bool
    {
        return (count($this->children) > 0);
    }

    /**
     * Add children
     * @param array $children
     * @return void
     */
    public function addChildren(array $children): void
    {
        $this->children[] = $children;
    }

    /**
     * Get permissions
     * @return type
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set permissions
     * @param type $permissions
     * @return void
     */
    public function setPermissions($permissions): void
    {
        $this->permissions = $permissions;
    }

    /**
     * get new window
     * @return bool
     */
    public function getNewWindow(): bool
    {
        return $this->newWindow;
    }

    /**
     * Set new window
     * @param bool $val
     * @return void
     */
    public function setNewWindow(bool $val): void
    {
        $this->newWindow = $val;
    }

    /**
     * Get isAjax
     * @return bool
     */
    public function getIsAjax(): bool
    {
        return $this->isAjax;
    }

    /**
     * Set isAjax
     * @param bool $val
     * @return void
     */
    public function setIsAjax(bool $val): void
    {
        $this->isAjax = $val;
    }

    /**
     * Check if the current user has permissions to access this menu entry
     * @return bool
     */
    public function userHasPermission(): bool
    {
        $acl = new ACL();
        if (is_string($this->permissions) && !empty($this->permissions)) {
            return $acl->hasPermission($this->permissions);
        }

        if (is_array($this->permissions) && count($this->permissions) > 0) {
            $isPermitted = false;
            foreach ($this->permissions as $permission) {
                if (is_string($permission) && !empty($permission)
                        && $acl->hasPermission($permission)) {
                    $isPermitted = true;
                }
            }
            return $isPermitted;
        }

        // if there are no permissions required for accessing this menu entry
        return true;
    }

    /**
     * Render this menu entry as HTML
     * @return string
     */
    public function render(): string
    {
        $html = '<li>';
        $targetString = $this->getNewWindow() ? '_blank' : '_self';
        $cssClasses = "backend-menu-item-{$this->getIdentifier()}";
        if (get_action() == $this->getIdentifier()) {
            $cssClasses .= ' active';
        }
        if ($this->getIdentifier() !== 'logout') {
            $cssClasses .= $this->getIsAjax() ? ' is-ajax' : ' is-not-ajax';
        }
        // var_dump($cssClasses);
        $html .= "<a href=\"{$this->getLink()}\" "
                . "target=\"{$targetString}\" class=\"{$cssClasses}\">";
        $html .= $this->getTitle();
        $html .= '</a>';
        $html .= '</li>';
        return $html;
    }
}
