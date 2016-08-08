<!DOCTYPE HTML>
<html>
<head>
<title><?php echo Installer::getTitle();?></title>
<link rel="stylesheet" type="text/css"
	href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/href" href="media/style.css" />
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-4" id="steps">
				<ol id="navigation">
				<?php for($i=1; $i++ <= 5; $i++){?>
					<li><?php echo constant("TRANLATION_TITLE_STEP_".$i);?></li>
				<?php }?>
</ol>
			</div>
			<div class="col-sm-8" id="main">