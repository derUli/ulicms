<!doctype html>
<html lang="<?php echo getCurrentLanguage();?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php homepage_title()?> - <?php title() ?></title>
<?php base_metas()?>
<link rel="shortcut icon" href="favicon.ico" />
<!-- Load CSS -->
<link href="<?php echo getTemplateDirPath("effe");?>css/style.css" rel="stylesheet" type="text/css" />
<!-- Load Fonts -->
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Droid+Serif:regular,italic,bold,bolditalic" type="text/css" />
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold" type="text/css" />
<!-- Load jQuery library -->
<script type="text/javascript" src="<?php echo getTemplateDirPath("effe");?>scripts/jquery-1.6.2.min.js"></script>
<!-- Load custom js -->
<script type="text/javascript" src="<?php echo getTemplateDirPath("effe");?>scripts/panelslide.js"></script>
<script type="text/javascript" src="<?php echo getTemplateDirPath("effe");?>scripts/custom.js"></script>
<!-- Load topcontrol js -->
<script type="text/javascript" src="<?php echo getTemplateDirPath("effe");?>scripts/scrolltopcontrol.js"></script>
<!-- Load NIVO Slider -->
<link rel="stylesheet" href="<?php echo getTemplateDirPath("effe");?>css/nivo-slider.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo getTemplateDirPath("effe");?>css/nivo-theme.css" type="text/css" media="screen" />
<script src="<?php echo getTemplateDirPath("effe");?>scripts/jquery.nivo.slider.pack.js" type="text/javascript"></script>
<script src="<?php echo getTemplateDirPath("effe");?>scripts/nivo-options.js" type="text/javascript"></script>
<!-- Load fancybox -->
<script type="text/javascript" src="<?php echo getTemplateDirPath("effe");?>scripts/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="<?php echo getTemplateDirPath("effe");?>scripts/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="<?php echo getTemplateDirPath("effe");?>scripts/jquery.mousewheel-3.0.4.pack.js"></script>
<link rel="stylesheet" href="<?php echo getTemplateDirPath("effe");?>css/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />

</head>
<body>
<!--This is the START of the header-->
<div id="topcontrol" style="position: fixed; bottom: 5px; left: 960px; opacity: 1; cursor: pointer;" title="Go to Top"></div>
<div id="header-wrapper">
  <div id="header">
    <div id="logo"><?php if(getconfig("logo_disabled") == "no"){logo();}?></div>
    <div id="header-text">
      <h4><?php motto() ?></h4>
      <h6><?php title() ?></h6>
    </div>
  </div>
</div>
<!--END of header-->
<!--This is the START of the menu-->
<div id="menu-wrapper">
  <div id="main-menu">
    <?php menu("left")?>
  </div>
	<!--This is the START of the footer-->
	<div id="footer">
		
		<h6>Copyright © <?php year()?> - <?php homepage_owner()?></h6>
	</div>
	<!--END of footer-->
</div>
<!--END of menu-->
<!--This is the START of the content-->
<div id="content">
<h3><?php title()?></h3>