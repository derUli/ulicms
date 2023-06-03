<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Models\Content\VCS;
use App\Utils\CacheUtil;

class HistoryController extends \App\Controllers\Controller {
    public function doRestore(): void {
        if (isset($_GET['version_id'])) {
            $version_id = (int)$_GET['version_id'];

            $revision = $this->_doRestore($version_id);

            if ($revision) {
                CacheUtil::clearPageCache();

                Response::redirect(
                    \App\Helpers\ModuleHelper::buildActionURL(
                        'pages_edit',
                        'page=' . $revision->content_id
                    )
                );
            } else {
                ExceptionResult(
                    get_translation('not_found', \App\Constants\HttpStatusCode::NOT_FOUND)
                );
            }
        } else {
            ExceptionResult(
                get_translation(
                    'UNPROCESSABLE_ENTITY',
                    \App\Constants\HttpStatusCode::UNPROCESSABLE_ENTITY
                )
            );
        }
    }

    public function _doRestore(int $version_id): ?object {
        $rev = VCS::getRevisionByID($version_id);
        if ($rev) {
            VCS::restoreRevision($version_id);
            return $rev;
        }

        return null;
    }
}
