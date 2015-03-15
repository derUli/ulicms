<?php if(defined("_SECURITY")){
     $acl = new ACL();
     if($acl -> hasPermission("settings_simple")){
        
         $query = db_query("SELECT * FROM " . tbname("settings") . " ORDER BY name", $connection);
         $settings = Array();
         while($row = db_fetch_object($query)){
            
             $settings[$row -> name] = $row -> value;
             $settings[$row -> name] = htmlspecialchars($settings[$row -> name],
                 ENT_QUOTES, "UTF-8");
            
             }
        
        
         ?>

<h2><?php echo TRANSLATION_GENERAL_SETTINGS;
         ?></h2>
<p>Hier können Sie die Einstellungen für Ihre Internetseite verändern.</p>
<form id="settings_simple" action="index.php?action=save_settings" method="post">
<?php csrf_token_html();?>
<table>
<tr>
<td><strong><?php echo TRANSLATION_HOMEPAGE_TITLE;
         ?></strong></td>
<td><a href="index.php?action=homepage_title"><?php echo TRANSLATION_EDIT;
         ?></a></td>
</tr>
<tr>
<td><strong><?php echo TRANSLATION_MOTTO;
         ?></strong></td>
<td><a href="index.php?action=motto"><?php echo TRANSLATION_EDIT;
         ?></a></td>
</tr>
<tr>
<td><strong><?php echo TRANSLATION_HOMEPAGE_OWNER;
         ?></strong></td>
<td><input type="text" name="homepage_owner" value="<?php echo $settings["homepage_owner"];
         ?>"></td>
</tr>
<tr>
<td><strong><?php echo TRANSLATION_HIDE_LOGO;
         ?></strong></strong></td>
<td>
<select name="logo_disabled" size=1>
<option <?php if (getconfig("logo_disabled") == "yes") echo 'selected '?> value="yes"><?php echo TRANSLATION_YES;
         ?></option>
<option <?php if (getconfig("logo_disabled") != "yes") echo 'selected '?> value="no"><?php echo TRANSLATION_NO;
         ?></option>
</select>
</td>
</tr>
<tr>
<td><strong><?php echo TRANSLATION_OWNER_MAILADRESS;
         ?></strong></td>
<td><input type="email" name="email" value="<?php echo $settings["email"];
         ?>"></td>
</tr>
<tr>
<td>
<strong><?php echo TRANSLATION_FRONTPAGE;
         ?></strong>
</td>
<td>

<a href="index.php?action=frontpage_settings"><?php echo TRANSLATION_EDIT;
         ?></a>


</td>
</tr>
<tr>
<td><strong><?php echo TRANSLATION_MAINTENANCE_MODE_ENABLED;
         ?></strong></td>
<td><input type="checkbox" name="maintenance_mode" <?php
         if(strtolower($settings["maintenance_mode"] == "on") || $settings["maintenance_mode"] == "1" || strtolower($settings["maintenance_mode"]) == "true"){
             echo " checked";
            
            
             }
        
         ?>
></strong></td>
</tr>
<tr>
<td><strong><?php echo TRANSLATION_GUEST_MAY_REGISTER;
         ?></strong></td>
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
<td></td>
<td><strong><?php echo TRANSLATION_METADATA;
         ?></strong></strong></td>
</tr>
<tr>
<td><strong><?php echo TRANSLATION_DESCRIPTION;
         ?></strong></td>
<td>
<a href="index.php?action=meta_description"><?php echo TRANSLATION_EDIT;
         ?></a>
</td>
</tr>
<tr>
<td><strong><?php echo TRANSLATION_KEYWORDS;
         ?></strong></td>
<td>

<a href="index.php?action=meta_keywords"><?php echo TRANSLATION_EDIT;
         ?></a>
</td>
</tr>
<tr>
<td></td>
<td><strong><?php echo TRANSLATION_TECHNICAL_STUFF;
         ?></strong></td>
</strong>
</tr>
<tr>
<td><strong><?php echo TRANSLATION_FIX_W3C_VALIDATION_ERRORS;
         ?></strong></td>
<td><input type="checkbox" name="disable_html_validation" <?php
         if(!isset($settings["disable_html_validation"]))
             echo " checked";
         ?> value="enabled">
</tr>
<tr>
<td></td>
<td><strong><?php echo TRANSLATION_COMMENTS;
         ?></strong>
</td>
</tr>
<tr>
<td><strong><?php echo TRANSLATION_COMMENTING_SYSTEM;
         ?></td>
<td>
<select name="comment_mode" size=1>
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
         ?>><?php echo TRANSLATION_OFF;
         ?></option>
</select>
</td>
</tr>
<tr>
<td><strong><?php echo TRANSLATION_FACEBOOK_ID;
         ?></strong></td>
<td><input type="text" name="facebook_id" value="<?php echo $settings["facebook_id"];
         ?>">
</tr>
<tr>
<td><strong><?php echo TRANSLATION_DISQUS_SHORTNAME;
         ?></strong></td>
<td><input type="text" name="disqus_id" value="<?php echo $settings["disqus_id"];
         ?>">
</tr>
<tr>
<td><strong><?php echo TRANSLATION_TIMEZONE;
         ?></strong></td>
<td>
<select name="timezone" size=1>
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
<td><strong><?php echo TRANSLATION_SEARCH_ENGINES;
         ?></strong></td>
<td>
<select name="robots" size=1>
<?php
         if(getconfig("robots") == "noindex,nofollow"){
             ?>
   
   <option value="index,follow"><?php echo TRANSLATION_SEARCH_ENGINES_INDEX;
             ?></option>
   <option value="noindex,nofollow" selected><?php echo TRANSLATION_SEARCH_ENGINES_NOINDEX;
             ?></option>
   
<?php }else{
             ?>
   <option value="index,follow" selected><?php echo TRANSLATION_SEARCH_ENGINES_INDEX;
             ?></option>
   <option value="noindex,nofollow"><?php echo TRANSLATION_SEARCH_ENGINES_NOINDEX;
             ?></option>
<?php }
         ?>
</select>
</td>
</tr>
<?php add_hook("settings_simple");
         ?>
<tr>
<td>
<td align="center"><input type="submit" value="OK" style="width:100%;"></td>
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
