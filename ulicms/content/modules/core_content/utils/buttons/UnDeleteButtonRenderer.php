<?php

declare(strict_types=1);

namespace UliCMS\CoreContent\Partials;

use Template;
use ViewBag;
use User;
use ModuleHelper;
use PageController;
use UliCMS\Security\ContentPermissionChecker;
use function UliCMS\HTML\icon;
use function UliCMS\HTML\link;

class UnDeleteButtonRenderer {

    const MODULE_NAME = "core_content";

    public function render($pageId, User $user) {
        $permitted = true;

        // check edit permissions
        $pagePermissionChecker = new ContentPermissionChecker($user->getId());

        if (!$pagePermissionChecker->canDelete($pageId)) {
            $permitted = false;
        }

        return $permitted ? "Wiederherstellen" : null;
    }

}
