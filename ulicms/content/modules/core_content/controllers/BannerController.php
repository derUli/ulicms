<?php

class BannerController extends Controller
{

    private $logger;

    public function __construct()
    {
        $this->logger = LoggerRegistry::get("audit_log");
    }

    public function createPost()
    {
        do_event("before_create_banner");
        
        $banner = new Banner();
        $banner->name = strval($_POST["banner_name"]);
        $banner->image_url = strval($_POST["image_url"]);
        $banner->link_url = strval($_POST["link_url"]);
        $banner->category = intval($_POST["category"]);
        $banner->setType($_POST["type"]);
        $banner->html = strval($_POST["html"]);
        
        $banner->setDateFrom(stringOrNull($_POST["date_from"]));
        $banner->setDateTo(stringOrNull($_POST["date_to"]));
        
        $banner->enabled = boolval($_POST["enabled"]);
        $banner->language = $_POST["language"] != "all" ? strval($_POST["language"]) : null;
        $banner->save();
        if ($this->logger) {
            $user = getUserById(get_user_id());
            $userName = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;
            $this->logger->debug("User $userName - Created new banner with type ({$_POST ['type']})");
        }
        
        do_event("after_create_banner");
        
        Request::redirect(ModuleHelper::buildActionURL("banner"));
    }

    public function deletePost()
    {
        $id = intval($_GET["banner"]);
        $banner = new Banner($id);
        do_event("before_banner_delete");
        $banner->delete();
        
        if ($this->logger) {
            $user = getUserById(get_user_id());
            $userName = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;
            $this->logger->debug("User $userName - Deleted Banner with id ($id)");
        }
        do_event("after_banner_delete");
        Request::redirect(ModuleHelper::buildActionURL("banner"));
    }

    public function updatePost()
    {
        do_event("before_edit_banner");
        
        $id = intval($_POST["id"]);
        $banner = new Banner($id);
        $banner->name = strval($_POST["banner_name"]);
        $banner->image_url = strval($_POST["image_url"]);
        $banner->link_url = strval($_POST["link_url"]);
        $banner->category = intval($_POST["category"]);
        $banner->setType($_POST["type"]);
        $banner->html = strval($_POST["html"]);
        
        $banner->setDateFrom(stringOrNull($_POST["date_from"]));
        $banner->setDateTo(stringOrNull($_POST["date_to"]));
        
        $banner->enabled = boolval($_POST["enabled"]);
        $banner->language = $_POST["language"] != "all" ? strval($_POST["language"]) : null;
        $banner->save();
        
        if ($this->logger) {
            $user = getUserById(get_user_id());
            $userName = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;
            $this->logger->debug("User $userName - Updated Banner with id ($id)");
        }
        
        do_event("after_edit_banner");
        Request::redirect(ModuleHelper::buildActionURL("banner"));
    }
}