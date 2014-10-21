<?php if(defined("_SECURITY")){
    include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "string_functions.php";
     $acl = new ACL();
     if(is_admin() or $acl -> hasPermission("users")){
         if(empty($_GET["order"])){
             $order = "username";
             }
        else{
             $order = basename($_GET["order"]);
             }
         $query = db_query("SELECT * FROM " . tbname("users") . " ORDER BY $order", $connection);
         if(db_num_rows($query)){
             ?>
<h2>Benutzer</h2>
<p>Hier können Sie die Benutzer Ihrer Homepage verwalten und die Passwörter ändern.
<br/><br/>
<a href="index.php?action=admin_new">Benutzer anlegen</a>
<br/>
</p>
<table border=1>
<tr style="font-weight:bold;">
<td style="width:40px;"><a href="index.php?action=admins&order=id">ID</a></td>
<td><span data-tooltip="Der Benutzername dient zur Anmeldung im Adminbereich..."><a href="index.php?action=admins&order=username">Benutzername</a></span></td>
<td><a href="index.php?action=admins&order=lastname">Nachname</a></td>
<td><a href="index.php?action=admins&order=firstname">Vorname</a></td>
<td><a href="index.php?action=admins&order=email">Email</a></td>
<td><a href="index.php?action=admins&order=group_id">Gruppe</a></td>
<td>Bearbeiten</td>
<td><span data-tooltip="Wenn ein Benutzer gelöscht wird, bleiben seine Beiträge erhalten, verlieren allerdings seinen Namen als Autor.">Löschen</span></td>
</tr>
<?php
             while($row = db_fetch_object($query)){
                 $group = $acl -> getPermissionQueryResult($row -> group_id);
                 $group = $group["name"];
                 ?>
<?php
                 echo '<tr>';
                 echo "<td style=\"width:40px;\">" . $row -> id . "</td>";
                 echo "<td>" . real_htmlspecialchars($row -> username) . "</td>";
                 echo "<td>" . real_htmlspecialchars($row -> lastname) . "</td>";
                 echo "<td>" . real_htmlspecialchars($row -> firstname) . "</td>";
                 echo "<td>" . real_htmlspecialchars($row -> email) . "</td>";
                 echo "<td>" . real_htmlspecialchars($group) . "</td>";
                 echo "<td>" . '<a href="index.php?action=admin_edit&admin=' . $row -> id . '"><img src="gfx/edit.gif"> Bearbeiten</a></td>';
                
                 if($row -> id == 1 || $row -> id == $_SESSION["login_id"]){
                     echo "<td><img src=\"gfx/delete.gif\"> <a href=\"#\" onclick=\"alert('Der Admin kann nicht gelöscht werden')\">Löschen</a></td>";
                     }else{
                     echo "<td>" . '<a href="index.php?action=admin_delete&admin=' . $row -> id . '" onclick="return confirm(\'Wirklich löschen?\');"><img src="gfx/delete.gif"> Löschen</a></td>';
                     }
                
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
