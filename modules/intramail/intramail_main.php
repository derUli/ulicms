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
    case 'inbox':
    default:
      intramail_post_inbox();
    break;
    break;
    
    }
}


function intramail_view_message(){
  $message_id = intval($_GET["message"]);

  $message_query = mysql_query("SELECT * FROM `".tbname("messages").
  "` WHERE id = $message_id and (mail_from='".
  $_SESSION["ulicms_login"]."' or mail_to = '".$_SESSION["ulicms_login"]."') LIMIT 1");
  
  while($row = mysql_fetch_object($message_query)){
   echo '<table style="border:0px;">
   <tr>
   <td><strong>Von:</strong></td>
   <td>'.$row->mail_from.'</td>
   </tr>'. 
   '<tr>
   <td><strong>An:</strong></td>
   <td>'.$row->mail_to.'</td>
   </tr>'
   . 
   '<tr>
   <td><strong>Betreff:</strong></td>
   <td>'.$row->subject.'</td>
   </tr>'. 
   '<tr>
   <td><strong>Datum:</strong></td>
   <td>'.date(getconfig("date_format"), $row->date).'</td>
   </tr>
   '.
   '<tr>
   <td>
   <br/><br/> 
   </td>
   <td>'.nl2br($row->message).'</td>
   </tr>'.
   '</table>';
   
  }

}

function intramail_post_inbox(){
  // get all unread messages
  $new_mails_query = mysql_query("SELECT * FROM `".tbname("messages")."` WHERE mail_to='".$_SESSION["ulicms_login"]."' AND `read`=0");
  $new_mails_count = mysql_num_rows($new_mails_query);
  if(isset($_GET["message"])){
     intramail_view_message();
     return;
  }
  
  // Output new mails count
  if($new_mails_count == 1){
     echo "<p style='color:red;font-size:1.3em;'>Sie haben eine neue Nachricht.</p>";
  }
  else if($new_mails_count>1){
    echo "<p style='color:red;font-size:1.3em;'>Sie haben <strong>$new_mails_count</strong> neue Nachrichten.</p>";

  }
  
  $all_mails = mysql_query("SELECT * FROM `".tbname("messages")."` WHERE mail_to='".$_SESSION["ulicms_login"]."' AND `read`=0 ORDER by date DESC");
  if(mysql_num_rows($all_mails)>0){
    echo "<ol>";
    while($row = mysql_fetch_object($all_mails)){
      echo "<li>".
      "<a href='?seite=".get_requested_pagename()."&box=inbox&message=".$row->id.
      "'>".$row->subject."</a>"." [".date(getconfig("date_format"), $row->date).
      "]"."</li>";
    }
    echo "</ol>";
  
  
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
    $message = strip_tags($_POST["message"], getconfig("allowed_html"));
    $message = mysql_real_escape_string($message);
    
    mysql_query("INSERT INTO  `".tbname("messages")."` (mail_from, mail_to, subject,
    message, date, `read`) 
    VALUES ('$mail_from', '$mail_to', '$subject', '$message', $date, 0)");
      
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