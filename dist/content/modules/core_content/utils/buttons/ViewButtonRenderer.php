<?php

declare(strict_types=1);

namespace App\CoreContent\Partials;

use ContentFactory;
use Template;
use ViewBag;
use User;
use App\Security\ContentPermissionChecker;

use function App\HTML\icon;
use function App\HTML\Link;

class ViewButtonRenderer
{
    public const MODULE_NAME = "core_content";

    public function render(int $pageId, User $user)
    {
        $permitted = true;

        $content = ContentFactory::getByID($pageId);
        if (!$content->isRegular()) {
            return "";
        }

        // check permissions for this specific content
        $pagePermissionChecker = new ContentPermissionChecker($user->getId());

        if (!$pagePermissionChecker->canRead($pageId)) {
            $permitted = false;
        }

        $icon = icon("fa fa-eye fa-2x");

        $url = "../?goid={$pageId}";
        $link = link($url, $icon, true);
        ViewBag::set("button", $link);

        $templateFile = Template::executeModuleTemplate(
            self::MODULE_NAME,
            "pages/partials/view_button.php"
        );
        return $permitted ? $templateFile : "";
    }
}
