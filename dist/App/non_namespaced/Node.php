<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

// nodes are categories for dropdown menus which
// have no content attached
class Node extends Link {
    public $type = 'node';

    public $link_url = '#';

     /**
      * Get css classes for Font Awesome icon
      * @return string
      */
    public function getIcon(): string {
        return 'far fa-folder';
    }

    protected function fillVars($result = null): void {
        parent::fillVars($result);
        $this->link_url = '#';
    }
}
