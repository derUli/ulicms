<!doctype html>
<html>
<head>
<title><?php homepage_title()?> | <?php title()?></title>
<?php base_metas()?>
<link rel="stylesheet" type="text/css" href="<?php echo getTemplateDirPath("default");?>style.css"/>
<style type="text/css">
body{
color:<?php echo getconfig("body-text-color");?>;
background-color:<?php echo getconfig("body-background-color")?>;
font-family:<?php echo getconfig("default-font")?>;
}

#header{
background-color:<?php echo getconfig("header-background-color")?>;
}
</style>
</head>
<body>
<div class="header">
<div class="logo">
<?php
if(getconfig("logo_disabled")=="no")
{
  logo();
?>
<br/>  
<?php
}
else{
?><h1><?php homepage_title()?></h1>
<?php }?>
<span><?php motto()?></span>
</div>
<div class="navbar_top">
</div>
</div>
<div class="clear"></div>
<div class="container">
<div class="content">
<h2>Wartungsmodus</h2>
<hr>

<p>Die Seite befindet sich momentan im Wartungsmodus.<br>
Bitte versuchen Sie es später wieder.</p> 

<br><br>
<br>
</div>
</div>
<div class="news">
</div>
<div style="clear:both;">
</div>

<div class="navbar_down">
</div>
<div class="clear">
</div>


<div class="copyright">
<p>&copy; <?php year()?> by  <?php homepage_owner()?></p>
</div>
</body>
</html>
