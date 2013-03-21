<?php
define("MODULE_ADMIN_HEADLINE", "Automatisches Sicherung der MySQL-Datenbank");

$required_permission = getconfig("mysql_backup_required_permission");

if($required_permission === false){
   $required_permission = 50;
}

define("MODULE_ADMIN_REQUIRED_PERMISSION", $required_permission);


include_once getModulePath("mysql_backup")."mysql_backup_install.php";
mysql_backup_check_install();



function mysql_backup_admin(){

// Geänderte Optionen in der Datenbank eintragen
if(isset($_POST["mysql_backup_every_days"])){
   setconfig("mysql_backup_every_days", 
   intval($_POST["mysql_backup_every_days"]));
}


if(isset($_POST["backup_now"])){
   setconfig("mysql_backup_last_time", 0);
   include_once getModulePath("mysql_backup").'mysql_backup_cron.php';
   setconfig("mysql_backup_last_time", time());
   echo "<span style='color:green;'>".
   "Die Sicherung wurde durchgeführt!".
   "</span>".
   "<br/>";
}


if(!isset($_POST["backup_now"]) and
 $_SERVER["REQUEST_METHOD"] == "POST"){
  echo "<span style='color:green;'>".
   "Die Einstellungen wurde gespeichert!".
   "</span>".
   "<br/>";
 }

// get current options
$mysql_backup_last_time  = getconfig("mysql_backup_last_time");
$mysql_backup_every_days = getconfig("mysql_backup_every_days");

?>
<form method="post" action="<?php echo getModuleAdminSelfPath()?>">
<table style="border:0px">
<tr>
<td><strong>Die Datenbank alle X Tage sichern</strong></td>
<td><input name="mysql_backup_every_days" type="number" step="any" value="<?php 
echo $mysql_backup_every_days;
?>" min="1" max="365"></td>
</tr>
<tr>
<td><strong>Letzte Sicherung durchgeführt am:
&nbsp;&nbsp;&nbsp;&nbsp;
</strong></td>
<td><?php echo date("d.m.Y", $mysql_backup_last_time);?>
</tr>
<tr>
<td><strong>Jetzt eine Sicherung durchführen:</td>
<td>
<input type="checkbox" name="backup_now" value="backup_now"/>
</td>
</tr>
</table>
<input type="submit" name="submit" value="Einstellungen speichern"/>
</form>



<?php }?>
