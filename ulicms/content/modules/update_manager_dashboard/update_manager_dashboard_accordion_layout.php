<?php
$acl = new ACL ();
if ($acl->hasPermission ( "install_packages" )) {
	?>
<div id="update-manager-dashboard-container" style="display: none">
	<h2 class="accordion-header"><?php translate("update_manager");?></h2>
	<div class="accordion-content">
		<?php translate("PACKAGE_UPDATES_ARE_AVAILABLE")?><br /> <a
			href="<?php echo ModuleHelper::buildAdminURL("update_manager");?>">[<?php translate("install_updates");?>]
			</a>
	</div>
</div>
<script
	src="<?php echo getModulePath("update_manager_dashboard");?>scripts/update_manager_dashboard.js"></script>
<?php
}