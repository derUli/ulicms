<?php 
include_once "../lib/string_functions.php";
if(in_array("fullcalendar", getAllModules())){
?>

<h2 class="accordion-header">Anstehende Termine</h2>
<div class="accordion-content">
<?php 
$query = mysql_query("SELECT * FROM ".tbname("events"). " WHERE `start` >= ".(time()-(60 * 60) * 23)." ORDER by `start` ASC LIMIT 5");

if(mysql_num_rows($query) === 0){
  echo "<p>Es stehen derzeit keine Termine an.</p>";
} else{

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
    echo "</tr>";
    
    
 

while($row = mysql_fetch_object($query)){
error_reporting(E_ALL);
   echo "<tr>";
   echo "<td style=\"width:120px;\">".date("d.m.Y", $row->start)."</td>";
   echo "<td style=\"width:120px;\">".date("d.m.Y", $row->end)."</td>";
   echo "<td>";
   if(!empty($row->url) and $row->url != "http://")
     echo "<a href=\"".$row->url."\" style=\"font-weight:normal\" target=\"_blank\">";
   echo real_htmlspecialchars($row->title);
   if(!empty($row->url) and $row->url != "http://")
      echo "</a>";
   echo "</td>";
   echo "</tr>";


}

echo "</table>";
?>


<?php }?>
</div>

<?php
}

?>