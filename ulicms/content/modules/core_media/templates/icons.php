<?php
$currentAction = BackendHelper::getAction();
$icons = array(
    "images" => "fas fa-images",
    "files" => "fa fa-file",
    "videos" => "fa fa-file-video",
    "audio" => "fa fa-file-audio"
);

$selectedButton = "btn btn-primary";
$notSelectedButton = "btn btn-default"?>

<div class="btn-toolbar" role="toolbar"
	aria-label="Toolbar with button groups">

	<div class="btn-group" role="group">
		<a href="<?php echo ModuleHelper::buildActionURL("media");?>"
			class="btn btn-default btn-back" title="<?php translate("back");?>"><i
			class="fa fa-arrow-left"></i></a>
	</div>
	<?php foreach($icons as $action=>$cssClass){?>
	  <div class="btn-group" role="group">
		<a href="<?php echo ModuleHelper::buildActionURL($action);?>"
			class="<?php echo $action == $currentAction ? $selectedButton : $notSelectedButton; ?>"
			title="<?php translate($action);?>"> <i
			class="<?php echo $cssClass?>"></i>
		</a>
	</div>
	
	<?php }?>
	</div>