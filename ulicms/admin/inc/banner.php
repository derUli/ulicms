<?php if(defined("_SECURITY")){
$acl = new ACL();
if($acl->hasPermission("banners")){
?>


<h2>Werbebanner</h2>
<p>Hier können Sie die Werbebanner für Ihre Internetseite verwalten.
<br/><br/>
<a href="index.php?action=banner_new">Banner hinzufügen</a><br/>
</p>
<table border=1>

<tr style="font-weight:bold;">
<td style="width:40px;">--></td>
<td>Banner</td>
<td>Bearbeiten</td>
<td>Löschen</td>
</tr>
<?php
         $query = db_query("SELECT * FROM " . tbname("banner") . " ORDER BY id", $connection);
         if(db_num_rows($query) > 0){
             while($row = db_fetch_object($query)){
                 ?>
<?php
                 echo '<tr>';
                 echo "<td style=\"width:40px;\">--></td>";
                 echo '<td><a href="' . $row -> link_url . '" target="_blank"><img src="' . $row -> image_url . '" title="' . $row -> name . '" alt="' . $row -> name . '" border=0></a></td>';
                 echo "<td>" . '<a href="index.php?action=banner_edit&banner=' . $row -> id . '"><img src="gfx/edit.gif"> Bearbeiten</a></td>';
                 echo "<td>" . '<a href="index.php?action=banner_delete&banner=' . $row -> id . '" onclick="return confirm(\'Wirklich löschen?\');"><img src="gfx/delete.gif"> Löschen</a></td>';
                 echo '</tr>';
                
                 }
            
             }
         ?>
</table>

<br/><br/>

<?php
        
         }
    else{
         noperms();
         }
    
     ?>




<?php }
?>
