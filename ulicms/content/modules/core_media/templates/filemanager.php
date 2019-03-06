<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission($_GET["action"])) {
    ?>
<?php echo Template::executeModuleTemplate("core_media", "icons.php");?>
<h2>
<?php translate("media"); ?>
</h2>
<iframe
	src="kcfinder/browse.php?type=<?php
    
    echo basename($_GET["action"]);
    ?>&lang=<?php echo htmlspecialchars(getSystemLanguage());?>"
	style="border: 0px; width: 100%; height: 500px;"> </iframe>

<?php
} else {
    noPerms();
}

?>