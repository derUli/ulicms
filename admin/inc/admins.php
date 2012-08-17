<?php if(defined("_SECURITY")){
if($_SESSION["group"]>=50){

$query=mysql_query("SELECT * FROM ".tbname("admins")." ORDER BY id",$connection);
if(mysql_num_rows($query)){
?>
<h2>Benutzer</h2>
<p>Hier können Sie die Administratoren Ihrer Homepage verwalten und die Passwörter ändern.
<br/><br/>
<a href="index.php?action=admin_new">Administrator anlegen</a>
<br/>
</p>
<table border=1>
<tr style="font-weight:bold;">
<td style="width:40px;">--></td>
<td><span data-tooltip="Der Benutzername dient zur Anmeldung im Adminbereich...">Benutzername</span></td>
<td>Nachname</td>
<td>Vorname</td>
<td>Email</td>
<td>Bearbeiten</td>
<td><span data-tooltip="Wenn ein Administrator gelöscht wird, bleiben seine Beiträge erhalten, verlieren allerdings seinen Namen als Autor.">Löschen</span></td>
</tr>
<?php 
while($row=mysql_fetch_object($query)){
?>
<?php 
echo '<tr>';
echo "<td style=\"width:40px;\">--></td>";
echo "<td>".htmlspecialchars($row->username)."</td>";
echo "<td>".htmlspecialchars($row->lastname)."</td>";
echo "<td>".htmlspecialchars($row->firstname)."</td>";
echo "<td>".htmlspecialchars($row->email)."</td>";
echo "<td>".'<a href="index.php?action=admin_edit&admin='.$row->id.'"><img src="gfx/edit.gif"> Bearbeiten</a></td>';

if($row->id==1||$row->id==$_SESSION["login_id"]){
echo "<td><img src=\"gfx/delete.gif\"> Löschen</td>";
}else{
echo "<td>".'<a href="index.php?action=admin_delete&admin='.$row->id.'" onclick="return confirm(\'Wirklich löschen?\');"><img src="gfx/delete.gif"> Löschen</a></td>';
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




<?php }?>
