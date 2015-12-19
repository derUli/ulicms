<?php
html5_doctype ();
og_html_prefix ();
?>
<head>
<?php
base_metas ();
og_tags ();
?>
<meta name="viewport" content="width=device-width" />
<link rel="stylesheet" type="text/css"
	href="<?php echo getTemplateDirPath(get_theme());?>mobile.css" />
<style type="text/css">
header, footer {
	background-color: <?php
	
echo getconfig ( "header-background-color" );
	?>;
}

h1, h2, h3, h4, h5, h6 {
	color: <?php
	
echo getconfig ( "header-background-color" );
	?>;
}

nav a.menu_active_link, nav a.contains-current-page {
	border-bottom: 3px solid<?php
	
echo getconfig ( "header-background-color" );
	?>;
}
</style>
<script>
	$(function(){
		$('ul.menu_top').slicknav({
           "prependTo" : "div#mobile-menu",
           "label" : "<?php translate("pages");?>",
           "allowParentLinks" : true
        });
	});
</script>
</head>
<body class="<?php body_classes();?>">
	<header>
		<a href="./">
<?php
if (getconfig ( "logo_disabled" ) == "no") {
	logo ();
	?>
<br />
<?php
} else {
	?><strong><?php
	
	homepage_title ();
	?></strong>
<?php
}
?>
</a>
	</header>
	<div id="root-container">
		<nav><?php menu("top");?></nav>
		<div id="mobile-menu"></div>
		<img class="header-image"
			src="<?php echo getTemplateDirPath(get_theme());?>header.jpg"
			alt="Hedsaer-grafik">
		<main>
  <?php
		if (! containsModule ( null, "blog" )) {
			?>
<h1><?php headline();?></h1>
<?php } ?>
