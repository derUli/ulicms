<?php
$users_online = db_query ( "SELECT * FROM " . tbname ( "users" ) . " WHERE last_action > " . (time () - 300) . " ORDER BY username" );
?>
<?php

while ( $row = db_fetch_object ( $users_online ) ) {
	?>
<li><?php echo $row -> username?></li>
<?php
}
?>