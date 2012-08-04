<?php if(defined("_SECURITY")){

if($_SESSION["group"]>=30){


?>
<h2>Seiten</h2>
<p>Hier können Sie die einzelnen Seiten Ihrer Homepage bearbeiten oder löschen.</p>
<br/>
<p><a href="index.php?action=pages_new">Seite verfassen</a><br/><br/></p>

<table border=1>
<tr style="font-weight:bold;">
<td style="width:40px;">--></td>
<td>Systemname</td>
<td>Menü</td>
<td>Position</td>
<td>Eltern</td>
<td>Aktiviert</td>
<td>Kommentare</td>
<td><span data-tooltip="Die Seite auf der Webpräsenz öffnen">Anzeigen</span></td>
<td>Bearbeiten</td>
<td>Löschen</td>
</tr>
<?php 
$query=mysql_query("SELECT * FROM ".tbname("content")." ORDER BY menu,position, systemname ASC",$connection);
if(mysql_num_rows($query)>0){
while($row=mysql_fetch_object($query)){
?>
<?php 
echo '<tr>';
echo "<td style=\"width:40px;\">--></td>";
echo "<td>".$row->systemname."</td>";

switch($row->menu){
case "top":
echo "<td>Oben</td>";
break;
case "down":
echo "<td>Unten</td>";
break;
case "left":
echo "<td>Links</td>";
break;
case "right":
echo "<td>Rechts</td>";
break;
default:
echo "<td>Nicht im Menü</td>";
break;
}

echo "<td>".$row->position."</td>";
echo "<td>".$row->parent."</td>";

if($row->active){
echo "<td>Ja</td>";
}
else{
echo "<td>Nein</td>";
}

if($row->comments_enabled){
echo "<td>Offen</td>";
}
else{
echo "<td>Geschlossen</td>";
}

echo "<td><a href=\"../?seite=".$row->systemname."\" target=\"_blank\"><img src=\"gfx/preview.gif\">Anzeigen</a></td>";
echo "<td>".'<a href="index.php?action=pages_edit&page='.$row->id.'"><img src="gfx/edit.gif"> Bearbeiten</a></td>';
if($_SESSION["group"]>=40){
echo "<td>".'<a href="index.php?action=pages_delete&page='.$row->id.'" onclick="return confirm(\'Wirklich löschen?\');"><img src="gfx/delete.gif">  Löschen</a></td>';
}else{
echo "<td><img src=\"gfx/delete.gif\"> Löschen</td>";
}
echo '</tr>';

}
?>
<?php 
}
?>
</table>


<br/>

<?php 
}else{
noperms();
}

?>

<?php }?>
