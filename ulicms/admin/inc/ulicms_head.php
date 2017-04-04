<?php
$admin_logo = Settings::get ( "admin_logo" );
if (! $admin_logo) {
	$admin_logo = "gfx/logo.png";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport"
	content="width=device-width, user-scalable=yes, initial-scale=1" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>[<?php Template::escape(Settings::get("homepage_title"));?>] - UliCMS</title>
<?php
$styles = array ();
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
		"scripts/users.js",
		"scripts/global.js",
		"scripts/bootstrap.min.js"
);
?>
<?php

if (is_logged_in ()) {
	$enq [] = "scripts/cookie.js";
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

$styles [] = "css/bootstrap.min.css";
$styles [] = "codemirror/lib/codemirror.css";
$styles [] = "codemirror/mode/xml/xml.css";
$styles [] = "codemirror/mode/javascript/javascript.css";
$styles [] = "codemirror/mode/clike/clike.css";
$styles [] = "codemirror/lib/codemirror.css";
$styles [] = "codemirror/mode/css/css.css";
$styles [] = "css/modern.css";

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
<div class="fluid-container main">

	<div class="row">
		<div class="col-xs-8">
			<a href="../" title="<?php translate("goto_frontend");?>"><img
				src="<?php Template::escape($admin_logo);?>" alt="UliCMS"
				class="ulicms-logo"></a>
		</div>
		<div class="col-xs-4 menu-container">
		<?php
		
		if (is_logged_in ()) {
			?>
			<div class="row pull-right">
				<div class="col-xs-6">
					<img src="gfx/clear-cache.png" id="menu-clear-cache"
						alt="<?php translate("clear_cache");?>">
				</div>
				<div class="col-xs-6">
					<img src="gfx/menu-icon.png" id="menu-toggle"
						alt="<?php translate("toggle_menu");?>">
				</div>
			</div>
			<?php }?>
		</div>
	</div>
	<div class="row main-content">
		<div class="col-xs-12">