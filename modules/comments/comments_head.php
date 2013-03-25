<?php
function comments(){
	$status=check_status();
	if($status != "200 OK"){
		return;
	}

	$connection=MYSQL_CONNECTION;


	$ipage=mysql_real_escape_string($_GET["seite"]);
	$query=mysql_query("SELECT * FROM ".tbname("content")." WHERE systemname='$ipage'", $connection);
	$dataset = mysql_fetch_array($query);


	if($dataset["systemname"]=="impressum" || $dataset["systemname"] == "kontakt"){
		return;
	}
	
	if(getconfig("comment_mode") == "off"){
		return true;
	}

	$mode = getconfig("comment_mode");


	echo "<div class='ulicms_comments'>";
		if($dataset["comments_enabled"] == 0){
			if(!getconfig("hide_comments_are_closed")){
			echo "<p>Kommentare sind deaktiviert</p>";
			}
		}
		else if($mode == "facebook"){
			require_once "comments/facebook.php";
		}
		else if($mode == "off"){
			echo "<p>Kommentare sind deaktiviert</p>";
		}
		else if($mode == "disqus" && getconfig("disqus_id") != ""){
			require_once "comments/disqus.php";
		}
		else{
			echo "<p>Interner Fehler.<br/><br/>Eventuell ist die ID f&uuml;r das Kommentarsystem nicht richtig gesetzt?</p>";
		}

		echo "</div>";

		}
?>