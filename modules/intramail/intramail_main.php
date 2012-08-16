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
    <a href="?seite='.get_requested_pagename().'&box=outbox">Ausgang</a> | 
    <a href="?seite='.get_requested_pagename().'&box=new">Mail verfassen</a>
    ';
    switch($_GET["box"]){
    case "new":
      intramail_new_mail();
    break;
    case "reply":
      intramail_reply();
    break;
    case 'inbox':
    default:
      intramail_post_inbox();
    break;
    break;
    case 'outbox':
      intramail_post_outbox();
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
   '<tr>
   <td></td>
   <td>'.
    "<a href='?seite=".get_requested_pagename()."&box=reply&message=".$row->id.
      "'>"."Antworten"."</a>"
   . 
   '</td>'.
   '</table>';
   
   mysql_query("UPDATE `".tbname("messages")."` SET `read` = 1 WHERE id = $message_id");
   
  }

}

function intramail_delete_mail($delete){
  mysql_query("DELETE FROM `".tbname("messages").
  "` WHERE id = $delete and (mail_from='".
  $_SESSION["ulicms_login"]."' or mail_to = '".$_SESSION["ulicms_login"]."')"); 
}

function intramail_post_inbox(){

  // Delete Mails
  if(isset($_GET["delete"])){
     $delete = intval($_GET["delete"]);
     intramail_delete_mail($delete);
  }
  
  
  // get all unread messages
  $new_mails_query = mysql_query("SELECT * FROM `".tbname("messages")."` WHERE mail_to='".$_SESSION["ulicms_login"]."' AND `read` = 0");
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
  
  $all_mails = mysql_query("SELECT * FROM `".tbname("messages")."` WHERE mail_to='".$_SESSION["ulicms_login"]."' ORDER by date DESC");
  if(mysql_num_rows($all_mails)>0){
    echo "<ol>";
    while($row = mysql_fetch_object($all_mails)){
      echo "<li>";
      if(!$row->read){
        echo "<strong style='color:red;'>Neu</strong> ";
      }
      echo
      "<a href='?seite=".get_requested_pagename()."&box=inbox&message=".$row->id.
      "'>".$row->subject."</a>"." [".date(getconfig("date_format"), $row->date).
      "] "."[<a href='?seite=".get_requested_pagename().
      "&box=inbox&delete=".$row->id."' onclick='return confirm(\"Diese Mail wirklich löschen?\")'>X</a>"."]".
      "</li>";
    }
    echo "</ol>";
  
  
  }
  
  
  }




function intramail_post_outbox(){

  
  $all_mails = mysql_query("SELECT * FROM `".tbname("messages")."` WHERE mail_from='".$_SESSION["ulicms_login"]."' AND `read` = 0 ORDER by date DESC");
  
  if(mysql_num_rows($all_mails)>0){
    echo "<ol>";
    while($row = mysql_fetch_object($all_mails)){
      echo "<li>";
      if(!$row->read){
        echo "<strong style='color:red;'>Neu</strong> ";
      }
      echo
      "<a href='?seite=".get_requested_pagename()."&box=inbox&message=".$row->id.
      "'>".$row->subject."</a>"." [".date(getconfig("date_format"), $row->date).
      "] ".
      "</li>";
    }
    echo "</ol>";
  
  
  }
  
                    
  }


  function intramail_reply(){
      $message_id = intval($_GET["message"]);

  $message_query = mysql_query("SELECT * FROM `".tbname("messages").
  "` WHERE id = $message_id and (mail_from='".
  $_SESSION["ulicms_login"]."' or mail_to = '".$_SESSION["ulicms_login"]."') LIMIT 1");
  
  while($row = mysql_fetch_object($message_query)){
    $new_subject = $row->subject;
    if(!StartsWith($row->subject,"RE: ")){
      $new_subject = "RE: ". $row->subject;
    }
    
    $message = explode("\n", $row->message);
    for($i=0;$i<count($message);$i++){
           $message[$i] = trim($message[$i]);
         if(strlen($message[$i])>1){
           $message[$i] = "> ". $message[$i];
           }

    }
    $new_message =join("\n", $message);
    
    intramail_new_mail($mail_to = $row->mail_from, $subject = $new_subject, $message = $new_message );
  }



  }


function intramail_new_mail($mail_to = '', $subject = '', $message = ''){
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
    
     $notification_mail="Hallo ".$mail_to.",\n\n".
     "Der Nutzer \"".$mail_from.
     "\" hat dir auf der Website \"".getconfig("homepage_title").
     "\" eine Nachricht geschickt:\n\n".
     strip_tags(str_replace("\\r\\n", "\n", $message))."\n\n".
     "Unter folgendem Link kannst du dich anmelden und die Mail beantworten:\n".
     "http://".$_SERVER["HOST_NAME"];
     

     
     
     
  $header = "From: ".getconfig("email")."\n".
  "Content-type: text/plain; charset=utf-8";
  

   mail($_SESSION["email"],
   "Eine neue Nachricht von ".$mail_from,
   $notification_mail, $header);

      
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
     
     
        echo "<option value='".$users[$i]."'"; 
        
        if(isset($mail_to) and $users[$i ] == $mail_to){
            echo " selected";
        }
        echo ">".$users[$i]."</option>";
        
      
      
  }
  
  echo "</select>";
  
  echo '<br/><br/>';
  
  echo '<strong>Betreff:</strong><br/><input type="text" name="subject" value="'.
  $subject.'" maxlength=78 size=40>';  
  
  echo '<br/><br/>';
  
  
  
  echo '<textarea name="message" cols=50 rows=15>'.
  htmlspecialchars($message).'</textarea>';
   
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