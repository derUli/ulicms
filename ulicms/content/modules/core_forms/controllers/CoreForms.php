<?php
class CoreForms extends Controller {
	private $moduleName = "core_forms";
	public function beforeHttpHeader() {

		if (StringHelper::isNotNullOrWhitespace ( Request::getVar ( "submit-cms-form" ) )) {
			// apply spam filter if disabled
			if($_POST ["spamfilter_enabled"] == "yes"){
				// check if honeypot field is filled
				if(!empty($_POST["business_fax"])){
					Settings::set("contact_form_refused_spam_mails", Settings::get("contact_form_refused_spam_mails") + 1);
					HTMLResult(get_translation("spam_trapped"), 403);
				}
			}
			$form_id = Request::getVar ( "submit-cms-form", null, "int" );
			Forms::submitForm ( $form_id );
		}
	}
}
