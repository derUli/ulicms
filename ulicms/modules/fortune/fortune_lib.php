<?php 
function getRandomFortune(){
   $fortuneDir = getModulePath("fortune")."data/";
   $fortuneFiles = scandir($fortuneDir);
   do{
   $file = array_rand($fortuneFiles, 1);
   $file = $fortuneFiles[$file];
   $file = $fortuneDir.$file;
   }while(!is_file($file));
   
   $fileContent = file_get_contents($file);
   $fileContent = trim($fileContent);
   $fileContent = utf8_encode($fileContent);
   $fortunes = explode("%\n", $fileContent);
   $text = array_rand($fortunes, 1);
   $text = $fortunes[$text];
   return $text;
}
?>