<?php 
function fullcalendar_dashboard_render(){
   $mainFile = getModuleMainFilePath("fullcalendar");
   if(file_exists($mainFile)){
     include $mainFile;
     return blog_render();
   }
  
   return "<p class=\"ulicms_error\">$mainFile fehlt!</p>";
}
?>
