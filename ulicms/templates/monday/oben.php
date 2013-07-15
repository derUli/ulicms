<!doctype html>
<html lang="<?php echo getCurrentLanguage();
?>">
<head>
<?php base_metas()?>
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo getTemplateDirPath("monday");
?>style.css"/>
<?php
if(!getconfig("header-background-color")){
     setconfig("header-background-color", "rgb(35, 148, 96)");
     }

if(!getconfig("body-background-color")){
     setconfig("body-background-color", "rgb(255,255,255)");
     }


if(!getconfig("body-text-color")){
     setconfig("body-text-color", "rgb(0,0,0)");
     }


?>
<style type="text/css">
.header{
background-color:<?php echo getconfig("header-background-color");
?>;
}
</style>
</head>
<body>
<div id="header">
</div>
<div id="rootContainer">
<div id="logo">
<?php 
if(getconfig("logo_disabled") != "yes"){
  logo();
} else{
  echo "<h1 class=\"website_name\">";
  title();
  echo "</h1>";
}?>
</div>
<?php menu("top");?>