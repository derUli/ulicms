<?php
$acl = new ACL ();
if (! $acl->hasPermission ( "install_packages" )) {
	noperms ();
} else {
	if (isNotNullOrEmpty ( $_REQUEST ["file"] ) and endsWith ( $_REQUEST ["file"], ".sin" )) {
		$tempfile = Path::resolve ( "ULICMS_TMP/" . basename ( $_REQUEST ["file"] ) );
		if (file_exists ( $tempfile )) {
			$pkg = new SinPackageInstaller ( $tempfile );
			$installable = $pkg->isInstallable ();
			$errors = $pkg->getErrors ();
			
			$id = $pkg->getProperty ( "id" );
			$version = $pkg->getProperty ( "version" );
			$name = $pkg->getProperty ( "name" );
			$description = $pkg->getProperty ( "description" );
			$compatible_from = $pkg->getProperty ( "compatible_from" );
			$compatible_to = $pkg->getProperty ( "compatible_to" );
			$dependencies = $pkg->getProperty ( "dependencies" );
			$license = $pkg->getProperty ( "license" );
				
			?>
<h1><?php
			
			Template::escape ( $id );
			?></h1>
<table>
			<?php
			if ($name) {
				?>
			<tr>
		<td><strong><?php translate("name")?></strong></td>
		<td><?php Template::escape($name)?></td>

	</tr>		
			<?php }?>
			
			<tr>
		<td><strong><?php translate("version")?></strong></td>
		<td><?php Template::escape($version)?></td>
	</tr>
	
			<?php
			if ($description) {
				?>
			<tr>
		<td><strong><?php translate("description")?></strong></td>
		<td><?php Template::escape($description);?></td>

	</tr>		
			<?php }?>
			
			
			<?php
			if ($compatible_from) {
				?>
			<tr>
		<td><strong><?php translate("compatible_from")?></strong></td>
		<td><?php Template::escape($compatible_from);?></td>

	</tr>		
			<?php }?>

			
			<?php
			if ($compatible_to) {
				?>
			<tr>
		<td><strong><?php translate("compatible_to")?></strong></td>
		<td><?php Template::escape($compatible_to);?></td>

	</tr>		
			<?php }?>

			<?php
			if ($dependencies) {
				?>
			<tr>
		<td><strong><?php translate("dependencies")?></strong></td>
		<td><?php
				
foreach ( $dependencies as $dep ) {
					?>
		<?php Template::escape($dep);?><br />
		<?php }?></td>

	</tr>		
			<?php }?>
</table>

<?php if($license){?>
<h2><?php translate("license_agreement");?></h2>
<div class="license-agreement"><?php echo nl2br(Template::getEscape
		($license))?></div>
<?php }?>
<?php
			if (! $installable) {
			}
		} else {
			translate ( "file_not_found", array (
					"%file%" => $tempfile 
			) );
		}
	}
}