<?php 
define("MODULE_ADMIN_HEADLINE", "Häufige Suchbegriffe");

$required_permission = getconfig("statistics_required_permission");

if($required_permission === false){
   $required_permission = 20;
}

define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);



function search_subjects_admin(){
  $data = db_query("SELECT * FROM ".tbname("search_subjects"). " ORDER by `amount` DESC LIMIT 10");
  
  if(mysql_num_rows($data) > 0){
     echo "<table>";
     while($row = mysql_fetch_object($data)){
        echo "<tr>";
		echo "<td style=\"font-weight:bold; min-width:100px;\">".htmlspecialchars($row->subject, ENT_QUOTES, "UTF-8")."</td>";
		echo "<td style=\"text-align:right\">".intval($row->amount)."</td>";
		echo "</tr>";
     }
	 
     echo "</table>";
  
  } else{
    echo "<p>Bisher noch keine Suchanfragen vorhanden</p>";
  }




}
 