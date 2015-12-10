<?php
$antispam_field_name = getconfig("antispam_field_name");
if(!$antispam_field_name){
   $antispam_field_name = "fax";
}
if(isset($_GET["submit-cms-form"]) and !empty($_GET["submit-cms-form"]) and get_request_method() === "POST" and !empty($_POST["fax"])){
   die("Spam detected!");
}
