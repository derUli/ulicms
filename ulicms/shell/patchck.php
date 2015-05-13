#!/usr/bin/php -q
<?php
function patchck_usage(){
  echo "patchck - Command Line Patch Management Tool for UliCMS\n";
  echo "UliCMS Version ".cms_version()."\n";
  echo "Copyright (C) 2015 by Ulrich Schmidt\n";
  echo "\n";
  echo "Usage php -f patchck.php [command] [parameters]\n\n";
  echo "Commands:\n";
  echo "avail | available\n";
  echo "Show available patches\n\n";
  echo "installed | list\n";
  echo "List installed patches\n\n";
  echo "install [patch 1] [patch 2]\n";
  echo "install one or multiple patches.\n";
  echo "\"all\" for install all available patches\n\n";
  echo "truncate\n";
  echo "Truncate list of installed patches in database\n\n";
  echo "help\n";
  echo "Show this usage help\n";
  exit();
}

function patchck_available(){
   return file_get_contents_wrapper(PATCH_CHECK_URL, true);
}

if (php_sapi_name() != "cli") {
   die("This script can be run from command line only.");
}

$parent_path = dirname(__file__)."/../";
include $parent_path."init.php";
        array_shift($argv);


	$pkg = new PackageManager ();
        if (count($argv) == 0) {
            patchck_usage();
        } else {
          if($argv[0] == "avail"){
             $available = patchck_available();
             if(!$available)
                $available = "No patches available";
             echo $available;
             exit();
             } else if($argv[0] == "help"){
               patchck_usage();
              } else if($argv[0] == "truncate"){
               $pkg->truncateInstalledPatches();
                die("List of installed patches truncated.\n");
                }else if($argv[0] == "install"){
                if(count($argv) > 1){
                   $available = patchck_available();
		  
                   if($available and !empty($available)){
                   $toinstall = array();
                   for($i = 1; $i  < count($argv); $i++){
                        $toinstall[] = $argv[$i];
                  } $installed_amount = 0;
                        $available = str_ireplace("\r\n", "\n", $available);
			$available = explode("\n", $available);
                        foreach($available as $line){
                        $line = trim($line);
if(!empty($line)){
                       $splitted = explode ( "|", $line );
if(count($splitted) >= 3){
                        if(in_array($splitted[0], $toinstall) or in_array("all", $toinstall)){
				$success = $pkg->installPatch ( $splitted[0], $splitted [1], $splitted[2]);
				if($success){
                                   echo "Patch ".$splitted[0]. " was installed.\n";
				   $installed_amount++;
                                } else {
                                   echo "Installation of patch ".$success. " failed.\n";
                                   echo "Abort.\n";
                                   exit();
                                 }

}

} else {
    echo "Patch ".$splitted[0]." is not available\n";
}

}
}
if($installed_amount != 1 ){
  echo $installed_amount. " patches installed.\n";
} else {
  echo $installed_amount. " patch installed.\n";
}
              } else {
                echo "no patches available\n";
                }
               } else {
                echo "no patches available\n";
}
               
              } else if($argv[0] == "installed"){
		$installed_patches = $pkg->getInstalledPatchNames();
		if(count($installed_patches) > 0){
                   foreach($installed_patches as $patch){
                       echo $patch;
                       echo "\n";
                   }
                } else {
                   echo "No patches installed.\n";
}

              } else {
                 patchck_usage();
}
       }

?>
