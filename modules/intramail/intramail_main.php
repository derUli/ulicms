<?php 
function intramail_render(){	
		check_installation();	
		
		ob_start();
		
		if(!isset($_SESSION["group"])){
      echo "<p class='ulicms_error'>Um das interne Mailsystem nutzen zu können, 
      müssen Sie sich erst registrieren.</p>";
    }
		else{
       intramail_generate_page();
    }

   
   
		
		
			$html_output = ob_get_clean();
			
			return $html_output;
		
}


function intramail_generate_page(){
  	echo '<a href="?seite='.get_requested_pagename().'&box=inbox">Eingang</a> | 
    <a href="?seite='.get_requested_pagename().'&box=outbox">Gesendet</a> | 
    <a href="?seite='.get_requested_pagename().'&box=new">Mail verfassen</a>
    ';
    switch($_GET["box"]){
    case "new":
    intramail_new_mail();
    break;
    }
}


function intramail_new_mail(){
  if(isset($_POST["submit"]) and
  !empty($_POST["subject"])  and
  !empty($_POST["message"])  and
  in_array($_POST["mail_to"] , getUsers())){
  
    $date = time();
    $mail_from = $_SESSION["ulicms_login"];
    $mail_to = mysql_real_escape_string($_POST["mail_to"]);
    $subject = htmlspecialchars($_POST["subject"]);
    $subject = mysql_real_escape_string($subject);
    $message = strip_tags($_POST["subject"], getconfig("allowed_html"));
    $message = mysql_real_escape_string($message);
      
    echo "<p>Mail wurde Erfolgreich versand!<br/><br/>
    Bitte warten! Sie werden weitergeleitet</p>
    
    <script type='text/javascript'>
    setTimeout('location.replace(\'?seite=".get_requested_pagename()."&box=inbox\')', 2000);
    </script>
    ";
    
    return;
  }
  
  
  echo '<form method="post" action="?seite='.get_requested_pagename().'&box=new">
  
  ';
  
 
  $users = getUsers();
  
  echo '<strong>Empfänger:</strong><br/><select name="mail_to">';
                      
  
  
  for($i=0; $i<count($users); $i++){
    
      echo "<option value='".$users[$i]."'>".$users[$i]."</option>";
      
      
  }
  
  echo "</select>";
  
  echo '<br/><br/>';
  
  echo '<strong>Betreff:</strong><br/><input type="text" name="subject" value="" maxlength=78 size=40>';  
  
  echo '<br/><br/>';
  
  
  
  echo '<textarea name="message" cols=50 rows=15></textarea>';
   
  echo '
  <br/><br/><strong>Folgende HTML-Codes sind erlaubt:</strong><br/>
  '.htmlspecialchars(getconfig("allowed_html")); 

   
  
  echo '<br/><br/>'; 
   
  echo '
  <input type="submit" name="submit" value="Senden">
  </form>';
}

function check_installation(){
	$test = mysql_query("SELECT * FROM ".tbname("messages"));
	if(!$test){
	require_once getModulePath("intramail")."intramail_install.php";
	intramail_install();		
		}	
	}

?>