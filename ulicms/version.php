<?php
class ulicms_version{
     function ulicms_version(){
         $this -> version = "Purple Sky";
         $this -> internalVersion = Array(8, 0, 2);
         $this -> update = "";
         $this -> developmentVersion = false;
         }

     // Gibt den Codenamen der UliCMS Version zurück (z.B. 2013R2)
    function getVersion(){
         return $this -> version;
         }

     function getUpdate(){
         return $this -> update;
         }

     function getDevelopmentVersion(){
         return $this -> developmentVersion;
         }

     // Gibt die interne Versionsnummer als Array mit Integer-Datentyp zurück
    function getInternalVersion(){
         return $this -> internalVersion;
         }


     }
