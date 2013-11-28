<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<?php base_metas(); ?>
<link rel="stylesheet" type="text/css" href="<?php echo getTemplateDirPath("rounded2");?>style.css" />
</head>
<body>
	<div id="wrapper">
		<div id="top">
		</div>
		<div id="content">
			<div id="header">
			
			<?php 
			if(getconfig("logo_disabled") == "no"){
			logo();
			} else {
			homepage_title();
			}?>
			
			</div>
			<div id="menu">
				<?php menu("left"); ?>
			</div>
			<div id="stuff">
			