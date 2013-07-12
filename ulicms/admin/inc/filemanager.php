<?php if(defined("_SECURITY")){
     if($_SESSION["group"] >= 30){
         ?>

<h2>Dateimanager</h2>
<iframe src="kcfinder/browse.php?type=<?php echo $_GET["action"];
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
