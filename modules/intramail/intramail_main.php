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
}

function check_installation(){
	$test = mysql_query("SELECT * FROM ".tbname("messages"));
	if(!$test){
	require_once getModulePath("intramail")."intramail_install.php";
	intramail_install();		
		}	
	}

?>