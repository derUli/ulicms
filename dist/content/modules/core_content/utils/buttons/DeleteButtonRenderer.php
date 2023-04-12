<?php

declare(strict_types=1);

namespace App\CoreContent\Partials;

use function App\HTML\icon;
use function App\HTML\Link;
use App\Security\ContentPermissionChecker;
use ModuleHelper;
use PageController;
use Template;

use User;
use ViewBag;

class DeleteButtonRenderer
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

        $icon = icon('fa fa-trash fa-2x');

        $url = '#';
        $message = get_secure_translation(
            'confirm_delete_page',
            [
                '%title%' => getPageTitleByID($pageId)
            ]
        );
        $actionUrl = ModuleHelper::buildMethodCallUrl(
            PageController::class,
            'delete',
            "id={$pageId}&csrf_token=" . get_csrf_token()
        );

        $attributes = [
            'data-confirm' => $message,
            'data-url' => $actionUrl,
            'class' => 'delete-icon'
        ];
        $link = link($url, $icon, true, null, $attributes);
        ViewBag::set('button', $link);

        return $permitted ? Template::executeModuleTemplate(
            self::MODULE_NAME,
            'pages/partials/delete_button.php'
        ) : '';
    }
}
