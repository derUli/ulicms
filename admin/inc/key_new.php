﻿<?php if(defined("_SECURITY")){
if($_SESSION["group"]>=50){
?>

<form action="index.php?action=settings" method="post">
<input type="hidden" name="add_key" value="add_key">
<strong>Option:</strong><br/>
<input type="text" style="width:300px;" name="name" value="">
<br/><br/>
<strong>Wert:</strong><br/>
<input type="text" style="width:300px;" name="value" value="">

<br/><br/>
<input type="submit" value="Datensatz hinzufügen">
</form>

<?php 
}
else{
noperms();
}

?>




<?php }?>
