<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Constants\AuditLog;
use UliCMS\Models\Content\Categories;
use UliCMS\Registries\LoggerRegistry;

class CategoryController extends Controller {

    private $logger;

    public function __construct() {
        parent::__construct();
        $this->logger = LoggerRegistry::get("audit_log");
    }

    public function createPost(): void {
        $name = Request::getVar("name", "", "str");
        $description = Request::getVar("description", "", "str");

        // TODO: validate required fields
        Categories::addCategory($name, $description);

        Request::redirect(ModuleHelper::buildActionURL("categories"));
    }

    public function _createPost(string $name, string $description): ?int {
        $logger = LoggerRegistry::get("audit_log");
        $categoryId = Categories::addCategory($name, $description);
        if ($categoryId && $this->logger) {
            $user = getUserById(get_user_id());
            $userName = isset($user["username"]) ?
                    $user["username"] : AuditLog::UNKNOWN;
            $this->logger->debug("User $userName - "
                    . "Created a new category ({$name})");
        }
        return $categoryId;
    }

    public function updatePost(): void {
        $id = Request::getVar("id", 0, "int");
        $name = Request::getVar("name", "", "str");
        $description = Request::getVar("description", "", "str");

        // TODO: validate required fields
        Categories::updateCategory($id, $name, $description);

        Request::redirect(ModuleHelper::buildActionURL("categories"));
    }

    public function _updatePost(int $id, string $name, string $description): ?int {
        $updateId = Categories::updateCategory($id, $name, $description);
        if ($this->logger) {
            $user = getUserById(get_user_id());
            $userName = isset($user["username"]) ?
                    $user["username"] : AuditLog::UNKNOWN;
            $this->logger->debug("User $userName - Update category with id "
                    . "({$id}) new title is "
                    . "\"{$name}\"");
        }

        return $updateId;
    }

    public function deletePost(): void {
        $del = intval($_GET["del"]);
        if ($del != 1) {
            Categories::deleteCategory($del);
            if ($this->logger) {
                $user = getUserById(get_user_id());
                $name = isset($user["username"]) ?
                        $user["username"] : AuditLog::UNKNOWN;
                $this->logger->debug("User $name - "
                        . "delete category with id ({$_REQUEST['id']})");
            }
        }
        Request::redirect(ModuleHelper::buildActionURL("categories"));
    }

    public function _deletePost($id): bool {
        $success = false;
        if ($id != 1) {
            $success = Categories::deleteCategory($id);
            if ($this->logger) {
                $user = getUserById(get_user_id());
                $name = isset($user["username"]) ?
                        $user["username"] : AuditLog::UNKNOWN;
                $this->logger->debug("User $name - "
                        . "delete category with id ({$id})");
            }
        }
        return $success;
    }

}
