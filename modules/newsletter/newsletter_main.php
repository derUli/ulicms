<?php 

include getModulePath("newsletter")."newsletter_install.php";
newsletter_check_install();


function cancel_newsletter($mail){

   $mail = mysql_real_escape_string($mail);
   if($_SESSION["language"] == "de"){
     $translation_newsletter_canceled = "Ihre E-Mail Adresse wurde aus der Liste entfernt.";
     $translation_email_not_subscribed = "Diese E-Mail Adresse befindet sich nicht in der Liste";
     }
     
   else{
     $translation_newsletter_canceled = "Your mail adress was removed from the list";
     $translation_email_not_subscribed = "This mail adress is not in the list.";
   }
   
   
   if(!checkIfSubscribed($mail)){
        return "<p>$translation_email_not_subscribed</p>";
   }
   
   mysql_query("DELETE FROM ".tbname("newsletter_subscribers"). " WHERE email = '$mail'");
   
   
   return "<p>$translation_newsletter_canceled</p>";
}

function subscribe_newsletter($mail){

   $html_output = "";

   if($_SESSION["language"] == "de"){
      $translation_thank_you_for_subscribing = "Danke für das Abonnieren des Newsletters";
      $translation_already_subscribed = "Sie haben den Newsletter bereits abonniert!";
   }
   
   else{
     $translation_thank_you_for_subscribing = "Thank you for subscribing";
      $translation_already_subscribed = "You've Subscribed the newsletter.";
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
   
   
   if(!empty($_GET["newsletter_email_adress"]) and 
   !empty($_GET["newsletter_subscribe"])){
     $subscribe = $_GET["newsletter_subscribe"];
      if($subscribe == "yes"){
        return subscribe_newsletter($_GET["newsletter_email_adress"]);
      }
      else if($subscribe == "no"){
        return cancel_newsletter($_GET["newsletter_email_adress"]);
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
   $html_output .= "<br/><br/>";
   }
   
   
   
   
   if($email and !empty($email)){
     $subscribed = checkIfSubscribed($email);
   }
   else{
     $subscribed = false;   
   }
   
   
   if($subscribed or empty($email)){
      $html_output .= "<input type=\"radio\" name=\"newsletter_subscribe\" checked value=\"yes\"> $translation_subscribe_newsletter<br/>";
      $html_output .= "<input type=\"radio\" name=\"newsletter_subscribe\" value=\"no\"> $translation_cancel_newsletter";
   
   }
   
  else{
  
      $html_output .= "<input type=\"radio\" name=\"newsletter_subscribe\" value=\"yes\"> $translation_subscribe_newsletter<br/>";
      $html_output .= "<input type=\"radio\" name=\"newsletter_subscribe\" checked value=\"no\"> $translation_cancel_newsletter";
      
          
  }
   
   $html_output .= "<br/><br/><input type=\"submit\" value=\"$translation_submit\">";
   
   $html_output .= "</form>";
   
   return $html_output;
}




?>