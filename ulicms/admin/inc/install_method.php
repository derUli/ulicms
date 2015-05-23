<?php
$acl = new ACL ();
if (! $acl -> hasPermission ("install_packages")){
     noperms ();
    }else{
     ?>
<h1>
<?php
    
     echo TRANSLATION_INSTALL_PACKAGE;
     ?>
</h1>
<p>
	<a href="?action=upload_package"><?php
    
     echo TRANSLATION_UPLOAD_FILE;
     ?>
	</a> <br /> <a href="?action=available_modules"><?php
    
     echo TRANSLATION_FROM_THE_PACKAGE_SOURCE;
     ?>
	</a>
</p>

<?php
    }

?>