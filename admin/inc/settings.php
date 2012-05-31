<?php if(defined("_SECURITY")){
if($_SESSION["group"]>=50){

$query=mysql_query("SELECT * FROM ".tbname("settings")." ORDER BY name",$connection);
if(mysql_num_rows($query)>0){
?>

<table border=1>
<tr style="font-weight:bold;">
<td style="width:40px;">--></td>
<td>Option</td>
<td>Wert</td>
<td>Bearbeiten</td>
<td>Löschen</td>
</tr>
<?php 
while($row=mysql_fetch_object($query)){
?>
<?php 
echo '<tr>';
echo "<td style=\"width:40px;\">--></td>";
echo "<td>".htmlspecialchars($row->name)."</td>";
echo "<td>".htmlspecialchars($row->value)."</td>";
echo "<td>".'<a href="index.php?action=key_edit&key='.$row->id.'"><img src="gfx/edit.gif"> Bearbeiten</a></td>';
echo "<td>".'<a href="index.php?action=key_delete&key='.$row->id.'" onclick="return confirm(\'Wirklich löschen?\');"><img src="gfx/delete.gif"> Löschen</a></td>';
echo '</tr>';

}

}
?>
</table>

<br/><br/>
<a href="index.php?action=key_new">Datensatz hinzufügen</a>


<?php 
}
else{
noperms();
}

?>




<?php }?>
