<!doctype html>
<html lang="<?php echo getCurrentLanguage(true);?>">
<head>
<title><?php homepage_title()?> &gt; <?php title()?></title>
<?php base_metas()?>
<link rel="stylesheet" type="text/css" href="<?php echo getTemplateDirPath("zwanzigdreizehn");?>style.css"/>
</head>
<body>
<div class="root">
<div class="header">
<h1><?php homepage_title()?></h1>
<span><?php motto()?></span>
</div>
<div class="menu">
<?php menu("top");?>
</div>
<div class="container">
<div class="content">
<h2><?php title()?></h2>    
