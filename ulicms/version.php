<?php
class ulicms_version{
    
     function ulicms_version(){
         $this -> version = "2015";
         $this -> internalVersion = Array(7, 2, 1);
         $this -> developmentVersion = true;
         
         }
    
    
     // Gibt den Namen der UliCMS Version zurück (z.B. 2013R2)
    function getVersion(){
         return $this -> version;
         }
    
     function getDevelopmentVersion(){
         return $this -> developmentVersion;
         }
    
     // Gibt die interne Versionsnummer als Array mit Integer-Datentyp zurück
    function getInternalVersion(){
         return $this -> internalVersion;
         }
    
    
     }
