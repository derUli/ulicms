<?php
use UliCMS\Security\PermissionChecker;

$currentAction = BackendHelper::getAction();
$icons = array(
    "images" => "fas fa-images",
    "files" => "fa fa-file",
    "videos" => "fa fa-file-video",
    "audio" => "fa fa-file-audio"
);

$icons = array_filter($icons, function ($cssClass, $action) {
    $permissionChecker = new PermissionChecker(get_user_id());
    return $permissionChecker->hasPermission($action);
}, ARRAY_FILTER_USE_BOTH);

$selectedButton = "btn btn-primary";
$notSelectedButton = "btn btn-default"?>

<div class="btn-toolbar" role="toolbar"
	aria-label="Toolbar with button groups">

	<div class="btn-group" role="group">
		<a href="<?php echo ModuleHelper::buildActionURL("media");?>"
			class="btn btn-default btn-back" title="<?php translate("back");?>"><i
			class="fa fa-arrow-left"></i> <?php translate("back");?></a>
	</div>
	<?php foreach($icons as $action=>$cssClass){?>
	  <div class="btn-group" role="group">
		<a href="<?php echo ModuleHelper::buildActionURL($action);?>"
			class="<?php echo $action == $currentAction ? $selectedButton : $notSelectedButton; ?>">
			<i class="<?php echo $cssClass?>"></i><span class="hide-on-820"> <?php translate($action);?></span>
		</a>
	</div>
	
	<?php }?>
	</div>