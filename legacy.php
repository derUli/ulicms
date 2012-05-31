<?php

//will be replaced with getconfig()
function env($key){

$connection=MYSQL_CONNECTION;
$key=mysql_real_escape_string($key);
$query=mysql_query("SELECT * FROM ".tbname("settings")." WHERE name='$key'",$connection);
if(mysql_num_rows($query)>0){
while($row=mysql_fetch_object($query)){
return $row->value;
}
}
else{
return false;
}
}

function print_env($ikey){
$value=env($ikey);
if($value!=false){
echo $value;
}
}


?>