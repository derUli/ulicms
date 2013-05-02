<?php if(!is_admin()){?>
<p>Zugriff verweigert</p>
<?php } else {

$pkg_src = getconfig("pkg_src");

?>

<h1>Verfügbare Pakete</h1>
<?php 
if(!$pkg_src){?>
<p><strong>Fehler:</strong> <br/>
pkg_src wurde nicht definiert!</p>
<?php } else {
$packageListURL = $pkg_src."list.txt";
$packageList = @file($packageListURL);

if(!$packageList or count($packageList) === 0){
?>
<p><strong>Fehler:</strong> <br/>
Keine Pakete verfügbar oder Paketquelle nicht erreichbar.</p>

<?php
} else {
for($i=0; $i<count($packageList); $i++){
  $pkg = $packageList[$i];
  $pkgDescriptionURL = $pkg_src."descriptions/".$pkg.".txt";
  
  echo "<p><strong>".$pkg."</strong><br/>";
  
  $pkgDescription = @file_get_contents($pkgDescriptionURL);
  
  if(!$pkgDescription or strlen($pkgDescription) < 1)
     echo "Keine Beschreibung verfügbar.";
  else
     echo nl2br($pkgDescripiton);
  
  
  echo "</p>";

}

}

?>


<?php 
}

}?>