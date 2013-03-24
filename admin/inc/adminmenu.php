<?php if(defined("_SECURITY")){
$menu_items = getAllMenuItems();
?>
<h2>UliCMS <a href="../">[<?php echo getconfig("homepage_title")?>]</a></h2>
<div class="navbar_top">
<ul class="menu">
 <li><a href='?action=home'>Willkommen</a>
</li>
  <li><a href='?action=contents' >Inhalte</a>
<ul class='sub_menu'>
      <li><a href='?action=pages'>Seiten</a></li>
      <li><a href='?seite=sitemap'>Werbebanner</a>      
  </ul></li>
</ul>
</ul>
</div>
<div class="clear"></div>
<div id="pbody">
<?php 
}
?>