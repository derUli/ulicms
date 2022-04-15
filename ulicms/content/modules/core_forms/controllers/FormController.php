<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Constants\AuditLog;
use UliCMS\Registries\LoggerRegistry;

class FormController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->logger = LoggerRegistry::get("audit_log");
    }

    public function createPost(): void {
        $this->_createPost();
        Request::redirect(ModuleHelper::buildActionURL("forms"));
    }

    public function _createPost(): ?int {
        $name = $_POST["name"];
        $enabled = $_POST["enabled"];
        $email_to = $_POST["email_to"];
        $subject = $_POST["subject"];
        $category_id = $_POST["category_id"];
        $fields = $_POST["fields"];
        $required_fields = $_POST["required_fields"];
        $mail_from_field = $_POST["mail_from_field"];
        $target_page_id = $_POST["target_page_id"];

        $success = Forms::createForm(
                        $name,
                        $email_to,
                        $subject,
                        $category_id,
                        $fields,
                        $required_fields,
                        $mail_from_field,
                        $target_page_id,
                        $enabled
        );
        $id = $success ? Database::getLastInsertID() : null;
        if ($this->logger) {
            $user = getUserById(get_user_id());
            $name = isset($user["username"]) ?
                    $user["username"] : AuditLog::UNKNOWN;
            $this->logger->debug("User $name - Created a new form ({$name})");
        }
        return $id;
    }

    public function updatePost(): void {
        $this->_updatePost();
        Request::redirect(ModuleHelper::buildActionURL("forms"));
    }

    public function _updatePost(): bool {
        $id = $_POST["id"];
        $name = $_POST["name"];
        $enabled = $_POST["enabled"];
        $email_to = $_POST["email_to"];
        $subject = $_POST["subject"];
        $category_id = $_POST["category_id"];
        $fields = $_POST["fields"];

        $required_fields = $_POST["required_fields"];
        $mail_from_field = $_POST["mail_from_field"];
        $target_page_id = $_POST["target_page_id"];

        Forms::editForm(
                $id,
                $name,
                $email_to,
                $subject,
                $category_id,
                $fields,
                $required_fields,
                $mail_from_field,
                $target_page_id,
                $enabled
        );
        $affectedRows = Database::getAffectedRows();

        if ($this->logger) {
            $user = getUserById(get_user_id());
            $name = isset($user["username"]) ?
                    $user["username"] : AuditLog::UNKNOWN;
            $this->logger->debug("User $name - Updated form with Id ({$id})");
        }
        return $affectedRows > 0;
    }

    public function deletePost(): void {
        $id = Request::getVar("del", 0, "int");
        $this->_deletePost($id);
        Request::redirect(ModuleHelper::buildActionURL("forms"));
    }

    public function _deletePost(int $id): bool {
        $success = Forms::deleteForm($id);
        if ($this->logger) {
            $user = getUserById(get_user_id());
            $name = isset($user["username"]) ?
                    $user["username"] : AuditLog::UNKNOWN;
            $this->logger->debug("User $name - Deleted form with Id ({$id})");
        }
        return $success;
    }

}
