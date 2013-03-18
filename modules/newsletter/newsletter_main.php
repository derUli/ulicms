<?php 

include getModulePath("newsletter")."newsletter_install.php";
newsletter_check_install();

function subscribe_newsletter($mail){

   $html_output = "";

   if($_SESSION["language"] == "de"){
      $translation_thank_you_for_subscribing = "Danke für das Abonnieren des Newsletters";
      $translation_already_subscribed = "Sie haben den Newsletter bereits abonniert!";
   }

   $subscribe_date = time();
   if(check_email_address($mail)){
   
    if(checkIfSubscribed($mail)){
      $html_output .= "<p>$translation_already_subscribed</p>";
    }
    
    
    else
    {
   
      $mail = mysql_real_escape_string($mail);
      mysql_query("INSERT INTO ".tbname("newsletter_subscribers").
      "(email, subscribe_date) VALUES('$mail',  $subscribe_date)");
       
      $html_output .= "<p>$translation_thank_you_for_subscribing</p>";
       }      
      
      
      
      }
      
      return $html_output;
}


function checkIfSubscribed($mail){
   $mail = mysql_real_escape_string($mail);
   $query = mysql_query("SELECT email FROM ".tbname("newsletter_subscribers"). " WHERE email = '$mail'");
   return mysql_num_rows($query) > 0;
}



function check_email_address($email) {
  $at_array = explode("@", $email);
  $dot_array = explode("@", $email);
  return count($at_array) == 2 and count($dot_array) >= 1;
}



function newsletter_render(){
   $html_output = "";
  
   
   if($_SESSION["language"] == "de"){
      $translation_your_mail_adress = "Ihre E-Mail Adresse";
      $translation_subscribe_newsletter = "Newsletter abonnieren";
      $translation_cancel_newsletter = "Newsletter kündigen";
      $translation_submit = "Absenden";
   }
   else{
      $translation_your_mail_adress = "Your mail adress";
      $translation_subscribe_newsletter = "subscribe newsletter";
      $translation_cancel_newsletter = "Cancel newsletter";
      $translation_submit = "Submit";  
   }

   $email = false;
   if(isset($_SESSION["login_id"])){
      $userdata = getUserById($_SESSION["login_id"]);
      $email = $userdata["email"];
   }
   newsletter_check_install();
   
   if(!empty($_GET["newsletter_email_adress"]) and 
   !empty($_GET["newsletter_subscribe"])){
     $subcribe = $_GET["newsletter_subscribe"];
      if($subcribe == "yes"){
        return subscribe_newsletter($_GET["newsletter_email_adress"]);
      }
   
   }
   
   
   $html_output.= "<form class=\"newsletter_form\" action=\"./\" method=\"get\">";
   
   
   $html_output.='<input type="hidden" name="seite" value="'.get_requested_pagename().'">';
   
   if($email){
     $html_output .= "<input name=\"newsletter_email_adress\" type=\"hidden\" value=\"$email\">";
   }
   else{
   
   
   if(isset($_GET["newsletter_email_adress"])){
      $email = htmlspecialchars($_GET["newsletter_email_adress"]);
   } else{
      $email = "";   
   }
   
   $html_output .= "$translation_your_mail_adress: <input name=\"newsletter_email_adress\" type=\"email\" value=\"$email\">";
   }
   
   $html_output .= "<br/><br/>";
   $html_output .= "<input type=\"radio\" name=\"newsletter_subscribe\" value=\"yes\"> $translation_subscribe_newsletter<br/>";
   $html_output .= "<input type=\"radio\" name=\"newsletter_subscribe\" value=\"no\"> $translation_cancel_newsletter";
   
   
   
   
   $html_output .= "<br/><br/><input type=\"submit\" value=\"$translation_submit\">";
   
   $html_output .= "</form>";
   
   return $html_output;
}




?>