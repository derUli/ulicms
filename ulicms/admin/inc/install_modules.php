<?php
$acl = new ACL ();
$pkg = new packageManager ();
if (! $acl->hasPermission ( "install_packages" )) {
	?>
<p>Zugriff verweigert</p>
<?php
} else {
	$pkg_src = getconfig ( "pkg_src" );
	@set_time_limit ( 0 );
	?>
<h1>Pakete installieren</h1>
<?php
	if (! $pkg_src) {
		?>
<p>
	<strong><?php
		
		echo TRANSLATION_ERROR;
		?>
	</strong> <br />
	<?php
		echo TRANSLATION_PKGSRC_NOT_DEFINED;
		?></p>
<?php
	} else if (! class_exists ( "PharData" )) {
		?>
<p>
	<strong><?php
		
		echo TRANSLATION_ERROR;
		?>
	</strong> <br />
	<?php
		
		echo TRANSLATION_PHARDATA_NOT_AVAILABLE;
		?></p>
<?php
	} 

	else {
		
		include_once "../version.php";
		$version = new ulicms_version ();
		$internalVersion = implode ( ".", $version->getInternalVersion () );
		$pkg_src = str_replace ( "{version}", $internalVersion, $pkg_src );
		
		$packageArchiveFolder = $pkg_src . "archives/";
		$packagesToInstall = explode ( ",", $_REQUEST ["packages"] );
		
		$post_install_script = "../post-install.php";
		if (file_exists ( $post_install_script ))
			unlink ( $post_install_script );
		
		if (count ( $packagesToInstall ) === 0 or empty ( $_REQUEST ["packages"] )) {
			?>
<p>
	<strong><?php
			
			echo TRANSLATION_ERROR;
			?>/strong> <br /> <?php
			
			echo TRANSLATION_NOTHING_TO_DO;
			?>
</strong></p>

<?php
		} else {
			for($i = 0; $i < count ( $packagesToInstall ); $i ++) {
				
				if (! empty ( $packagesToInstall [$i] )) {
					$pkgURL = $packageArchiveFolder . basename ( $packagesToInstall [$i] ) . ".tar.gz";
					$pkgContent = @file_get_contents_wrapper ( $pkgURL );
					
					// Wenn Paket nicht runtergeladen werden konnte
					if (! $pkgContent or strlen ( $pkgContent ) < 1) {
						echo "<p style='color:red;'>" . str_ireplace ( "%pkg%", $packagesToInstall [$i], TRANSLATION_DOWNLOAD_FAILED ) . "</p>";
					} else {
						$tmpdir = "../content/tmp/";
						if (! is_dir ( $tmpdir )) {
							mkdir ( $tmpdir, 0777 );
						}
						
						$tmpFile = $tmpdir . $packagesToInstall [$i] . ".tar.gz";
						
						// write downloaded tarball to file
						$handle = fopen ( $tmpFile, "wb" );
						fwrite ( $handle, $pkgContent );
						fclose ( $handle );
						
						if (file_exists ( $tmpFile )) {
							// Paket installieren
							if ($pkg->installPackage ( $tmpFile )) {
								echo "<p style='color:green;'>" . str_ireplace ( "%pkg%", $packagesToInstall [$i], TRANSLATION_INSTALLATION_SUCCESSFULL ) . "</p>";
							} else {
								echo "<p style='color:red;'>" . str_ireplace ( "%pkg%", $packagesToInstall [$i], TRANSLATION_EXTRACTION_OF_PACKAGE_FAILED ) . "</p>";
							}
						}
						@unlink ( $tmpFile );
					}
				}
			}
		}
		
		?>


		<?php
	}
}
?>