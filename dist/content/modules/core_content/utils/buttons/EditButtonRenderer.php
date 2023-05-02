<?php

declare(strict_types=1);

namespace App\CoreContent\Partials;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\ContentPermissionChecker;
use Template;
use User;

use function App\HTML\icon;

use function App\HTML\Link;

class EditButtonRenderer {
    public const MODULE_NAME = 'core_content';

    public function render(int $pageId, User $user): string {
        $permitted = true;

        // check edit permissions
        $pagePermissionChecker = new ContentPermissionChecker($user->getId());

        if (! $pagePermissionChecker->canWrite($pageId)) {
            $permitted = false;
        }

        $icon = icon('fas fa-pencil-alt fa-2x');

        $url = \App\Helpers\ModuleHelper::buildActionURL('pages_edit', "page={$pageId}");
        $link = link($url, $icon, true);
        \App\Storages\ViewBag::set('button', $link);

        return $permitted ? Template::executeModuleTemplate(
            self::MODULE_NAME,
            'pages/partials/edit_button.php'
        ) : '';
    }
}
