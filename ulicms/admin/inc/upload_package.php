<?php
$acl = new ACL();
if(!$acl -> hasPermission("install_packages")){
     noperms();
    }else{
    
    $temp_folder = ULICMS_ROOT . DIRECTORY_SEPARATOR . "content" . DIRECTORY_SEPARATOR . "tmp";
    
    if(count($_FILES) > 0){
         $file_in_tmp = $temp_folder . DIRECTORY_SEPARATOR . $_FILES['file']['name'];
         if(move_uploaded_file($_FILES['file']['tmp_name'], $file_in_tmp)){
             $pkg = new packageManager();
             if($pkg -> installPackage($file_in_tmp)){
                 @unlink($file_in_tmp);
                 echo "<p style='color:green'>".str_ireplace("%file%", $_FILES['file']['name'], TRANSLATION_PACKAGE_SUCCESSFULL_UPLOADED)."</p>";
                
                 }else{
                 echo "<p style='color:red'>".str_ireplace("%file%", $_FILES['file']['name'], TRANSLATION_INSALLATION_FAILED)."</p>";
                 }
            
             }else{
             echo "<p style='color:red'>".TRANSLATION_UPLOAD_FAILED."</p>";
             }
        
        }
    
    
    ?>
<h1><?php echo TRANSLATION_UPLOAD_PACKAGE;?></h1>
<form action="?action=upload_package" enctype="multipart/form-data" method="post">
<input type="file" name="file"><br/><br/>
<input type="submit" value="<?php echo TRANSLATION_INSTALL_PACKAGE;?>">
</form>


<?php }

?>