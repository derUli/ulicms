<?php 
define("MODULE_ADMIN_HEADLINE", "Bildkompression");

$required_permission = getconfig("compress_image_required_permission");

if($required_permission === false){
   $required_permission = 50;
}

define(MODULE_ADMIN_REQUIRED_PERMISSION, $required_permission);

include_once getModulePath("compress_images")."compress_images_lib.php";

if(isset($_POST["submit"])){
   // Max Execution Time auf Endlos
   @set_time_limit(0);
   @ini_set('max_execution_time', 0);

   $quality = getconfig("image_quality");
   if(!$quality)
     $quality = 70;
   
   $image_dir = "../content/images";
   
   if(is_dir($image_dir)){
      $files = find_all_files($image_dir);
      for($i=0; $i < count($files); $i++);
        $f = $files[$i];
        $ext = pathinfo($f, PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        if($ext === "jpg" or $ext === "jpeg" or $ext === "png" or $ext === "gif"){
           echo "Komprimiere ".basename($f)."... ";
           flush();
           compress_image($f, $f, $quality);
           echo "<span stylr='color:green'>[fertig]</span>";
           flush();
        }

   }
}

// Konfiguration checken
$send_comments_via_email = getconfig("blog_send_comments_via_email") == "yes";

function compress_images_admin(){
?>

<form action="<?php echo getModuleAdminSelfPath()?>" method="post">
<input type="submit" name="submit" value="Kompression durchführen"/>
</form>
<?php
}
 
?>
