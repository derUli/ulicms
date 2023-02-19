<?php

declare(strict_types=1);

use App\Exceptions\DatasetNotFoundException;
use App\Models\Content\Advertisement\Banner;
use App\Utils\CacheUtil;

class BannerController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function createPost(): void
    {
        $this->_createPost();
        Request::redirect(ModuleHelper::buildActionURL("banner"));
    }

    public function _createPost(): Banner
    {
        do_event("before_create_banner");

        $banner = new Banner();
        $banner->setName(strval($_POST["banner_name"]));
        $banner->setImageUrl(strval($_POST["image_url"]));
        $banner->setLinkUrl(strval($_POST["link_url"]));
        $banner->setCategoryId(intval($_POST["category_id"]));
        $banner->setType($_POST["type"]);
        $banner->setHtml(strval($_POST["html"]));

        $banner->setDateFrom(stringOrNull($_POST["date_from"]));
        $banner->setDateTo(stringOrNull($_POST["date_to"]));

        $banner->setEnabled(boolval($_POST["enabled"]));
        $banner->setLanguage($_POST['language'] != "all" ?
                        strval($_POST['language']) : null);
        $banner->save();

        do_event("after_create_banner");

        CacheUtil::clearPageCache();
        return $banner;
    }

    public function updatePost(): void
    {
        $this->_updatePost();

        Request::redirect(ModuleHelper::buildActionURL("banner"));
    }

    public function _updatePost(): Banner
    {
        $id = intval($_POST['id']);

        do_event("before_edit_banner");

        $banner = new Banner($id);
        $banner->setName(strval($_POST["banner_name"]));
        $banner->setImageUrl(strval($_POST["image_url"]));
        $banner->setLinkUrl(strval($_POST["link_url"]));
        $banner->setCategoryId(intval($_POST["category_id"]));
        $banner->setType($_POST["type"]);
        $banner->setHtml(strval($_POST["html"]));

        $banner->setDateFrom(stringOrNull($_POST["date_from"]));
        $banner->setDateTo(stringOrNull($_POST["date_to"]));

        $banner->setEnabled(boolval($_POST["enabled"]));
        $banner->setLanguage($_POST['language'] != "all" ?
                        strval($_POST['language']) : null);
        $banner->save();

        do_event("after_edit_banner");

        CacheUtil::clearPageCache();

        return $banner;
    }

    public function deletePost(): void
    {
        $id = Request::getVat("banner", 0, 'int');

        $this->_deletePost($id);
        // Todo: handle errors
        Request::redirect(ModuleHelper::buildActionURL("banner"));
    }

    public function _deletePost(int $id): bool
    {
        try {
            $banner = new Banner($id);
        } catch (DatasetNotFoundException $e) {
            return false;
        }

        do_event("before_banner_delete");
        $banner->delete();

        do_event("after_banner_delete");

        CacheUtil::clearPageCache();

        return !$banner->isPersistent();
    }
}