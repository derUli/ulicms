<?php 


function fullcalendar_version(){
   return array(0,0,1);
}

function fullcalendar_render(){
   $loading = "Loading...";
   if($_SESSION["language"] === "de")
      $loading = "Laden...";
   return "<div id='loading' style='display:none'>$loading</div>
<div id='fullcalendar'></div>";
}
?>