<?php

declare(strict_types=1);

namespace UliCMS\CoreContent\Partials;

use Template;
use ViewBag;
use User;
use UliCMS\Security\ContentPermissionChecker;
use function UliCMS\HTML\icon;
use function UliCMS\HTML\link;

class DeleteButtonRenderer {

    const MODULE_NAME = "core_content";

    public function render($pageId, User $user) {

        // FIXME: check edit restrictions
        $permitted = true;

        // check edit permissions
        $pagePermissionChecker = new ContentPermissionChecker($user->getId());

        if (!$pagePermissionChecker->canDelete($pageId)) {
            $permitted = false;
        }
        // FIXME: check permissions
        $permitted = true;

        $icon = icon("fa fa-trash fa-2x");

        $url = "#";
        $link = link($url, $icon, true);
        ViewBag::set("button", $link);

        return $permitted ? Template::executeModuleTemplate(self::MODULE_NAME, "pages/partials/delete_button.php") : "";
    }

}
