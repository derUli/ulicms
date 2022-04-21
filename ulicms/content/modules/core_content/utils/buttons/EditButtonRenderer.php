<?php

declare(strict_types=1);

namespace UliCMS\CoreContent\Partials;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use Template;
use UliCMS\Models\Users\User;
use ViewBag;
use ModuleHelper;
use UliCMS\Security\Permissions\ContentPermissionChecker;
use function UliCMS\HTML\icon;
use function UliCMS\HTML\link;

class EditButtonRenderer {

    const MODULE_NAME = "core_content";

    public function render(int $pageId, User $user): string {
        $permitted = true;

        // check edit permissions
        $pagePermissionChecker = new ContentPermissionChecker($user->getId());

        if (!$pagePermissionChecker->canWrite($pageId)) {
            $permitted = false;
        }

        $icon = icon("fas fa-pencil-alt fa-2x");

        $url = ModuleHelper::buildActionURL("pages_edit", "page={$pageId}");
        $link = link($url, $icon, true);
        ViewBag::set("button", $link);

        return $permitted ? Template::executeModuleTemplate(
                        self::MODULE_NAME,
                        "pages/partials/edit_button.php"
                ) : "";
    }

}
