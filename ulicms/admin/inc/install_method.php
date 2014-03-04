<?php
$acl = new ACL();
if(!$acl -> hasPermission("install_packages")){
   noperms();
} else {
?>
<h1>Pakete installieren</h1>
<p><a href="?action=available_modules">Aus einer Paketquelle</a>
<br/>
<a href="?action=upload_package">Datei hochladen</a></p>

<?php }

?>