<?php
class CoreForms extends Controller {
	private $moduleName = "core_forms";
	public function beforeHttpHeader() {

		if (StringHelper::isNotNullOrWhitespace ( Request::getVar ( "submit-cms-form" ) )) {
			// apply spam filter if disabled
			if(Settings::get("spamfilter_enabled") == "yes"){
				// check if honeypot field is filled
				if(!empty($_POST["business_fax"])){
					Settings::set("contact_form_refused_spam_mails", Settings::get("contact_form_refused_spam_mails") + 1);
					HTMLResult(get_translation( "honeypot_is_not_empty" ), 403);
					// TODO: Vollständigen Spamfilter implementieren
				}
			}
			$form_id = Request::getVar ( "submit-cms-form", null, "int" );
			Forms::submitForm ( $form_id );
		}
	}
}
