<?php
define("MODULE_ADMIN_HEADLINE", "HTTPS Erzwingen");
define("MODULE_ADMIN_REQUIRED_PERMISSION", "settings_enforce_https");

function enforce_https_admin(){
    
     if(isset($_POST["submit"])){
        
         
        
         if(isset($_POST["enforce_https"])){
             setconfig("enforce_https", "enforces");
         } else {
             deleteconfig("enforce_https");
         }
        
         }
    
     // Konfiguration checken
    $enforce_https = getconfig("enforce_https");

    
     ?>

<form action="<?php echo getModuleAdminSelfPath()?>" method="post">
<?php if(!$enforce_https){
?>
<p style="color:red">
Bevor Sie diese Option aktivieren, stellen Sie unbedingt sicher, dass Ihr Webserver HTTPS unterstützt. Klicken Sie hier für auf <a href="https://<?php echo $_SERVER["HTTP_HOST"];?>" target="_blank">diesen Link</a>.
</p>
<p style="color:red;">
Wen Ihr Server kein HTTPS unterstützt, führt die Aktivierung dieser Option zur Nichterreichbarkeit der Website.<br/>
In diesem Fall müssen Sie in der MySQL Datenbank den Datensatz mit dem Namen "enforce_https" löschen.
</p>
<?php 
}
?>
<p><input type="checkbox" name="enforce_https" value="enforce"
<?php if($enforce_https){
         echo " checked";
         }
     ?>/> <label for="enforce_https">Verschlüsselte HTTP Verbindung erzwingen</p>
    
     

<p><input type="submit" name="submit" value="Einstellungen speichern"/></p>
</form>
<?php
     }

?>
