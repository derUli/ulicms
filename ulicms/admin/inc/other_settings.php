<?php
@include_once "Cache/Lite.php";

include_once ULICMS_ROOT.DIRECTORY_SEPERATOR."lib".DIRECTORY_SEPERATOR."string_functions.php";

if(!is_admin()){
     echo "<p class='ulicms_error'>Zugriff verweigert</p>";
     }else{
    
     if(isset($_POST["submit"])){
         if(isset($_POST["mailer"])){
             setconfig("mailer", db_escape($_POST["mailer"]));
             }
        
         if(isset($_POST["cache_period"])){
             setconfig("cache_period", intval($_POST["cache_period"]) * 60);
             }



         if(isset($_POST["cache_type"])){
             setconfig("cache_type", db_escape($_POST["cache_type"]));
             }
        
         if(isset($_POST["mail_mode"])){
             setconfig("mail_mode", db_escape($_POST["mail_mode"]));
             }
             
          if(isset($_POST["domain_to_language"])){
             $domain_to_language = $_POST["domain_to_language"];
             $domain_to_language = str_replace("\r\n", "\n", $domain_to_language);
             $domain_to_language = trim($domain_to_language);
             setconfig("domain_to_language", db_escape($domain_to_language));
             }
        
         if(isset($_POST["override_shortcuts"])){
             setconfig("override_shortcuts", db_escape($_POST["override_shortcuts"]));
            
             }
        
         if(isset($_POST["cache_enabled"]))
             deleteconfig("cache_disabled");
         else
             setconfig("cache_disabled", "disabled");


         if(isset($_POST["smtp_auth"]))
            setconfig("smtp_auth", "auth");
         else
            deleteconfig("smtp_auth");


         if(isset($_POST["smtp_host"]))
            setconfig("smtp_host", db_escape($_POST["stmp_host"]));

         if(isset($_POST["smtp_port"]))
            setconfig("smtp_port", intval($_POST["smtp_port"]));


         if(isset($_POST["smtp_user"]))
            setconfig("smtp_user", db_escape($_POST["smtp_user"]));

         if(isset($_POST["smtp_password"]))
            setconfig("smtp_password", db_escape($_POST["smtp_password"]));

        
         if($_POST["move_from"] != "-" and $_POST["move_to"] != "-"){
             db_query("UPDATE " . tbname("content") . " SET menu='" . db_escape($_POST["move_to"]) . "' WHERE menu='" . db_escape($_POST["move_from"]) . "'");
             }
         }
    
     $mailer = getconfig("mailer");
     $cache_type = getconfig("cache_type");
     $cache_enabled = !getconfig("cache_disabled");
     $cache_period = round(getconfig("cache_period") / 60);
     $override_shortcuts = getconfig("override_shortcuts");
     $email_mode = getconfig("email_mode");
     $menus = getAllMenus();

    $smtp_host = getconfig("smtp_host");
    if(!$smtp_host) 
        $smtp_host = "127.0.0.1";

    $smtp_port = getconfig("smtp_port");
    if(!$smtp_port) 
        $smtp_port = "25";


    $smtp_user = getconfig("smtp_user");
    if(!$smtp_user) 
        $smtp_user = null;

    $smtp_password = getconfig("smtp_password");
    if(!$smtp_password) 
        $smtp_password = null;

    $smtp_auth = getconfig("smtp_auth");


    
     ?>
<h1>Sonstiges</h1>
<form id="other_settings" action="index.php?action=other_settings" method="post">
<div class="label">Mailer</div>
<div class="inputWrapper"><select name="mailer" size=1>
<option value="php-mail"<?php if($mailer == "php-mail"){
         echo " selected";
         }
     ?>>PHP Mail</option>
</select>
</div>
<div class="seperator"></div>

<div class="label">Cache aktiviert</div>
<div class="inputWrapper"><input type="checkbox" name="cache_enabled" value="cache_enabled" <?php if($cache_enabled) echo " checked=\"checked\"";
     ?>></div>
<div class="label">Cache Gültigkeitsdauer</div>
<div class="inputWrapper"><input type="number" name="cache_period" min=1 max=20160 value="<?php echo $cache_period;
     ?>"> Minuten</div>

<div class="label">Cache-Speicher</div>
<div class="inputWrapper"><select name="cache_type" size=1>
<option value="file"<?php if($cache_type === "file" or !$cache_type){
         echo " selected";
         }
     ?>>Datei</option>
<option value="cache_lite"<?php if($cache_type === "cache_lite"){
         echo " selected";
         }
     ?>>Cache_Lite <?php if(!class_exists("Cache_Lite")) echo " (nicht verfügbar)"?></option>
</select>
</div>

<div class="seperator"></div>

<div class="label">Shortcuts ersetzen</div>
<div class="inputWrapper">
<select name="override_shortcuts" size=1>
<option value="off" <?php if($override_shortcuts == "off" or !$override_shortcuts) echo " selected=\"selected\""?>>Aus</option>
<option value="frontend" <?php if($override_shortcuts == "frontend") echo " selected=\"selected\""?>>Nur im Frontend</option>
<option value="backend" <?php if($override_shortcuts == "backend") echo " selected=\"selected\""?>>Nur im Backend</option>
<option value="on" <?php if($override_shortcuts == "on") echo " selected=\"selected\""?>>Im Frontend und Backend</option>
</select>
</div>
<p>Standard-Tastenkombinationen des Browsers werden ersetzt, so dass Sie z.B. durch Drücken von Strg+S ebenfalls speichern können</p>




<div class="seperator"></div>

<h2>Menüeinträge verschieben</h2>
<p>Verschiebe alle Menüeinträge von <select name="move_from" size="1">
                                    <option value="-" selected>-</option>
                                    <?php foreach ($menus as $menu){
                         ?>
                                    <option value="<?php echo $menu?>"><?php echo $menu?></option>
                                    <?php
                         }
                     ?>
                                    </select> nach <select name="move_to" size="1">
                                    <option value="-" selected>-</option>
                                    <?php foreach ($menus as $menu){
                         ?>
                                    <option value="<?php echo $menu?>"><?php echo $menu?></option>
                                    <?php
                         }
                     ?>
                                    </select> 
                                    </p>
<div class="seperator"></div>

<h2>Domain2Language-Mapping</h2>
<p>Hier können Sie Domains auf Sprachen mappen.
</p>
<p>
Die Zuweisungen müssen in folgendem Format erfolgen:<br/>
www.meinefirma.de=>de<br/>
www.meinefirma.co.uk=>en<br/>
www.meinefirma.fr=>fr
</p>

<p><textarea name="domain_to_language" rows="10" cols="40">
<?php echo real_htmlspecialchars(getconfig("domain_to_language"));?>
</textarea>
</p>
<div class="seperator"></div>
<h2>Email-Versand:</h2>
<div class="label">Modus:</div>
<div class="inputWrapper">
<select id='email_mode' name="email_mode" size="1">
<option value="internal"<?php 
if($email_mode == "internal")
echo ' selected="selected"';
    ?>>PHP</option>
<?php if(defined("NO_PEAR_MAIL")) {?>
<option value="pear_mail"<?php 
if($email_mode == "pear_mail")
echo ' selected="selected"';
    ?>>PEAR Mail</option>
    <?php }?>
</select>
</div>
<br/>
<div class="smtp_settings" id="smtp_settings" style="display:none">
    <h3>SMTP Einstellungen</h3>
<div class="label">Hostname</div>
<div class="inputWrapper"><input type="text" name="smtp_host" value="<?php echo real_htmlspecialchars($smtp_host);?>"</div>
</div>


<div class="label">Port</div>
<div class="inputWrapper"><input type="text" name="smtp_port" value="<?php echo real_htmlspecialchars($smtp_port);?>"</div>
</div>

<div class="label">
    Authentifizierung benötigt
</div>
<div class="inputWrapper">
<input type="checkbox" id="smtp_auth" name="smtp_auth" <?php 
if($smtp_auth)
    echo ' checked="checked"'?> value="auth">
</div>


<div id="smtp_auth_div" style="display:none">
<div class="label">User</div>
<div class="inputWrapper"><input type="text" name="smtp_user" value="<?php echo real_htmlspecialchars($smtp_user);?>"</div>
</div>

<div class="label">Passwort</div>
<div class="inputWrapper"><input type="text" name="smtp_password" value="<?php echo real_htmlspecialchars($smtp_password);?>"</div>
</div>

</div>
</div>
<script type="text/javascript">
<?php 
if($smtp_auth){
?>

$('#smtp_auth_div').show();

<?php } ?>
$('#smtp_auth').change(function(){
if($('#smtp_auth').prop('checked')){
   $('#smtp_auth_div').slideDown();
} else {
   $('#smtp_auth_div').slideUp();

}

});
</script>
<script type="text/javascript">
<?
if($email_mode == "pear_mail"){
?>
$('#smtp_settings').show();

<?php } ?>

$('#email_mode').change(function(){
if($('#email_mode').val() == "pear_mail"){
   $('#smtp_settings').slideDown();

} else {
   $('#smtp_settings').slideUp();

}

});

</script>

<div class="seperator"></div>

<p><a href="index.php?action=settings">Experteneinstellungen</a></p>
<br/>

<input name="submit" type="submit" value="Einstellungen speichern"/>

<?php
                     if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
                         ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }
                     ?>
</form>
</div>
<script type="text/javascript">
$("#other_settings").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  }, 
  success:function(e){
  $("#loading").hide();  
  $("#message").html("<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
  }
  

}); 

</script>
<?php }
                 ?> 
