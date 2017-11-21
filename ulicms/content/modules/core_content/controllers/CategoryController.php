<?php
class CategoryController extends Controller {
	public function createPost() {
		if (! empty ( $_REQUEST ["name"] )) {
			Categories::addCategory ( $_REQUEST ["name"], $_REQUEST ["description"] );
		}
		Request::redirect ( ModuleHelper::buildActionURL ( "categories" ) );
	}
	public function updatePost() {
		if (! empty ( $_REQUEST ["name"] ) and ! empty ( $_REQUEST ["id"] )) {
			Categories::updateCategory ( intval ( $_REQUEST ["id"] ), $_REQUEST ["name"], $_REQUEST ["description"] );
		}
		Request::redirect ( ModuleHelper::buildActionURL ( "categories" ) );
	}
	public function deletePost() {
		$del = intval ( $_GET ["del"] );
		if ($del != 1) {
			Categories::deleteCategory ( $del );
		}
		Request::redirect ( ModuleHelper::buildActionURL ( "categories" ) );
	}
}