<?php
if(defined("_SECURITY")){
     if($_SESSION["group"] >= 50){
        
         $query = db_query("SELECT * FROM " . tbname("settings") . " ORDER BY name", $connection);
         if(mysql_num_rows($query) > 0){
             ?>
<br/>
<a href="index.php?action=key_new">Datensatz hinzufügen</a>
<br/><br/>

<table border=1>
<tr style="font-weight:bold;">
<td style="width:40px;">--></td>
<td>Option</td>
<td>Wert</td>
<td>Bearbeiten</td>
<td>Löschen</td>
</tr>
<?php
             while($row = mysql_fetch_object($query)){
                 ?>
<?php
                 echo '<tr>';
                 echo "<td style=\"width:40px;\">--></td>";
                 echo "<td>" . htmlspecialchars($row -> name, ENT_QUOTES, "UTF-8") . "</td>";
                 echo "<td style=\"word-break:break-all;\">" . nl2br(htmlspecialchars($row -> value)) . "</td>";
                 echo "<td style=\"text-align:center\">" . '<a href="index.php?action=key_edit&key=' . $row -> id . '"><img src="gfx/edit.gif"> Bearbeiten</a></td>';
                 echo "<td style=\"text-align:center;\">" . '<a href="index.php?action=key_delete&key=' .
                 htmlspecialchars($row -> name,
                     ENT_QUOTES) . '" onclick="return confirm(\'Wirklich löschen?\');"><img src="gfx/delete.gif"> Löschen</a></td>';
                 echo '</tr>';
                
                 }
            
             }
         ?>
</table>



<?php
         }
    else{
         noperms();
         }
    
     ?>




<?php }
?>
