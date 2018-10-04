<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission($_GET["action"])) {
    ?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("media");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
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