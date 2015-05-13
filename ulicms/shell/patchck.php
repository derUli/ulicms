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
              } else if($argv[0] == "installed"){
      		$pkg = new PackageManager ();
		$installed_patches = $pkg->getInstalledPatchNames();
		if(count($installed_patches) > 0){
                   foreach($installed_patches as $patch){
                       echo $patch;
                       echo "\n";
                   }
                } else {
                   echo "No patches installed.\n";
}

              }
       }

?>
