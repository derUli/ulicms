<?php 
if(defined("_SECURITY")){
   $modules = getAllModules();
   $modules_with_admin_page = Array();
   for($i=0; $i < count($modules); $i++){
    if(file_exists(getModuleAdminFilePath($modules[$i])))
       array_push($modules_with_admin_page, $modules[$i]);
}
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
  <li><a href="?action=modules">Module</a>
  <?php if(count($modules_with_admin_page) > 0){?>
  <ul>
    <?php for($n=0; $n < count($modules_with_admin_page); $n++){
    ?>
    <li><a href="?action=module_settings&module=<?php echo $modules_with_admin_page[$n]?>"><?php echo $modules_with_admin_page[$n]?></a></li>
    <?php
    }
    ?>
   </ul>
  <?php } ?>
  <li><a href="?action=system_update">Update</a></li>
   <li><a href="?action=settings_categories">Einstellungen</a>
    <ul><li><a href="?action=settings_simple">Grundeinstellungen</a></li>
    <ul><li><a href="?action=spam_filter">Spamfilter</a></li>
    <ul><li><a href="?action=cache">Cache</a></li>
    <ul><li><a href="?action=motd">MOTD</a></li>
    <ul><li><a href="?action=logo">Logo</a></li>
    <ul><li><a href="?action=languages">Sprachen</a></li>
    <ul><li><a href="?action=settings">Experteneinstellungen</a></li>

</ul>
</div>
<div class="clear"></div>
<div id="pbody">
<?php 
}
?>