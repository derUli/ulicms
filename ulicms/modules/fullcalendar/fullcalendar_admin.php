<?php 
define("MODULE_ADMIN_HEADLINE", "Kalender");

$required_permission = getconfig("calendar_required_permission");

if($required_permission === false){
   $required_permission = 20;
}

define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);


function fullcalendar_list(){
  $query = db_query("SELECT * FROM `".tbname("events"). "` ORDER by `start` DESC");
  
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
     echo "<td>".date("d.m.Y", $row->start)."</strong></td>";
     echo "<td>".date("d.m.Y", $row->end)."</strong></td>";
     echo "<td>".htmlspecialchars($row->title)."</strong></td>";
     echo "<td><a href=\"?action=module_settings&module=fullcalendar&calendar_action=edit&id=".$row->id."\">Bearbeiten</a></td>";
     echo "<td><a href=\"?action=module_settings&module=fullcalendar&calendar_action=delete&id=".$row->id."\" onclick=\"return confirm('Diesen Termin wirklich löschen?');\">Löschen</a></td>";
     echo "</tr>";
  }
  
  
  echo "</table>";
  
   }
  
}

function fullcalendar_admin(){

if(isset($_GET["calendar_action"]))
   $action = $_GET["calendar_action"];
   
if($action == "delete"){
  $id = intval($_GET["id"]);
  db_query("DELETE FROM `".tbname(events)."` WHERE id = $id");
  unset($action);
}
if(isset($_POST["save"])){

     $title = mysql_real_escape_string(trim($_POST["title"]));
     $url = mysql_real_escape_string(trim($_POST["url"]));
     
     $start = $_POST["start"];
     $end = $_POST["end"];
     
     $start = explode(".", $start);
     $end = explode(".", $end);
     
     if(count($start) === 3 ){
        $start[1] = ltrim($start[1], "0");
        $start[0] = ltrim($start[0], "0");
        $start = mktime(0, 0, 0, $start[1], $start[0], $start[2]);
     } else {
        $start = time();     
     }
        
     if(count($end) === 3 ){
        $end[1] = ltrim($end[1], "0");
        $end[0] = ltrim($end[0], "0");
        $end = mktime(0, 0, 0, $end[1], $end[0], $end[2]);
     } else {
        $end = $start;     
     }
     


     $id = intval($_POST["id"]);
     
     if($id == 0){
        db_query("INSERT INTO `".tbname("events")."` (title, url, start, end) VALUES ('$title', '$url', $start, $end)")or die(mysql_error());
     
     } else {
       db_query("UPDATE `".tbname("events")."` SET title='$title', url='$url', start='$start', end='$end' WHERE id=$id");
     }
      
     
     
  
}
?>
<?php if(!isset($action)){?>
<a href="?action=module_settings&module=fullcalendar&calendar_action=add">Termin eintragen</a>
<br/><br/>
<?php fullcalendar_list();?>

<?php } 

else if($action == "add" or $action == "edit"){
   include getModulePath("fullcalendar")."fullcalendar_add.php";
   
}


}
 
?>