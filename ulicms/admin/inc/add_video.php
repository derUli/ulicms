<?php
$acl = new ACL ();
if ($acl -> hasPermission ("videos")){
     ?>
<h1>
<?php
    
     translate ("UPLOAD_VIDEO");
     ?>
</h1>
<form action="index.php?action=videos" method="post"
	enctype="multipart/form-data">
	<input type="hidden" name="add" value="add">
	<?php
    
     csrf_token_html ();
     ?>
	<strong><?php
    
     translate ("name");
     ?>
	</strong><br /> <input type="text" name="name" required="true" value=""
		maxlength=255 /> <br /> <br /> <strong><?php
    
     echo TRANSLATION_CATEGORY;
     ?>
	</strong><br />
	<?php
     echo categories :: getHTMLSelect ();
     ?>

	<br /> <br /> <strong><?php
    
     echo translate ("video_ogg");
     ?>
	</strong><br /> <input name="ogg_file" type="file"><br /> <br /> <strong><?php
    
     echo translate ("video_webm");
     ?>
	</strong><br /> <input name="webm_file" type="file"><br /> <br /> <strong><?php
    
     echo translate ("video_mp4");
     ?>
	</strong><br /> <input name="mp4_file" type="file"><br /> <br /> <strong><?php
    
     translate ("width");
     ?>
	</strong><br /> <input type="number" name="width" value="1280" step="1">
	<br /> <br /> <strong><?php
    
     translate ("height");
     ?></strong><br /> <input type="number" name="height" value="720"
		step="1"> <br /> <br /> <input type="submit"
		value="<?php
    
     translate ("UPLOAD_VIDEO");
     ?>">
</form>
<?php
    }else{
     noperms ();
    }
