<!doctype html>
<html>
<head>
<meta name="viewport" content="width=1024"/>

<link rel="stylesheet" type="text/css" href="css/admin.css"/>

<script type="text/javascript" src="scripts/php.js/strip_tags.js"></script>
<script type="text/javascript" src="scripts/php.js/htmlspecialchars.js"></script>
<script type="text/javascript" src="scripts/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $.ajaxSetup({ cache: false });
});
</script>
<script type="text/javascript" src="scripts/jeditable.js"></script>
<script type="text/javascript" src="scripts/jscolor/jscolor.js"></script>
<script type="text/javascript" src="scripts/jquery.form.min.js"></script>
<?php if(is_logged_in()){?>
<script type="text/javascript" src="scripts/cookie.js"></script>
<script type="text/javascript" src="scripts/notification.js"></script>
<?php }?>
<script type="text/javascript" src="scripts/audio.js"></script>
<script src="scripts/vallenato/vallenato.js" type="text/javascript"></script>
<link rel="stylesheet" href="scripts/vallenato/vallenato.css" type="text/css">
<link rel="icon" href="gfx/favicon.ico" type="image/x-icon"> 
<link rel="shortcut icon" href="gfx/favicon.ico" type="image/x-icon">

<link rel="stylesheet" type="text/css" href="codemirror/lib/codemirror.css">
<script src="codemirror/lib/codemirror.js" type="text/javascript"></script>
<script src="codemirror/mode/php/php.js" type="text/javascript"></script>
<script src="codemirror/mode/xml/xml.js" type="text/javascript"></script>
<link rel="stylesheet" href="codemirror/mode/xml/xml.css" type="text/css">
<script src="codemirror/mode/javascript/javascript.js" type="text/javascript"></script>
<link rel="stylesheet" href="codemirror/mode/javascript/javascript.css" type="text/css">
<script src="codemirror/mode/clike/clike.js"></script>
<link rel="stylesheet" href="codemirror/mode/clike/clike.css" type="text/css">
<link rel="stylesheet" href="codemirror/mode/css/css.css" type="text/css">
<script src="codemirror/mode/css/css.js" type="text/javascript"></script>
<title>[<?php echo getconfig("homepage_title")?>] - UliCMS</title>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<?php add_hook("admin_head");?>
</head>
<body>
