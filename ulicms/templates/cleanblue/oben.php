<!doctype html>
<html lang="<?php echo getCurrentLanguage();
?>">
<head>
<meta name="viewport" content="width=1024"/>
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo getTemplateDirPath("cleanblue");
?>style.css"/>
<?php base_metas()?>
<style type="text/css">
header{
background-color:<?php echo getconfig("header-background-color");
?>;
}
</style>
</head>
<body>
<div id="root-container">
<header>
<section id="logo">
<a href="./">
<?php
if(getconfig("logo_disabled") == "no")
    {
     logo();
     ?>
<br/>  
<?php
     }
else{
     ?><strong><?php homepage_title();?></strong>
<?php }
?>
</a>
</section>
<nav><?php menu("top");?></nav>
</header>
<main>
<?php if(!containsModule()){
?>
<h1><?php headline();?></h1>
<?php
}
?>