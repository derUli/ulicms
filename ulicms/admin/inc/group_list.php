<?php 
if(!defined("ULICMS_ROOT"))
   die("Dummer Hacker!");
   
$acl = new ACL();
$groups = $acl->getAllGroups();

?>
<p><a href="?action=groups&add=add">Neue Gruppe anlegen</a></p>

<?php if(count($groups) > 0){
foreach($groups as $id => $name){
?>
<table>
<th>
<td>ID</td>
<td>Name</td>
<td></td>
<td></td>
</th>
<tr>
<td><?php echo $id;?></td>
<td><?php echo $name;?></td>
<td><a href="?action=groups&edit=<?php echo $id;?>"><img src="gfx/edit.gif" alt="Bearbeiten" title="Bearbeiten"></a></td>
<td><a href="?action=groups&delete=<?php echo $id;?> onclick="return confirm('Wirklich löschen?');"?>"><img src="gfx/delete.gif" alt="Löschen" title="Löschen"></a></td>
</tr>
</table>
<?php }

  } ?>