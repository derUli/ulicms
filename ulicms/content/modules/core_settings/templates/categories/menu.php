<?php
use UliCMS\Security\PermissionChecker;

$permissionChecker = new PermissionChecker(get_user_id());
?>
<h1><?php translate("settings");?></h1>
<?php

if ($permissionChecker->hasPermission("settings_simple") or $permissionChecker->hasPermission("design") or $permissionChecker->hasPermission("spam_filter") or $permissionChecker->hasPermission("cache") or $permissionChecker->hasPermission("motd") or $permissionChecker->hasPermission("pkg_settings") or $permissionChecker->hasPermission("logo") or $permissionChecker->hasPermission("languages") or $permissionChecker->hasPermission("other")) {
    ?>
<p>
<?php
    
    if ($permissionChecker->hasPermission("settings_simple")) {
        ?>
	<a href="index.php?action=settings_simple"><?php translate("general_settings");?></a>
	<br /> <br />
	<?php
    }
    ?>
<?php
    if ($permissionChecker->hasPermission("design")) {
        ?>
	<a href="index.php?action=design"><?php translate("design");?></a> <br />
	<br />
	<?php
    }
    ?>
<?php
    
    if ($permissionChecker->hasPermission("spam_filter")) {
        ?>
	<a href="index.php?action=spam_filter"><?php translate("spamfilter");?></a>
	<br /> <br />
	<?php
    }
    ?>
	<?php
    
    if ($permissionChecker->hasPermission("privacy_settings")) {
        ?>
	<a href="?action=privacy_settings"><?php translate("privacy");?></a><br />
	<br />
	<?php
    }
    ?>
<?php
    
    if ($permissionChecker->hasPermission("performance_settings")) {
        ?>
	<a
		href="<?php echo ModuleHelper::buildActionURL("performance_settings");?>"><?php
        translate("performance");
        ?></a> <br /> <br />
	<?php
    }
    ?>
    <?php
    
    if ($permissionChecker->hasPermission("community_settings")) {
        ?>
	<a
		href="<?php echo ModuleHelper::buildActionURL("community_settings");?>"><?php
        translate("community");
        ?></a> <br /> <br />
	<?php
    }
    ?>
<?php
    
    if ($permissionChecker->hasPermission("motd")) {
        ?>
	<a href="index.php?action=motd"><?php translate("motd");?></a> <br /> <br />
	<?php
    }
    ?>
<?php
    
    if ($permissionChecker->hasPermission("pkg_settings")) {
        ?>
	<a href="?action=pkg_settings"><?php
        translate("package_source");
        ?></a> <br /> <br />
	<?php
    }
    ?>
<?php
    
    if ($permissionChecker->hasPermission("logo")) {
        ?>
	<a href="index.php?action=logo_upload"><?php translate("logo");?></a> <br />
	<br />
	<?php
    }
    ?>
<?php
    
    if ($permissionChecker->hasPermission("languages")) {
        ?>
	<a href="index.php?action=languages"><?php translate("languages");?></a>
	<br /> <br />
	<?php
    }
    ?>
	<?php
    
    if ($permissionChecker->hasPermission("other")) {
        ?>
	<a href="?action=other_settings"><?php translate("other");?></a>
 	<?php
    }
    ?>
</p>
<?php
} else {
    noPerms();
}

?>
