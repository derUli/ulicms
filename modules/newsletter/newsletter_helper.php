<?php 
function getSubscribers(){
   $adresses = array();
   $query = mysql_query("SELECT email FROM ".tbname("newsletter_subscribers"). " ORDER by email ASC");
   
   if(!$query){
     return $adresses;   
   }

   if(mysql_num_rows($query) > 0){
      while($row = mysql_fetch_assoc($query)){
        array_push($adresses, $row["email"]);
      }
   }

    return $adresses;   

}


if(!function_exists("send_html_mail")){

// HTML-Mail senden


function send_html_mail($mail_from, $mail_to, 
                   $subject, $text){
   $html = "<html>
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
        <title>".htmlspecialchars($subject)."</title>
    </head>
    <body>
        $text
    </body>
</html>";

   $html = str_replace("\r\n", "\n", $html);


   $header  = "MIME-Version: 1.0\n";
   $header .= "Content-type: text/html; charset=utf-8\n";
   $header .= "From: $mail_from\n";
   
   // $header .= "Reply-To: $replay_to\n";
   // $header .= "Cc: $cc\n";  // falls an CC gesendet werden soll
   $header .= "X-Mailer: PHP ". phpversion();
 
   return mail($subject, $betreff, $mailtext, $header);

      
   
}


}


?>