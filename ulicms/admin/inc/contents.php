<?php
if(!defined("ULICMS_ROOT"))
     die("schlechter Hacker");

$acl = new ACL();

if($acl -> hasPermission("pages") or 
$acl -> hasPermission("banners") or $acl -> hasPermission("news")
or $acl -> hasPermission("comments")){
    ?>

<h2>Inhalte</h2>
<p><strong>Bitte wählen Sie einen Inhaltstyp aus:</strong><br/>
<?php if($acl -> hasPermission("news")){
            ?>
  
        <a href='?action=news'>News</a><br/>
     
      <?php }
        ?>
<?php if($acl -> hasPermission("comments")){
            ?>
      
        <a href='?action=comments'>Kommentare</a><br/>
     
      <?php }?>

<?php if($acl -> hasPermission("pages")){
        ?>
<a href="index.php?action=pages">Seiten</a><br/>
<?php }
    ?>
<?php if($acl -> hasPermission("banners")){
        ?>
<a href="index.php?action=banner">Werbebanner</a><br/>
<?php }
    ?>
    
 
<?php if($acl -> hasPermission("categories")){ ?>

<a href="index.php?action=categories">Kategorien</a><br/>
<?php }?>
</p>
<?php add_hook("content_type_list_entry");
     ?>






<?php
    }else{
     noperms();
    }
?>
