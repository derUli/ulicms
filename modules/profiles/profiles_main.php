<?php


function profiles_render(){
  ob_start();
  
  if(!isset($_GET["profile"])){
    profile_list();
  }
  else{
    single_profile($_GET["profile"]);
  }
	
  $html_output = ob_get_clean();
	
  return $html_output;
		
		}
		
// show a user profile
function single_profile(){
  $data = getUserByName($_GET["profile"]);
  echo '<h3>'.$data["username"].'</h3>';
  echo "<p>";
  if($data["avatar_file"]){
    echo '<img src="content/avatars/'.$data["avatar_file"].'"><br/><br/>';
    
  }
  
  if($data["icq_id"]){ 
 

    // Werte von 1 bis 10 für verschiedene Grafiken
    $image = 5;
    
    // Hier die ICQ-Nummer eintragen
    $icq = $data["icq_id"];
    
    // Der Text beim Ueberfahren mit der Maus
    $imagetext = 'ICQ User: ';
    
    // Die Aktion die ausgeführt werden soll
    // About: Zeigt Informationen zum ICQ-Nutzer
    // Add: ICQ-Nutzer als Kontakt hinzufuegen
    // Message: Nachricht an ICQ-Nutzer senden
    // Leere Veriable: Es wird nur die Grafik angezeigt
    $todo = 'Add';
    
    echo "<strong>ICQ:</strong> ".$data["icq_id"]." ";
    echo icqStatus ( $icq, $imagetext, $image, $todo );
    echo "<br/><br/>";
    

  }
  
   if($data["skype_id"]){
      echo "<strong>Skype:</strong> ".$data["skype_id"]." ";
      echo '<a href="skype:'.$data["skype_id"].'?call"><img src="http://download.skype.com/share/skypebuttons/buttons/call_blue_white_124x52.png" style="border: none;" width="124" height="52" alt="Skype Me™!" /></a>
<br /><a href="http://www.skype.com/go/download">Get Skype</a> and call me for free.<br /><br />';

   } 
   echo "</p>";
    if($data["about_me"]){
      echo "<h3>Über mich</h3>";
      echo "<p>";
      echo nl2br(htmlspecialchars($data["about_me"]));
      echo "</p>";
    }
  
  
}


// output icq status
function icqStatus ( $icq, $imagetext, $image = 1, $todo ) {
    
    $url = 'http://www.icq.com/whitepages/';
    
    $status = 'http://status.icq.com/online.gif';
    
    $generateLink = true;
    
    switch ( $todo ) {
        case "Add":
            $url .= 'cmd.php?uin='.$icq.'&action=add';
            break;
        case "Message":
            $url .= 'cmd.php?uin='.$icq.'&action=message';
            break;
        case "About":
            $url .= 'about_me.php?Uin='.$icq;
            break;
        default:
            $generateLink = false;
    }
    
    
    $title = 'title="' . $imagetext . $icq . '"';
    
    $connect = '';
    
    if ( $generateLink )
        $connect .= '<a ' . $title . ' href="'.$url.'">';
    
    $connect.= '<img src="' . $status . '?icq=' . 
                $icq . '&img=' .$image . '" border="0">';
    
    if ( $generateLink )
        $connect.= '</a>';
    
    return $connect;
    
}

		
		
function profile_list(){
  $users = getUsers();
  echo "<ol>";
  for($i=0; $i<count($users); $i++){
     $data = getUserByName($users[$i]);
     echo "<li>".'<a href="?seite='.get_requested_pagename()."&profile=".$data["username"].'">'.$data["username"]."</a></li>";
  }
}
		
?>