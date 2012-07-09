<?php 
function guestbook_render(){	
		check_installation();	
		

		if(empty($_GET["action"])){
			$action = "list";			
			}		
		else{
			$action = $_GET["action"];
			}
			
			switch($action){
			case "list":
				$html_output = guestbook_list();
			break;
			case "add":
			// validating guestbook entry				
			$html_output = "";			
			if(isset($_POST["gb_submit"])){
				$errors = false;
			if(empty($_POST["gb_name"])){
				$errors = true;
				$html_output .= "<p class='ulicms-error'>Bitte geben Sie einen Namen ein.</p>";
				}
			if(empty($_POST["gb_city"])){
				$errors = true;
				$html_output .= "<p class='ulicms-error'>Bitte geben Sie einen Ort ein.</p>";
				}
				
			
			if(empty($_POST["gb_mail"])){
				$errors = true;
				$html_output .= "<p class='ulicms-error'>Bitte geben Sie ihre Emailadresse ein.</p>";
				}
				
			
			if(!empty($_POST["gb_homepage"])){
				if(!startsWith($_POST["gb_homepage"], "http://")){
					$_POST["gb_homepage"] = "http://".$_POST["gb_homepage"];
					}				
					
				if(endsWith($_POST["gb_homepage"], ".ru") or endsWith($_POST["gb_homepage"], ".info")){
					$errors = true;
			    	$html_output .= "<p class='ulicms-error'>Ihre Homepage hat eine unerlaubte Endung.</p>";
					}					
				}
				
				
			
			if(empty($_POST["gb_content"])){
				$errors = true;
				$html_output .= "<p class='ulicms-error'>Bitte geben Sie eine Nachricht ein.</p>";
				}			
				
			$badwords = file_get_contents(getModulePath("guestbook")."badwords.txt");
			$badwords = explode("\n", $badwords);
			
			for($i=0;$i<count($badwords);$i++){
				
				if(strpos($_POST["gb_content"], $badwords[$i]) !== false){
					$html_output .= "<p class='ulicms-error'>Sie haben ein nicht erlaubtes Wort in Ihrer Nachricht verwendet.</p>";
					break;
					}				
				}
				
			if($_POST["gb_spam_protection"] != "11"){
				$errors = true;
				$html_output .= "<p class='ulicms-error'>Sie haben die Rechenaufgabe nicht richtig gelöst.<br/>
				Sollten Sie unter Dyskalkulie leiden, nehmen Sie bitte einen Taschenrechner zur Hand.				
				</p>";
				}
				
				
				if(!$errors){
					$gb_name = mysql_real_escape_string(
					htmlspecialchars( $_POST["gb_name"]));
					$gb_city = mysql_real_escape_string(htmlspecialchars(
					$_POST["gb_city"]));
					$gb_mail = mysql_real_escape_string(htmlspecialchars(
					$_POST["gb_mail"]));
					$gb_homepage = mysql_real_escape_string(htmlspecialchars(
					$_POST["gb_homepage"]));
    				$gb_content = mysql_real_escape_string(htmlspecialchars($_POST["gb_content"]));
					$date = date("Y-m-d H:i:s");
					$sql = "INSERT INTO ".tbname("guestbook_entries")." (name, ort, email, date, homepage, content)
					VALUES ('$gb_name', '$gb_city', '$gb_mail', '$date', '$gb_homepage', '$gb_content');";		
					mysql_query($sql)or die(mysql_error())	;		
					
					$html_output.=guestbook_list();
					return $html_output;					
					}				
				
				}
			$html_output .= get_add_entry_form();
			break;
			default:
				$html_output = "Invalid Action specified";
			break;
			}
			
		$html_output.="<br/<br/><center><small>Powered by Guestbook Module for UliCMS</small></center>";
		
		return $html_output;
}

function guestbook_get_add_entry_link(){
		return "<a href=\""."?seite=".get_requested_pagename()."&action=add"."\">Eintrag hinzufügen</a>";	
	}
	
	
	
function get_add_entry_form(){
		$add_entry_form_template = file_get_contents(getModulePath("guestbook")."templates/add_entry_from.tpl");
		$add_entry_form_template = str_replace("{form_action_url}",
		"?seite=".get_requested_pagename()."&action=add", $add_entry_form_template);		
		return $add_entry_form_template;		
}

function guestbook_list(){
	
	$html_output = "";
	if(empty($_GET["limit"])){
		$limit = 10;		
		}	
	else{
		$limit = intval($_GET["limit"]);		
		}
		
	$html_output .= guestbook_get_add_entry_link();
	$html_output .= "
	<br/><br/>
	<br/>";
	$entries_query = mysql_query("SELECT * FROM ".tbname("guestbook_entries")." ORDER by date DESC LIMIT $limit");
	
	while($row=mysql_fetch_object($entries_query)){
		$html_output .= "
		<a href=\"".$row->homepage."\">Homepage von ".$row->name."</a>
		<br/><br/>
		<small>".$row->date."</small>
		<br/><br/>
		".$row->content.
		"<br/><br/><em>von ".$row->name." aus ".$row->ort."</em><hr/><br/>";
		
		$last = $row->id;
		
	}
	
	$html_output .="<a id=\"gb_more\"></a>";
	if($limit < $last){
			$new_limit = $limit + 10;
			
			$html_output .= "<a href='?seite=".get_requested_pagename()."&action=list&limit=$new_limit#gb_more'>"."Mehr</a>";
		}
	
	
	
	return $html_output;
	}




function check_installation(){
	$test = mysql_query("SELECT * FROM ".tbname("guestbook_entries"));
	if(!$test){
	require_once getModulePath("guestbook")."guestbook_install.php";
	guestbook_install();		
		}	
	}

?>