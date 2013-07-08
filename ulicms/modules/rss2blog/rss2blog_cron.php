<?php 
$srclist = getModulePath("rss2blog")."etc/"."sources.ini";
if(!is_file($srclist))
  die();
  
$srclist = file_get_contents($srclist);
$srclist = str_replace("\r\n", "\n", $srclist);
$srclist = explode("\n", $srclist);
for($i=0; $i < count($srclist); $i++){
  $currentLine = trim($srclist[$i]);
  if(!startsWith($currentLine, "#")
     @$feedXML = file_get_contents_wrapper($currentLine);
     
     if($feedXML)
        echo htmlspecialchars($feedXML);

}
?>