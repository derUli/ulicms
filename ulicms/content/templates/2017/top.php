<?php
html5_doctype ();
og_html_prefix ();
$motto = get_motto ();
$data = get_custom_data ();
if (isset ( $data ["motto"] )) {
	$motto = $data ["motto"];
}
?>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet"
	href="<?php echo getModulePath("bootstrap");?>css/bootstrap.min.css">
  <?php
		base_metas ();
		og_tags ();
		?>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<style type="text/css">
.slicknav_btn, .slicknav_nav, nav .active, nav .sub_menu li:hover, nav .menu_active_link
	{
	background: <?php
	
echo getconfig ( "header-background-color" );
	?>
	!
	important;
}

h1, h2, h3, h4, h5, h6 {
	color: <?php
	
echo getconfig ( "header-background-color" );
	?>;
}
</style>
</head>
<body>
	<div class="container" id="root">
		<div class="header clearfix">
			<nav>
				<?= jumbotron_get_menu("top");?>
			</nav>
			<h3 class="text-muted">
			<?php homepage_title ();?></h3>

			<div id="mobile-nav"></div>
		</div>
<?php if(is_frontpage()){?>
		<div class="jumbotron">
		<?php
	if (getconfig ( "logo_disabled" ) == "no") {
		logo ();
	}
	?>
			<p class="lead"><?php echo Settings::get("motd");?></p>
			<p>
				<a class="btn btn-lg btn-success" href="admin/" role="button"><?php translate("login") ?></a>
			</p>
		</div>
		<?php }?>

		<div class="row marketing">
			<?php if($motto){?>
			<blockquote>
		<?php echo $motto;?></blockquote>
		<?php }?>
			<main>
		<?php Template::headline();?>