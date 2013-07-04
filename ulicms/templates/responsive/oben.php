<!DOCTYPE html>
<html lang="<?php echo getCurrentLanguage();?>">
<head>
<title><?php title()?></title>
<?php base_metas()?>
<link rel="stylesheet" type="text/css" href="<?php echo getTemplateDirPath("responsive");?>style.css"/>
<style type="text/css">
body{
color:<?php echo getconfig("body-text-color");?>;
background-color:<?php echo getconfig("body-background-color")?>;
font-family:<?php echo getconfig("default-font")?>;
}

#header{
background-color:<?php echo getconfig("header-background-color")?>;
}

<?php if(isset($_GET["blog_admin"]) and containsModule(get_requested_pagename(), "blog")){?>
#menu-left-container{
display:none;
}

div#content{
width:97%;
margin-left:25px !important;
border-left:none !important;
}

<?php } ?>

</style>
</head>
<body>
<div id="root-container">
<div id="header">

<?php 
if(getconfig("logo_disabled") == "no"){
   echo '<a href="./">';
   logo();
   echo '</a>';
}
else
{
   echo "<h1>";
   echo '<a href="./">';
   homepage_title();
   echo '</a>';
   echo "</h1>";
}

if(getconfig("motto")){
  echo "<h2>";
  motto();
  echo "</h2>";
}
?>
<hr/>
</div>
<?php language_selection()?>
<div id="menu-left-container">
<?php menu("left")?>

</div>
<div id="content">
