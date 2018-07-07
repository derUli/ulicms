<?php
$acl = new ACL();
$pkg = new PackageManager();
if (! $acl->hasPermission("install_packages")) {
    noPerms();
} else {
    $pkg_src = Settings::get("pkg_src");
    @set_time_limit(0);
    ?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("modules");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate("install");?></h1>
<?php
    if (! $pkg_src) {
        ?>
<p>
	<strong><?php translation ( "error" );?></strong> <br />
	<?php translate ( "pkgsrc_not_defined" );?></p>
<?php
    } else if (! class_exists("PharData")) {
        ?>
<p>
	<strong><?php translate ( "error" );?></strong> <br />
	<?php translate ( "phardata_not_available" );?></p>
<?php
    } else {
        $version = new UliCMSVersion();
        $internalVersion = implode(".", $version->getInternalVersion());
        $pkg_src = str_replace("{version}", $internalVersion, $pkg_src);
        
        $packageArchiveFolder = $pkg_src . "archives/";
        $packagesToInstall = explode(",", $_REQUEST["packages"]);
        
        $post_install_script = ULICMS_DATA_STORAGE_ROOT . "/post-install.php";
        if (is_file($post_install_script)) {
            unlink($post_install_script);
        }
        
        if (count($packagesToInstall) === 0 or empty($_REQUEST["packages"])) {
            ?>
<p>
	<strong><?php translate("error");?></strong> <br />
	 <?php translate("nothing_to_do");?>
</p>

<?php
        } else {
            for ($i = 0; $i < count($packagesToInstall); $i ++) {
                if (! empty($packagesToInstall[$i])) {
                    $pkgURL = $packageArchiveFolder . basename($packagesToInstall[$i]) . ".tar.gz";
                    $pkgContent = @file_get_contents_wrapper($pkgURL);
                    
                    // Wenn Paket nicht runtergeladen werden konnte
                    if (! $pkgContent or strlen($pkgContent) < 1) {
                        echo "<p style='color:red;'>" . str_ireplace("%pkg%", $packagesToInstall[$i], get_translation("download_failed")) . "</p>";
                    } else {
                        $tmpdir = "../content/tmp/";
                        if (! is_dir($tmpdir)) {
                            mkdir($tmpdir, 0777);
                        }
                        
                        $tmpFile = $tmpdir . $packagesToInstall[$i] . ".tar.gz";
                        
                        // write downloaded tarball to file
                        $handle = fopen($tmpFile, "wb");
                        fwrite($handle, $pkgContent);
                        fclose($handle);
                        
                        if (is_file($tmpFile)) {
                            // Paket installieren
                            if ($pkg->installPackage($tmpFile, false)) {
                                echo "<p style='color:green;'>" . str_ireplace("%pkg%", $packagesToInstall[$i], get_translation("INSTALLATION_SUCCESSFULL")) . "</p>";
                            } else {
                                echo "<p style='color:red;'>" . str_ireplace("%pkg%", $packagesToInstall[$i], get_translation("EXTRACTION_OF_PACKAGE_FAILED")) . "</p>";
                            }
                        }
                        @unlink($tmpFile);
                    }
                }
            }
            
            clearCache();
            ?>
<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("available_modules");?>"
		class="btn btn-default"><?php translate("install_another_package")?></a>
</p>
<?php
        }
    }
}
