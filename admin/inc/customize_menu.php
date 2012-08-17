<h2>Menüband anpassen</h2>
<?php if(defined("_SECURITY")){
if($_SESSION["group"]>=50){

$menu_items = mysql_query("SELECT * FROM ".tbname("backend_menu_structure")." ORDER BY position ASC");

?>
<form action="index.php?action=customize_menu" method="post">
<table border=0>
<tr>
<td><strong>Beschriftung:</strong> </td>
<td><input type="text" name="label" maxlength=100 size=40></td>
</tr>
<tr>
<td style="width:100px;"><strong>Action:</strong> </td>
<td><input type="text" name="action" maxlength=100 size=40></td>
</tr>
<tr>
<td></td>
<td><input type="submit" name="add_menu_item" value="Menüpunkt anlegen"></td>
</tr>  

</table>
</form>
<br>
<hr>
<br>
<?php if(mysql_num_rows($menu_items)>0){?>
<table border=1 style="width:800px;"> 
<tr>
<td><strong>Position</strong></td>
<td><strong>Beschriftung</strong></td>
<td><strong>Action</td>
<td></td>
<td></td>  
<td>
</td>
</tr>
<?php
while($row = mysql_fetch_object($menu_items)){?>
<tr>
<td>
<?php echo $row->position?>
</td>  
<td>
<?php echo htmlspecialchars($row->label)?>
</td>
<td><?php echo htmlspecialchars($row->action);?></td>

<td align="center">
<a href="index.php?action=customize_menu&up=<?php echo $row->position;?>">
<img src="gfx/up.gif" alt="Nach Oben" title="Nach Oben"></a>
</td>

<td align="center">
<a href="index.php?action=customize_menu&down=<?php echo $row->position;?>">
<img src="gfx/down.gif" alt="Nach Unten" title="Nach Unten"></a>
</td>


<td align="center">
<a href="index.php?action=customize_menu&delete=<?php echo $row->position;?>">
<img src="gfx/delete.gif" alt="Löschen" title="Löschen"></a>
</td>



<?php }?>
</table>
<?php
}
?>



<?php
}
else{
  noperms();
}
?>

<?php }?>
