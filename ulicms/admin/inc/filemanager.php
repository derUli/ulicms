﻿<?php if(defined("_SECURITY")){
     if($acl->hasPermission($_GET["action"])){
         ?>

<h2>Dateimanager</h2>
<iframe src="kcfinder/browse.php?type=<?php echo basename($_GET["action"]);
         ?>&lang=de" style="border:0px;width:80%; height:500px;">
</iframe>

<?php
         }
    else{
         noperms();
         }
    
     ?>




<?php }
?>
