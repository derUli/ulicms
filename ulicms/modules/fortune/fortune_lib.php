<?php
function getRandomFortune(){
     if (is_admin_dir ())
         $lang = getSystemLanguage ();
     else
         $lang = getCurrentLanguage (true);
     $fortuneDir = getModulePath ("fortune") . "cookies/" . $lang . "/";
     if (! is_dir ($fortuneDir)){
         $fortuneDir = getModulePath ("fortune") . "cookies/en/";
         }
     $fortuneFiles = scandir ($fortuneDir);
     do{
         $file = array_rand ($fortuneFiles, 1);
         $file = $fortuneFiles [$file];
         $file = $fortuneDir . $file;
         } while (! is_file ($file));
    
     $fileContent = file_get_contents ($file);
     $fileContent = trim ($fileContent);
     $fileContent = utf8_encode ($fileContent);
     $fileContent = str_replace ("\r\n", "\n", $fileContent);
     $fortunes = explode ("%\n", $fileContent);
     $text = array_rand ($fortunes, 1);
     $text = $fortunes [$text];
     return $text;
    }
?>
