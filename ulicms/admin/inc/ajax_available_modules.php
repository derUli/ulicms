<?php
$acl = new ACL ();
if (! $acl->hasPermission ( "install_packages" )) {
	noperms ();
	?>
	<?php
}

$pkg_src = Settings::get ( "pkg_src" );
@set_time_limit ( 0 );

include_once "../lib/file_get_contents_wrapper.php";
if (! $pkg_src) {
	?>
<p>
	<strong><?php translate("error");?> </strong> <br />
	<?php
	
	translate ( "pkgsrc_not_defined" );
	?>
</p>
<?php
} else {
	include_once "../version.php";
	$version = new ulicms_version ();
	$internalVersion = implode ( ".", $version->getInternalVersion () );
	$pkg_src = str_replace ( "{version}", $internalVersion, $pkg_src );
	
	$packageListURL = $pkg_src . "list.txt";
	
	$packageList = @file_get_contents_wrapper ( $packageListURL );
	
	if ($packageList) {
		$packageList = strtr ( $packageList, array (
				"\r\n" => PHP_EOL,
				"\r" => PHP_EOL,
				"\n" => PHP_EOL 
		) );
		$packageList = explode ( PHP_EOL, $packageList );
	}
	
	if ($packageList) {
		natcasesort ( $packageList );
		$packageList = array_filter ( $packageList, 'strlen' );
	}
	
	if (! $packageList or count ( $packageList ) === 0) {
		?>
<p>
	<strong><?php translate("error");?> </strong> <br />
	<?php translate("no_packages_available");?>
</p>
<?php
	} else {
		for($i = 0; $i < count ( $packageList ); $i ++) {
			$pkg = trim ( $packageList [$i] );
			
			if (! empty ( $pkg )) {
				$pkgDescriptionURL = $pkg_src . "descriptions/" . $pkg . ".txt";
				echo "<p><strong>" . $pkg . "</strong> <a href=\"?action=install_modules&amp;packages=$pkg\" onclick=\"return confirm('" . str_ireplace ( "%pkg%", $pkg, get_translation ( "ASK_FOR_INSTALL_PACKAGE" ) ) . "');\"> [" . get_translation ( "install" ) . "]</a><br/>";
				
				$pkgDescription = @file_get_contents_wrapper ( $pkgDescriptionURL );
				
				if (! $pkgDescription or strlen ( $pkgDescription ) < 1) {
					translate ( "no_description_available" );
				} else {
					echo nl2br ( $pkgDescription );
				}
				
				echo "</p>";
				fcflush ();
			}
		}
	}
}
