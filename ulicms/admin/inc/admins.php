<?php if(defined("_SECURITY")){
     include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "string_functions.php";
     $acl = new ACL();
     if(is_admin() or $acl -> hasPermission("users")){
         if(empty($_GET["order"])){
             $order = "username";
             }
        else if(in_array($_GET["order"], array("id", "firstname", "lastname",
                     "email", "group_id"))){
             $order = basename($_GET["order"]);
             }
        else{
            
             $order = "username";
             }
         $query = db_query("SELECT * FROM " . tbname("users") . " ORDER BY $order", $connection);
         if(db_num_rows($query)){
             ?>
<h2><?php echo TRANSLATION_USERS;
             ?></h2>
<p><?php echo TRANSLATION_USERS_INFOTEXT;
             ?>
<br/><br/>
<a href="index.php?action=admin_new"><?php echo TRANSLATION_CREATE_USER;
             ?></a>
<br/>
</p>
<table class="tablesorter">
<thead>

<tr style="font-weight:bold;">
<th style="width:40px;"><a href="index.php?action=admins&order=id">ID</a></th>
<th><span><a href="index.php?action=admins&order=username"><?php echo TRANSLATION_USERNAME;
             ?></a></span></th>
<th><a href="index.php?action=admins&order=lastname"><?php echo TRANSLATION_LASTNAME;
             ?></a></th>
<th><a href="index.php?action=admins&order=firstname"><?php echo TRANSLATION_FIRSTNAME;
             ?></a></th>
<th><a href="index.php?action=admins&order=email"><?php echo TRANSLATION_EMAIL;
             ?></a></th>
<th><a href="index.php?action=admins&order=group_id"><?php echo TRANSLATION_GROUP;
             ?></a></th>
<td><?php echo TRANSLATION_EDIT;
             ?></td>
<td><span><?php echo TRANSLATION_DELETE;
             ?></span></td>
</tr>
</thead>
<body>
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
                 echo "<td style='text-align:center;'>" . '<a href="index.php?action=admin_edit&admin=' . $row -> id . '"><img src="gfx/edit.png" alt="' . TRANSLATION_EDIT . '" title="' . TRANSLATION_EDIT . '"></a></td>';
                
                 if($row -> id == $_SESSION["login_id"]){
                     echo "<td style='text-align:center;'><a href=\"#\" onclick=\"alert('" . TRANSLATION_CANT_DELETE_ADMIN . "')\"><img src=\"gfx/delete.gif\" alt=\"" . TRANSLATION_DELETE . "\" title=\"" . TRANSLATION_EDIT . "\"></a></td>";
                     }else{
                     echo "<td style='text-align:center;'>" . '<a href="index.php?action=admin_delete&admin=' . $row -> id . '" onclick="return confirm(\'' . TRANSLATION_ASK_FOR_DELETE . '\');"><img src="gfx/delete.gif"></a></td>';
                     }
                
                 echo '</tr>';
                
                 }
            
             }
         ?>
</tbody>
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
