<?php 
function search_title_filter($title){
   if(!empty($_GET["q"])){
      return "Suchergebnisse für \"".htmlspecialchars($_GET["q"], ENT_QUOTES, "UTF-8")."\"";
	  }
   else{
      return $title;
   }
}
?>