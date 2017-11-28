<?php
class CoreForms extends Controller {
	private $moduleName = "core_forms";
	public function beforeHttpHeader() {
		if (StringHelper::isNotNullOrWhitespace ( Request::getVar ( "submit-cms-form" ) )) {
			$form_id = Request::getVar ( "submit-cms-form", null, "int" );
			Forms::submitForm ( $form_id );
		}
	}
}