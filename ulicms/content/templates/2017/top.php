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
			<h3 class="text-muted">Project name</h3>
		</div>

		<div class="jumbotron">
			<h1>Jumbotron heading</h1>
			<p class="lead">Cras justo odio, dapibus ac facilisis in, egestas
				eget quam. Fusce dapibus, tellus ac cursus commodo, tortor mauris
				condimentum nibh, ut fermentum massa justo sit amet risus.</p>
			<p>
				<a class="btn btn-lg btn-success" href="#" role="button">Sign up
					today</a>
			</p>
		</div>

		<div class="row marketing">