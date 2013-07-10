<?php 
function code_highlighting_content_filter($html){
  if(strpos($html, "[code ") === false)
     return $html;
  
  $baseDir = "./content/files/";
  $allContentFiles = find_all_files($baseDir);
  for($l=0; $l < count($allContentFiles); $l++){
     $replaceName = str_replace($baseDir, "", $allContentFiles[$l]);
     $replaceName = str_replace("../", "", $replaceName); 
     $replaceName = trim($replaceName, "/");
     $replaceName = html_entity_decode($replaceName, ENT_QUOTES, "UTF-8");
     $replaceString1 = "[code src=\"".$replaceName."\"]";
     $replaceString2 = "[code src=&quot;".$replaceName."&quot;]";
     $fileContent = highlight_file($allContentFiles[$l], true);
     $html = str_replace($replaceString1, $fileContent, $html);
     $html = str_replace($replaceString2, $fileContent, $html);
  }
  return $html;
}
?>