<!DOCTYPE html>
<!--

Design by Free CSS Templates
http://www.freecsstemplates.org
Released for free under a Creative Commons Attribution 2.5 License

Title      : Night Vision
Version    : 1.0
Released   : 20080119
Description: Three-column blog design with the third column allocated for ads. Features Web 2.0 design ideal for 1024x768 resolutions.

-->
<html lang="<?php echo getCurrentLanguage();?>">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php title()?></title>
<?php base_metas()?>

<link href="<?php echo getTemplateDirPath("nightvision");?>style.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body>
<!-- start header -->
<div id="wrapper">
<div id="header">
	<div id="logo">
		<h1><a href="<?php echo getconfig("frontpage")?>.html"><?php homepage_title()?></a></h1>
		<p><span>by <?php homepage_owner()?></span></p>
	</div>
	
</div>
<!-- end header -->
<!-- star menu -->
<div id="menu">
	<ul>
		<?php menu("top")?>
	</ul>
</div>
<!-- end menu -->
<!-- start page -->
<div id="page">
	<!-- start ads -->
	<div id="ads"><?php random_banner()?></div>
	<!-- end ads -->
	<!-- start content -->
	<div id="content">
		<div class="post">
			<div class="title">
				<h2><a href="#"><?php title()?></a></h2>
				<?php if(!is_404() and !is_403){?>
				<p><small>
				
				<?php autor()?></small></p>
				<?php }?>
			</div>