<?php

declare(strict_types=1);

// this class renders the backend main navigation menu
class AdminMenu
{
    private $children = [];

    public function __construct(array $children = [])
    {
        $this->children = $children;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $value): void
    {
        $this->children = $value;
    }

    public function hasChildren(): bool
    {
        return (count($this->children) > 0);
    }

    // render the menu as list which is formatted by SCSS
    public function render(): string
    {
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
