<?php
session_start();
setcookie(session_name(), session_id());
header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<head>
<title>UliCMS Installation</title>
<link rel="stylesheet" type="text/css" href="media/style.css"/>
</head>
<body>
<p><img src="media/logo.png" alt="UliCMS" title="UliCMS"><strong style="margin-left:30px; float:right; font-size:18pt; font-family:impact;">Installation</strong></p>
<hr/>
<?php
if(!isset($_POST["step"])){
     ?>
<h2>Willkommen</h2>
<p>Willkommen zur Installation von UliCMS.</p>
<?php
     include "../version.php";
     $version = new ulicms_version();
    
     if($version -> getDevelopmentVersion()){
         ?>
<p style="color:red;">Das hier ist eine Vorab-Version von UliCMS.<br/>
Das bedeutet, diese Version ist noch nicht 100-prozentig fertig und dient nur der Vorschau auf ein neues Release.<br/>
Setzen Sie diese Version bitte nicht produktiv ein!<br/>
</p>
<?php
         }
     ?>
<p>Folgen Sie den Anweisungen um das CMS auf Ihrem Server zu installieren.</p>
<p>Setzen Sie bitte vorher die Dateirechte der folgenden Dateien auf 0755.<br/>
<ol>
<li>cms-config.php</li>
<li>templates/ (inklusive Inhalt und Unterordner)</li>
<li>content/ (inklusive Inhalt und Unterordner)</li>
<li>modules/ (inklusive Inhalt und Unterordner)</li>
</ol>
</p>
<br/>
<h3>So müssen die Berechtigungen gesetzt sein</h3>
<p><img src="media/chmod_02.png" alt="FTP Rechtevergabe" title="FTP Rechtevergabe" border=1/></p>
<br/>
<?php
    if (!function_exists('gd_info')){
        ?>
<hr/>
<p style="color:red;"><strong>php5-gd</strong> ist nicht installiert.<br/>Ohne <strong>php5-gd</strong> lässt sich UliCMS zwar installieren,<br/>jedoch wird der Dateimanager und die Verarbeitung von Grafikdateien nicht funktionieren.</p>
<hr/>
<?php }
    ?>


<?php
    if (!function_exists('mysqli_connect')){
        ?>
<p style="color:red;"><strong>php5-mysql</strong> ist nicht installiert.<br/>Ohne <strong>php5-mysql</strong> lässt sich UliCMS nicht installieren,<br/>da dieses PHP-Modul für die Kommunikation mit<br/>dem Datenbankserver benötigt wird.</p>

<?php }else{
        ?>

<form action="index.php" method="post">
<input type="hidden" name="step" value="1">
<input type="submit" value="Weiter">
</form>
<br/>

<?php }
    ?>
<?php
     }else{
     ?>
<?php if($_POST["step"] == "1"){
         ?>
<h2>MySQL-Logindaten</h2>
<p>Bitte tragen Sie in das untere Formular die Logindaten für den MySQL-Server ein. Diese bekommen Sie von Ihrem Provider.</p>
<form action="index.php" method="post">
<table border=1>
<tr>
<td>Servername:</td>
<td><input name="servername" type="text" value="localhost"></td>
</tr>
<tr>
<td>Loginname:</td>
<td><input name="loginname"type="text" value=""></td>
</tr>
<tr>
<td>Passwort:</td>
<td><input name="passwort" type="password" value=""></td>
</tr>
<tr>
<td>Datenbank:</td>
<td><input name="datenbank" type="text" value=""></td>
</tr>
<tr>
<td>Prefix:</td>
<td><input name="prefix" type="text" value="ulicms_"></td>
</tr>
</table>
<p><input type="submit" value="Weiter"></p>
<input type="hidden" name="step" value="2">
</form>

<?php }
     ?>
<?php if($_POST["step"] == "2"){
        
         ?>
<h2>MySQL-Logindaten</h2>
<?php
         @$connection = mysqli_connect($_POST["servername"], $_POST["loginname"], $_POST["passwort"]);
         if($connection == false){
             echo "Die Verbindung mit dem MySQL-Datenbankserver konnte nicht hergestellt werden.<br/>Dies kann z.B. an einem falschen Passwort liegen. Wenn Sie sich sicher sind, dass das Passwort richtig ist, prüfen Sie ob der MySQL-Datenbankserver läuft und erreichbar ist.";
             }else{
            
             @$select = mysqli_select_db($connection, $_POST["datenbank"]);
            
             if($select == false){
                 echo "<p>Die Datenbank \"" . htmlspecialchars($_POST["datenbank"]) . "\" konnte nicht geöffnet werden.<br/>Eventuell müssen Sie die Datenbank vorher anlegen.</p>";
                 }else{
                 $_SESSION["mysql"] = array();
                 $_SESSION["mysql"]["server"] = $_POST["servername"];
                 $_SESSION["mysql"]["loginname"] = $_POST["loginname"];
                 $_SESSION["mysql"]["passwort"] = $_POST["passwort"];
                 $_SESSION["mysql"]["datenbank"] = $_POST["datenbank"];
                 $_SESSION["mysql"]["prefix"] = $_POST["prefix"];
                 ?>
<p>Die Verbindung mit dem Datenbankserver wurde erfolgreich hergestellt.</p>

<form action="index.php" method="post">
<input type="hidden" name="step" value="3">
<input type="submit" value="Weiter">
</form>

<?php
                
                 }
            
             }
         ?>



<?php
         }
     ?>

<?php if($_POST["step"] == "3"){
         ?>
<h2>Homepage-Einstellungen</h2>
<form action="index.php" method="post">
<table border=1>
<tr>
<td>Titel der Homepage:</td>
<td><input name="homepage_title" type="text" value="Meine Homepage"></td>
</tr>
<tr>
<td>Motto:</td>
<td><input name="motto" type="text" value="Dies und Das"></td>
</tr>
<tr>
<td>Ihr Vorname:</td>
<td><input name="firstname" type="text" value="Max"></td>
</tr>
<tr>
<td>Ihr Nachname:</td>
<td><input name="lastname" type="text" value="Mustermann"></td>
</tr>
<tr>
<td>Ihre Emailadresse:</td>
<td><input name="email" type="text" value="max@muster.de"></td>
</tr>
<tr>
<td>Ihr Passwort:</td>
<td><input name="passwort" type="password" value=""></td>
</tr>
</table>
<p><input type="submit" value="Installation starten"></p>
<input type="hidden" name="step" value="4">
</form>


<?php
         }
     ?>

<?php if($_POST["step"] == "4"){
        
        
        
         $salt = uniqid();
        
         $connection = mysqli_connect($_SESSION["mysql"]["server"], $_SESSION["mysql"]["loginname"], $_SESSION["mysql"]["passwort"]);
        
         mysqli_select_db($connection, $_SESSION["mysql"]["datenbank"]);
        
         $prefix = mysqli_real_escape_string($connection, $_SESSION["mysql"]["prefix"]);
        
         mysqli_query($connection, "SET NAMES 'utf8'")or die(mysqli_error($connection));
        
         mysqli_query($connection, "CREATE TABLE IF NOT EXISTS `" . $prefix . "admins` (
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;")or die(mysqli_error($connection));;
        
        
         $vorname = mysqli_real_escape_string($connection, $_POST["firstname"]);
         $nachname = mysqli_real_escape_string($connection, $_POST["lastname"]);
         $zusammen = mysqli_real_escape_string($connection, "$vorname $nachname");
         $email = mysqli_real_escape_string($connection, $_POST["email"]);
         $passwort = mysqli_real_escape_string($connection, $_POST["passwort"]);
        
         $encrypted_passwort = sha1($salt . $passwort);
        
         mysqli_query($connection, "INSERT INTO `" . $prefix . "admins` (`id`, `old_encryption`,  `username`, `lastname`, `firstname`, `email`, `password`, `group`) VALUES
(1, 0, 'admin', '" . $nachname . "', '" . $vorname . "', '" . $email . "', '" . $encrypted_passwort . "',50);")or die(mysqli_error($connection));
        
         mysqli_query($connection, "CREATE TABLE IF NOT EXISTS `" . $prefix . "banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `link_url` text NOT NULL,
  `image_url` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")or die(mysqli_error($connection));
        
         mysqli_query($connection, "CREATE TABLE IF NOT EXISTS `" . $prefix . "content` (
  
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notinfeed` tinyint(1) NOT NULL,
  `systemname` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `target` varchar(255) DEFAULT '_self',
  `content` longtext NOT NULL,
  `language` varchar(6) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created` bigint(20) NOT NULL,
  `lastmodified` bigint(20) NOT NULL,
  `autor` int(11) NOT NULL,
  `category` int(11) NOT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")or die(mysqli_error($connection));
        
        
         mysqli_query($connection, "INSERT INTO `" . $prefix . "content` (`id`, `notinfeed`, `systemname`, `title`, `target`, `content`, `language`, `active`, `created`, `lastmodified`, `autor`, `category`, `lastchangeby`, `views`, `comments_enabled`, `redirection`, `menu`, `position`, `parent`, `valid_from`, `valid_to`, `access`, `meta_description`, `meta_keywords`, `deleted_at`) VALUES
(1, 0, 'willkommen', 'Willkommen', '_self', '<p>Willkommen auf einer neuen Website die mit UliCMS betrieben wird.</p>\r\n', 'de', 1, 1364242679, 1364242833, 1, 0, 1, 19, 1, '', 'top', 10, NULL, '0000-00-00', NULL, 'all', '', '', NULL),
(2, 0, 'welcome', 'Welcome', '_self', '<p>Welcome to a new website running with UliCMS.</p>\r\n', 'en', 1, 1364242890, 1364242944, 1, 0, 1, 2, 1, '', 'top', 10, NULL, '0000-00-00', NULL, 'all', '', '', NULL) ;")or die(mysqli_error($connection));
        
         mysqli_query($connection, "CREATE TABLE IF NOT EXISTS `" . $prefix . "settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")or die(mysqli_error($connection));
        
        
        
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
(13, 'logo_image', '0b27dc99b9875f306287bb3965c57304.png'),    
(14, 'motd', '<p>Willkommen bei <strong>UliCMS</strong>!<br/>
Eine Dokumentation finden Sie unter <a href=\"http://www.ulicms.de\" target=\"_blank\">www.ulicms.de</a>.</p>'),
(15, 'visitors_can_register', 'off'),
(16, 'frontpage', 'willkommen'),
(17, 'contact_form_refused_spam_mails', '0'),
(18, 'default_language', 'de'),
(19, 'country_blacklist', 'ru, cn, in'),
(20, 'spamfilter_enabled', 'yes'),
(21, 'comment_mode', 'off'),
(22, 'facebook_id', ''),
(23, 'disqus_id', ''),
(24, 'spamfilter_words_blacklist', 
'$badwords'),
(25, 'empty_trash_days', '30'),
(26, 'password_salt', '$salt'),
(27, 'timezone', 'Europe/Berlin'),
(28, 'db_schema_version', '6.7'),
(29, 'pkg_src', 'http://www.ulicms.de/packages/{version}/'),
(30, 'theme', 'default'),
(31, 'zoom', '100'),
(32, 'default-font', 'Arial, \'Helvetica Neue\', Helvetica, sans-serif'),
(33, 'font-size', '12pt'),
(34, 'header-background-color', '#E8912A'),
(35, 'body-background-color', '#FFFFFF'),
(36, 'body-text-color', '#000000'),
(37, 'disable_html_validation', 'disable'),
(38, 'title_format', '%homepage_title% > %title%'),
(39, 'mailer', 'php-mail'),
(40, 'cache_type', 'file'),
(41, 'registered_user_default_level', '10'),
(42, 'override_shortcuts', 'backend'),
(43, 'session_timeout', '60');")or die(mysqli_error($connection));
        
         mysqli_query($connection, "UPDATE `" . $prefix . "content` SET parent=NULL")or die(mysqli_error($connection));
        
         mysqli_query($connection, "CREATE TABLE IF NOT EXISTS `" . $prefix . "languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `language_code` varchar(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3;")or die(mysqli_error($connection));
        
        
         mysqli_query($connection, "INSERT INTO `" . $prefix . "languages` (`id`, `name`, `language_code`) VALUES
(1, 'Deutsch', 'de'), 
(2, 'English', 'en');")or die(mysqli_error($connection));
        
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

var $db_server="' . $_SESSION["mysql"]["server"] . '";
var $db_user="' . $_SESSION["mysql"]["loginname"] . '";
var $db_password="' . $_SESSION["mysql"]["passwort"] . '";
var $db_database="' . $_SESSION["mysql"]["datenbank"] . '";
var $db_prefix="' . $_SESSION["mysql"]["prefix"] . '";
var $db_type="mysql";

}';
        
        
         if(!is_writable("../cms-config.php")){
             echo "<p>Die Konfigurationsdatei konnte wegen fehlenden Berechtigungen nicht erzeugt werden. Bitte bearbeiten Sie die Datei cms-config.php mit einem Texteditor und fügen Sie den Code aus der Textbox ein.</p>" .
             "<p><textarea cols=50 rows=10>" . htmlspecialchars($config_string) . "</textarea></p>";
             }else{
             $handle = fopen("../cms-config.php", "w");
             fwrite($handle, $config_string);
             fclose($handle);
             }
        
         $message = "Hallo $zusammen!\n" .
         "Auf " . $_SERVER["SERVER_NAME"] . " wurde UliCMS erfolgreich installiert\n\n" .
         "Die Zugangsdaten lauten:\n" .
         "Benutzername: admin\n" .
         "Passwort: $passwort\n\n" .
         "Den Adminbereich finden Sie, indem Sie an die URL hinter dem letzen / (Schrägstrich) ein /admin anhängen.";
        
        
        
         $success = @mail($email,
             "UliCMS Installation auf " . $_SERVER["SERVER_NAME"],
             $message, "From: $email\nContent-Type: text/plain; charset=UTF-8"
            );
        
         session_destroy();
        
        
        
        
         ?>
<h2>Installation beendet</h2>
<p>Die Installation von UliCMS wurde erfolgreich beendet.<br/>Bitte löschen Sie nun aus Sicherheitsgründen den Ordner "installer" vom Server. Sie können sich nun <a href="../admin/">hier</a> einloggen. Der Benutzername lautet <i>admin</i>.<br/><br/>
<?php if($success){
             ?>
<span style="color:green;">Die Zugangsdaten wurden Ihnen per Mail geschickt.
</span>

<?php }else{
             ?>
<span style="color:red;">Die Zugangsdaten konnten Ihnen wegen einem technischen Problem nicht per E-Mail geschickt werden.</span>
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
<p style="color:#6f6f6f; font-size:small;">&copy; 2011 - 2013 by <a href="http://www.ulicms.de" target="_blank">ulicms.de</a></p>
</body>
</html>
