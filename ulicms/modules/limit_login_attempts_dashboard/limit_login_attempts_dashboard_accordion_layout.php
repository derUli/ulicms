<?php 
include_once "../lib/string_functions.php";
if(in_array("limit_login_attempts", getAllModules()) && is_admin()){

$alreadyDisplayed = Array();

$max_login_attempts = getconfig("max_login_attempts");
if(!$max_login_attempts)
   $max_login_attempts = 5;

?>

<h2 class="accordion-header">Fehlgeschlagene Anmeldeversuche</h2>
<div class="accordion-content">
<?php 
$query = mysql_query("SELECT *  FROM ".tbname("failed_logins")." ORDER by time DESC");

if(mysql_num_rows($query) === 0){
  echo "<p>Es gab keine fehlerhaften Loginversuche</p>";
} else{

echo "<table style=\"outline:4px solid #d4d4d4; background-color:#f0f0f0;width:96%; margin:auto;\">";
    echo "<tr style=\"background-color:#f0f0f0;font-weight:bold;\">";
    echo "<td>";
    echo "Anzahl";
    echo "</td>";
    echo "<td>";
    echo "IP";
    echo "</td>";
    echo "<td>";
    echo "Letzte Zeit";
    echo "</td>";
    echo "</tr>";
    
    

while($row = mysql_fetch_object($query)){
  $query2 = mysql_query("SELECT * FROM ".tbname("failed_logins").
" WHERE ip='".$row->ip."' ORDER by time DESC");


   $result = mysql_fetch_assoc($query2);
   
   if(!in_array($row->ip, $alreadyDisplayed)){
     if(mysql_num_rows($query2) >= $max_login_attempts)
        echo "<tr style=\"color:red;\">";
     else
        echo "<tr>";
     echo "<td style=\"width:200px;\">".mysql_num_rows($query2)."</td>";
     echo "<td style=\"width:300px;\">".$result["ip"]."</td>";
     echo "<td>";
     echo "Vor ";
     echo round((time() - $result["time"]) / 60 / 60, 2);
     echo " Stunden";
     echo "</td>";
     echo "</tr>";
   
     array_push($alreadyDisplayed, $row->ip);
   }


}

echo "</table>";
?>


<?php }?>
</div>

<?php
}

?>