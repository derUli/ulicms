<head>
<meta name="viewport" content="width=1000, user-scalable=yes" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>[<?php Template::escape(Settings::get("homepage_title"));?>] - UliCMS</title>
<link rel="stylesheet" type="text/css" href="css/blue.css" />
<?php
$styles = array ();
?>
<?php

if (is_mobile ()) {
	$styles [] = "css/mobile.css";
}
?>
<link rel="stylesheet" type="text/css"
	href="scripts/tablesorter/style.css" />
<?php
$enq = array (
		"scripts/php.js/strip_tags.js",
		"scripts/php.js/htmlspecialchars.js",
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
		"scripts/url.min.js",
		"scripts/util.js",
		"scripts/users.js"
);
?>
<?php

if (is_logged_in ()) {
	$enq [] = "scripts/cookie.js";
	$enq [] = "scripts/notification.js";
	$enq [] = "scripts/jquery-shiftclick.js";
	$enq [] = "scripts/shift_checkbox.js";
}

if (! is_mobile ()) {
	$enq [] = "scripts/doubletaptogo/doubletaptogo.min.js";
}
?>
<?php

foreach ( $enq as $script ) {
	enqueueScriptFile ( $script );
}
?>
<?php combined_script_html();?>

<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="scripts/jscolor/jscolor.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $.ajaxSetup({ cache: false });

<?php

if (! is_mobile ()) {
	?>
  $(window).scroll(function() {
    if ($(this).scrollTop()) {
        $('a.scrollup:hidden').stop(true, true).fadeIn();
    } else {
        $('a.scrollup').stop(true, true).fadeOut();
    }
});
$(".menu li:has(ul)").doubleTapToGo();

  <?php
}
?>
});
</script>
<link rel="stylesheet" type="text/css"
	href="scripts/vallenato/vallenato.css" />
<?php include "inc/ulicms_touch_icons.php";?>
<?php

$styles [] = "codemirror/lib/codemirror.css";
$styles [] = "codemirror/mode/xml/xml.css";
$styles [] = "codemirror/mode/javascript/javascript.css";
$styles [] = "codemirror/mode/clike/clike.css";
$styles [] = "codemirror/lib/codemirror.css";
$styles [] = "codemirror/mode/css/css.css";

foreach ( $styles as $style ) {
	enqueueStylesheet ( $style );
}

combined_stylesheet_html ();
?>
<script type="text/javascript">
$(document).ready(function(){
    $(".tablesorter").tablesorter({widgets: ["zebra"]});
    }
);
</script>
<?php

add_hook ( "admin_head" );
?>
</head>
