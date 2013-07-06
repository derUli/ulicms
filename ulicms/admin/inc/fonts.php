<?php 
if(!function_exists('getFontFamilys')){
  function getFontFamilys(){
     $fonts = Array();
     $fonts["Times New Roman"] = "Times, Times New Roman, serif";
     $fonts["Georgia"] = "Georgia";
     $fonts["Sans Serif"] = "sans-serif";
     $fonts["Arial"] = "arial";
     $fonts["Comic Sans MS"] = "Comic Sans MS";
     $fonts["Helvetica"] = "helvetica";
     $fonts["Tahoma"] = "Tahoma";
     $fonts["Verdana"] = "";
     $fonts["Lucida Sans Unicode"] = "'Lucida Sans Unicode'";
     $fonts["Trebuchet MS"] = "'Trebuchet MS'";
     $fonts["Lucida Sans"] = "'Lucida Sans'";
     $fonts["monospace"] = "monospace";
     $fonts["Courier"] = "Courier";
     $fonts["Courier New"] = "'Courier New', Courier";
     $fonts["Lucida Console"] = "'Lucida Console'";
     $fonts["fantasy"] = "fantasy";
     $fonts["cursive"] = "cursive";
     
     // Hier bei Bedarf weitere Fonts einfügen
     // $fonts["Meine Font 1"] = "myfont1";
     // $fonts["Meine Font 2"] = "myfont2";
     // $fonts["Meine Font 3"] = "myfont3";
     
     // Weitere Fonts Ende
     
     ksort($fonts);
     
     return $fonts;
  }
}
?>