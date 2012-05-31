<?php 
session_start();
setcookie(session_name(),session_id());
header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<head>
<title>UliCMS Installation</title>
<style type="text/css">
</style>
</head>
<body>
<h1>UliCMS [Installation]</h1>
<?php 
if(!isset($_POST["step"])){
?>
<h2>Willkommen</h2>
<p>Willkommen zur Installation von UliCMS.</p>
<p>Folgen Sie den Anweisungen um das CMS auf Ihrem Server zu installieren.</p>
<p>Setzen Sie bitte vorher die Dateirechte der folgenden Dateien auf 0755:<br/>
<ol>
<li>cms-config.php</li>
<li>templates/oben.php</li>
<li>templates/unten.php</li>
<li>templates/news.txt</li>
<li>templates/comments.php</li>
<li>templates/maintenance.php</li>
<li>content/</li>
</ol>
</p>
<p></p>
<form action="index.php" method="post">
<input type="hidden" name="step" value="1">
<input type="submit" value="Weiter">
</form>
<?php 
}else{
?>
<?php if($_POST["step"]=="1"){?>
<h2>MySQL-Logindaten</h2>
<p>Bitte tragen Sie in das untere Formular die Logindaten für den MySQL-Server ein. Diese bekommen Sie von Ihrem Provider.</p>
<form action="index.php" method="post">
<table border=1>
<tr>
<td>Servername:</td>
<td><input name="servername"type="text" value="localhost"></td>
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

<?php }?>
<?php if($_POST["step"]=="2"){

?>
<h2>MySQL-Logindaten</h2>
<?php
@$connection=mysql_connect($_POST["server"],$_POST["loginname"],$_POST["passwort"]);
if($connection==false){
echo "Die Verbindung mit dem MySQL-Datenbankserver konnte nicht hergestellt werden.<br/>Dies kann z.B. an einem falschen Passwort liegen. Wenn Sie sich sicher sind, dass das Passwort richtig ist, prüfen Sie ob der MySQL-Datenbankserver läuft oder eventuell abgestürzt ist.";
}else{

@$select=mysql_select_db($_POST["datenbank"]);

if($select==false){
echo "<p>Die Datenbank \"".htmlspecialchars($_POST["datenbank"])."\" Konnte nicht geöffnet werden.<br/>Eventuell müssen Sie die Datenbank vorher anlegen.</p>";
}else{
$_SESSION["mysql"]=array();
$_SESSION["mysql"]["server"]=$_POST["servername"];
$_SESSION["mysql"]["loginname"]=$_POST["loginname"];
$_SESSION["mysql"]["passwort"]=$_POST["passwort"];
$_SESSION["mysql"]["datenbank"]=$_POST["datenbank"];
$_SESSION["mysql"]["prefix"]=$_POST["prefix"];
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

<?php if($_POST["step"]=="3"){
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
<td><input name="passwort" type="text" value=""></td>
</tr>
</table>
<p><input type="submit" value="Installation starten"></p>
<input type="hidden" name="step" value="4">
</form>


<?php
}
?>

<?php if($_POST["step"]=="4"){



$connection=mysql_connect($_SESSION["mysql"]["server"],$_SESSION["mysql"]["loginname"],$_SESSION["mysql"]["passwort"]);

mysql_select_db($_SESSION["mysql"]["datenbank"]);

$prefix=mysql_real_escape_string($_SESSION["mysql"]["prefix"]);

mysql_query("SET NAMES 'utf-8'");

mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(500) NOT NULL,
  `lastname` varchar(500) NOT NULL,
  `firstname` varchar(500) NOT NULL,
  `email` varchar(800) NOT NULL,
  `password` varchar(500) NOT NULL,
  `group` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;");


$vorname=mysql_real_escape_string($_POST["firstname"]);
$nachname=mysql_real_escape_string($_POST["lastname"]);
$zusammen=mysql_real_escape_string("$vorname $nachname");
$email=mysql_real_escape_string($_POST["email"]);
$passwort=mysql_real_escape_string($_POST["passwort"]);



mysql_query("INSERT INTO `".$prefix."admins` (`id`, `username`, `lastname`, `firstname`, `email`, `password`, `group`) VALUES
(1, 'admin', '".$nachname."', '".$vorname."', '".$email."', '".$passwort."',50);");

mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `link_url` text NOT NULL,
  `image_url` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;");


mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `date` bigint(20) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `autor` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;");

mysql_query("INSERT INTO `".$prefix."news` (`id`, `title`, `content`, `date`, `active`, `autor`) VALUES 
(NULL, 'Neue Homepage Online', '<p>Ab heute sind wir mit neuer Homepage online.</p>\r\n', ".time().", 1, 1);");



mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notinfeed` tinyint(1) NOT NULL,
  `systemname` varchar(300) NOT NULL,
  `title` varchar(600) NOT NULL,
  `content` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created` bigint(20) NOT NULL,
  `lastmodified` bigint(20) NOT NULL,
  `autor` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `lastchangeby` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  `comments_enabled` tinyint(1) NOT NULL,
  `redirection` varchar(2083) NOT NULL,
  `menu` varchar(10) NOT NULL,
  `position` int(11) NOT NULL,
  `parent` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;");



mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `value` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;");



$homepage_title=mysql_real_escape_string($_POST["homepage_title"]);
$motto=mysql_real_escape_string($_POST["motto"]);





mysql_query("INSERT INTO `".$prefix."settings` (`id`, `name`, `value`) VALUES
(1, 'homepage_title', '$homepage_title'),
(2, 'maintenance_mode', '0'),
(3, 'redirection', ''),
(4, 'disable_cache', 'off'),
(5, 'language', 'de-DE'),
(6, 'homepage_owner', '$zusammen'),
(7, 'email', '$email'),
(8, 'motto', '$motto'),
(9, 'date_format', 'd.m.Y H:i:s'),
(10, 'autor_text', 'Diese Seite wurde verfasst von Vorname Nachname'),
(11, 'max_news', '10'),
(12, 'meta_keywords', 'Stichwort 1, Stichwort 2, Stichwort 3'),
(13, 'meta_description', 'Eine kurzer Beschreibungstext'),
(14, 'frontpage', 'hallo_welt');");
                                
                   
                                                 
mysql_query("INSERT INTO  `".$prefix."settings`(
`id` ,
`name` ,
`value`
)
VALUES (
NULL ,  'comment_mode',  'off'
);
");



mysql_query("INSERT INTO  `".$prefix."settings`(
`id` ,
`name` ,
`value`
)
VALUES (
NULL ,  'facebook_id',  ''
);
");

mysql_query("INSERT INTO  `".$prefix."settings`(
`id` ,
`name` ,
`value`
)
VALUES (
NULL ,  'disqus_id',  ''
);
");






mysql_query("INSERT INTO  `".$prefix."settings` (
`id` ,
`name` ,
`value`
)
VALUES (
NULL ,  'items_in_rss_feed',  '10')");





mysql_query('INSERT INTO `'.$prefix.'content` (`id`, `notinfeed`, `systemname`, `title`, `content`, `active`, `created`, `lastmodified`, `autor`, `category`, `lastchangeby`, `views`, `comments_enabled`, `redirection`, `menu`, `position`) VALUES
(16, 0, \'hallo_welt\', \'Hallo Welt\', \'<p>\r\n	Wenn Sie das Lesen k&ouml;nnen, war Ihre UliCMS Installation erfolgreich.<br />\r\n	Am Besten, Sie loggen sich in den Administrationsbereich ein und beginnen nun mit der Erstellung Ihrer Internetseite.</p>\r\n<p>\r\n	&nbsp;</p>\r\n\', 1, 1337975057, 1337975159, 1, 0, 0, 5, 1, \'\', \'top\', 0),
(5, 1, \'impressum\', \'Impressum\', \'<p>\r\n	<b>Verantwortlich f&uuml;r diese Seite ist:</b><br />\r\n	Max Mustermann<br />\r\n	<br />\r\n	<b>Anschrift:</b><br />\r\n	Musterstrasse 1<br />\r\n	42877 Musterhausen<br />\r\n	Deutschland</p>\r\n<p>\r\n	<strong>Email:</strong> <a href="mailto:max@muster.de">max@muster.de</a></p>\r\n<p>\r\n	<strong>Telefonnummer: </strong>0123456789</p>\r\n<p>\r\n	&nbsp;</p>\r\n\', 1, 1296347129, 1337975251, 0, 0, 0, 10, 0, \'\', \'down\', 10),
(6, 1, \'kontakt\', \'Kontaktformular\', \'<form action="mail.php" method="post">\r\n	<table border="0" cellpadding="1" cellspacing="1" style="width: 641px; height: 479px;">\r\n		<tbody>\r\n			<tr>\r\n				<td>\r\n					<strong>Ihr Vorname: </strong></td>\r\n				<td>\r\n					<input name="vorname" size="40" type="text" /></td>\r\n			</tr>\r\n			<tr>\r\n				<td>\r\n					<strong>Ihr Nachname:</strong></td>\r\n				<td>\r\n					<input name="nachname" size="40" type="text" /></td>\r\n			</tr>\r\n			<tr>\r\n				<td>\r\n					<strong>Ihre Emailadresse</strong>:</td>\r\n				<td>\r\n					<input name="emailadresse" size="40" type="text" /></td>\r\n			</tr>\r\n			<tr>\r\n				<td>\r\n					<strong>Ihr Betreff:</strong></td>\r\n				<td>\r\n					<input name="betreff" size="40" type="text" /></td>\r\n			</tr>\r\n			<tr>\r\n				<td>\r\n					<strong>Ihre Nachricht:</strong></td>\r\n				<td>\r\n					<p>\r\n						<textarea cols="60" name="nachricht" rows="20"></textarea></p>\r\n					<p>\r\n						&nbsp;</p>\r\n				</td>\r\n			</tr>\r\n			<tr>\r\n				<td>\r\n					<strong>Formular:</strong></td>\r\n				<td>\r\n					<input type="reset" value="Zurücksetzen" />&nbsp;&nbsp; <input type="submit" value="Jetzt absenden" /> <input name="absenden" type="hidden" value="absenden" /></td>\r\n			</tr>\r\n			<tr>\r\n				<td>\r\n					&nbsp;</td>\r\n				<td>\r\n					&nbsp;</td>\r\n			</tr>\r\n		</tbody>\r\n	</table>\r\n</form>\r\n<p>\r\n	&nbsp;</p>\r\n\', 1, 1296347750, 1337972920, 0, 0, 0, 0, 0, \'\', \'down\', 30);');

mysql_query("UPDATE `".$prefix."content` SET parent='-'");


@chmod("../cms-config.php", 0777);

@mkdir("../content");
@chmod("../content", 0777);

@chmod("../templates/oben.php", 0777);
@chmod("../templates/unten.php", 0777);
@chmod("../templates/news.txt", 0777);
@chmod("../templates/maintenance.php", 0777);


$handle=fopen("../cms-config.php","w");
fwrite($handle,'<?php 
class config{

var $mysql_server="'.$_SESSION["mysql"]["server"].'";
var $mysql_user="'.$_SESSION["mysql"]["loginname"].'";
var $mysql_password="'.$_SESSION["mysql"]["passwort"].'";
var $mysql_database="'.$_SESSION["mysql"]["datenbank"].'";
var $mysql_prefix="'.$_SESSION["mysql"]["prefix"].'";

}
?>');
fclose($handle);

session_destroy();

?>
<h2>Installation beendet</h2>
<p>Die Installation von UliCMS wurde erfolgreich beendet.<br/>Bitte löschen Sie nun aus Sicherheitsgründen den Ordner "installer" vom Server. Sie können sich nun <a href="../admin/">hier</a> einloggen. Der Benutzername lautet <i>admin</i>.<br/></p>


<?php }

?>
<?php

}
?>
<p>&copy; 2011 - 2012 by <a href="http://www.ulicms.de" target="_blank">ulicms.de</a></p>
</body>
</html>