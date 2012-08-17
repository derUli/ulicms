<?php if(defined("_SECURITY")){
$menu_items = getAllMenuItems();
?>
<h2>UliCMS <a href="../">[<?php echo getconfig("homepage_title")?>]</a></h2>
<div id="menu">
<?php for($i=0;$i<count($menu_items);$i++){?>
  <a href="index.php?action=<?php
  echo $menu_items[$i]["action"]?>"><?php echo $menu_items[$i]["label"]?></a> 
  <?php if($i+1 <count($menu_items)) echo "| "; ?> 

<?php
}
?>


</div>
<div id="pbody">
<?php 
}
?>