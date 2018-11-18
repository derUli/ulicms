<?php
$admin_logo = Settings::get("admin_logo");
if (! $admin_logo) {
    $admin_logo = "gfx/logo.png";
}

// translation for select2 dropdown boxes
$select2TranslationFile = "scripts/js/i18n/" . getSystemLanguage() . ".js";
$select2Language = getSystemLanguage();
if (! file_exists($select2TranslationFile)) {
    $select2TranslationFile = "scripts/js/i18n/en.js";
    $select2Language = "en";
}

?>
<!DOCTYPE html>
<html data-select2-language="<?php esc($select2Language)?>">
<head>
<meta name="viewport"
	content="width=device-width, user-scalable=yes, initial-scale=1" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>[<?php Template::escape(Settings::get("homepage_title"));?>] - UliCMS</title>
<?php
$styles = array();
?>
<?php
$enq = array(
    "scripts/php.js/strip_tags.js",
    "scripts/php.js/htmlspecialchars.js",
    "scripts/jquery.min.js",
    "scripts/jquery.form.min.js",
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
    "scripts/bootstrap.min.js",
    "scripts/js/select2.min.js",
    "scripts/bootbox.min.js",
    $select2TranslationFile,
    "scripts/datatables/datatables.min.js",
    "../lib/js/global.js"
);

if (is_logged_in()) {
    $enq[] = "scripts/cookie.js";
    $enq[] = "scripts/jquery-shiftclick.js";
    $enq[] = "scripts/shift_checkbox.js";
}
if (! is_mobile()) {
    $enq[] = "scripts/doubletaptogo/doubletaptogo.min.js";
}
?>
<?php
foreach ($enq as $script) {
    enqueueScriptFile($script);
}
?>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>

<?php combinedScriptHtml();?>
<script type="text/javascript" src="scripts/jscolor/jscolor.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $.ajaxSetup({ cache: false });
<?php
if (! is_mobile()) {
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
$styles[] = "css/bootstrap.min.css";
$styles[] = "codemirror/lib/codemirror.css";
$styles[] = "codemirror/lib/codemirror.css";
$styles[] = "css/modern.css";
$styles[] = "scripts/css/select2.min.css";

foreach ($styles as $style) {
    enqueueStylesheet($style);
}

combinedStylesheetHtml();
?>
<?php

do_event("admin_head");
?>

<link rel="stylesheet" type="text/css"
	href="scripts/datatables/datatables.min.css" />
</head>
<?php
do_event("before_backend_header");
?>
<?php
$cssClasses = "";
if (get_user_id()) {
    $cssClasses .= "user-" . get_user_id() . "-logged-in ";
} else {
    $cssClasses .= "not-logged-in ";
}
if (get_action()) {
    $cssClasses .= "action-" . get_action();
} else {
    $cssClasses .= "no-action";
}
?>

<body class="<?php esc($cssClasses);?>"
	data-datatables-translation="<?php echo DataTablesHelper::getLanguageFileURL(getSystemLanguage());?>">
<?php

do_event("after_backend_header");
?>
<div
		class="container main <?php

if (get_action()) {
    echo 'action-' . Template::getEscape(get_action());
}

?>">

		<div class="row">
			<div class="col-xs-8">
				<a href="../" title="<?php translate("goto_frontend");?>"><img
					src="<?php Template::escape($admin_logo);?>" alt="UliCMS"
					class="ulicms-logo"></a>
			</div>
			<div class="col-xs-4 menu-container">
		<?php

if (is_logged_in()) {
    ?>
			<div class="row pull-right">
					<div class="col-xs-6">
						<img src="gfx/clear-cache.png" id="menu-clear-cache"
							data-url="<?php echo ModuleHelper::buildMethodCallUrl("PerformanceSettingsController", "clearCache", "clear_cache=1");?>"
							alt="<?php translate("clear_cache");?>"> <img
							src="gfx/loading2.gif" id="menu-clear-cache-loading"
							style="display: none" alt="<?php translate("loading");?>">
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
