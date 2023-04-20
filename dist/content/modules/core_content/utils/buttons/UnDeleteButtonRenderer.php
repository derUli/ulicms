<?php

declare(strict_types=1);

namespace App\CoreContent\Partials;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\ContentPermissionChecker;
use ModuleHelper;
use PageController;
use Template;
use User;
use function App\HTML\icon;

use function App\HTML\Link;

class UnDeleteButtonRenderer
{
    public const MODULE_NAME = 'core_content';

    public function render(int $pageId, User $user)
    {
        $permitted = true;

        // check edit permissions
        $pagePermissionChecker = new ContentPermissionChecker($user->getId());

        if (! $pagePermissionChecker->canDelete($pageId)) {
            $permitted = false;
        }

        $icon = icon('fas fa-trash-restore fa-2x');

        $url = '#';
        $message = get_secure_translation(
            'confirm_undelete_page',
            [
                '%title%' => getPageTitleByID($pageId)
            ]
        );
        $actionUrl = ModuleHelper::buildMethodCallUrl(
            PageController::class,
            'undelete',
            "id={$pageId}csrf_token=" . get_csrf_token()
        );
        $attributes = [
            'data-confirm' => $message,
            'data-url' => $actionUrl,
            'class' => 'delete-icon'
        ];

        $link = link($url, $icon, true, null, $attributes);
        \App\Storages\ViewBag::set('button', $link);

        return $permitted ? Template::executeModuleTemplate(
            self::MODULE_NAME,
            'pages/partials/delete_button.php'
        ) : '';
    }
}
