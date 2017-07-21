<?php
$menus = getAllMenus ( true );
$controller = ModuleHelper::getMainController ( "sitemap2" );
if (! $controller->getShowNotInMenu () and faster_in_array ( "none", $menus )) {
	$menus = array_flip ( $menus );
	unset ( $menus ["none"] );
	$menus = array_flip ( $menus );
}
?>
<?php foreach($menus as $menu){?>
<h3><?php translate($menu);?></h3>
<?php menu($menu);?>
<?php }?>