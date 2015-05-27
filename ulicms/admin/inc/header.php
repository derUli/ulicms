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
<?php 
$enq = array("scripts/php.js/strip_tags.js", "scripts/php.js/htmlspecialchars.js",
"scripts/jquery.min.js",
"scripts/jquery.form.min.js",
"scripts/jquery.tablesorter.min.js",
"scripts/vallenato/vallenato.js",
"codemirror/lib/codemirror.js",
"codemirror/mode/php/php.js",
"codemirror/mode/xml/xml.js",
"codemirror/mode/javascript/javascript.js",
"codemirror/mode/clike/clike.js",
"codemirror/mode/css/css.js",
"scripts/util.js"
);
?>
<?php
if (is_logged_in ()){

$enq[] = "scripts/cookie.js";
$enq[] = "scripts/notification.js";
$enq[] = "scripts/jquery-shiftclick.js";
$enq[] = "scripts/shift_checkbox.js";

    }
?>
<?php
foreach($enq as $script){
  enqueueScriptFile($script);
}
?>
<script type="text/javascript" src="<?php echo getCombinedScriptURL();?>"></script>

<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="scripts/jscolor/jscolor.js"></script>
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
<link rel="stylesheet" href="scripts/vallenato/vallenato.css"
	type="text/css">
  <link rel="icon" href="gfx/favicon.ico" type="image/x-icon" />
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
<link rel="stylesheet" href="codemirror/mode/xml/xml.css"
	type="text/css">
<link rel="stylesheet" href="codemirror/mode/javascript/javascript.css"
	type="text/css">
<link rel="stylesheet" href="codemirror/mode/clike/clike.css"
	type="text/css">
<link rel="stylesheet" href="codemirror/mode/css/css.css"
	type="text/css">
<title>[<?php echo getconfig("homepage_title")?>] - UliCMS</title>
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