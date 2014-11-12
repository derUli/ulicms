<?php 
function kontaktformular_name(){
   $names = array();
   $names["de"] = "Kontaktformular";
   $names["en"] = "Contact form";
   
   if(isset($names[$_SESSION["system_language"]])){
      return $names[$_SESSION["system_language"]];
   } else {
      return $names["de"];
   }
}