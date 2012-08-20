<?php if(defined("_SECURITY")){?>

<?php
if($_SESSION["group"]>=20){
?>

<h2>News</h2>
<p>Hier können Sie News anlegen und bearbeiten.<br/><br/></p>

<p><a href="index.php?action=add_news">News veröffentlichen</a><br/><br/></p>

<table border=1>
<tr style="font-weight:bold;">
<td>Datum</td>
<td>Titel</td>
<td>Autor</td>
<td>Bearbeiten</td>
<td>Löschen</td>
</tr>
<?php 
$query=mysql_query("SELECT * FROM ".tbname("news")." ORDER BY date DESC");
while($row=mysql_fetch_object($query)){
$query2=mysql_query("SELECT * FROM ".tbname("admins")." WHERE id = '".$row->autor."'",$connection);
$result2=mysql_fetch_object($query2);

echo "<tr>";
echo "<td>".date(env("date_format"),$row->date)."</td>";
echo "<td>".$row->title."</td>";
echo "<td>".$result2->firstname." ".$result2->lastname."</td>";
echo "<td><a href=\"index.php?action=edit_news&news=".$row->id."\"><img src=\"gfx/edit.gif\"> Bearbeiten</a></td>";
if($_SESSION["group"]>=40){
echo "<td><a href=\"index.php?delete_news=delete_news&news=".$row->id."\" onclick=\"return confirm('Diese News wirklich löschen?');\"><img src=\"gfx/delete.gif\"> Löschen</a></td>";}
else{
echo "<td><img src=\"gfx/delete.gif\"> Löschen</td>";
}

}
echo "</tr>";

?>
</table>

<?php }
else{
noperms();
}




}?>