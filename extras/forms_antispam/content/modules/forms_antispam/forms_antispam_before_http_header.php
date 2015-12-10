<?php
$antispam_field_name = getconfig("antispam_field_name");
if(!$antispam_field_name){
   $antispam_field_name = "fax";
}
if(isset($_GET["submit-cms-form"]) and !empty($_GET["submit-cms-form"]) and get_request_method() === "POST" and !empty($_POST[$antispam_field_name])){
  $count = intval(getconfig("contact_form_refused_spam_mails")) + 1;
  setconfig("contact_form_refused_spam_mails", $count);
  die("Spam detected!");
}
