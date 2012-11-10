<?php

function language_selection(){
	$query=mysql_query("SELECT * FROM ".tbname("languages")." ORDER by name");
	echo "<ul class='language_selection'>";
	while($row = mysql_fetch_object($query)){
	    echo "<li>"."<a href='?seite=".get_requested_pagename()."&language=".$row->language_code."'>".$row->name."</a></li>";
	}
		echo "</ul>";

}

function random_banner(){
	$connection=MYSQL_CONNECTION;
	$query=mysql_query("SELECT * FROM ".tbname("banner")." ORDER BY RAND() LIMIT 1");
	if(mysql_num_rows($query)>0){
		while($row=mysql_fetch_object($query)){

			$title=$row->name;
			$link_url=$row->link_url;
			$image_url=$row->image_url;
			echo "<a href='$link_url' target='_blank'><img src='$image_url' title='$title' alt='$title' border=0></a>";
	}

}

}


function logo(){
  if(!getconfig("logo_image")){
    setconfig("logo_image", "");
  }
  if(!getconfig("logo_disabled")){
    setconfig("logo_disabled", "no");
  }
  
  $logo_path = "content/images/".getconfig("logo_image");
  
  if(getconfig("logo_disabled") == "no" and file_exists($logo_path)){
    echo '<img class="website_logo" src="'.$logo_path.'" alt="'.getconfig("homepage_title").'"/>';
  }
  
}




function year(){
	echo date("Y");
}

function homepage_owner(){
	print_env("homepage_owner");
}


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



function homepage_title(){
	print_env("homepage_title");
}



	$status=check_status();





function meta_keywords($ipage=null){
	$status=check_status();	
	$connection=MYSQL_CONNECTION;
	$ipage=mysql_real_escape_string($_GET["seite"]);
	$query=mysql_query("SELECT * FROM ".tbname("content")." WHERE systemname='$ipage'",$connection);
	if($ipage==""){
		$query=mysql_query("SELECT * FROM ".tbname("content")." ORDER BY id LIMIT 1",$connection);
	}
	if(mysql_num_rows($query)>0){
		while($row=mysql_fetch_object($query)){
		  if(!empty($row->meta_keywords)){
			 return $row->meta_keywords;
			}
		}


	}
	
	
	return false;
}



function meta_description($ipage=null){
	$status=check_status();	
	$connection=MYSQL_CONNECTION;
	$ipage=mysql_real_escape_string($_GET["seite"]);
	$query=mysql_query("SELECT * FROM ".tbname("content")." WHERE systemname='$ipage'",$connection);
	if($ipage==""){
		$query=mysql_query("SELECT * FROM ".tbname("content")." ORDER BY id LIMIT 1",$connection);
	}
	if(mysql_num_rows($query)>0){
		while($row=mysql_fetch_object($query)){
		  if(!empty($row->meta_description)){
			 return $row->meta_description;
			}
		}


	}
	
	
	return false;
}






function title($ipage=null){
	$status=check_status();
	if($status=="404 Not Found"){
		echo "Seite nicht gefunden";
		return false;
	}else if($status=="403 Forbidden"){
		echo "Zugriff verweigert";
	return false;
  }
	
	$connection=MYSQL_CONNECTION;
	$ipage=mysql_real_escape_string($_GET["seite"]);
	$query=mysql_query("SELECT * FROM ".tbname("content")." WHERE systemname='$ipage'",$connection);
	if($ipage==""){
		$query=mysql_query("SELECT * FROM ".tbname("content")." ORDER BY id LIMIT 1",$connection);
	}
	if(mysql_num_rows($query)>0){
		while($row=mysql_fetch_object($query)){
			echo $row->title;
			return true;
		}


	}
}



//import and print a page();
function import($ipage){
	$connection=MYSQL_CONNECTION;
	$ipage=mysql_real_escape_string($ipage);
	if($ipage==""){                                                          
		$query=mysql_query("SELECT * FROM ".tbname("content")." ORDER BY id LIMIT 1",$connection);
	
	}
	else{
		$query=mysql_query("SELECT * FROM ".tbname("content")." WHERE systemname='$ipage'",$connection);
	}

	if(mysql_num_rows($query)==0){
		return false;
	}else{

	while($row=mysql_fetch_object($query)){
	
		$row->content = replaceShortcodesWithModules($row->content);
		echo $row->content;
		return true;
}

}

}


function motto(){
  print_env("motto");
}


function get_requested_pagename(){
  $value = mysql_real_escape_string($_GET["seite"]);
  if($value == ""){
    $value = getconfig("frontpage");
  }
  return $value;
}

function is_frontpage(){
  return get_requested_pagename() === getconfig("frontpage");
}



function is_200(){
  return(check_status("200 OK"));
}

function is_404(){
  return(check_status("404 Not Found"));
}

function is_403(){
  return(check_status("404 Forbidden"));
}

function menu($name){
        $language = $_SESSION["language"];
	$query = mysql_query("SELECT * FROM ".tbname("content")." WHERE menu ='$name' AND language = '$language' AND active = 1 AND parent='-' ORDER by position");
	echo "<ul class='menu_".$name."'>\n";
	while($row = mysql_fetch_object($query)){
	echo "  <li>" ;
	if(get_requested_pagename() != $row->systemname){
		echo "<a href='?seite=".$row->systemname."' target='".
		$row->target."'>";
	}else{
		echo "<a class='menu_active_link' href='?seite=".$row->systemname.
		"' target='".$row->target."'>";
	}


	echo $row->title;

	echo "</a>\n";
	
	// Unterebene 1
	$query2 = mysql_query("SELECT * FROM ".tbname("content")." WHERE active = 1 AND language = '$language' AND parent='".$row->systemname."' ORDER by position");
		if(mysql_num_rows($query2)>0){
			echo "<ul class='sub_menu'>\n";
			while($row2 = mysql_fetch_object($query2)){
				echo "      <li>";
				if(get_requested_pagename() != $row2->systemname){ 
					echo "<a href='?seite=".$row2->systemname."' target='".
					$row->target."'>";
				}else{
					echo "<a class='menu_active_link' href='?seite=".$row2->systemname."' target='".
					$row->target."'>";
				}
				echo $row2->title;
				echo '</a>';
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				// Unterebene 2
				$query3 = mysql_query("SELECT * FROM ".tbname("content")." WHERE active = 1 AND language = '$language' AND parent='".$row2->systemname."' ORDER by position");
		if(mysql_num_rows($query3)>0){
			echo "  <ul class='sub_menu'>\n";
			while($row3 = mysql_fetch_object($query3)){
				echo "      <li>";
				if(get_requested_pagename() != $row3->systemname){ 
					echo "<a href='?seite=".$row3->systemname."' target='".
					$row3->target."'>";
				}else{
					echo "<a class='menu_active_link' href='?seite=".$row3->systemname."' target='".
					$row3->target."'>";
				}
				echo $row3->title;
				echo '</a>';
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				// Unterebene 3
				$query4 = mysql_query("SELECT * FROM ".tbname("content")." WHERE active = 1 AND language = '$language' AND parent='".$row3->systemname."' ORDER by position");
		if(mysql_num_rows($query4)>0){
			echo "  <ul class='sub_menu'>\n";
			while($row4 = mysql_fetch_object($query4)){
				echo "<li>";
				if(get_requested_pagename() != $row4->systemname){ 
					echo "<a href='?seite=".$row4->systemname."' target='".
					$row4->target."'>";
				}else{
					echo "<a class='menu_active_link' href='?seite=".$row4->systemname."' target='".
					$row4->target."'>";
				}
				echo $row4->title;
				echo '</a>';
				echo "</li>\n";
			}
			echo "  </ul></li>\n";
		}else{
		}
				
				

				

	
		
			
				
					
						
							
								
									
										
											
												
													
														
															
																
																	
																		
																			
																				
																					
																						
																							
																								
										
										
										
										
										
										
										
										
										
										
										
										
										
										
										
									
								
				echo "</li>\n";
			}
			echo "  </ul></li>\n";
		}else{
		echo "</li>\n";
		}
				
			}
			echo "  </ul></li>\n";
		}else{
		echo "</li>\n";
		}
	}


















	


echo "</ul>\n";

}




function base_metas(){
	
	$dir=dirname($_SERVER["SCRIPT_NAME"]);
	$dir = str_replace("\\","/", $dir);
	
	if(endsWith($dir,"/")==false){
		$dir.="/";
	}

	if(!getconfig("hide_rss_link")){
		echo '<link href="'."http://".$_SERVER["HTTP_HOST"].$dir.'?rss=rss" rel="alternate" type="application/rss+xml" title="RSS 2.0" />';
		echo "\r\n";
	}

	if(!getconfig("hide_meta_generator")){
		echo '<meta name="generator" content="UliCMS Release '.cms_version()
		.'" />';
		echo "\r\n";

	$facebook_id = getconfig("facebook_id");
	
	if(!empty($facebook_id)){
		echo '<meta property="fb:admins" content="'.$facebook_id.'"/>';
	}
	echo "\r\n";
	}
	
	$keywords = meta_keywords();
	if(!$keywords){
	 $keywords = getconfig("meta_keywords");
	}
	if($keywords!=""&&$keywords!=false){
		
		if(!getconfig("hide_meta_keywords")){
			echo '<meta name="keywords" content="'.$keywords.'"/>';
			echo "\r\n";
		}
	}
	$description = meta_description();
  if(!$description){
    $description = getconfig("meta_description");
	}
	if($description!="" && $description!=false){
		if(!getconfig("hide_meta_description")){
			echo '<meta name="description" content="'.$description.'"/>';
			echo "\r\n";
		}
	}

}







function autor(){
	$connection=MYSQL_CONNECTION;
	$seite=$_GET["seite"];
	if(empty($seite)){
		$query = mysql_query("SELECT * FROM ".tbname("content")." ORDER BY id LIMIT 1");
		$result = mysql_fetch_object($query);
		$seite = $result->systemname;
	}
	
	if(check_status() != "200 OK"){
			return;
		}
		
	$query=mysql_query("SELECT * FROM ".tbname("content")." WHERE systemname='".mysql_real_escape_string($seite)."'",$connection);
	if(mysql_num_rows($query)<1){
		return;
	}
	$result=mysql_fetch_array($query);
	if($result["systemname"]=="kontakt"||$result["systemname"]=="impressum"||StartsWith($result["systemname"],"menu_")){
		return;
	}
	$query2=mysql_query("SELECT * FROM ".tbname("admins")." WHERE id=".$result["autor"],$connection);
	$result2=mysql_fetch_array($query2);
	if(mysql_num_rows($query2)==0){
		return;
	}
	$datum=date(getconfig("date_format"),$result["created"]);
	$out=getconfig("autor_text");
	$out=str_replace("Vorname", $result2["firstname"],$out);
	$out=str_replace("Nachname", $result2["lastname"],$out);       
	$out=str_replace("Username", $result2["username"],$out);
	$out=str_replace("Datum", $result2["datum"],$out);
	if(!is_403() or $_SESSION["group"]>=20){
	   echo $out;
	}
}

/*
function news(){
	$connection=MYSQL_CONNECTION;
	$max=(int)getconfig("max_news");
	if($max==false || is_nan($max)){
		$max=5;
	}
	$news_template=file_get_contents("templates/news.txt");
	
	$query=mysql_query("SELECT * FROM ".tbname("news")." ORDER BY date DESC LIMIT $max",$connection);
	while($row=mysql_fetch_object($query)){
		$out=$news_template;
		$content = replaceShortcodesWithModules($row->content);
		$query2=mysql_query("SELECT * FROM ".tbname("admins")." WHERE id=".$row->autor);
		$result2=mysql_fetch_object($query2);
		$out=str_replace("{datum}",date(getconfig("date_format"),$row->date),$out);
		$out=str_replace("{titel}",$row->title,$out);
		$out=str_replace("{id}",$row->id,$out);
		$out=str_replace("{text}",$content,$out);
		$out=str_replace("{autor}",$result2->firstname." ".$result2->lastname,$out);
		echo $out;
	}
}

*/



function content(){
	$status=check_status();
	if($status=="404 Not Found"){
		echo "Die von Ihnen gew&uuml;nschte Seite existiert nicht.";
		return false;
	}else if($status=="403 Forbidden"){
		echo "Sie verfügen nicht über die erforderlichen Rechte um auf diese Seite zugreifen zu können.";
		return false;
	}
	

	mysql_query("UPDATE ".tbname("content")." SET views = views + 1 WHERE systemname='".$_GET["seite"]."'");
	return import($_GET["seite"]);
	
}




function check_status(){
	if($_GET["seite"]==""){
		$_GET["seite"] = getconfig("frontpage");
	}
	
	$connection=MYSQL_CONNECTION;
	$test=mysql_query("SELECT * FROM `".tbname("content")."` WHERE systemname='".mysql_real_escape_string($_GET["seite"])."'");
	
	if(mysql_num_rows($test)==0){
		return "404 Not Found";
	}else{
		$test_array=mysql_fetch_array($test);
		
		// Prüfe, ob der Nutzer die Berechtigung zum Zugriff auf die Seite hat.	
		if($test_array["active"]==1 or $_SESSION["group"] >= 20){
		
			$access = explode(",",$test_array["access"]);
			
			$permitted = false;
			
			if(in_array("all", $access)){
				$permitted = true;
			}
			if(in_array("admin", $access) and $_SESSION["group"] >= 50){
				$permitted = true;
			}
			
			if(in_array("registered", $access) and $_SESSION["group"] >= 10){
				$permitted = true;
			}
		
			if($permitted){
		
			if($test_array["redirection"]!=""){
				header("Location: ".$test_array["redirection"]);
				exit();
			}
			return "200 OK";
			
			}
			else{
				return "403 Forbidden";
			}
		}
		else{
			return "403 Forbidden";
		}
		
	}

}

?>
