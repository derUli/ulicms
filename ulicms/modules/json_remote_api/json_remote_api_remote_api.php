<?php 

// Wenn REMOTE_API_AUTHENTIFICATION_OK definiert ist, 
// dann waren die Logindaten richtig
if(REMOTE_API_AUTHENTIFICATION_OK){
   if($_REQUEST["call"] == "hallo"){
      $result = array("message" => "Hallo Welt!");
        die(json_encode($result));
   }

}
?>
