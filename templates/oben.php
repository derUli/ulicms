<!doctype html>
<html>
<head>
<title><?php homepage_title()?> | <?php title()?></title>
<?php base_metas()?>
<link rel="stylesheet" type="text/css" href="templates/style.css"/>
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
<?php menu("top")?>
</div>
</div>
<div class="clear"></div>
<div class="container">
<div class="content">
<h2><?php title()?></h2>
<em style="font-size:0.8em"><?php autor()?></em>
<hr>