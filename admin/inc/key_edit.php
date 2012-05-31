<?php if(defined("_SECURITY")){
if($_SESSION["group"]>=50){
$key=intval($_GET["key"]);
$query=mysql_query("SELECT * FROM ".tbname("settings")." WHERE id='$key'");
while($row=mysql_fetch_object($query)){
?>

<form action="index.php?action=settings" method="post">

<input type="hidden" name="id" value="<?php echo $row->id;?>">
<input type="hidden" name="edit_key" value="edit_key">
<strong>Option:</strong><br/>
<input type="text" style="width:300px;" name="name" value="<?php echo $row->name;?>" readonly="readonly">
<br/><br/>
<strong>Wert:</strong><br/>
<input type="text" style="width:300px;" name="value" value="<?php echo $row->value;?>">

<br/><br/>
<input type="submit" value="OK">
</form>


<?php 
break;
}
?>
<?php 
}
else{
noperms();
}

?>




<?php }?>
