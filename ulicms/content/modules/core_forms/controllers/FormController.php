<?php
class FormController extends Controller {
	public function createPost() {
		$name = $_POST ["name"];
		$email_to = $_POST ["email_to"];
		$subject = $_POST ["subject"];
		$category_id = $_POST ["category"];
		$fields = $_POST ["fields"];
		$mail_from_field = $_POST ["mail_from_field"];
		$target_page_id = $_POST ["target_page_id"];
		
		Forms::createForm ( $name, $email_to, $subject, $category_id, $fields, $mail_from_field, $target_page_id );
		Request::redirect ( ModuleHelper::buildActionURL ( "forms" ) );
	}
	public function updatePost() {
		$id = $_POST ["id"];
		$name = $_POST ["name"];
		$email_to = $_POST ["email_to"];
		$subject = $_POST ["subject"];
		$category_id = $_POST ["category"];
		$fields = $_POST ["fields"];
		$mail_from_field = $_POST ["mail_from_field"];
		$target_page_id = $_POST ["target_page_id"];
		
		Forms::editForm ( $id, $name, $email_to, $subject, $category_id, $fields, $mail_from_field, $target_page_id );
		Request::redirect ( ModuleHelper::buildActionURL ( "forms" ) );
	}
	public function deletePost() {
		$del = Request::getVar ( "del", 0, "int" );
		Forms::deleteForm ( $del );
		Request::redirect ( ModuleHelper::buildActionURL ( "forms" ) );
	}
}