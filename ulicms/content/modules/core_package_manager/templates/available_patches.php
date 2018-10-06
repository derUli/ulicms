<?php
$acl = new ACL ();
// no patch check in google cloud
$runningInGoogleCloud = class_exists ( "GoogleCloudHelper" ) ? GoogleCloudHelper::isProduction () : false;

if ($acl->hasPermission ( "update_system" ) and ! $runningInGoogleCloud) {
	$patches = file_get_contents_wrapper ( PATCH_CHECK_URL, true );
	?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("home");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate ( "available_patches" );?></h1>
<?php
	if (! $patches or empty ( $patches )) {
		echo "<p class='ulicms_error'>" . get_translation ( "no_patches_available" ) . "</p>";
	} else {
		?>
<form action="index.php?action=install_patches" method="post">
<?php csrf_token_html ();?>
<?php
		$lines = explode ( "\n", $patches );
		foreach ( $lines as $line ) {
			if (! empty ( $line )) {
				$splitted = explode ( "|", $line );
				$name = $splitted [0];
				$description = $splitted [1];
				$url = $splitted [2];
				?>
	<p>
		<label> <input name="patches[]" type="checkbox" checked="checked"
			value="<?php echo htmlspecialchars ( $line );?>"> <strong><?php	echo htmlspecialchars ( $name );?></strong><br /> <?php echo htmlspecialchars ( $description );?> </label>
	</p>
	<?php
			}
		}
		?>
	<button type="submit" class="btn btn-warning"><?php translate ( "install_selected_patches" );?></button>
	<button type="button"
		onclick="window.open('?action=help&help=patch_install');"
		class="btn btn-info"><?php translate ( "help" );?></button>
</form>
<?php
	}
} else {
	noPerms ();
}
