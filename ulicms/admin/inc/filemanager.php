<?php
if (defined ("_SECURITY")){
     $acl = new ACL ();
     if ($acl -> hasPermission ($_GET ["action"])){
         ?>

<h2>
<?php
        
         echo TRANSLATION_MEDIA;
         ?>
</h2>
<iframe
	src="kcfinder/browse.php?type=<?php
        
         echo basename ($_GET ["action"]);
         ?>&lang=de"
	style="border: 0px; width: 100%; height: 500px;"> </iframe>

<?php
         }else{
         noperms ();
         }
    
     ?>




	<?php
    }
?>
