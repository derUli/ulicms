<?php
$acl = new ACL();
if(!$acl -> hasPermission("install_packages")){
   noperms();
} else {
?>
<h1>Pakete installieren</h1>
<p>
<a href="?action=upload_package">Datei hochladen</a>
<br/><a href="?action=available_modules">Aus der Paketquelle</a>
</p>

<?php }

?>