<?php
$acl = new ACL();
include_once ULICMS_ROOT . "/lib/formatter.php";

if($acl -> hasPermission("dashboard")){
     ?>

<?php
     if(defined("_SECURITY") and logged_in()){
         $pages_count = db_num_rows(db_query("SELECT * FROM " . tbname("content")));
        
         $topPages = db_query("SELECT * FROM " . tbname("content") . " WHERE notinfeed = 0 AND redirection NOT LIKE '#%' ORDER BY views DESC LIMIT 5");
         $lastModfiedPages = db_query("SELECT * FROM " . tbname("content") . " WHERE redirection NOT LIKE '#%' ORDER BY lastmodified DESC LIMIT 5");
        
         $admins_query = db_query("SELECT * FROM " . tbname("users"));
        
         $admins = Array();
        
         while($row = db_fetch_object($admins_query)){
             $admins[$row -> id] = $row -> username;
             }
        
        
         $users_online = db_query("SELECT * FROM " . tbname("users") . " WHERE last_action > " . (time() - 300) . " ORDER BY username");
        
         ?>
<p><?php
         $str = TRANSLATION_HELLO_NAME;
         $str = str_ireplace("%firstname%", $_SESSION["firstname"], $str);
         $str = str_ireplace("%lastname%", $_SESSION["lastname"], $str);
         echo $str;
         ?>
 [<a href="?action=admin_edit&admin=<?php echo $_SESSION["login_id"]?>"><?php echo TRANSLATION_EDIT_PROFILE;
         ?></a>]
</p>




<?php $motd = getconfig("motd");
         if($motd or strlen($motd) > 10){
             $motd = nl2br($motd);
             ?>

<div id="accordion-container"> 

<h2 class="accordion-header"><?php echo TRANSLATION_MOTD;
             ?></h2>
<div class="accordion-content">
<?php echo $motd;
             ?>
</div>
<?php
             }
         ?>
         
<?php
         if($acl -> hasPermission("update_system")){
             $data = file_get_contents_wrapper(PATCH_CHECK_URL, true);
             $data = trim($data);
             if(!empty($data)){
                ?>
            <h2 class="accordion-header"><?php translate("there_are_patches_available");
                ?></h2>
<div class="accordion-content">
<a href="?action=available_patches"><strong><?php translate("install_patches");
                ?></strong></a>
            </div>
            <?php
                 }
             }
        ?>
         
         
         
         
<?php
         if($acl -> hasPermission("update_system")){
             $updateInfo = checkForUpdates();
            
             if($updateInfo){
                 ?>
<h2 class="accordion-header"><?php echo TRANSLATION_UPDATE_AVAILABLE;
                 ?></h2>
<div class="accordion-content">
<?php echo strip_tags($updateInfo,
                     "<p><a><strong><b><u><em><i><span><img>");
                 ?>
             </div> 
<?php }
             ?>
<h2 class="accordion-header"><?php echo TRANSLATION_ULICMS_NEWS;
             ?></h2>
<div class="accordion-content" id="ulicms-feed">
<img src="gfx/loading.gif" alt="Feed wird geladen..."/>
</div>
<script type="text/javascript">
$(document).ready(function() {
 $('#ulicms-feed').load('?action=ulicms-news');
});
</script>

<?php }
         ?>


<h2 class="accordion-header"><?php echo TRANSLATION_STATISTICS;
         ?></h2>      
<div class="accordion-content">
<table>    
<?php
         $installed_at = getconfig("installed_at");
         if($installed_at){
             $time = time() - $installed_at;
             $formatted = formatTime($time);
             ?>
<tr>
<td><?php echo TRANSLATION_SITE_ONLINE_SINCE;
             ?></td>
<td><?php echo $formatted;
             ?></td>
</tr>
<?php }
         ?>
<tr>
<td><?php echo TRANSLATION_PAGES_COUNT;
         ?></td>
<td><?php echo $pages_count?></td>
</tr>
<tr>
<td><?php echo TRANSLATION_REGISTERED_USERS_COUNT;
         ?></td>
<td><?php echo count(getUsers())?></td>
</tr>

<?php if(getconfig("contact_form_refused_spam_mails") !== false){
             ?>
<tr>
<td><?php echo TRANSLATION_BLOCKED_SPAM_MAILS;
             ?></td>
<td><?php echo getconfig("contact_form_refused_spam_mails")?></td>
</tr>
<?php
             }
         ?>
<?php $test = db_query("SELECT * FROM " . tbname("guestbook_entries"));
         if($test){
             ?>
<tr>
<td><?php echo TRANSLATION_GUESTBOOK_ENTRIES;
             ?></td>
<td><?php echo db_num_rows($test)?></td>
</tr>
<?php }
         ?>
</table>
</div>
<h2 class="accordion-header"><?php echo TRANSLATION_ONLINE_NOW;
         ?></h2>
<div class="accordion-content">
<ul id="users_online">
<?php include_once "inc/users_online_dashboard.php";
         ?>
</ul>
</div>
<h2 class="accordion-header"><?php echo TRANSLATION_TOP_PAGES;
         ?></h2>
<div class="accordion-content">
<table cellpadding="2">
<tr style="font-weight:bold;">
<td><?php echo TRANSLATION_TITLE;
         ?></td>
<td><?php echo TRANSLATION_VIEWS;
         ?></td>
</tr>
<?php while($row = db_fetch_object($topPages)){
            
            
             $domain = getDomainByLanguage($row -> language);
             if(!$domain){
                 $url = "../" . $row -> systemname . ".html";
                 }else{
                 $url = "http://" . $domain . "/" . $row -> systemname . ".html";
                 }
             ?>
<tr>
<td><a href="<?php echo $url;
             ?>" target="_blank"><?php echo htmlspecialchars($row -> title, ENT_QUOTES, "UTF-8");
             ?></a></td>
<td align="right"><?php echo $row -> views;
             ?></td>
<?php }
         ?>
</tr>
</table>
</p>
</div>

<h2 class="accordion-header"><?php echo TRANSLATION_LAST_CHANGES;
         ?></h2>
<div class="accordion-content">
<table cellpadding="2">
<tr style="font-weight:bold;">
<td><?php echo TRANSLATION_TITLE;
         ?></td>
<td><?php echo TRANSLATION_DATE;
         ?></td>
<td><?php echo TRANSLATION_DONE_BY;
         ?></td>
</tr>

<?php while($row = db_fetch_object($lastModfiedPages)){
            
            
             $domain = getDomainByLanguage($row -> language);
             if(!$domain){
                 $url = "../" . $row -> systemname . ".html";
                 }else{
                 $url = "http://" . $domain . "/" . $row -> systemname . ".html";
                 }
            
             ?>
<tr>
<td><a href="<?php echo $url;
             ?>" target="_blank"><?php echo htmlspecialchars($row -> title, ENT_QUOTES, "UTF-8");
             ?></a></td>

<td><?php echo strftime("%x %X", $row -> lastmodified)?></td>
<td>
<?php
             $autorName = $admins[$row -> lastchangeby];
             if(!empty($autorName)){
                 }else{
                 $autorName = $admins[$row -> autor];
                 }
            
             echo $autorName;
             ?></td>

</tr>
<?php }
         ?>
</table>
</div>
<?php add_hook("accordion_layout");
         ?>
</div>
</div>

<?php
         }
    
     ?>

<?php }else{
     noperms();
     }
?>