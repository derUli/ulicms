<!doctype html>
<html>
<head>
<title><?php homepage_title()?> &gt; <?php title()?></title>
<?php base_metas()?>
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo getTemplateDirPath("dogs");?>style.css"/>
<meta name="viewport" content="width=1280, initial-scale=1"/>

</head>
<body>
<div id="root_container">
<img src="templates/images/56blnk.jpg" id="header_image">
<div id="nav_top">
<?php menu("top")?>
</div>
<div class="clear"></div>
<div id="nav_left">
<?php if(getconfig("logo_disabled") == "no"){
logo();
}else{
   echo "<h1 class='website_logo'>";
   homepage_title();
   echo "</h1>";
}?>
<?php menu("left");?>
</div>
<div id="content">
<h1>Wartungsmodus</h1>

<p>Die Seite befindet sich momentan im Wartungsmodus.<br>
Bitte versuchen Sie es später wieder.</p> 
</div>
<div class="clear"></div>
<br/>
<div id="nav_bottom">
<?php menu("down")?>
</div>
<div id="footer">
Powered By <a href="http://www.ulicms.de" target="_blank">UliCMS</a> | &copy; <?php year()?> by <?php homepage_owner()?><br/><a href="http://www.publicdomainpictures.net/view-image.php?image=1105&picture=hund" target="_blank">Hund</a> von Peter Griffin
</div>

</div>
</body>
</html>
