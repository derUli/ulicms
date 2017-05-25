<?php
include_once Path::resolve ( "ULICMS_ROOT/lib/formatter.php" );
$acl = new ACL ();
if (! $acl->hasPermission ( "install_packages" )) {
	noperms ();
} else {
	if (StringHelper::isNotNullOrEmpty ( $_REQUEST ["file"] ) and endsWith ( $_REQUEST ["file"], ".sin" )) {
		$tempfile = Path::resolve ( "ULICMS_TMP/" . basename ( $_REQUEST ["file"] ) );
		if (faster_file_exists ( $tempfile )) {
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
			$size = intval ( $pkg->getSize () );
			$size = formatSizeUnits ( $size );
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
	<tr>
		<td><strong><?php translate("size")?></strong></td>
		<td><?php Template::escape($size)?></td>
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
		<td>UliCMS <?php Template::escape($compatible_from);?></td>

	</tr>		
			<?php }?>

			
			<?php
			if ($compatible_to) {
				?>
			<tr>
		<td><strong><?php translate("compatible_to")?></strong></td>
		<td>UliCMS <?php Template::escape($compatible_to);?></td>

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
<div class="license-agreement"><?php
				
				echo nl2br ( Template::getEscape ( $license ) )?></div>
<?php }?>
<?php

			if (! $installable) {
				?>
<h2><?php translate("errors");?></h2>
<?php
				echo implode ( "<br/>", $errors );
			}
			?>
<?php

			if ($installable) {
				?>

<div style="text-align: right; margin-top: 30px;">
	<form action="index.php" method="post">
		<input type="hidden" name="action" value="install-sin-package"> <input
			type="hidden" name="file"
			value="<?php Template::escape(basename($tempfile));?>"> <input
			type="submit" value="<?php translate("install");?>">
				<?php csrf_token_html();?></form>
</div>
<?php
			}
		} else {
			translate ( "file_not_found", array (
					"%file%" => $tempfile 
			) );
		}
	}
}