<?php
 if(isset($_POST["absenden"]) and containsModule(get_requested_pagename(), "kontaktformular")){
    
     if(empty($_POST["vorname"])){
         if($_SESSION["language"] == "de"){
             $fehler = "Bitte geben Sie Ihren Vornamen ein.";
             }
        else{
             $fehler = "Please enter your first name.";
             }
         }
    
     if(empty($_POST["nachname"])){
         if($_SESSION["language"] == "de"){
             $fehler = "Bitte geben Sie Ihren Nachnamen ein.";
             }
        else{
             $fehler = "Please enter your first name";
             }
         }
    
     if(empty($_POST["emailadresse"])){
         if($_SESSION["language"] == "de"){
             $fehler = "Bitte geben Sie Ihren Emailadresse ein, da wir Ihre Mail sonst nicht beantworten können.";
             }
        else{
             $fehler = "please enter your mail adress, because if you do it not, we can't answer your request.";
             }
         }
    
     if(empty($_POST["betreff"])){
         if($_SESSION["language"] == "de"){
             $fehler = "Bitte geben Sie einen Betreff ein.";
             }
        else{
             $fehler = "Please enter a subject.";
             }
         }
    
     if(empty($_POST["nachricht"])){
         if($_SESSION["language"] == "de"){
             $fehler = "Sie haben keine Nachricht eingegeben.";
             }
        else{
             $fehler = "Please enter a message.";
             }
        
         }
    
     $spamfilter_enabled = getconfig("spamfilter_enabled") == "yes";
    
     // Spamschutz
    if($spamfilter_enabled){
        
        
         // Blacklist
        // Spamschutz per Honeypot
        if($_POST["email"] != ""){
             if($_SESSION["language"] == "de"){
                 $fehler = "Das Spamschutz-Feld bitte leer lassen.";
                 }
            else{
                 $fehler = "Please don't fill the spam-protection field.";
                 }
            
             }
        
        
         // Wortfilter (Badwords)
        if(stringcontainsbadwords($_POST["vorname"]) or
                 stringcontainsbadwords($_POST["nachname"]) or
                 stringcontainsbadwords($_POST["betreff"]) or
                 stringcontainsbadwords($_POST["nachricht"])){
            
            
             if($_SESSION["language"] == "de"){
                 $fehler = "<p class='ulicms_error'>" .
                 "Ihre Nachricht enthält nicht erlaubte Wörter.</p>";
                 }
            else{
                 $fehler = "<p class='ulicms_error'>" .
                 "Your comment contains not allowed words.</p>";
                 }
            
            
             }
        
        // Filter nach chinesisch
		     if(getconfig("disallow_chinese_chars") and $spamfilter_enabled and
	(is_chinese($_POST["betreff"]) or
                 is_chinese($_POST["nachricht"]))){
				 if($_SESSION["language"] == "de"){
				 $fehler = "Chinesische Schriftzeichen sind nicht erlaubt!";
			 } else {
			  $fehler ="Chinese chars are not allowed!";
			 }
             }
    
		
         // Filter nach Land
        if(function_exists("isCountryBlocked")){
             if(isCountryBlocked()){
                
                
                 if($_SESSION["language"] == "de"){
                    
                    
                    
                     $fehler = "Sie dürfen diesen Formular leider nicht nutzen, da ihr Land im Spamfilter gesperrt ist. Falls Sie denken, dass dies ein Fehler sein sollte, benachrichtigen Sie bitte den Administrator dieser Internetseite";
                     }
                else{
                     $fehler = "You can't use this form, because your country is blocked. If you think this is an failure, then contact the administrator of this website." ;
                     }
                 }
             }
        
        
        
        
         }
    
     $kontaktformular_thankyou_page = getconfig("kontaktformular_thankyou_page");
    
     if(!$fehler and $kontaktformular_thankyou_page){
         header("Location: " . buildSEOUrl($kontaktformular_thankyou_page));
         exit();
        
        
         }
    
    
     }
