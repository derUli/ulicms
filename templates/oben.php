<!DOCTYPE html>
<html>
<head>                                
<?php base_metas()?>
<link rel="stylesheet" href="templates/style.css" type="text/css" />
<title><?php title()?></title>
</head>

<body>
<!-- wrap starts here -->
<div id="wrap">

	<div id="header"><div id="header-content">	
		
		<h1 id="logo"><a href="?seite=<?php echo getconfig("frontpage")?>" title=""><?php homepage_title()?></a></h1>	
		<h2 id="slogan"><?php motto()?></h2>		
		
		<!-- Menu Tabs -->
		<?php menu("top")?>
			                              
	
	</div></div>
	
	<div class="headerphoto"></div>
				
	<!-- content-wrap starts here -->
	<div id="content-wrap"><div id="content">		
		
		<div id="sidebar" >
		
	

			<div class="sidebox">	
			
				<h1 class="clear">Sidebar Menü</h1>
				<?php menu("right")?>
				
			</div>	
			
		
			
			<div class="sidebox">	
			
				<h1>Neuigkeiten</h1>
				<div style="padding:10px;">
				<?php news()?>
				</div>
					
			</div>		
			
		
					
		</div>	
	
		<div id="main">		
		
			<div class="post">
			<h1><?php title()?></h1>