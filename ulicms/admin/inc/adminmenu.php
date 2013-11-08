<?php
if(defined("_SECURITY")){
     $modules = getAllModules();
     $modules_with_admin_page = Array();
     for($i = 0; $i < count($modules); $i++){
         if(file_exists(getModuleAdminFilePath($modules[$i])))
             array_push($modules_with_admin_page, $modules[$i]);
         }
    
    
     $theme = getconfig("theme");
     $theme_dir = getTemplateDirPath($theme);
     $acl = new ACL();
    
     ?>
<div style="float:left"><h2>UliCMS <a href="../">[<?php echo getconfig("homepage_title")?>]</a></h2></div>


<div style="margin-right:10px;margin-top:30px;float:right">
<img id="loading" src="gfx/loading.gif" alt="Bitte warten..." style="display:none;">
</div>
<div id="message" style="margin-top:30px;text-align:center;margin-right:10px;float:right;"></div>
<div class="clear"></div>
<div class="navbar_top">
<ul class="menu">
  <li>
    <a href='?action=home'>Willkommen</a>
    <ul>
    <li><a href="?action=admin_edit&admin=<?php echo $_SESSION["login_id"]?>">Profil bearbeiten</a></li></ul>
  </li>
  <?php if($acl->hasPermission("banners") or  $acl->hasPermission("pages")){?>
 <li>
    <a href='?action=contents'>Inhalte</a>
    <ul>
     <?php if($acl->hasPermission("pages")){?>
      <li>
        <a href='?action=pages'>Seiten</a>
      </li>
      <?php }?>
      
     <?php if($acl->hasPermission("banners")){?>
      <li>
        <a href='?action=banner'>Werbebanner</a>
      </li>
      
      <?php }?>
    </ul>
   
  </li>
  <?php } ?>
  <?php if($acl->hasPermission("files") or $acl->hasPermission("images") or $acl->hasPermission("flash")){?>
    <li><a href="?action=media">Medien</a>
    <ul>
    <?php if($acl->hasPermission("images")) {?>
      <li>
        <a href="?action=images">Bilder</a>
      </li>
      <?php }?>
      <?php if($acl->hasPermission("flash")) {?> 
      <li>
        <a href="?action=flash">Flash</a>
      </li>
      <?php }?>
      <?php if($acl->hasPermission("files")) {?>
      <li>
        <a href="?action=files">Dateien</a>
      </li>
      <?php }?>
    </ul>
  </li>
  
      <?php }?>
  <?php if($acl->hasPermission("users")){?><li><a href="?action=admins">Benutzer</a></li>
  <?php } ?>
  <?php if(is_admin() or $acl->hasPermission("groups")){?>
  <li><a href="?action=groups">Gruppen</a></li>
  <?php } ?>
  <?php if($acl->hasPermission("templates")){?>
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
      <?php
     if(file_exists($theme_dir . "403.php")){
         ?>
        <li><a href="index.php?action=templates&edit=403.php">403</a></li>
     <?php
         }
     ?>

     <?php
     if(file_exists($theme_dir . "404.php")){
         ?>
      <li><a href="index.php?action=templates&edit=404.php">404</a></li>
     <?php
         }
     ?>
     
      <?php
     if(file_exists($theme_dir . "functions.php")){
         ?>
      <li><a href="index.php?action=templates&edit=functions.php">Functions</a></li>
     <?php
         }
     ?>
     
    </ul>
  </li>
  
  <?php } ?>
  <?php if($acl->hasPermission("list_packages")){?>
  <li><a href="?action=modules">Pakete</a>
  <?php if(count($modules_with_admin_page) > 0){
         ?>
  <ul>
    <?php for($n = 0; $n < count($modules_with_admin_page); $n++){
             ?>
    <li><a href="?action=module_settings&module=<?php echo $modules_with_admin_page[$n]?>"><?php echo $modules_with_admin_page[$n]?></a></li>
    <?php
             }
         ?>
   </ul>
  <?php }
     ?>
     
      </li>
         <?php }?>
  <?php if(file_exists("../update.php")){
         ?>
         
        
  <li><a href="?action=system_update">Update</a></li>
  <?php }
     ?>
   <li><a href="?action=settings_categories">Einstellungen</a>
    <ul><li><a href="?action=settings_simple">Grundeinstellungen</a></li>
    <li><a href="?action=design">Design</a></li>
    <li><a href="?action=spam_filter">Spamfilter</a></li>
    <li><a href="?action=cache">Cache</a>
     <ul>
     <li><a id="clear_cache" href="?action=cache&clear_cache=yes">Cache leeren</a></li>
     </ul>    
    </li>
    <li><a href="?action=motd">MOTD</a></li>
    <li><a href="?action=pkg_settings">Paketquelle</a></li>
    <li><a href="?action=logo_upload">Logo</a></li>
    <li><a href="?action=languages">Sprachen</a></li>
    <li><a href="?action=other_settings">Sonstiges</a></li>
    </ul>
    <li><a href="?action=info">Info</a>
    <ul> 
     <li><a href="http://www.ulicms.de/" target="_blank">UliCMS Portal</a></li>
     <li><a href="http://ulicms.de/?seite=community" target="_blank">Community</a></li>
     <li><a href="license.html" target="_blank">Lizenz</a></li>
     <li><a href="http://www.ulicms.de/?seite=kontakt" target="_blank">Feedback</a></li>
    </ul>
    <?php add_hook("admin_menu_item");
     ?>
    <li><a href="?action=destroy">Logout</a></li>
</ul>
<script type="text/javascript">

$('#clear_cache')
   .click(function (event) {
       $("#message").html("")
       $("#loading").show();
       
       $.ajax({
       url: "index.php?action=cache&clear_cache=yes",
       success: function(evt){
       $("#loading").hide();
       $("#message").html("<span style=\"color:green\">Der Cache wurde geleert!</span>");
               
       },
       error: function(evt){
       
          $("#loading").hide();
          alert("AJAX Error");
       },
       dataType: "html"
});

       
       event.preventDefault();
       event.stopPropagation();
});
</script>
</div>

<div class="clear"></div>
<div id="pbody">
<?php
     }
?>
