<?php
$acl = new ACL();
if(defined("_SECURITY")){
     if($acl -> hasPermission("expert_settings")){
        
         $query = db_query("SELECT * FROM " . tbname("settings") . " ORDER BY name", $connection);
         if(db_num_rows($query) > 0){
             ?>
<br/>
<a href="index.php?action=key_new"><?php echo TRANSLATION_CREATE_OPTION;
             ?></a>
<br/><br/>

<table border=1>
<tr style="font-weight:bold;">
<td style="width:40px;">--></td>
<td><?php echo TRANSLATION_OPTION;
             ?></td>
<td><?php echo TRANSLATION_VALUE;
             ?></td>
<td><?php echo TRANSLATION_EDIT;
             ?></td>
<td><?php echo TRANSLATION_DELETE;
             ?></td>
</tr>
<?php
             while($row = db_fetch_object($query)){
                 ?>
<?php
                 echo '<tr>';
                 echo "<td style=\"width:40px;\">--></td>";
                 echo "<td>" . htmlspecialchars($row -> name, ENT_QUOTES, "UTF-8") . "</td>";
                 echo "<td style=\"word-break:break-all;\">" . nl2br(htmlspecialchars($row -> value)) . "</td>";
                 echo "<td style=\"text-align:center\">" . '<a href="index.php?action=key_edit&key=' . $row -> id . '"><img src="gfx/edit.png" class="mobile-big-image" alt="' . TRANSLATION_EDIT . '" title="' . TRANSLATION_EDIT . '"></a></td>';
                 echo "<td style=\"text-align:center;\">" . '<a href="index.php?action=key_delete&key=' .
                 htmlspecialchars($row -> name,
                     ENT_QUOTES) . '" onclick="return confirm(\'' . TRANSLATION_ASK_FOR_DELETE . '\');"><img src="gfx/delete.gif" class="mobile-big-image" alt="' . TRANSLATION_DELETE . '" title="' . TRANSLATION_DELETE . '"></a></td>';
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
