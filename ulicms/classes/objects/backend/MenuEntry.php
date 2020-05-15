<?php

declare(strict_types=1);

// this method implements the rendering of a single backend main menu item
class MenuEntry {

    private $title;
    private $link;
    private $identifier;
    private $permissions;
    private $children = [];
    private $newWindow = false;

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
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getLink(): string {
        return $this->link;
    }

    public function getIdentifier(): string {
        return $this->identifier;
    }

    public function setTitle(string $value): void {
        $this->title = $value;
    }

    public function setLink(string $value): void {
        $this->link = $value;
    }

    public function setIdentifier(string $value): void {
        $this->identifier = $value;
    }

    public function getChildren(): array {
        return $this->children;
    }

    public function setChildren(array $value): void {
        $this->children = $value;
    }

    public function hasChildren(): bool {
        return (count($this->children) > 0);
    }

    public function addChildren(array $children): void {
        $this->children[] = $children;
    }

    public function getPermissions() {
        return $this->permissions;
    }

    public function setPermissions($permissions): void {
        $this->permissions = $permissions;
    }

    public function getNewWindow(): bool {
        return $this->newWindow;
    }

    public function setNewWindow(bool $val): void {
        $this->newWindow = $val;
    }

    // check if the user has permissions to access this menu entry
    public function userHasPermission(): bool {
        $acl = new ACL();
        if (is_string($this->permissions) and ! empty($this->permissions)) {
            return $acl->hasPermission($this->permissions);
        }
        if (is_array($this->permissions) and count($this->permissions) > 0) {
            $isPermitted = false;
            foreach ($this->permissions as $permission) {
                if (is_string($permission)
                        and ! empty($permission)
                        and $acl->hasPermission($permission)) {
                    $isPermitted = true;
                }
            }
            return $isPermitted;
        }
        // if there are no permissions required for accessing this menu entry
        return true;
    }

    // render a single menu item
    public function render(): string {
        $html = "<li>";
        $targetString = $this->getNewWindow() ? "_blank" : "_self";
        $cssClasses = "backend-menu-item-{$this->getIdentifier()}";
        if (get_action() == $this->getIdentifier()) {

            $cssClasses .= " active";
        }
        $html .= "<a href=\"{$this->getLink()}\" "
                . "target=\"{$targetString}\" class=\"{$cssClasses}\">";

        $html .= $this->getTitle();
        $html .= "</a>";
        $html .= "</li>";
        return $html;
    }

}
