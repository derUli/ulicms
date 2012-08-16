<?php if(defined("_SECURITY")){?>
<h2>UliCMS</h2>
<div id="menu">
<a href="index.php?action=home">Willkommen</a> | 

<a href="index.php?action=contents">Inhalte</a> | 
<a href="index.php?action=media">Medien</a> |
<a href="index.php?action=admins">Benutzer</a> | 
<a href="index.php?action=templates">Templates</a> |        
<a href="index.php?action=modules">Module</a> | 
<a href="index.php?action=settings_categories">Einstellungen</a> | 
<?php 
if(is_file("../update.php") and $_SESSION["group"] >= 40){
?>
	<a href="index.php?action=system_update" style="color:red !important; font-size:1.3em;">Update</a> | 
<?php }?>
<a href="index.php?action=info">Info</a> | 
<a href="index.php?destroy" onclick="return confirm('ausloggen?')">Logout</a>
</div>
<div id="pbody">
<?php 
}
?>