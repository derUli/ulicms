<?php 
define("MODULE_ADMIN_HEADLINE", "Kalender");

$required_permission = getconfig("calendar_required_permission");

if($required_permission === false){
   $required_permission = 20;
}

define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);

function fullcalendar_list(){
  $query = mysql_query("SELECT * FROM `".tbname("events"). "` ORDER by `start` DESC");
  
  if(mysql_num_rows($query) > 0){
    echo "<table style=\"outline:4px solid #d4d4d4; background-color:#f0f0f0;width:96%; margin:auto;\">";
    echo "<tr style=\"background-color:#f0f0f0;font-weight:bold;\">";
    echo "<td>";
    echo "Start";
    echo "</td>";
    echo "<td>";
    echo "Ende";
    echo "</td>";
    echo "<td>";
    echo "Titel";
    echo "</td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "</tr>";
 
  
  while($row = mysql_fetch_object($query)){
     echo "<tr>";
     echo "<td>".date("d.m.Y", $row->start)."</strong><td>";
     echo "<td>".date("d.m.Y", $row->ende)."</strong><td>";
     echo "<td>".htmlspecialchars($row->title)."</strong><td>";
     echo "<td><a href=\"#\">Bearbeiten</a></td>";
     echo "<td><a href=\"#\">Löschen</a></td>";
     echo "</tr>";
  }
  
  
  echo "</table>";
  
   }
  
}

function fullcalendar_admin(){

if(isset($_GET["calendar_action"]))
   $action = $_GET["calendar_action"];
?>
<?php if(!isset($action)){?>
<a href="<?php echo getModuleAdminSelfPath()?>&calendar_action=add">Termin eintragen</a>
<br/><br/>
<?php fullcalendar_list();?>

<?php }?>
<?php
}
 
?>