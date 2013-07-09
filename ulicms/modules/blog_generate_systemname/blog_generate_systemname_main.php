<?php 
function blog_generate_systemname_render(){
  $mainFile = getModuleMainFilePath("blog");
  if(file_exists($mainFile)){
     include $mainFile;
     return blog_render();
  }
  
  return "<p class=\"ulicms_error\">$mainFile fehlt!</p>";
}
?>