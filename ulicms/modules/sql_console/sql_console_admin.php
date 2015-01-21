<?php
define("MODULE_ADMIN_HEADLINE", "SQL Console");
define("MODULE_ADMIN_REQUIRED_PERMISSION", "sql_console");

// Konfiguration checken
$send_comments_via_email = getconfig("blog_send_comments_via_email") == "yes";

// Session-Variable initialisieren
if(!isset($_SESSION["sql_code"]))
     $_SESSION["sql_code"] = "";

function sql_console_admin(){
     include getModulePath("sql_console") . "sql_console_functions.php";
    
     include getModulePath("sql_console") . "sql_console_styles.php";
     if(isset($_POST["sql_code"])){
         sqlQueryFromString($_POST["sql_code"]);
         $_SESSION["sql_code"] = $_POST["sql_code"];
         }
    
    
     include getModulePath("sql_console") . "sql_console_form.php";
     $config = new config();
     db_select_db($config -> db_database);
     }


?>