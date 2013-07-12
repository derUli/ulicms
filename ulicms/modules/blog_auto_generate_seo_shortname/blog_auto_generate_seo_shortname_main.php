<?php
function blog_auto_generate_seo_shortname_render(){
     $mainFile = getModuleMainFilePath("blog");
     if(file_exists($mainFile)){
         include $mainFile;
         return blog_render();
         }
    
     return "<p class=\"ulicms_error\">$mainFile fehlt!</p>";
     }
?>