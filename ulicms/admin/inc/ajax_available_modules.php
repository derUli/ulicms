<?php
$acl = new ACL();
if(!$acl -> hasPermission("install_packages")){
     noperms();
     ?>
<?php
     }

$pkg_src = getconfig("pkg_src");
@set_time_limit(0);

include_once "../lib/file_get_contents_wrapper.php";

?>
<?php
if(!$pkg_src){
     ?>
<p><strong><?php echo TRANSLATION_ERROR;
     ?></strong> <br/>
<?php echo TRANSLATION_PKGSRC_NOT_DEFINED;
     ?>
</p>
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
<p><strong><?php echo TRANSLATION_ERROR;
         ?></strong> <br/>
<?php echo TRANSLATION_NO_PACKAGES_AVAILABLE;
         ?></p>

<?php
         }else{
         for($i = 0; $i < count($packageList); $i++){
             $pkg = trim($packageList[$i]);
            
             if(!empty($pkg)){
                 $pkgDescriptionURL = $pkg_src . "descriptions/" . $pkg . ".txt";
                
                 echo "<p><strong>" . $pkg . "</strong> <a href=\"?action=install_modules&amp;packages=$pkg\" onclick=\"return confirm('" . str_ireplace("%pkg%", $pkg, TRANSLATION_ASK_FOR_INSTALL_PACKAGE) . "');\"> [" . TRANSLATION_INSTALL . "]</a><br/>";
                
                 $pkgDescription = @file_get_contents_wrapper($pkgDescriptionURL);
                
                 if(!$pkgDescription or strlen($pkgDescription) < 1)
                     echo TRANSLATION_NO_DESCRIPTION_AVAILABLE;
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
