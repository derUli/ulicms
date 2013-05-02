<?php if(!is_admin()){?>
<p>Zugriff verweigert</p>
<?php } else {

$pkg_src = getconfig("pkg_src");

?>

<h1>Pakete installieren</h1>
<?php 
if(!$pkg_src){?>
<p><strong>Fehler:</strong> <br/>
pkg_src wurde nicht definiert!</p>
<?php } else if(!class_exists("PharData")){
?>
<p><strong>Fehler:</strong> <br/>
Eine für das Entpacken der Pakete benötigte PHP-Funktion ist nicht verfügbar.<br/>
Bitte aktualisieren Sie die Serversoftware auf PHP Version 5.3.0 oder neuer.
</p>
<?php
}

else {

include_once "../version.php";
$version = new ulicms_version();
$internalVersion = implode(".", $version->getInternalVersion());
$pkg_src = str_replace("{version}", $internalVersion, $pkg_src);

$packageArchiveFolder = $pkg_src."archives/";
$packagesToInstall = explode(",", $_REQUEST["packages"]);


if(count($packagesToInstall) === 0 or empty($_REQUEST["packages"])){
?>
<p><strong>Fehler:</strong> <br/>
Nichts zu tun.</p>

<?php
} else {
for($i=0; $i<count($packagesToInstall); $i++){
  
  if(!empty($packagesToInstall[$i])){
     $pkgURL = $packageArchiveFolder.basename($packagesToInstall[$i]).
     ".tar.gz";
     $pkgContent = @file_get_contents($pkgURL);
     
     // Wenn Paket nicht runtergeladen werden konnte
     if(!$pkgContent or strlen($pkgContent) < 1){
        echo "<p style='color:red;'>Download fehlgeschlagen ($packagesToInstall[$i])"."</p>";
     } else {
       $tmpdir = "../content/tmp/";
       if(!is_dir($tmpdir)){
          mkdir($tmpdir, 0777);
       }
       
       $tmpFile = $tmpdir.$packagesToInstall[$i].".tar.gz";
       
       // write downloaded tarball to file       
       $handle = fopen($tmpFile, "wb");
       $fwrite($handle, $pkgContent);
       fclose($handle);
       
       if(file_exists($tmpFile)){
          
          try{
            $phar = new PharData($tmpFile);
            $phar->extractTo("../", null, true);
            @unlink($tmpFile);
            echo "<p style='color:green;'>Installation erfolgreich ($packagesToInstall[$i])"."</p>";
          } catch (Exception $e) {
            echo "<p style='color:red;'>Entpacken der Datei fehlgeschlagen ($packagesToInstall[$i])"."</p>";
          }
       }
       
     
     }
     
  }
  

}

}

?>


<?php 
}

}?>