<?php include "inc/ulicms_head.php";?>
<?php $cssClasses = "";
if(get_user_id()){
	$cssClasses .= "user-" . get_user_id() . "-logged-in ";
} else {
	$cssClasses .=  "not-logged-in ";
}
if(get_action()){
	$cssClasses .="action-" . get_action();
} else {
	$cssClasses .= "no-action";
}
?>

<body class="<?php esc($cssClasses);?>">