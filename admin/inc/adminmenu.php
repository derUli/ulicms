<?php if(defined("_SECURITY")){
$menu_items = getAllMenuItems();
?>
<h2>UliCMS <a href="../">[<?php echo getconfig("homepage_title")?>]</a></h2>
<div class="navbar_top">
<ul class="menu">
  <li>
    <a href='?action=home'>Willkommen</a>
  </li>
  <li>
    <a href='?action=contents'>Inhalte</a>
    <ul>
      <li>
        <a href='?action=pages'>Seiten</a>
      </li>
      <li>
        <a href='?seite=sitemap'>Werbebanner</a>
      </li>
    </ul>
  </li>
  <li>
    <a href="?action=media">Medien</a>
    <ul>
      <li>
        <a href="?action=media">Medien</a>
      </li>
      <li>
        <a href="?action=images">Bilder</a>
      </li>
      <li>
        <a href="?action=flash">Flash</a>
      </li>
    </ul>
  </li>
  <li><a href="?action=admins">Benutzer</a></li>
  <li>
    <a href="?action=templates">Templates</a>
    <ul>
      <li>
        <a href="?action=templates&edit=oben.php">Oben</a>
      </li>
      <li>
        <a href="?action=templates&edit=unten.php">Unten</a>
      </li>
      <li>
        <a href="?action=templates&edit=maintenance.php">Wartungsseite</a>
      </li><li>
        <a href="?action=templates&edit=style.css">CSS</a>
      </li>
    </ul>
  </li>
</ul>
</div>
<div class="clear"></div>
<div id="pbody">
<?php 
}
?>