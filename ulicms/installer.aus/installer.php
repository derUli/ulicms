<?php
session_start();
setcookie(session_name(), session_id());
error_reporting (E_ALL ^ E_NOTICE);

define("REQUIRED_PHP_VERSION", "5.3.0");

if(!isset($_SESSION["language"])){
     $_SESSION["language"] = "de";
     }

if(isset($_GET["language"])){
     $_SESSION["language"] = basename($_GET["language"]);
     }

$file = "lang/" . $_SESSION["language"] . ".php";

if(!file_exists($file)){
     $file = "lang/de.php";
     }

$required_php_version = version_compare(phpversion(), REQUIRED_PHP_VERSION, ">=");

include_once $file;

date_default_timezone_set("Europe/Berlin");
header("Content-Type: text/html; charset=UTF-8");
include_once "../lib/workaround.php";
?>
<!DOCTYPE html>
<head>
<title><?php echo TRANSLATION_TITLE;
?></title>
<link rel="stylesheet" type="text/css" href="media/style.css"/>
</head>
<body>
<p><img src="../admin/gfx/logo.png" alt="UliCMS" title="UliCMS"><strong style="margin-left:30px; float:right; font-size:18pt;">Installation</strong></p>
<hr/>
<?php
if(!isset($_REQUEST["step"])){
     ?>
   <form action="index.php" method="get">
<p><strong>Sprache auswählen / Select a language</strong><br/>

<select name="language" onchange="window.location.replace('?language=' + this.value)">
<option value="de" <?php if($_SESSION["language"] == "de") echo "selected";
     ?>">Deutsch</option>
<option value="en" <?php if($_SESSION["language"] == "en") echo "selected";
     ?>>English</option>
</select>
</p>
<p>
<input type="hidden" name="step" value="0">
<input type="submit" value="<?php echo TRANSLATION_NEXT;
     ?>">

</p>
</form>
<?php }else if($_REQUEST["step"] == "0"){
     ?>
<h2><?php echo TRANSLATION_WELCOME;
     ?></h2>
<p><?php echo TRANSLATION_WELCOME2;
     ?></p>
<?php
     include "../version.php";
     $version = new ulicms_version();
    
     if($version -> getDevelopmentVersion()){
         ?>
<p style="color:red;"><?php echo TRANSLATION_BETA_VERSION;
         ?>
</p>
<?php
         }
     ?>
<p><?php echo TRANSLATION_FOLLOW_INSTRUCTIONS;
     ?></p>
<?php echo TRANSLATION_CHMOD;
     ?>
<h3><?php echo TRANSLATION_PERMISSION;
     ?></h3>
<p><img src="media/chmod_<?php echo htmlspecialchars($_SESSION["language"]);
     ?>.png" alt="<?php echo TRANSLATION_PERMISSIONS2;
     ?>" title="<?php echo TRANSLATION_PERMISSIONS2;
     ?>" border=1/></p>
     
<?php if(!$required_php_version){
         ?>
<p style="color:red;"><?php echo TRANSLATION_PHP_VERSION_TOO_LOW;
         ?></p>
<?php
         }
     ?>
<?php
     if (!function_exists('gd_info')){
         ?>
<hr/>
<p style="color:red;"><?php echo TRANSLATION_GD_MISSING;
         ?></p>
<hr/>
<?php }
     ?>


<?php
     if (!function_exists('mysqli_connect')){
         $error = true;
         ?>
<p style="color:red;"><?php echo TRANSLATION_MYSQLI_MISSING;
         ?></p>

<?php
        
         }
     if(!function_exists("json_encode")){
         $error = true;
         ?>

<p style="color:red;"><?php echo TRANSLATION_JSON_MISSING;
         ?></p>

<?php }
    
     if(!isset($error)){
         ?>

<form action="index.php" method="post">
<input type="hidden" name="step" value="1">
<input type="submit" value="<?php echo TRANSLATION_NEXT;
         ?>">
</form>
<br/>

<?php }
     ?>
<?php
     }else{
     ?>
<?php if($_REQUEST["step"] == "1"){
         ?>
<h2><?php echo TRANSLATION_MYSQL_LOGIN;
         ?></h2>
<p><?php echo TRANSLATION_MYSQL_LOGIN_HELP;
         ?></p>
<form action="index.php" method="post">
<table border=1>
<tr>
<td><?php echo TRANSLATION_SERVERNAME;
         ?></td>
<td><input name="servername" type="text" value="localhost"></td>
</tr>
<tr>
<td><?php echo TRANSLATION_LOGINNAME;
         ?></td>
<td><input name="loginname"type="text" value=""></td>
</tr>
<tr>
<td><?php echo TRANSLATION_PASSWORD;
         ?></td>
<td><input name="passwort" type="password" value=""></td>
</tr>
<tr>
<td><?php echo TRANSLATION_DATABASE_NAME;
         ?></td>
<td><input name="datenbank" type="text" value=""></td>
</tr>
<tr>
<td><?php echo TRANSLATION_PREFIX;
         ?></td>
<td><input name="prefix" type="text" value="ulicms_"></td>
</tr>
</table>
<p><input type="submit" value="<?php echo TRANSLATION_NEXT;
         ?>"></p>
<input type="hidden" name="step" value="2">
</form>

<?php }
     ?>
<?php if($_REQUEST["step"] == "2"){
        
         ?>
<h2><?php echo TRANSLATION_MYSQL_LOGIN;
         ?></h2>
<?php
         @$connection = mysqli_connect($_POST["servername"], $_POST["loginname"], $_POST["passwort"]);
         if($connection == false){
             echo TRANSLATION_DB_CONNECTION_FAILED;
             }else{
            
             @$select = mysqli_select_db($connection, $_POST["datenbank"]);
            
             if($select == false){
                 echo TRANSLATION_CANT_OPEN_SCHEMA;
                 }else{
                 $_SESSION["mysql"] = array();
                 $_SESSION["mysql"]["server"] = $_POST["servername"];
                 $_SESSION["mysql"]["loginname"] = $_POST["loginname"];
                 $_SESSION["mysql"]["passwort"] = $_POST["passwort"];
                 $_SESSION["mysql"]["datenbank"] = $_POST["datenbank"];
                 $_SESSION["mysql"]["prefix"] = $_POST["prefix"];
                 ?>
<p><?php echo TRANSLATION_SUCCESSFULL_DB_CONNECT;
                 ?></p>

<form action="index.php" method="post">
<input type="hidden" name="step" value="3">
<input type="submit" value="<?php echo TRANSLATION_NEXT;
                 ?>">
</form>

<?php
                
                 }
            
             }
         ?>



<?php
         }
     ?>

<?php if($_REQUEST["step"] == "3"){
         ?>
<h2><?php echo TRANSLATION_HOMEPAGE_SETTINGS;
         ?></h2>
<form action="index.php" method="post">
<table border=1>
<tr>
<td><?php echo TRANSLATION_HOMEPAGE_TITLE;
         ?></td>
<td><input name="homepage_title" type="text" value="Meine Homepage"></td>
</tr>
<tr>
<td><?php echo TRANSLATION_SITE_SLOGAN;
         ?></td>
<td><input name="motto" type="text" value="Dies und Das"></td>
</tr>
<tr>
<td><?php echo TRANSLATION_YOUR_FIRSTNAME;
         ?></td>
<td><input name="firstname" type="text" value="Max"></td>
</tr>
<tr>
<td><?php echo TRANSLATION_YOUR_LASTNAME;
         ?></td>
<td><input name="lastname" type="text" value="Mustermann"></td>
</tr>
<tr>
<td><?php echo TRANSLATION_YOUR_EMAIL_ADRESS;
         ?></td>
<td><input name="email" type="text" value="max@muster.de"></td>
</tr>
<tr>
<td><?php echo TRANSLATION_ADMIN_NAME;
         ?></td>
<td><input name="admin_user" type="text" value="admin"></td>
</tr>
<tr>
<td><?php echo TRANSLATION_ADMIN_PASSWORD;
         ?></td>
<td><input name="passwort" type="password" value=""></td>
</tr>
</table>
<p><input type="submit" value="<?php echo TRANSLATION_DO_INSTALL;
         ?>"></p>
<input type="hidden" name="step" value="4">
</form>


<?php
         }
     ?>

<?php if($_REQUEST["step"] == "4"){
        
        
        
         $salt = uniqid();
         $connection = mysqli_connect($_SESSION["mysql"]["server"], $_SESSION["mysql"]["loginname"], $_SESSION["mysql"]["passwort"]);
         mysqli_select_db($connection, $_SESSION["mysql"]["datenbank"]);
        
        
         $prefix = mysqli_real_escape_string($connection, $_SESSION["mysql"]["prefix"]);
        
         mysqli_query($connection, "SET NAMES 'utf8'")or die(mysqli_error($connection));
        
         // sql_mode auf leer setzen, da sich UliCMS nicht im strict_mode betreiben lässt
        mysqli_query($connection, "SET SESSION sql_mode = '';");
        
         mysqli_query($connection, "CREATE TABLE IF NOT EXISTS `" . $prefix . "users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `group` int(11) NOT NULL,
  `old_encryption` boolean NOT NULL DEFAULT 0,
  `skype_id` varchar(32) NOT NULL,
  `icq_id` varchar(20) NOT NULL,
  `avatar_file` varchar(40) NOT NULL,
  `about_me` text NOT NULL,
  `last_action` bigint(20) NOT NULL,
  `group_id` int(11) NULL,
  `notify_on_login` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;")or die(mysqli_error($connection));
        
         $create_table_groups_sql = "CREATE TABLE IF NOT EXISTS `" . $prefix . "groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `permissions` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        
         mysqli_query($connection, $create_table_groups_sql)or die(mysqli_error($connection));
        
         $insert_group_query = 'INSERT INTO `' . $prefix . 'groups` (`id`, `name`, `permissions`) VALUES
(1, \'Administrator\', \'{"banners":true,"cache":true,"dashboard":true,"design":true,"expert_settings":true,"files":true,"flash":true,"groups":true, "categories" : true, "images":true,"info":true,"install_packages":true,"languages":true,"list_packages":true,"logo":true,"module_settings":true,"motd":true,"other":true,"pages":true,"pkg_settings":true,"remove_packages":true,"settings_simple":true,"spam_filter":true,"templates":true,"update_system":true,"users":true,"export":true, "import" : true, "videos":true}\')';
        
         mysqli_query($connection, $insert_group_query);
        
        
         $vorname = mysqli_real_escape_string($connection, $_POST["firstname"]);
         $nachname = mysqli_real_escape_string($connection, $_POST["lastname"]);
         $zusammen = mysqli_real_escape_string($connection, "$vorname $nachname");
         $email = mysqli_real_escape_string($connection, $_POST["email"]);
         $passwort = $_POST["passwort"];
         $admin_user = mysqli_real_escape_string($connection, $_POST["admin_user"]);
         $encrypted_passwort = hash("sha512", $salt . $passwort);
         $encrypted_passwort = mysqli_real_escape_string($connection, $encrypted_passwort);
        
         mysqli_query($connection, "INSERT INTO `" . $prefix . "users` (`id`, `old_encryption`,  `username`, `lastname`, `firstname`, `email`, `password`, `group`, `group_id`) VALUES
(1, 0, '" . $admin_user . "', '" . $nachname . "', '" . $vorname . "', '" . $email . "', '" . $encrypted_passwort . "',50, 1);")or die(mysqli_error($connection));
        
         mysqli_query($connection, "CREATE TABLE IF NOT EXISTS `" . $prefix . "banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `link_url` text NOT NULL,
  `image_url` text NOT NULL,
  `category` int(11) DEFAULT '1',
  `type` varchar(255) DEFAULT 'gif',
  `html` text DEFAULT '',
  `language` VARCHAR( 255 ) NULL DEFAULT  'all',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")or die(mysqli_error($connection));
        
         mysqli_query($connection, "CREATE TABLE IF NOT EXISTS `" . $prefix . "content` (
  
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notinfeed` tinyint(1) NOT NULL,
  `systemname` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `alternate_title` VARCHAR(255) DEFAULT '',
  `target` varchar(255) DEFAULT '_self',
  `category` int(11) DEFAULT '1',
  `content` longtext NOT NULL,
  `language` varchar(6) NOT NULL,
  `menu_image` varchar(255) NULL,
  `active` tinyint(1) NOT NULL,
  `created` bigint(20) NOT NULL,
  `lastmodified` bigint(20) NOT NULL,
  `autor` int(11) NULL,
  `lastchangeby` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  `comments_enabled` tinyint(1) NOT NULL,
  `redirection` varchar(255) NOT NULL,
  `menu` varchar(10) NOT NULL,
  `position` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `valid_from` date NOT NULL,
  `valid_to` date DEFAULT NULL,
  `access` varchar(100) DEFAULT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `deleted_at` bigint(20) DEFAULT NULL,
  `html_file` varchar(255) DEFAULT NULL,
  `theme` varchar(200) null,
  `custom_data` varchar(255) NULL DEFAULT '{}',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")or die(mysqli_error($connection));
        
        
         mysqli_query($connection, "INSERT INTO `" . $prefix . "content` (`id`, `notinfeed`, `systemname`, `title`, `target`, `content`, `language`, `active`, `created`, `lastmodified`, `autor`, `category`, `lastchangeby`, `views`, `comments_enabled`, `redirection`, `menu`, `position`, `parent`, `valid_from`, `valid_to`, `access`, `meta_description`, `meta_keywords`, `deleted_at`) VALUES
(1, 0, 'willkommen', 'Willkommen', '_self', '<p>Willkommen auf einer neuen Website die mit UliCMS betrieben wird.</p>\r\n', 'de', 1, 1364242679, 1364242833, 1, 1, 1, 19, 1, '', 'top', 10, NULL, '0000-00-00', NULL, 'all', '', '', NULL),
(2, 0, 'welcome', 'Welcome', '_self', '<p>Welcome to a new website running with UliCMS.</p>\r\n', 'en', 1, 1364242890, 1364242944, 1, 1, 1, 2, 1, '', 'top', 10, NULL, '0000-00-00', NULL, 'all', '', '', NULL) ;")or die(mysqli_error($connection));
        
         mysqli_query($connection, "CREATE TABLE IF NOT EXISTS `" . $prefix . "settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")or die(mysqli_error($connection));
        
        
        
         $homepage_title = mysqli_real_escape_string($connection, $_POST["homepage_title"]);
         $motto = mysqli_real_escape_string($connection, $_POST["motto"]);
        
         $badwords = "viagra
vicodin
cialis
xanax
mortgage
refinance
pharm
diploma
enlargement
pills";
        
         $badwords = str_replace("\r\n", "||", $badwords);
         $badwords = str_replace("\n", "||", $badwords);
        
         $badwords = mysqli_real_escape_string($connection, $badwords);
        
        
         mysqli_query($connection, "INSERT INTO `" . $prefix . "settings` (`id`, `name`, `value`) VALUES
(1, 'homepage_title', '$homepage_title'),
(2, 'maintenance_mode', '0'),
(3, 'redirection', ''),
(4, 'homepage_owner', '$zusammen'),
(5, 'email', '$email'),
(6, 'motto', '$motto'),
(7, 'date_format', 'd.m.Y H:i'),
(8, 'autor_text', 'Diese Seite wurde verfasst von Vorname Nachname'),
(9, 'robots', 'index,follow'),
(10, 'meta_keywords', 'Stichwort 1, Stichwort 2, Stichwort 3'),
(11, 'meta_description', 'Eine kurzer Beschreibungstext'),
(12, 'logo_disabled', 'no'),
(13, 'logo_image', '67cc042b9ee9eb28cdc81ae7d7420d8a.png'),    
(14, 'motd', '<p>Willkommen bei <strong>UliCMS</strong>!<br/>
Eine Dokumentation finden Sie unter <a href=\"http://www.ulicms.de\" target=\"_blank\">www.ulicms.de</a>.</p>'),
(15, 'visitors_can_register', 'off'),
(16, 'frontpage', 'willkommen'),
(17, 'contact_form_refused_spam_mails', '0'),
(18, 'default_language', '" . $_SESSION["language"] . "'),
(19, 'system_language', '" . $_SESSION["language"] . "'),
(20, 'country_blacklist', 'ru, cn, in'),
(21, 'spamfilter_enabled', 'yes'),
(22, 'comment_mode', 'off'),
(23, 'facebook_id', ''),
(24, 'disqus_id', ''),
(25, 'spamfilter_words_blacklist', 
'$badwords'),
(26, 'empty_trash_days', '30'),
(27, 'password_salt', '$salt'),
(28, 'timezone', 'Europe/Berlin'),
(29, 'db_schema_version', '8.0.1'),
(30, 'pkg_src', 'http://packages.ulicms.de/{version}/'),
(31, 'theme', 'cleanblue'),
(32, 'zoom', '100'),
(33, 'default-font', 'Arial, \'Helvetica Neue\', Helvetica, sans-serif'),
(34, 'font-size', 'medium'),
(35, 'header-background-color', '#E8912A'),
(36, 'body-background-color', '#FFFFFF'),
(37, 'body-text-color', '#000000'),
(38, 'disable_html_validation', 'disable'),
(39, 'title_format', '%homepage_title% > %title%'),
(40, 'cache_type', 'file'),
(41, 'registered_user_default_level', '10'),
(42, 'override_shortcuts', 'backend'),
(43, 'domain_to_language', ''),
(44, 'frontpage_de', 'willkommen'),
(45, 'frontpage_en', 'welcome'),
(46, 'email_mode', 'internal'),
(47, 'ckeditor_skin', 'moono'),
(48, 'installed_at', '" . time() . "'),
(49, 'cache_disabled', 'disabled'),
(50, 'locale', 'de_DE.UTF-8; de_DE; deu_deu'),
(51, 'locale_de', 'de_DE.UTF-8; de_DE; deu_deu'),
(52, 'locale_en', 'en_US.UTF-8; en_GB.UTF-8; en_US; en_GB; english-uk; eng; uk'),
(53, 'session_timeout', '60');")or die(mysqli_error($connection));
        
         mysqli_query($connection, "UPDATE `" . $prefix . "content` SET parent=NULL")or die(mysqli_error($connection));
        
         mysqli_query($connection, "CREATE TABLE IF NOT EXISTS `" . $prefix . "languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `language_code` varchar(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3;")or die(mysqli_error($connection));
        
mysqli_query($connection, "CREATE TABLE IF NOT EXISTS `".$prefix."installed_patches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `date` DATETIME NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
        
        
        
         mysqli_query($connection, "INSERT INTO `" . $prefix . "languages` (`id`, `name`, `language_code`) VALUES
(1, 'Deutsch', 'de'), 
(2, 'English', 'en');")or die(mysqli_error($connection));
        
        
        mysqli_query($connection, "CREATE TABLE IF NOT EXISTS `".$prefix ."videos` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `mp4_file` varchar(255) DEFAULT NULL,
  `ogg_file` varchar(255) DEFAULT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created` bigint(20) NOT NULL,
  `updated` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
        
         $sql_categories_table = "CREATE TABLE " . $prefix . "categories (
          id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(100),
          `description` TEXT NULL DEFAULT ''
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
         mysqli_query($connection, $sql_categories_table);
        
        
         $insert_categories_general = "INSERT INTO " . $prefix . "categories (name) VALUES('Allgemein')";
         mysqli_query($connection, $insert_categories_general);
        
        
        
         $sql = "ALTER TABLE `" . $prefix . "languages` ADD UNIQUE(`language_code`)";
        
         mysqli_query($connection, $sql);
        
         // Da InnoDB erst ab MySQL 5.6 Foreign Keys und Fulltext unterstützt
        // Foreign Keys nur setzen, wenn MySQL Server Version >= 5.6
        if(mysqli_get_server_version($connection) >= 50600){
            
             $constraint1 = "ALTER TABLE `" . $prefix . "users` ADD FOREIGN KEY (`group_id`) REFERENCES `" . $prefix . "groups`(`id`) 
ON DELETE SET NULL";
             mysqli_query($connection, $constraint1);
             $constraint2 = "ALTER TABLE `" . $prefix . "content` ADD FOREIGN KEY (`category`) REFERENCES `" . $prefix . "categories`(`id`) 
ON DELETE SET NULL";
             mysqli_query($connection, $constraint2);
            
            
            
             $constraint3 = "ALTER TABLE `" . $prefix . "banner` ADD FOREIGN KEY (`category`) REFERENCES `" . $prefix . "categories`(`id`) 
ON DELETE SET NULL";
             mysqli_query($connection, $constraint3);
            
            
             $constraint4 = "ALTER TABLE `" . $prefix . "content` ADD FOREIGN KEY (`autor`) REFERENCES `" . $prefix . "users`(`id`) 
ON DELETE SET NULL";
             mysqli_query($connection, $constraint4);
            
            /**
             * $constraint5 = "ALTER TABLE `" . $prefix . "content` ADD FOREIGN KEY (`language`) REFERENCES `".$prefix."languages`(`language_code`) 
             * ON DELETE SET NULL";
             * mysqli_query($connection, $constraint5);
             */
            
             }
         @chmod("../cms-config.php", 0777);
        
         @mkdir("../content");
         @chmod("../content", 0777);
        
         if(!file_exists("../content/cache")){
             @mkdir("../content/cache", 0777, true);
             }
        
         if(!file_exists("../modules/")){
             @mkdir("../modules/", 0777, true);
             }
        
        
        
        
        
         @chmod("../templates/oben.php", 0777);
         @chmod("../templates/unten.php", 0777);
         //  @chmod ("../templates/news.txt", 0777);
        @chmod("../templates/maintenance.php", 0777);
        
         $config_string = '<?php 
class config extends baseConfig{
  
  var $db_server = "' . $_SESSION["mysql"]["server"] . '";
  var $db_user = "' . $_SESSION["mysql"]["loginname"] . '";
  var $db_password = "' . $_SESSION["mysql"]["passwort"] . '";
  var $db_database = "' . $_SESSION["mysql"]["datenbank"] . '";
  var $db_prefix = "' . $_SESSION["mysql"]["prefix"] . '";
  var $db_type = "mysql";
  var $debug = false;
}';
        
        
         if(!is_writable("../")){
             echo "<p>Die Konfigurationsdatei konnte wegen fehlenden Berechtigungen nicht erzeugt werden. Bitte bearbeiten Sie die Datei cms-config.php mit einem Texteditor und fügen Sie den Code aus der Textbox ein.</p>" .
             "<p><textarea cols=50 rows=10>" . htmlspecialchars($config_string) . "</textarea></p>";
             }else{
             $handle = fopen("../cms-config.php", "w");
             fwrite($handle, $config_string);
             fclose($handle);
             }
        
         $message =
        
         $title = str_ireplace("%domain%", $_SERVER["HTTP_HOST"], TRANSLATION_MAIL_MESSAGE_TITLE);
        
         $content = TRANSLATION_MAIL_MESSAGE_TEXT;
         $content = str_ireplace("%domain%", $_SERVER["HTTP_HOST"], $content);
         $content = str_ireplace("%person_name%", $zusammen, $content);
         $content = str_ireplace("%username%", $admin_user, $content);
         $content = str_ireplace("%password%", $passwort, $content);
         $success = @mail($email,
             $title , $content, "From: $email\nContent-Type: text/plain; charset=UTF-8"
            );
        
         session_destroy();
        
        
        
        
         ?>
<h2><?php echo TRANSLATION_INSTALLATION_FINISHED;
         ?></h2>
<p><?php echo TRANSLATION_FIRST_LOGIN_HELP;
         ?><br/><br/>
<?php if($success){
             ?>
<span style="color:green;"><?php echo TRANSLATION_LOGIN_DATA_SENT_BY_MAIL;
             ?>
</span>

<?php }else{
             ?>
<span style="color:red;"><?php echo TRANSLATION_LOGIN_DATA_NOT_SENT_BY_MAIL;
             ?></span>
<?php }
         ?>
<br/>
</p>

<?php }
    
     ?>
<?php
    
     }
?>
<hr style="margin-top:30px;"/>
<p style="color:#6f6f6f; font-size:small;">&copy; 2011 - 2015 by <a href="http://www.ulicms.de" target="_blank">ulicms.de</a></p>
</body>
</html>
