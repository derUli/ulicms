<?php

use UliCMS\Constants\AuditLog;

class FormController extends Controller {

    public function __construct() {
        $this->logger = LoggerRegistry::get("audit_log");
    }

    public function createPost() {
        $name = $_POST["name"];
        $enabled = $_POST["enabled"];
        $email_to = $_POST["email_to"];
        $subject = $_POST["subject"];
        $category_id = $_POST["category_id"];
        $fields = $_POST["fields"];
        $required_fields = $_POST["required_fields"];
        $mail_from_field = $_POST["mail_from_field"];
        $target_page_id = $_POST["target_page_id"];

        Forms::createForm($name, $email_to, $subject, $category_id, $fields, $required_fields, $mail_from_field, $target_page_id, $enabled);
        if ($this->logger) {
            $user = getUserById(get_user_id());
            $name = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;
            $this->logger->debug("User $name - Created a new form ({$name})");
        }

        Request::redirect(ModuleHelper::buildActionURL("forms"));
    }

    public function updatePost() {
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

        Forms::editForm($id, $name, $email_to, $subject, $category_id, $fields, $required_fields, $mail_from_field, $target_page_id, $enabled);
        if ($this->logger) {
            $user = getUserById(get_user_id());
            $name = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;
            $this->logger->debug("User $name - Updated form with Id ({$id})");
        }

        Request::redirect(ModuleHelper::buildActionURL("forms"));
    }

    public function deletePost() {
        $del = Request::getVar("del", 0, "int");
        Forms::deleteForm($del);
        if ($this->logger) {
            $user = getUserById(get_user_id());
            $name = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;
            $this->logger->debug("User $name - Deleted form with Id ({$del})");
        }
        Request::redirect(ModuleHelper::buildActionURL("forms"));
    }

}
