<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class FormController extends \App\Controllers\Controller {
    public function __construct() {
        parent::__construct();
    }

    public function createPost(): void {
        $this->_createPost();
        Response::redirect(ModuleHelper::buildActionURL('forms'));
    }

    public function _createPost(): ?int {
        $name = $_POST['name'];
        $enabled = $_POST['enabled'];
        $email_to = $_POST['email_to'];
        $subject = $_POST['subject'];
        $category_id = $_POST['category_id'];
        $fields = $_POST['fields'];
        $required_fields = $_POST['required_fields'];
        $mail_from_field = $_POST['mail_from_field'];
        $target_page_id = $_POST['target_page_id'];

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

        return Database::getLastInsertID();
    }

    public function updatePost(): void {
        $this->_updatePost();
        Response::redirect(ModuleHelper::buildActionURL('forms'));
    }

    public function _updatePost(): bool {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $enabled = $_POST['enabled'];
        $email_to = $_POST['email_to'];
        $subject = $_POST['subject'];
        $category_id = $_POST['category_id'];
        $fields = $_POST['fields'];

        $required_fields = $_POST['required_fields'];
        $mail_from_field = $_POST['mail_from_field'];
        $target_page_id = $_POST['target_page_id'];

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

        return $affectedRows > 0;
    }

    public function deletePost(): void {
        $id = Request::getVar('del', 0, 'int');
        $this->_deletePost($id);
        Response::redirect(ModuleHelper::buildActionURL('forms'));
    }

    public function _deletePost(int $id): bool {
        return Forms::deleteForm($id);
    }
}
