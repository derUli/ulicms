<?php $users_online = db_query("SELECT * FROM " . tbname("admins") . " WHERE last_action > " . (time() - 300) . " ORDER BY username");
?>
<?php while($row = mysql_fetch_object($users_online)){
     ?>
<li><?php echo $row->username;
     ?></li>
<?php }
?>