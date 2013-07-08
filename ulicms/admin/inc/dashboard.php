<?php {
if(defined("_SECURITY")){
  $pages_count = mysql_num_rows(db_query("SELECT * FROM ".tbname("content")));

  $topPages=db_query("SELECT * FROM ".tbname("content")." WHERE notinfeed = 0 AND systemname <> \"kontakt\" ORDER BY views DESC LIMIT 5");
  $lastModfiedPages = db_query("SELECT * FROM ".tbname("content")." WHERE systemname <> \"kontakt\" ORDER BY lastmodified DESC LIMIT 5");

  $admins_query = db_query("SELECT * FROM ".tbname("admins"));

  $admins = Array();

  while($row = mysql_fetch_object($admins_query)){
	 $admins[$row->id] = $row->username;
  }

 
  $users_online = db_query("SELECT * FROM ".tbname("admins")." WHERE last_action > ".(time() - 300)." ORDER BY username");
  
?>
<p>Hallo <?php echo $_SESSION["firstname"]." ".$_SESSION["lastname"];?>! [<a href="?action=admin_edit&admin=<?php echo $_SESSION["login_id"]?>">Profil bearbeiten</a>]
</p>

<?php 
$updateInfo = checkForUpdates();

if($updateInfo and is_admin()){
?>
<h2>Update Verfügbar</h2>
<?php echo strip_tags($updateInfo,
                                            "<p><a><strong><b><u><em><i><span><img>");?>
<?php }?>


<?php $motd = getconfig("motd");
if($motd or strlen($motd)>10){
$motd = nl2br($motd);
?>

<div id="accordion-container"> 

<h2 class="accordion-header">Nachricht des Tages</h2>
<div class="accordion-content">
<?php echo $motd;?>
</div>
<?php
}
?>
<h2 class="accordion-header">Statistiken</h2>      
<div class="accordion-content">
<table border=1>    
<tr>
<td>Anzahl der Seiten</td>
<td><?php echo $pages_count?></td>
</tr>
<tr>
</tr>
<tr>
<td>Registrierte Benutzer</td>
<td><?php echo count(getUsers())?></td>
</tr>

<?php if(getconfig("contact_form_refused_spam_mails") !== false){
?>
<tr>
<td>Blockierte Spam-Mails</td>
<td><?php echo getconfig("contact_form_refused_spam_mails")?></td>
</tr>
<?php
}?>
<?php $test = db_query("SELECT * FROM ".tbname("guestbook_entries"));
	if($test){
?>
<tr>
<td>Gästebucheinträge</td>
<td><?php echo mysql_num_rows($test)?></td>
</tr>
<?php }?>

</table>
</div>
<h2 class="accordion-header">Jetzt online sind</h2>
<div class="accordion-content">
<ul id="users_online">
<?php include_once "inc/users_online_dashboard.php";?>
</ul>
</div>
<h2 class="accordion-header">Top-Seiten</h2>
<div class="accordion-content">
<table cellpadding="2" border=0>
<tr style="font-weight:bold;">
<td>Titel</td>
<td>Views</td>
</tr>
<?php while($row = mysql_fetch_object($topPages) ){?>
<tr>
<td><a href="../<?php echo $row->systemname;?>.html" target="_blank"><?php echo $row->title;?></a></td>
<td align="right"><?php echo $row->views;?></td>
<?php }?>
</tr>
</table>
</p>
</div>

<h2 class="accordion-header">Letzte Änderungen</h2>
<div class="accordion-content">
<table cellpadding="2" style="width: 70%; border:0px;">
<tr style="font-weight:bold;">
<td>Titel</td>
<td>Datum</td>
<td>Durchgeführt von</td>
</tr>

<?php while($row = mysql_fetch_object($lastModfiedPages) ){?>
<tr>
<td><a href="../<?php echo $row->systemname;?>.html" target="_blank"><?php echo $row->title;?></a></td>

<td><?php echo date(env("date_format"), $row->lastmodified)?></td>
<td>
<?php 
$autorName = $admins[$row->lastchangeby];
if(!empty($autorName)){
}else{
$autorName = $admins[$row->autor];
}

echo $autorName;
?></td>
<?php }?>
</tr>
</table>
</div>
<?php add_hook("accordion_layout"); ?>
</div>
</div>

<script type="text/javascript">
function update_users_online(){
    $.ajax({
        type: "POST",
        url: "index.php?ajax_cmd=users_online_dashboard",
        async: true,
        success : function(e){
          if($('#users_online').html() != e){
              $('#users_online').html(e);
              setTimeout("update_users_online();", 10 * 1000);
          }
        }
    });
    
}

update_users_online();
</script>

<?php
}

}
?>
