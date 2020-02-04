<?php

declare(strict_types=1);

use UliCMS\Models\Content\VCS;
use UliCMS\Utils\CacheUtil;

class HistoryController extends Controller {

    public function doRestore(): void {
        if (isset($_GET ["version_id"])) {
            $version_id = intval($_GET ["version_id"]);
            $rev = VCS::getRevisionByID($version_id);
            if ($rev) {
                VCS::restoreRevision($version_id);
            }

            CacheUtil::clearPageCache();

            Request::redirect(ModuleHelper::buildActionURL("pages_edit",
                            "page=" . $rev->content_id));
        }
    }

}
