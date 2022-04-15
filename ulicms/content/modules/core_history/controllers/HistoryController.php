<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Models\Content\VCS;
use UliCMS\Utils\CacheUtil;
use UliCMS\Constants\HttpStatusCode;

class HistoryController extends Controller {

    public function doRestore(): void {
        if (isset($_GET ["version_id"])) {
            $version_id = intval($_GET ["version_id"]);

            $revision = $this->_doRestore($version_id);

            if ($revision) {
                CacheUtil::clearPageCache();

                Request::redirect(
                        ModuleHelper::buildActionURL(
                                "pages_edit",
                                "page=" . $revision->content_id
                        )
                );
            } else {
                ExceptionResult(
                        get_translation("not_found", HttpStatusCode::NOT_FOUND)
                );
            }
        } else {
            ExceptionResult(
                    get_translation(
                            "UNPROCESSABLE_ENTITY",
                            HttpStatusCode::UNPROCESSABLE_ENTITY
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
