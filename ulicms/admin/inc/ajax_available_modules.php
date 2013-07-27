<?php if(!is_admin()){
     ?>
<p>Zugriff verweigert</p>
<?php
     dir();
     }

$pkg_src = getconfig("pkg_src");
@set_time_limit(0);

include "../lib/file_get_contents_wrapper.php";

?>
<?php
if(!$pkg_src){
     ?>
<p><strong>Fehler:</strong> <br/>
pkg_src wurde nicht definiert!</p>
<?php }else{
     include_once "../version.php";
     $version = new ulicms_version();
     $internalVersion = implode(".", $version -> getInternalVersion());
     $pkg_src = str_replace("{version}", $internalVersion, $pkg_src);
    
     $packageListURL = $pkg_src . "list.txt";
    
     $packageList = @file_get_contents_wrapper($packageListURL);
    
     if($packageList){
         $packageList = strtr($packageList, array(
                "\r\n" => PHP_EOL,
                 "\r" => PHP_EOL,
                 "\n" => PHP_EOL,
                ));
         $packageList = explode(PHP_EOL, $packageList);
         }
    
    
     if($packageList){
         natcasesort($packageList);
         $packageList = array_filter($packageList, 'strlen');
         }
    
    
    
     if(!$packageList or count($packageList) === 0){
         ?>
<p><strong>Fehler:</strong> <br/>
Keine Pakete verfügbar oder Paketquelle nicht erreichbar.</p>

<?php
         }else{
         for($i = 0; $i < count($packageList); $i++){
             $pkg = trim($packageList[$i]);
            
             if(!empty($pkg)){
                 $pkgDescriptionURL = $pkg_src . "descriptions/" . $pkg . ".txt";
                
                 echo "<p><strong>" . $pkg . "</strong> <a href=\"?action=install_modules&amp;packages=$pkg\" onclick=\"return confirm('$pkg installieren?\\nBestehende Dateien werden überschrieben.');\"> [installieren]</a><br/>";
                
                 $pkgDescription = @file_get_contents_wrapper($pkgDescriptionURL);
                
                 if(!$pkgDescription or strlen($pkgDescription) < 1)
                     echo "Keine Beschreibung verfügbar.";
                 else
                     echo nl2br($pkgDescription);
                
                
                 echo "</p>";
                 flush();
                
                 }
            
             }
        
         }
    
     ?>


<?php
     }
