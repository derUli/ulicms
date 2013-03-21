<?php 
include getModulePath("mysql_backup")."mysql_backup_install.php";
mysql_backup_check_install();

error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

if(!function_exists("func_enabled")){
  function func_enabled($func) {
    $disabled = explode(',', ini_get('disable_functions'));
    foreach ($disabled as $disableFunction) {
        $is_disabled[] = trim($disableFunction);
    }
    if (in_array($func,$is_disabled)) {
        $it_is_disabled["m"] = $func.'() has been disabled for security reasons in php.ini';
        $it_is_disabled["s"] = 0;
    } else {
        $it_is_disabled["m"] = $func.'() is allow to use';
        $it_is_disabled["s"] = 1;
    }
    return $it_is_disabled;
  }

}


$current_time = time();
$last_time = getconfig("mysql_backup_last_time");
$difference = $current_time - $last_time;
$backup_interval = 60 * 60 * 24 * getconfig("mysql_backup_every_days");

$config = new config();

$mysql_user = $config->mysql_user;
$mysql_password = $config->mysql_password;
$mysql_database = $config->mysql_database;
$backup_file = path_to_backup_dir()."dump-" . date('Ymd_g_i'); 


$allowed = func_enabled("exec");
$writable = fileperms(path_to_backup_dir()) >= 0755;


if($difference >= $backup_interval and $allowed["s"] and $writable){
   // set last backup time to current

   @ignore_user_abort(1); // run script in background 
   @set_time_limit(0); // run script forever 
   
   // Save Dump
   exec("mysqldump -u $mysql_user -p$mysql_password --add-drop-table $mysql_database > $backup_file.sql");
   exec("gzip ".$backup_file. ".sql");
   setconfig("mysql_backup_last_time", time());

} else {
// Administrator per E-Mail benachrichtigen
}

error_reporting(getconfig("error_reporting"));

?>
