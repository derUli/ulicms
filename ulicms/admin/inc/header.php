<!doctype html>
<html>
<head>
<meta name="viewport" content="width=1000, user-scalable=no" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/blue.css" />
<?php
if (is_mobile ()){
     ?>
<link rel="stylesheet" type="text/css" href="css/mobile.css" />
	<?php
    }
?>
<link rel="stylesheet" type="text/css"
	href="scripts/tablesorter/style.css" />
<script type="text/javascript" src="scripts/php.js/strip_tags.js"></script>
<script type="text/javascript" src="scripts/php.js/htmlspecialchars.js"></script>
<script type="text/javascript" src="scripts/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $.ajaxSetup({ cache: false });
  
<?php if(!is_mobile()){
    ?>
  $(window).scroll(function() {
    if ($(this).scrollTop()) {
        $('a.scrollup:hidden').stop(true, true).fadeIn();
    } else {
        $('a.scrollup').stop(true, true).fadeOut();
    }
});
  <?php
    }
?>
});
</script>
<script type="text/javascript" src="scripts/jeditable.js"></script>
<script type="text/javascript" src="scripts/jscolor/jscolor.js"></script>
<script type="text/javascript" src="scripts/jquery.form.min.js"></script>
<?php

if (is_logged_in ()){
     ?>
<script type="text/javascript" src="scripts/cookie.js"></script>
<script type="text/javascript" src="scripts/notification.js"></script>
<script type="text/javascript" src="scripts/jquery-shiftclick.js"></script>
<script type="text/javascript" src="scripts/shift_checkbox.js"></script>
	<?php
    }
?>
<script src="scripts/vallenato/vallenato.js" type="text/javascript"></script>
<link rel="stylesheet" href="scripts/vallenato/vallenato.css"
	type="text/css">
<!--
    Touch Icon Generator BY daik.de
    http://www.web-stuebchen.de
    Lizenz: GNU General Public License
    Copyright: 2014 - 2015 Stephan Heller [daik.de] <heller@daik.de>
-->
<link rel="shortcut icon" type="image/x-icon" href="gfx/favicon.ico"/>
<link rel="icon" type="image/x-icon" href="gfx/favicon.ico"/>
<link rel="icon" type="image/gif" href="gfx/favicon.gif"/>
<link rel="icon" type="image/png" href="gfx/favicon.png"/>
<link rel="apple-touch-icon" href="gfx/apple-touch-icon.png"/>
<link rel="apple-touch-icon" href="gfx/apple-touch-icon-57x57.png" sizes="57x57"/>
<link rel="apple-touch-icon" href="gfx/apple-touch-icon-60x60.png" sizes="60x60"/>
<link rel="apple-touch-icon" href="gfx/apple-touch-icon-72x72.png" sizes="72x72"/>
<link rel="apple-touch-icon" href="gfx/apple-touch-icon-76x76.png" sizes="76x76"/>
<link rel="apple-touch-icon" href="gfx/apple-touch-icon-114x114.png" sizes="114x114"/>
<link rel="apple-touch-icon" href="gfx/apple-touch-icon-120x120.png" sizes="120x120"/>
<link rel="apple-touch-icon" href="gfx/apple-touch-icon-128x128.png" sizes="128x128"/>
<link rel="apple-touch-icon" href="gfx/apple-touch-icon-144x144.png" sizes="144x144"/>
<link rel="apple-touch-icon" href="gfx/apple-touch-icon-152x152.png" sizes="152x152"/>
<link rel="apple-touch-icon" href="gfx/apple-touch-icon-180x180.png" sizes="180x180"/>
<link rel="apple-touch-icon" href="gfx/apple-touch-icon-precomposed.png"/>
<link rel="icon" type="image/png" href="gfx/favicon-16x16.png" sizes="16x16"/>
<link rel="icon" type="image/png" href="gfx/favicon-32x32.png" sizes="32x32"/>
<link rel="icon" type="image/png" href="gfx/favicon-96x96.png" sizes="96x96"/>
<link rel="icon" type="image/png" href="gfx/favicon-160x160.png" sizes="160x160"/>
<link rel="icon" type="image/png" href="gfx/favicon-192x192.png" sizes="192x192"/>
<link rel="icon" type="image/png" href="gfx/favicon-196x196.png" sizes="196x196"/>
<meta name="msapplication-TileImage" content="gfx/win8-tile-144x144.png"/> 
<meta name="msapplication-TileColor" content="#ffffff"/> 
<meta name="msapplication-navbutton-color" content="#ffffff"/> 
<meta name="msapplication-square70x70logo" content="gfx/win8-tile-70x70.png"/> 
<meta name="msapplication-square144x144logo" content="gfx/win8-tile-144x144.png"/> 
<meta name="msapplication-square150x150logo" content="gfx/win8-tile-150x150.png"/> 
<meta name="msapplication-wide310x150logo" content="gfx/win8-tile-310x150.png"/> 
<meta name="msapplication-square310x310logo" content="gfx/win8-tile-310x310.png"/> 


<link rel="stylesheet" type="text/css"
	href="codemirror/lib/codemirror.css">
<script src="codemirror/lib/codemirror.js" type="text/javascript"></script>
<script src="codemirror/mode/php/php.js" type="text/javascript"></script>
<script src="codemirror/mode/xml/xml.js" type="text/javascript"></script>
<link rel="stylesheet" href="codemirror/mode/xml/xml.css"
	type="text/css">
<script src="codemirror/mode/javascript/javascript.js"
	type="text/javascript"></script>
<link rel="stylesheet" href="codemirror/mode/javascript/javascript.css"
	type="text/css">
<script src="codemirror/mode/clike/clike.js"></script>
<link rel="stylesheet" href="codemirror/mode/clike/clike.css"
	type="text/css">
<link rel="stylesheet" href="codemirror/mode/css/css.css"
	type="text/css">
<script src="codemirror/mode/css/css.js" type="text/javascript"></script>
<title>[<?php echo getconfig("homepage_title")?>] - UliCMS</title>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="scripts/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="scripts/util.js"></script>
<script type="text/javascript">
$(document).ready(function(){
     
        $(".tablesorter").tablesorter({widgets: ["zebra"]}); 
    } 
); 
</script>
<?php

add_hook ("admin_head");
?>
</head>
<body>
	<div id="root-container">
		<a name="top"></a>