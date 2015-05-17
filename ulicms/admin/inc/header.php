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
  <link rel="shortcut icon" href="gfx/favicon.ico" type="image/x-icon" />
  <link rel="apple-touch-icon" href="gfx/apple-touch-icon.png" />
  <link rel="apple-touch-icon" sizes="57x57" href="gfx/apple-touch-icon-57x57.png" />
  <link rel="apple-touch-icon" sizes="72x72" href="gfx/apple-touch-icon-72x72.png" />
  <link rel="apple-touch-icon" sizes="76x76" href="gfx/apple-touch-icon-76x76.png" />
  <link rel="apple-touch-icon" sizes="114x114" href="gfx/apple-touch-icon-114x114.png" />
  <link rel="apple-touch-icon" sizes="120x120" href="gfx/apple-touch-icon-120x120.png" />
  <link rel="apple-touch-icon" sizes="144x144" href="gfx/apple-touch-icon-144x144.png" />
  <link rel="apple-touch-icon" sizes="152x152" href="gfx/apple-touch-icon-152x152.png" />
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