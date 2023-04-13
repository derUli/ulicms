<?php

declare(strict_types=1);

use App\Utils\CacheUtil;

class OpenGraphController extends \App\Controllers\Controller
{
    public function _savePost(): void
    {
        if (isset($_POST['og_image'])) {
            Settings::set('og_image', $_POST['og_image']);
        }

        CacheUtil::clearPageCache();
    }

    public function savePost(): void
    {
        $this->_savePost();
        Response::sendHttpStatusCodeResultIfAjax(
            HttpStatusCode::OK,
            ModuleHelper::buildActionURL('open_graph')
        );
    }
}
