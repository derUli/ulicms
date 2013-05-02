<?php if(!is_admin()){?>
<p>Zugriff verweigert</p>
<?php } else {

$pkg_src = getconfig("pkg_src");

?>

<h1>Verfügbare Pakete</h1>
<?php 
if(!$pkg_src){?>
<p><strong>Fehler:</strong> <br/> pkg_src wurde nicht definiert!</p>
<?php }?>


<?php }?>