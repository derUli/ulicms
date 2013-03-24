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
   $newsletter_id = getconfig("newsletter_id");
   $date = date(getconfig("date_format"));
   $subject = str_replace("%newsletter_id%", $newsletter_id, $subject);
   
   $subject = str_replace("%year%", strftime("%Y"), $subject);
   $subject = str_replace("%month%", 
                          utf8_encode(strftime("%B")), $subject);
   $subject = str_replace("%date%", $date, $subject);
                      
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
   $html = str_replace("%newsletter_id%", $newsletter_id, $html);


   $html = str_replace("%year%", strftime("%Y"), $html);
   $html = str_replace("%month%", 
                       utf8_encode(strftime("%B")), 
                       $html);  
   $html = str_replace("%date%", $date, $html);

   $header  = "MIME-Version: 1.0\n";
   $header .= "Content-type: text/html; charset=utf-8\n";
   $header .= "From: $mail_from\n";
   
   // $header .= "Reply-To: $replay_to\n";
   // $header .= "Cc: $cc\n";  // falls an CC gesendet werden soll
   $header .= "X-Mailer: PHP ". phpversion();
 
   return mail($mail_to, $subject, $html, $header);

      
   
}


}


function send_loop(){
    define("ADMIN_EMAIL", getconfig("email"));
    
    if($_SESSION["newsletter_data"]["newsletter_remaining"] > 0){
        
        for($i=0; $i <= $_SESSION["newsletter_data"]["newsletter_receivers"]; $i++){
           
           if(!empty($_SESSION["newsletter_data"]["newsletter_receivers"][$i])){
              $sent = send_html_mail(ADMIN_EMAIL, 
                   $_SESSION["newsletter_data"]["newsletter_receivers"][$i],
                   $_SESSION["newsletter_data"]["newsletter_title"],
                   $_SESSION["newsletter_data"]["newsletter_text"]);
                   
              echo "Newsletter an ".
                 $_SESSION["newsletter_data"]["newsletter_receivers"][$i]." senden ";
                   
             
              flush();      
                   
              if($sent){
                 $_SESSION["newsletter_data"]["newsletter_remaining"]--;
                 $_SESSION["newsletter_data"]["newsletter_receivers"][$i] = "";
                 
                 echo '<span style="color:green">[Erfolgreich]</span>';
                 flush();            
              } else {
                 echo '<span style="color:red">[Fehlgeschlagen]</span>';  
                 flush();            
              }
           }
           
         
        echo "<br/>";
        
        if($_SESSION["newsletter_data"]["newsletter_remaining"] < 1) {
              echo "Fertig.";
              flush();
              // increment newsletter_id
              setconfig("newsletter_id", 
              getconfig("newsletter_id") + 1);              
              return;       
        }
           
           
       
        }
        
        
    } else {
      echo "<p>Nichts zu tun.</p>";
    }


} 


?>