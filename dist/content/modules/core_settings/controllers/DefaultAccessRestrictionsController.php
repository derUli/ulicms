<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class DefaultAccessRestrictionsController extends \App\Controllers\Controller {
    public function _savePost(): void {
        Settings::set(
            'only_admins_can_edit',
            Request::getVar('only_admins_can_edit', 0, 'int')
        );
        Settings::set(
            'only_group_can_edit',
            Request::getVar('only_group_can_edit', 0, 'int')
        );
        Settings::set(
            'only_owner_can_edit',
            Request::getVar('only_owner_can_edit', 0, 'int')
        );
        Settings::set(
            'only_others_can_edit',
            Request::getVar('only_others_can_edit', 0, 'int')
        );
    }

    public function savePost(): void {
        $this->_savePost();
        Response::sendHttpStatusCodeResultIfAjax(
            \App\Constants\HttpStatusCode::OK,
            \App\Helpers\ModuleHelper::buildActionURL(
                'default_access_restrictions',
                'submit_form=1'
            )
        );
    }
}
