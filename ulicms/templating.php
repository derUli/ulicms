<?php

function language_selection(){
	$query=db_query("SELECT * FROM ".tbname("languages")." ORDER by name");
	echo "<ul class='language_selection'>";
	while($row = mysql_fetch_object($query)){
	    echo "<li>"."<a href='".get_requested_pagename().".html?language=".$row->language_code."'>".$row->name."</a></li>";
	}
		echo "</ul>";

}

function random_banner(){
	$connection=MYSQL_CONNECTION;
	$query=db_query("SELECT * FROM ".tbname("banner")." ORDER BY RAND() LIMIT 1");
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
	echo getconfig("homepage_owner");
}


function homepage_title(){
	echo getconfig("homepage_title");
}



$status = check_status();




function meta_keywords($ipage=null){
	$status=check_status();	
	$connection=MYSQL_CONNECTION;
	$ipage=mysql_real_escape_string($_GET["seite"]);
	$query=db_query("SELECT * FROM ".tbname("content")." WHERE systemname='$ipage'");

	if(mysql_num_rows($query)>0){
		while($row=mysql_fetch_object($query)){
		  if(!empty($row->meta_keywords)){
		     // & durch &amp; ersetzen, damit der W3C Validator nicht meckert.
	        $row->meta_keywords = preg_replace( "/&(?!amp;)/", "&amp;",  $row->meta_keywords);
			 return $row->meta_keywords;
			}
		}
	}
	
	return getconfig("meta_keywords");
}

function meta_description($ipage=null){
	$status=check_status();	
	$connection=MYSQL_CONNECTION;
	$ipage=mysql_real_escape_string($_GET["seite"]);
	$query=db_query("SELECT * FROM ".tbname("content")." WHERE systemname='$ipage'",$connection);
	if($ipage==""){
		$query=db_query("SELECT * FROM ".tbname("content")." ORDER BY id LIMIT 1",$connection);
	}
	if(mysql_num_rows($query)>0){
		while($row=mysql_fetch_object($query)){
		  if(!empty($row->meta_description)){
		  // & durch &amp; ersetzen, damit der W3C Validator nicht meckert.
	        $row->meta_description = preg_replace( "/&(?!amp;)/", "&amp;",  $row->meta_description);
			 return $row->meta_description;
			}
		}


	}
	
	
	return getconfig("meta_description");
}


function get_title($ipage=null){
	$status=check_status();
	if($status=="404 Not Found"){
		return "Seite nicht gefunden";
	}else if($status=="403 Forbidden"){
		return "Zugriff verweigert";
  }
	
	$connection=MYSQL_CONNECTION;
	$ipage = mysql_real_escape_string($_GET["seite"]);
	$query=db_query("SELECT * FROM ".tbname("content")." WHERE systemname='$ipage'",$connection);
	if($ipage==""){
		$query=db_query("SELECT * FROM ".tbname("content")." ORDER BY id LIMIT 1");
	}
	if(mysql_num_rows($query)>0){
		while($row=mysql_fetch_object($query)){
		        $row->title =  apply_filter($row->title, "title");
		        $row->title = real_htmlspecialchars($row->title);
		        // & durch &amp; ersetzen, damit der W3C Validator nicht meckert.
	        $row->title = preg_replace( "/&(?!amp;)/", "&amp;",  $row->title);
			return $row->title;
		}
	}
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
	$query=db_query("SELECT * FROM ".tbname("content")." WHERE systemname='$ipage'",$connection);
	if($ipage==""){
		$query=db_query("SELECT * FROM ".tbname("content")." ORDER BY id LIMIT 1",$connection);
	}
	if(mysql_num_rows($query)>0){
		while($row=mysql_fetch_object($query)){
		        $row->title =  apply_filter($row->title, "title");
			echo $row->title;
			return true;
		}
	}
}

function import($ipage){
	$connection=MYSQL_CONNECTION;
	$ipage=mysql_real_escape_string($ipage);
	if($ipage==""){                                                          
		$query=db_query("SELECT * FROM ".tbname("content")." ORDER BY id LIMIT 1");
	
	}
	else{
		$query=db_query("SELECT * FROM ".tbname("content")." WHERE systemname='$ipage'");
	}

	if(mysql_num_rows($query)==0){
		return false;
	}else{

	while($row=mysql_fetch_object($query)){
		$row->content = replaceShortcodesWithModules($row->content);
		
	        $row->content = apply_filter($row->content, "content");
	       
	        // & durch &amp; ersetzen, damit der W3C Validator nicht meckert.
	        $row->content = preg_replace( "/&(?!amp;)/", "&amp;",  $row->content);

		echo $row->content;
		return true;
}

}

}

function apply_filter($text, $type){
  $modules = getAllModules();
  for($i=0; $i < count($modules); $i++){
    $module_content_filter_file = getModulePath($modules[$i]).
    $modules[$i]."_".$type."_filter.php";
    if(file_exists($module_content_filter_file)){
       @include $module_content_filter_file;
     
       if(function_exists($modules[$i]."_".$type."_filter")){
            $text = call_user_func($modules[$i]."_".$type."_filter", 
                                      $text);
       }
     
    }
  
  
  return $text;
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
  return check_status() == "200 OK";
}

function is_404(){
  return check_status() == "404 Not Found";
}

function is_403(){
  return check_status() == "403 Forbidden";
}

function menu($name){
    $language = $_SESSION["language"];
	$query = db_query("SELECT * FROM ".tbname("content")." WHERE menu='$name' AND language = '$language' AND active = 1 AND `deleted_at` IS NULL AND parent IS NULL ORDER by position");
	echo "<ul class='menu_".$name."'>\n";
	while($row = mysql_fetch_object($query)){
	echo "  <li>" ;
	if(get_requested_pagename() != $row->systemname){
		echo "<a href='".$row->systemname.".html' target='".
		$row->target."'>";
	}else{
		echo "<a class='menu_active_link' href='".$row->systemname.
		".html' target='".$row->target."'>";
	}
	
	echo $row->title;
	echo "</a>\n";
	
	// Unterebene 1

	$query2 = db_query("SELECT * FROM ".tbname("content")." WHERE active = 1 AND language = '$language' AND `deleted_at` IS NULL AND parent=".$row->id." ORDER by position");
	
		if(mysql_num_rows($query2)>0){
			echo "<ul class='sub_menu'>\n";
			while($row2 = mysql_fetch_object($query2)){
			
				echo "      <li>";
				if(get_requested_pagename() != $row2->systemname){ 
					echo "<a href='".$row2->systemname.".html' target='".
					$row->target."'>";
				}else{
					echo "<a class='menu_active_link' href='".$row2->systemname.".html' target='".
					$row->target."'>";
				}
				echo $row2->title;
				echo '</a>';
				
				// Unterebene 2
				$query3 = db_query("SELECT * FROM ".tbname("content")." WHERE active = 1 AND language = '$language' AND parent=".$row2->id." AND `deleted_at` IS NULL ORDER by position");
		if(mysql_num_rows($query3)>0){
			echo "  <ul class='sub_menu'>\n";
			while($row3 = mysql_fetch_object($query3)){
				echo "      <li>";
				if(get_requested_pagename() != $row3->systemname){ 
					echo "<a href='".$row3->systemname.".html' target='".
					$row3->target."'>";
				}else{
					echo "<a class='menu_active_link' href='".$row3->systemname.".html' target='".
					$row3->target."'>";
				}
				echo $row3->title;
				echo '</a>';
				
				// Unterebene 3
				$query4 = db_query("SELECT * FROM ".tbname("content")." WHERE active = 1 AND `deleted_at` IS NULL AND language = '$language' AND parent=".$row3->id." ORDER by position");
		if(mysql_num_rows($query4)>0){
			echo "  <ul class='sub_menu'>\n";
			while($row4 = mysql_fetch_object($query4)){
				echo "<li>";
				if(get_requested_pagename() != $row4->systemname){ 
					echo "<a href='".$row4->systemname.".html' target='".
					$row4->target."'>";
				}else{
					echo "<a class='menu_active_link' href='".$row4->systemname.".html' target='".
					$row4->target."'>";
				}
				echo $row4->title;
				echo '</a>';
				echo "</li>\n";
			}
			echo "  </ul></li>\n";
		}

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
	
        $title_format = getconfig("title_format");
        if($title_format){
           $title = $title_format;
           $title = str_ireplace("%homepage_title%", 
           getconfig("homepage_title"), $title);
           
           $title = str_ireplace("%title%", 
           get_title(), $title);
           
           $title = htmlspecialchars($title, ENT_QUOTES, "UTF-8");
           
           echo "<title>".$title."</title>\r\n";
           
        }
	
	$dir = dirname($_SERVER["SCRIPT_NAME"]);
	$dir = str_replace("\\","/", $dir);
	
	if(endsWith($dir,"/")==false){
		$dir.="/";
	}


	if(getconfig("robots")){
	   echo '<meta name="robots" content="'.getconfig("robots").'"/>';
	   echo "\r\n";
	}


	if(!getconfig("hide_meta_generator")){
		echo '<meta name="generator" content="UliCMS Release '.cms_version()
		.'" />';
		echo "\r\n";
		
		

	$facebook_id = getconfig("facebook_id");
	
	if(!empty($facebook_id)){
		echo '<meta property="fb:admins" content="'.$facebook_id.'"/>';
	        echo "\r\n";
	}
	
	}
	
	echo '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>';
	echo "\r\n";
	
	
	$keywords = meta_keywords();
	if(!$keywords){
	 $keywords = getconfig("meta_keywords");
	}
	if($keywords!=""&&$keywords!=false){
		
		if(!getconfig("hide_meta_keywords")){
	                $keywords = apply_filter($keywords, "meta_keywords");
			echo '<meta name="keywords" content="'.$keywords.'"/>';
			echo "\r\n";
		}
	}
	$description = meta_description();
  if(!$description){
    $description = getconfig("meta_description");
  }
	if($description!="" && $description != false){
	        $description = apply_filter($description, "meta_description");
		if(!getconfig("hide_meta_description")){
			echo '<meta name="description" content="'.$description.'"/>';
			echo "\r\n";
		}
	}




echo '<link rel="stylesheet" type="text/css" href="core.css"/>';
echo "\r\n";

add_hook("head");

$zoom = getconfig("zoom");
if($zoom === false){
  setconfig("zoom", 100);
  $zoom = 100;
}
  
if(!getconfig("disable_custom_layout_options")){
echo "
<style type=\"text/css\">
body{
zoom:".$zoom."%;
font-family:".getconfig("default-font").";
font-size:".getconfig("font-size")."pt;
background-color:".getconfig("body-background-color").";
color:".getconfig("body-text-color").";
}
</style>";
}


}







function autor(){
	$connection = MYSQL_CONNECTION;
	$seite = $_GET["seite"];
	if(empty($seite)){
		$query = db_query("SELECT * FROM ".tbname("content")." ORDER BY id LIMIT 1");
		$result = mysql_fetch_object($query);
		$seite = $result->systemname;
	}
	
	if(check_status() != "200 OK"){
			return;
		}
		
	$query = db_query("SELECT * FROM ".tbname("content")." WHERE systemname='".mysql_real_escape_string($seite)."'",$connection);
	if(mysql_num_rows($query)<1){
		return;
	}
	$result=mysql_fetch_array($query);
	if($result["systemname"]=="kontakt"||$result["systemname"]=="impressum"||StartsWith($result["systemname"],"menu_")){
		return;
	}
	$query2=db_query("SELECT * FROM ".tbname("admins")." WHERE id=".$result["autor"],$connection);
	$result2=mysql_fetch_array($query2);
	if(mysql_num_rows($query2)==0){
		return;
	}
	$datum = date(getconfig("date_format"),$result["created"]);
	$out = getconfig("autor_text");
	$out = str_replace("Vorname", $result2["firstname"],$out);
	$out = str_replace("Nachname", $result2["lastname"],$out);       
	$out = str_replace("Username", $result2["username"],$out);
	$out = str_replace("Datum", $result2["datum"],$out);
	if(!is_403() or $_SESSION["group"]>=20){
	   echo $out;
	}
}

function content(){
	$status=check_status();
	if($status == "404 Not Found"){
                if(file_exists("templates/404.php"))
                   include "templates/404.php";
                else
                   echo "Die von Ihnen gew&uuml;nschte Seite existiert nicht.";
		return false;
	}else if($status == "403 Forbidden"){
	        if(file_exists("templates/403.php"))
	           include "templates/403.php";
          else
              echo "Sie verfügen nicht über die erforderlichen Rechte um auf diese Seite zugreifen zu können.";
		return false;
	}


        if(!is_logged_in())
           db_query("UPDATE ".tbname("content")." SET views = views + 1 WHERE systemname='".$_GET["seite"]."'");
	return import($_GET["seite"]);
}


function check_status(){
	if($_GET["seite"]==""){
		$_GET["seite"] = getconfig("frontpage");
	}
	
        $page = $_GET["seite"];
        $cached_page_path = buildCacheFilePath($page);
	
	if(file_exists($cached_page_path)){
           $last_modified = filemtime($cached_page_path);
           if(time() - $last_modified < CACHE_PERIOD){  
              return "200 OK";
           }
	}
	
	$connection = MYSQL_CONNECTION;
	$test = db_query("SELECT * FROM `".tbname("content")."` WHERE systemname='".mysql_real_escape_string($_GET["seite"])."'");
	if(mysql_num_rows($test) == 0){
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
			if($test_array["deleted_at"] != null){
                           return "404 Not Found";
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
