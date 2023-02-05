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

    public function render(int $pageId, User $user) {
        $permitted = true;

        // check edit permissions
        $pagePermissionChecker = new ContentPermissionChecker($user->getId());

        if (!$pagePermissionChecker->canDelete($pageId)) {
            $permitted = false;
        }

        $icon = icon("fas fa-trash-restore fa-2x");

        $url = "#";
        $message = get_secure_translation(
                "confirm_undelete_page",
                [
                    "%title%" => getPageTitleByID($pageId)
                ]
        );
        $actionUrl = ModuleHelper::buildMethodCallUrl(
                        PageController::class,
                        "undelete",
                        "id={$pageId}csrf_token=" . get_csrf_token()
        );
        $attributes = [
            "data-confirm" => $message,
            "data-url" => $actionUrl,
            "class" => "delete-icon"
        ];

        $link = link($url, $icon, true, null, $attributes);
        ViewBag::set("button", $link);

        return $permitted ? Template::executeModuleTemplate(
                        self::MODULE_NAME,
                        "pages/partials/delete_button.php"
                ) : "";
    }

}
