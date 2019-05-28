<?php

use UliCMS\Constants\AuditLog;
use UliCMS\Models\Content\Categories;

class CategoryController extends Controller {

    private $logger;

    public function __construct() {
        $this->logger = LoggerRegistry::get("audit_log");
    }

    public function createPost() {
        $logger = LoggerRegistry::get("audit_log");

        if (!empty($_REQUEST["name"])) {
            Categories::addCategory($_REQUEST["name"], $_REQUEST["description"]);
            if ($this->logger) {
                $user = getUserById(get_user_id());
                $name = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;
                $this->logger->debug("User $name - Created a new category ({$_REQUEST['name']})");
            }
        }
        Request::redirect(ModuleHelper::buildActionURL("categories"));
    }

    public function updatePost() {
        if (!empty($_REQUEST["name"]) and ! empty($_REQUEST["id"])) {
            Categories::updateCategory(intval($_REQUEST["id"]), $_REQUEST["name"], $_REQUEST["description"]);
            if ($this->logger) {
                $user = getUserById(get_user_id());
                $name = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;
                $this->logger->debug("User $name - Update category with id ({$_REQUEST['id']}) new title is \"{$_REQUEST['name']}\"");
            }
        }
        Request::redirect(ModuleHelper::buildActionURL("categories"));
    }

    public function deletePost() {
        $del = intval($_GET["del"]);
        if ($del != 1) {
            Categories::deleteCategory($del);
            if ($this->logger) {
                $user = getUserById(get_user_id());
                $name = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;
                $this->logger->debug("User $name - delete category with id ({$_REQUEST['id']})");
            }
        }
        Request::redirect(ModuleHelper::buildActionURL("categories"));
    }

}
