<?php

namespace UliCMS\CoreContent\Partials;

use ContentFactory;
use Template;
use ViewBag;
use User;
use UliCMS\Security\ContentPermissionChecker;
use function UliCMS\HTML\icon;
use function UliCMS\HTML\link;

class DeleteButtonRenderer {

    const MODULE_NAME = "core_content";

    public function render($pageId) {
        $content = ContentFactory::getByID($pageId);
        // FIXME: check permissions
        $permitted = true;

        $icon = icon("fas fa-pencil-alt fa-2x");

        $url = "../?goid={$pageId}";
        $link = link($url, $icon, true);
        ViewBag::set("button", "Löschen Icon");

        return $permitted ? Template::executeModuleTemplate(self::MODULE_NAME, "pages/partials/delete_button.php") : "";
    }

}
