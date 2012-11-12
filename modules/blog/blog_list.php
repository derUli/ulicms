<?php 
function blog_list(){
   
   $html = "";
   
   // Wenn der Nutzer mindestens die Berechtigungen
   // eines Mitarbeiters hat, bekommt er den Link zum 
   // Anlegen eines neuen Blogbeitrag angezeigt
   
   if($_SESSION["group"] >= 20){
   $html .= "<p><a href='?seite=".
   get_requested_pagename().
   "&blog_admin=add'>Blogbeitrag einreichen</a><hr/></p>";
   }

   $html.="<p><strong>Hier kommt dann die News-Liste mit den Vorschautexten</strong></p>";

   return $html;

}
?>