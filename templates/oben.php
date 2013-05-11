<!DOCTYPE html>
<html>
<head>
<title><?php title()?></title>
<?php base_metas()?>
<link rel="stylesheet" type="text/css" href="templates/style.css"/>
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
<div id="root-container">
<div id="header">
<?php 
if(getconfig("logo_disabled") == "no"){
   logo();
}
else
{
   echo "<h1>";
   homepage_title();
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