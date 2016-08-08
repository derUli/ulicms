<!DOCTYPE HTML>
<html>
<head>
<title><?php echo InstallerController::getTitle();?> - <?php echo APPLICATION_TITLE;?> </title>
<link rel="stylesheet" type="text/css"
	href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/href" href="media/style.css" />
</head>
<body>

	<div class="container">
		<div class="row">
			<div class="col-sm-12" id="header">
				<p>
					<img src="../admin/gfx/logo.png" alt="UliCMS">
				</p>
				<p>
					<strong><?php echo TRANSLATION_INSTALLATION;?></strong>
				</p>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4" id="steps">
				<ol id="navigation">
				<?php for($i=1; $i <= 6; $i++){?>
					<li><a href="index.php?step=<?php echo $i;?>"
						class="<?php if($i == InstallerController::getStep()) echo "current-item";?>">
						<?php echo constant("TRANSLATION_TITLE_STEP_".$i);?></a></li>
					<?php }?>
</ol>
			</div>
			<div class="col-sm-8" id="main">