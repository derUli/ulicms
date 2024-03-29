<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Models\Content\Categories;

class CategoryController extends \App\Controllers\Controller {
    public function __construct() {
        parent::__construct();
    }

    public function createPost(): void {
        $name = Request::getVar('name', '', 'str');
        $description = Request::getVar('description', '', 'str');

        // TODO: validate required fields
        Categories::addCategory($name, $description);

        Response::redirect(\App\Helpers\ModuleHelper::buildActionURL('categories'));
    }

    public function _createPost(string $name, string $description): ?int {
        return Categories::addCategory($name, $description);
    }

    public function updatePost(): void {
        $id = Request::getVar('id', 0, 'int');
        $name = Request::getVar('name', '', 'str');
        $description = Request::getVar('description', '', 'str');

        // TODO: validate required fields
        Categories::updateCategory($id, $name, $description);

        Response::redirect(\App\Helpers\ModuleHelper::buildActionURL('categories'));
    }

    public function _updatePost(int $id, string $name, string $description): ?int {
        return Categories::updateCategory($id, $name, $description);
    }

    public function deletePost(): void {
        $del = (int)$_GET['del'];

        if ($del != 1) {
            Categories::deleteCategory($del);
        }

        Response::redirect(\App\Helpers\ModuleHelper::buildActionURL('categories'));
    }

    public function _deletePost($id): bool {
        $success = false;
        if ($id != 1) {
            $success = Categories::deleteCategory($id);
        }
        return $success;
    }
}
