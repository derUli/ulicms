<?php 
class ulicms_version{

   function ulicms_version(){
      $this->version = "2014R4";
      $this->internalVersion = Array(6, 1);
   }
   
   
   // Gibt den Namen der UliCMS Version zurück (z.B. 2013R2)
   function getVersion(){
          return $this->version;
   }
   
   // Gibt die interne Versionsnummer als Array mit Integer-Datentyp zurück
   function getInternalVersion(){
          return $this->internalVersion;
   }


}
?>