<?php
use UliCMS\Security\PermissionChecker;

$currentAction = BackendHelper::getAction();
$icons = array(
    "pages" => "fas fa-book",
    "comments_manage" => "fa fa-comment",
    "forms" => "fab fa-wpforms",
    "banner" => "fas fa-bullhorn",
    "categories" => "fa fa-list-alt"
);

$icons = array_filter($icons, function ($cssClass, $action) {
    $permissions = array(
        "pages" => "pages",
        "comments_manage" => "comments_manage",
        "forms" => "forms",
        "banner" => "banners",
        "categories" => "categories"
    );
    
    $permissionChecker = new PermissionChecker(get_user_id());
    return $permissionChecker->hasPermission($permissions[$action]);
}, ARRAY_FILTER_USE_BOTH);

$specialLabels = array(
    "comments_manage" => get_translation("comments")
);

$selectedButton = "btn btn-primary";
$notSelectedButton = "btn btn-default"?>

<div class="btn-toolbar" role="toolbar"
	aria-label="Toolbar with button groups">

	<div class="btn-group" role="group">
		<a href="<?php echo ModuleHelper::buildActionURL("contents");?>"
			class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i>
		<?php translate("back")?></a>

	</div>
	<?php foreach($icons as $action=>$cssClass){?>
	  <div class="btn-group" role="group">
		<a href="<?php echo ModuleHelper::buildActionURL($action);?>"
			class="<?php echo $action == $currentAction ? $selectedButton : $notSelectedButton; ?>">
			<i class="<?php echo $cssClass?>"></i><span class="hide-on-820"> <?php (isset($specialLabels[$action]) ? esc($specialLabels[$action]) : translate($action));?></span>
		</a>
	</div>
	
	<?php }?>
	</div>