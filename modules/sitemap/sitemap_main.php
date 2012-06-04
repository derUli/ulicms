<?php
function sitemap_render(){
	$html_output = "";
	$html_output.=sitemap_menu("top");
	$html_output.=sitemap_menu("left");
	$html_output.=sitemap_menu("right");
	$html_output.=sitemap_menu("down");
	$html_output.=sitemap_menu("hidden");
	return $html_output;
}

function sitemap_menu($name){
	$html_output = "";
	$query = mysql_query("SELECT * FROM ".tbname("content")." WHERE menu ='$name' AND active = 1 AND parent='-' ORDER by position");
	
	if(mysql_num_rows($query)<1){
	return "";
	}
	switch($name){
		case "top":
		$menu_in_german = "Oberes Menü";
		break;
		case "left":
		$menu_in_german = "Linkes Menü";
		break;
		case "right":
		$menu_in_german = "Rechtes Menü";
		break;
		case "down":
		$menu_in_german = "Unteres Menü";
		break;
		case "hidden":
		$menu_in_german = "Nicht im Menü";
		break;
	}
	$html_output.="<strong>".$menu_in_german."</strong>";
	$html_output.= "<ul>\n";
	while($row = mysql_fetch_object($query)){
		$html_output.= "<li>\n" ;
		$html_output.= "<a href='?seite=".$row->systemname."'>";
	
		$html_output.=$row->title;

		$html_output.= "</a>\n";
	
		$query2 = mysql_query("SELECT * FROM ".tbname("content")." WHERE active = 1 AND parent='".$row->systemname."' ORDER by position");
			if(mysql_num_rows($query2)>0){
				$html_output.= "<ul>\n";
				while($row2 = mysql_fetch_object($query2)){
					$html_output.= "<li>";
					$html_output.= "<a href='?seite=".$row2->systemname."'>";

				$html_output.=$row2->title;
				$html_output.='</a>';
				$html_output.= "</li>\n";
			}
			$html_output.= "</ul></li>\n";
		}else{
		$html_output.= "</li>\n";
		}
	}

$html_output.= "</ul>\n";
return $html_output;
}