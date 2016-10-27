<?php
html5_doctype ();
og_html_prefix ();
?>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
		base_metas ();
		og_tags ();
		?>
<link rel="stylesheet"
	href="<?php echo getModulePath("bootstrap");?>css/bootstrap.min.css">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
	<div class="container">
		<div class="header clearfix">
			<nav>
				<?= jumbotron_get_menu("top");?>
			</nav>
			<h3 class="text-muted"><?php homepage_title();?></h3>
		</div>
<?php if(is_frontpage()){?>
		<div class="jumbotron">
			<p class="lead"><?php echo Settings::get("motd");?></p>
			<p>
				<a class="btn btn-lg btn-success" href="admin/" role="button"><?php translate("login") ?></a>
			</p>
		</div>
		<?php }?>

		<div class="row marketing">
		<?php Template::headline();?>