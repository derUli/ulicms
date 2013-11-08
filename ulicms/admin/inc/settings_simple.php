<?php if(defined("_SECURITY")){
$acl = new ACL();
     if($acl->hasPermission("settings_simple")){
        
         $query = db_query("SELECT * FROM " . tbname("settings") . " ORDER BY name", $connection);
         $settings = Array();
         while($row = db_fetch_object($query)){
            
             $settings[$row -> name] = $row -> value;
             $settings[$row -> name] = htmlspecialchars($settings[$row -> name],
                 ENT_QUOTES, "UTF-8");
            
             }
        
         $query2 = db_query("SELECT * FROM " . tbname("content") . " ORDER BY systemname");
         $pages = Array();
        
         while($row = db_fetch_object($query2)){
             array_push($pages, $row -> systemname);
             }
         ?>

<h2>Grundeinstellungen</h2>
<p>Hier können Sie die Einstellungen für Ihre Internetseite verändern.</p>
<form id="settings_simple" action="index.php?action=save_settings" method="post">
<table border=1>
<tr>
<td><strong data-tooltip="Der Name dieser Webpräsenz.">Titel der Homepage:</strong></td>
<td><input type="text" name="homepage_title" style="width:400px" value="<?php echo $settings["homepage_title"];
         ?>"></td>
</tr>
<tr>
<td><strong data-tooltip="Eine kurze Beschreibung oder ein Slogan um was es auf dieser Webpräsenz geht.">Motto der Homepage:</strong></td>
<td><input type="text" name="homepage_motto" style="width:400px" value="<?php echo $settings["motto"];
         ?>"></td>
</tr>
<tr>
<td><strong data-tooltip="Der Name des Inhabers dieser Webpräsenz...">Inhaber der Homepage:</strong></td>
<td><input type="text" name="homepage_owner" style="width:400px" value="<?php echo $settings["homepage_owner"];
         ?>"></td>
</tr>
<tr>
<td><strong>Logo ausblenden:</strong></strong></td>
<td>
<select name="logo_disabled" style="width:400px" size=1>
<option <?php if (getconfig("logo_disabled") == "yes") echo 'selected '?> value="yes">Ja</option>
<option <?php if (getconfig("logo_disabled") != "yes") echo 'selected '?> value="no">Nein</option>
</select>
</td>
</tr>
<tr>
<td><strong data-tooltip="An diese Adresse werden Emails über das Kontaktformular versandt...">Emailadresse des Inhabers:</strong></td>
<td><input type="text" name="email" style="width:400px" value="<?php echo $settings["email"];
             ?>"></td>
</tr>
<tr>
<td>
<strong data-tooltip="Dies ist die Startseite Ihres Internetauftritts...">Startseite</strong>
</td>
<td>

<select name="frontpage" size=1 style="width:400px">
<?php for($i = 0;$i < count($pages);$i++){
                 if($pages[$i] == $settings["frontpage"]){
                     echo "<option value='" . $pages[$i] . "' selected='selected'>" . $pages[$i] . "</option>";
                     }else{
                     echo "<option value='" . $pages[$i] . "'>" . $pages[$i] . "</option>";
                     }
                
                 }
             ?>
</select>


</td>
</tr>
<tr>
<td><strong data-tooltip="Wenn Sie grundlegende Änderungen an Ihrer Webpräsenz vornehmen möchten, können Sie Ihre Seite solange für Besucher sperren und stattdessen eine Wartungsmeldung anzeigen. Diese können Sie in der Template maintenance.php anpassen.">Wartungsmodus aktiviert:</strong></td>
<td><input type="checkbox" name="maintenance_mode" <?php
             if(strtolower($settings["maintenance_mode"] == "on") || $settings["maintenance_mode"] == "1" || strtolower($settings["maintenance_mode"]) == "true"){
                 echo " checked";
                
                
                 }
            
             ?>
></strong></td>
</tr>
<tr>
<td><strong data-tooltip="Dann können sich Gäste ein Benutzerkonto anlegen.">Gäste dürfen sich registrieren:</strong></td>
<td><strong><input type="checkbox" name="visitors_can_register" <?php
             if(strtolower($settings["visitors_can_register"] == "on") ||
                     $settings["visitors_can_register"] == "1" ||
                     strtolower($settings["visitors_can_register"]) == "true"){
                 echo " checked";
                
                
                 }
            
             ?>
></td>
</tr>
<tr>
<td><strong>Standard-Gruppe für neue Nutzer:</strong>
</td>
<td>
<select name="registered_user_default_level" size=1 style="width:100%;">
<option value="50" <?php if($settings["registered_user_default_level"] == 50) echo "selected";
             ?>>Admin</option>
<option value="40" <?php if($settings["registered_user_default_level"] == 40) echo "selected";
             ?>>Redakteur</option>
<option value="30" <?php if($settings["registered_user_default_level"] == 30) echo "selected";
             ?>>Autor</option>
<option value="20" <?php if($settings["registered_user_default_level"] == 20) echo "selected";
             ?>>Mitarbeiter</option>
<option value="10" <?php if($settings["registered_user_default_level"] == 10 or $settings["registered_user_default_level"] === false) echo "selected";
             ?>>Gast</option>
<option value="0" <?php if($settings["registered_user_default_level"] == 0) echo "selected";
             ?>>Gesperrter Nutzer</option>
</select>

</td>
</tr>

<tr>
<td></td>
<td><strong data-tooltip="Zusätzliche Informationen die für Optimierung des Suchmaschinen-Rankings dienen...">Meta-Daten für Suchmaschinen:</strong></td>
</tr>
<tr>
<td><strong data-tooltip="Stichwörter, die den Inhalt dieser Website beschreiben...">Keywords:</strong></td>
<td><input type="text" name="meta_keywords" value="<?php echo $settings["meta_keywords"];
             ?>" style="width:400px">
</tr>
<tr>
<td><strong data-tooltip="Eine kurze Beschreibung der Website....">Beschreibung:</strong></td>
<td><input type="text" name="meta_description" value="<?php echo $settings["meta_description"];
             ?>" style="width:400px">
</tr>
<tr>
<td></td>
<td><strong>Technisches:</strong></td>
</strong>
</tr>
<tr>
<td><strong>W3C Validierungsfehler automatisch korrigieren</strong></td>
<td><input type="checkbox" name="disable_html_validation" <?php
             if(!isset($settings["disable_html_validation"]))
                 echo " checked";
             ?> value="enabled">
</tr>
<tr>
<td></td>
<td><strong>Kommentare:</strong>
</td>
</tr>
<tr>
<td><strong data-tooltip="Welches Kommentarsystem soll verwendet werden?
UliCMS verf&uuml;gt &uuml;ber direkte Schnittstellen zu Facebook und Disqus">Kommentarsystem</td>
<td>
<select name="comment_mode" size=1 style="width:100%;">
<!-- <option value="intern" <?php if($settings["comment_mode"] == "intern"){
                 echo 'selected';
                 }
             ?>>Intern</option> !-->
<option value="facebook" <?php if($settings["comment_mode"] == "facebook"){
                 echo 'selected';
                 }
             ?>>Facebook Comments</option>
<option value="disqus" <?php if($settings["comment_mode"] == "disqus"){
                 echo 'selected';
                 }
             ?>>Disqus</option>
<option value="off" <?php if($settings["comment_mode"] == "off"){
                 echo 'selected';
                 }
             ?>>Aus</option>
</select>
</td>
</tr>
<tr>
<td><strong data-tooltip="Die Facebook-ID wird ben&ouml;tigt, damit Sie wenn Sie wenn Sie die Kommentarfunktion von
Facebook nutzen, die Kommentare moderieren k&ouml;nnen">Facebook-ID:</strong></td>
<td><input type="text" name="facebook_id" value="<?php echo $settings["facebook_id"];
             ?>" style="width:400px">
</tr>
<tr>
<td><strong data-tooltip="Der Disqus-Shortname wird ben&ouml;tigt, damit Sie Sie 
die Kommentarfunktion von disqus verwenden k&ouml;nnen.
Daf&uuml;r ben&ouml;tigen Sie einen Account bei disqus.com">Disqus-Shortname:</strong></td>
<td><input type="text" name="disqus_id" value="<?php echo $settings["disqus_id"];
             ?>" style="width:400px">
</tr>
<tr>
<td><strong>Zeitzone:</strong></td>
<td>
<select name="timezone" size=1 style="width:100%;">
<?php
             $timezones = file("inc/timezones.txt");
            
             $current_timezone = getconfig("timezone");
             $current_timezone = trim($current_timezone);
             sort($timezones);
             for($i = 0; $i < count($timezones); $i++){
                
                 $thisTimezone = $timezones[$i];
                 $thisTimezone = trim($thisTimezone);
                 if($thisTimezone === $current_timezone){
                     echo '<option value="' . $thisTimezone . '" selected>' . $thisTimezone . '</option>';
                     }else{
                     echo '<option value="' . $thisTimezone . '">' . $thisTimezone . '</option>';
                     }
                 }
             ?>
</select>
</td>
</tr>
<tr>
<td><strong>Suchmaschinen:</strong></td>
<td>
<select name="robots" size=1 style="width:100%;">
<?php
             if(getconfig("robots") == "noindex,nofollow"){
                 ?>
   
   <option value="index,follow">Suchmaschinen dürfen die Website durchsuchen</option>
   <option value="noindex,nofollow" selected>Suchmaschinen werden ausgesperrt</option>
   
<?php }else{
                 ?>
   <option value="index,follow" selected>Suchmaschinen dürfen die Website durchsuchen</option>
   <option value="noindex,nofollow">Suchmaschinen werden ausgesperrt</option>
<?php }
             ?>
</select>
</td>
</tr>
<?php add_hook("settings_simple");
             ?>
<tr>
<td>
<td align="center"><input type="submit" value="OK" style="width:45%;"></td>
</tr>
</table>
<input type="hidden" name="save_settings" value="save_settings">

<?php
            if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
                ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }
            ?>
</form>
<script type="text/javascript">
$("#settings_simple").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  }, 
  success:function(e){
  $("#loading").hide();  
  $("#message").html("<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
  }
  

}); 

</script>



<?php
             }
        else{
             noperms();
             }
        
         ?>




<?php }
     ?>
