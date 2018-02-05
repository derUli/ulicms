<?php
class CoreForms extends Controller {
	private $moduleName = "core_forms";
	protected function incSpamCount(){
			Settings::set("contact_form_refused_spam_mails", Settings::get("contact_form_refused_spam_mails") + 1);
	}
	public function beforeHttpHeader() {
		if (StringHelper::isNotNullOrWhitespace ( Request::getVar ( "submit-cms-form" ) ) and Request::isPost()) {
			// apply spam filter if disabled
			if(Settings::get("spamfilter_enabled") == "yes"){
				// check if honeypot field is filled
				if(!empty($_POST["business_fax"])){
					$this->incSpamCount();
					HTMLResult(get_translation( "honeypot_is_not_empty" ), 403);
				}
						foreach($_POST as $key=>$value){
							  if(Settings::get("disallow_chinese_chars") and AntiSpamHelper::isChinese($_POST[$key])){
									  $this->incSpamCount();
										HTMLResult(get_translation( "chinese_chars_not_allowed" ), 403);
								}
								if(Settings::get("disallow_cyrillic_chars") and AntiSpamHelper::isCyrillic($_POST[$key])){
								  	$this->incSpamCount();
 										HTMLResult(get_translation( "cyrillic_charts_not_allowed" ), 403);
 								}

								$badwordsCheck = AntispamHelper::containsBadwords($_POST[$key]);
								if($badwordsCheck){
									  $this->incSpamCount();
								   	HTMLResult(get_translation( "request_contains_badword", array("%word%"=>$badwordsCheck) ), 403);
								}
						}

							if (isCountryBlocked ()) {
								$this->incSpamCount();
								$hostname = @gethostbyaddr(get_ip());
								HTMLResult(get_translation("your_country_is_blocked", array("%hostname%"=>$hostname)), 403);
						}
			}
			$form_id = Request::getVar ( "submit-cms-form", null, "int" );
			Forms::submitForm ( $form_id );
		}
	}
}
