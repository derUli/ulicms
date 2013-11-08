<?php 
if(!defined("ULICMS_ROOT"))
   die("Dummer Hacker!");
   
$acl = new ACL();
$groups = $acl->getAllGroups();


if(isset($_REQUEST["sort"])){
   $_SESSION["grp_sort"] = $_REQUEST["sort"];
}

if($_SESSION["grp_sort"] == "id"){
   if($_SESSION["sortDirection"] == "asc")
      ksort($groups);
   else if($_SESSION["sortDirection"] == "asc")
      krsort($groups);
}
else if($_SESSION["grp_sort"] == "name"){
   if($_SESSION["sortDirection"] == "asc")
      asort($groups);
   else
      arsort($groups);
} else{
 ksort($groups); 
}

?>
<p><a href="?action=groups&add=add">Neue Gruppe anlegen</a></p>
<?php if(count($groups) > 0){ ?>
<table>
<tr>
<td style="min-width:100px;"><a href="?action=groups&sort=id&sort_direction=change"><strong>ID</strong></a></td>
<td style="min-width:200px;"><a href="?action=groups&sort=name&sort_direction=change"><strong>Name</strong></a></td>
<td></td>
<td></td>
</tr>

<?php foreach($groups as $id => $name){ ?>
<tr>
<td><?php echo $id;?></td>
<td><?php echo $name;?></td>
<td><a href="?action=groups&edit=<?php echo $id;?>"><img src="gfx/edit.gif" alt="Bearbeiten" title="Bearbeiten"></a></td>
<td><a href="?action=groups&delete=<?php echo $id;?>" onclick="return confirm('Wirklich löschen?');"><img src="gfx/delete.gif" alt="Löschen" title="Löschen"></a></td>
</tr>



<?php } ?>

</table>
<?php } ?>