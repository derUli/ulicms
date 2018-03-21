<?php
$acl = new ACL ();
if ($acl->hasPermission ( "update_system" )) {
	// No time limit
	@set_time_limit ( 0 );
	
	?>
<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("available_patches");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate ( "install_patches" );?></h1>
<?php
	$patches = $_POST ["patches"];
	$pkg = new PackageManager ();
	
	if (count ( $patches ) <= 0) {
		translate ( "no_patches_selected" );
	} else {
		foreach ( $patches as $patch ) {
			$splitted = explode ( "|", $patch );

			$checksum = count($splitted) >= 4 ?  $splitted [3] : null;
			$success = $pkg->installPatch ( $splitted [0], $splitted [1], $splitted [2], false, $checksum );
			
			if ($success) {
				echo '<p style="color:green">' . htmlspecialchars ( $splitted [0] ) . " " . get_translation ( "was_successfully_installed" ) . '</p>';
			} else {
				echo '<p style="color:red">' . get_translation ( "installation_of" ) . " " . htmlspecialchars ( $splitted [0] ) . " " . get_Translation ( "is_failed" ) . "</p>";
			}
			fcflush ();
		}
	}
} else {
	noperms ();
}
