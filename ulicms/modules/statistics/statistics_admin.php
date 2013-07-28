<?php
define("MODULE_ADMIN_HEADLINE", "Besucherstatistiken");

$required_permission = getconfig("statistics_required_permission");

if($required_permission === false){
     $required_permission = 20;
     }

define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);



function statistics_admin(){
     if(!setlocale(LC_TIME, "de_DE")){
         if(!setlocale(LC_TIME, "de_DE.utf8")){
             setlocale(LC_TIME, "deu");
             }
         }
    
     $data = db_query("SELECT * FROM " . tbname("statistics") . " ORDER by date ASC");
    
     $visitor_total = mysql_num_rows($data);
    
     $views_total = 0;
    
     $gestern = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
     $heute = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
     $morgen = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
    
     $visitors_today = 0;
     $visitors_yesterday = 0;
     $visitors_month = 0;
    
     $firstYear = false;
     $firstMonth = false;
    
     while($row = mysql_fetch_object($data)){
         $views_total += $row -> views;
         if(!$firstYear){
             $firstYear = date("Y", $row -> date);
             }
        
         if($row -> date >= $heute and $row -> date < $morgen){
             $visitors_today += 1;
             }
        
         if($row -> date >= $gestern and $row -> date < $heute){
             $visitors_yesterday += 1;
             }
        
         if($row -> date >= monatserster and $row -> date < $monatsletzter){
             $visitors_month += 1;
             }
        
         }
    
    
    
    
     ?>
<table>
<tr>
<td style="width:200px;">
<strong>Besucher gesamt</strong></td>
<td style="text-align:right;"><?php echo intval($visitor_total);
     ?>
</td>
</tr>
<tr>
<td style="width:200px;">
<strong>Besucher heute</strong></td>
<td style="text-align:right;"><?php echo intval($visitors_today);
     ?>
</td>
</tr><tr>
<td style="width:200px;">
<strong>Besucher gestern</strong></td>
<td style="text-align:right;"><?php echo intval($visitors_yesterday);
     ?>
</td>
</tr>
<td style="width:200px;">
<strong>Aufrufe gesamt</strong></td>
<td style="text-align:right;"><?php echo intval($views_total);
     ?>
</td>
</tr>
</table>
<br/>
<hr/>
<?php
     if($views_total > 0){
         for($i = date("Y"); $i >= $firstYear ; $i--){
             echo "<h2>" . $i . "</h2>";
             echo "<table>
<tr>
<td style=\"width:200px;\"><strong>Monat</strong></td>
<td><strong>Besucher</strong></td>
</tr>";
             $j = $i;
             for($m = 1; $m <= 12; $m++){
                 $d = date('d');
                 $monatserster = mktime(0, 0, 0, $m, 1, $j);
                 $monatsletzter = mktime(0, 0, 0, $m + 1, 0, $j);
                 $data = db_query("SELECT * FROM " . tbname("statistics") . " WHERE date >= $monatserster
	and date < $monatsletzter ORDER by date ASC");
                 if(mysql_num_rows($data) > 0){
                     echo "<tr>";
                     echo "<td>" . strftime("%B", $monatserster) . "</td>";
                     echo "<td style=\"text-align:right\">" . mysql_num_rows($data) . "</td>";
                     echo "</tr>";
                     }
                
                 }
             echo "</table>";
             }
        
        
         }
     ?>

<?php
    
     }

?>