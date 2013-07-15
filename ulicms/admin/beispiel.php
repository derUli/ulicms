<?php 

function rootDirectory() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "https";}
 $pageURL .= "://";
 $dirname = str_replace("admin", "", dirname($_SERVER["REQUEST_URI"]));
 $dirname = str_replace("\\", "/", $dirname);
 $dirname = trim($dirname, "/");
 if($dirname != ""){
    $dirname = "/".$dirname."/";
 } else {
   $dirname = "/";
 }
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$dirname;
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$dirname;
 }
 return $pageURL;
}

?>
<?php echo rootDirectory();?>