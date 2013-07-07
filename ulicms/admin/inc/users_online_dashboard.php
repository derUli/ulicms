<?php $users_online = db_query("SELECT * FROM ".tbname("admins")." WHERE last_action > ".(time() - 300)." ORDER BY username"); ?>
<?php while($row = mysql_fetch_object($users_online)){?>
<li style="font-weight:bold">
<?php if($_SESSION["ulicms_login"] != $row->username){?>
<a href="#" onclick="openChat('<?php echo $row->username;?>')">
<?php }?>
<?php echo $row->username;?>
<?php if($_SESSION["ulicms_login"] != $row->username){?>
</a>
<?php }?>
</li>
<?php }?>