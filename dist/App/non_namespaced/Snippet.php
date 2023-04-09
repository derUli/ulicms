<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('no direct script access allowed');

// A Snippet is a content type which can not be opened directly
// but can be included into other pages by shortcodes
class Snippet extends Page
{
    public $type = 'snippet';
    public $menu = 'not_in_menu';
    public $hidden = true;

    protected function fillVars($result = null)
    {
        parent::fillVars($result);
        $this->type = 'snippet';
        $this->menu = 'not_in_menu';
        $this->hidden = true;
    }

     /**
      * Get css classes for Font Awesome icon
      * @return string
      */
    public function getIcon(): string
    {
        return 'fas fa-sticky-note';
    }
}
