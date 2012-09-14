<?php
require_once "init.php";
global $connection;

$config = new config();
$prefix = $config->mysql_prefix;

mysql_query("SET NAMES utf8");

mysql_query("DELETE FROM ".$prefix."admins");    
mysql_query("DELETE FROM ".$prefix."news");
mysql_query("DELETE FROM ".$prefix."content");
mysql_query("DELETE FROM ".$prefix."banners");
mysql_query("DELETE FROM ".$prefix."settings");
mysql_query("DELETE FROM ".$prefix."backend_menu_structure");



mysql_query("INSERT INTO `".$prefix."admins` (`id`, `username`, `lastname`, `firstname`, `email`, `password`, `group`, `skype_id`, `icq_id`, `avatar_file`, `about_me`) VALUES
(1, 'admin', 'Mustermann', 'Max', 'max@muster.de', '5f4dcc3b5aa765d61d8327deb882cf99', 50, '', '', '', ''),
(2, 'redakteur', 'Duck', 'Donald', 'donald@duck.de', '6cb75f652a9b52798eb6cf2201057c73', 40, '', '', '', ''),
(3, 'mitarbeiter', 'Kolumna', 'Karla', 'karla@kolumna.de', '819b0643d6b89dc9b579fdfc9094f28e', 20, '', '', '', '');");


mysql_query("INSERT INTO `".$prefix."content` (`id`, `notinfeed`, `systemname`, `title`, `content`, `active`, `created`, `lastmodified`, `autor`, `category`, `lastchangeby`, `views`, `comments_enabled`, `redirection`, `menu`, `position`, `parent`, `valid_from`, `valid_to`, `access`, `meta_description`, `meta_keywords`, `deleted_at`) VALUES
(21, 0, 'willkommen', 'Willkommen', '<p>\r\n	Herzlichen Gl&uuml;ckwunsch!<br />\r\n	UliCMS wurde erfolgreich auf dieser Website installiert.</p>\r\n<p>\r\n	<span style=\"color:#ff0000;\"><strong>Achtung! Diese Demo-Installation wird alle zwei Stunden automatisch zur&uuml;ckgesetzt.</strong></span></p>\r\n', 1, 1345472717, 1345569145, 1, 0, 1, 14, 0, '', 'top', 10, '-', '2012-08-20', NULL, 'all', '', '', NULL),
(22, 1, 'kontakt', 'Kontakt', '[module=\"kontaktformular\"]', 1, 1345472717, 1345472717, 1, 0, 1, 0, 0, '', 'down', 10, '-', '2012-08-20', NULL, 'all', '', '', NULL),
(23, 1, 'login', 'Anmelden', '', 1, 1345472717, 1345472717, 1, 0, 1, 0, 0, 'admin/?go=../index.php', 'down', 20, '-', '2012-08-20', NULL, 'all', '', '', NULL);");

mysql_query("INSERT INTO `bla_news` (`id`, `title`, `content`, `date`, `active`, `autor`) VALUES
(8, 'Lorem Ipsum', '<div id=\"lipsum\">\r\n	<p>\r\n		Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque dictum diam sed turpis pellentesque eget sollicitudin felis euismod. Fusce ullamcorper dolor vitae tellus condimentum ut commodo metus ultricies. Donec viverra ornare enim vitae eleifend. Pellentesque mi purus, consectetur accumsan tincidunt ac, convallis ut dolor. Ut vel placerat nisi. Fusce nisl dui, lobortis sed sollicitudin ut, tincidunt quis orci. Proin hendrerit neque et augue rhoncus in ultricies erat aliquet. Ut blandit commodo lorem, rhoncus dapibus lacus adipiscing in. Maecenas sagittis mauris vitae ligula hendrerit vitae euismod lorem gravida. Praesent vitae neque nunc, in pretium nisl. Nullam sed tellus vel enim viverra dictum.</p>\r\n	<p>\r\n		In hac habitasse platea dictumst. Vivamus ut est tortor, eget posuere lectus. Quisque in velit eget justo luctus pulvinar. Pellentesque at aliquam purus. Morbi in risus libero, non hendrerit ligula. Donec non nibh erat, pharetra elementum nunc. Pellentesque nisi nunc, lobortis at facilisis vitae, facilisis sit amet ipsum. Quisque tempus, erat nec pretium pretium, dolor erat gravida quam, quis cursus est quam id dolor. In molestie vestibulum mattis. Praesent volutpat, justo ut sagittis vehicula, nisi augue congue libero, eu auctor quam enim hendrerit libero. Nunc eget fringilla ligula. Vivamus gravida sagittis gravida.</p>\r\n	<p>\r\n		Morbi lectus dolor, facilisis quis sodales ultrices, sollicitudin at nisi. Etiam sed erat et turpis laoreet pellentesque commodo ut neque. Suspendisse potenti. Duis a nisi eu eros congue laoreet vel eu diam. Vestibulum convallis ultrices porttitor. Proin quis dolor purus, sed adipiscing eros. Nulla facilisi. Curabitur elementum enim in diam sollicitudin malesuada. Nunc dictum, dui non laoreet gravida, enim felis luctus leo, et iaculis justo libero sit amet dui. Suspendisse auctor elit ut arcu aliquet dapibus. Nulla molestie porta enim eget dignissim. Sed odio urna, eleifend vitae lobortis nec, lacinia quis metus. Sed sed ullamcorper sem. Maecenas vel turpis feugiat ipsum aliquam laoreet non eu mauris.</p>\r\n	<p>\r\n		Integer eget venenatis lacus. Vivamus dignissim, tellus eget blandit porttitor, magna enim lacinia massa, convallis auctor augue turpis rhoncus neque. Aliquam consectetur est sed nisi blandit interdum porta ante pulvinar. In quis pulvinar turpis. Nullam magna orci, auctor nec pellentesque quis, ornare quis lectus. Cras at est nisi, ac condimentum massa. Suspendisse a purus metus, in viverra felis. Aenean neque risus, porta eget ultrices vel, dictum eget leo. Vivamus ut est sodales erat vehicula congue. Suspendisse sem mauris, fermentum id viverra id, varius sit amet enim. Proin bibendum quam quis massa fringilla volutpat at nec mauris. Proin at laoreet nisl. Nunc condimentum commodo dui, quis volutpat tellus consequat nec.</p>\r\n	<p>\r\n		Praesent porttitor ligula ut nulla iaculis rhoncus. Quisque euismod, ligula fringilla sollicitudin mattis, enim tortor consequat dui, a viverra velit eros quis nisl. Mauris venenatis est a sapien mattis interdum. Pellentesque at purus diam. In placerat lacus in libero dapibus iaculis. Donec tempor interdum posuere. Nullam at quam quis ante fermentum egestas. Fusce porttitor erat eu lacus ullamcorper posuere id eu lacus.</p>\r\n	<p>\r\n		Nullam fermentum tincidunt ligula, sit amet imperdiet urna ullamcorper ac. Donec lectus eros, tristique ut ultricies a, dapibus vitae dui. Aliquam varius dictum gravida. Sed ac diam non felis cursus luctus. Suspendisse tortor nisl, dapibus nec placerat ut, ultrices semper urna. Nullam faucibus facilisis lectus, vel pulvinar dui facilisis at. Nullam ultrices elit vel neque vestibulum a iaculis ligula fermentum. Aenean pharetra ultricies eleifend. Integer vehicula ipsum sit amet lacus aliquet ultricies pharetra dolor dignissim. Ut vehicula tempor elit, ut congue nunc adipiscing sit amet. Mauris tempor dignissim ultricies. Cras purus leo, interdum vehicula pellentesque vitae, varius vitae libero. Nunc molestie mi vitae nisi semper eu rhoncus orci feugiat. Nullam sagittis interdum sem, ut elementum nibh lobortis eu. Fusce mauris ligula, sodales vel placerat eu, vestibulum sit amet ante.</p>\r\n	<p>\r\n		Cras sed purus sapien, a convallis purus. Aenean congue faucibus velit fermentum volutpat. Nam dictum, ipsum non scelerisque mattis, nunc enim tincidunt felis, at mollis ligula velit ullamcorper arcu. Curabitur ut nunc vulputate justo hendrerit imperdiet. Aliquam erat volutpat. Mauris tincidunt, ligula non scelerisque blandit, nisl justo consectetur odio, non ornare nunc tortor feugiat risus. Mauris sed erat at ante iaculis adipiscing. Mauris a nunc dolor. Pellentesque adipiscing, eros at faucibus tempus, mauris tellus congue felis, non aliquet purus mi sit amet ante. Quisque pretium, dui sed tristique malesuada, justo lectus eleifend sapien, sit amet egestas sem lacus eu nisl. Sed ac velit non erat adipiscing gravida quis in risus.</p>\r\n	<p>\r\n		Nunc non dui tellus, at lobortis leo. Suspendisse aliquam nibh sit amet odio sagittis aliquet. Mauris quis lectus non metus accumsan volutpat at in justo. Mauris imperdiet sapien a nunc ornare laoreet. Vivamus euismod vulputate odio eget condimentum. Nullam in sapien ut magna dignissim dapibus. Maecenas feugiat neque urna, non ullamcorper nisl. Integer condimentum placerat lorem sit amet fermentum. Nullam nec ipsum a turpis ultricies consequat luctus bibendum arcu. Nunc egestas tristique dui, eget consequat diam cursus feugiat. Suspendisse ullamcorper, metus quis porta vehicula, ante urna dignissim turpis, non venenatis metus velit sit amet erat. Duis vitae ante adipiscing lorem dignissim lacinia id vel tellus. Quisque imperdiet quam mattis elit mattis commodo. Nulla ac suscipit ipsum.</p>\r\n	<p>\r\n		Morbi interdum, purus sit amet luctus elementum, nunc orci interdum nibh, sit amet pulvinar nulla nisl sit amet sapien. Sed malesuada laoreet iaculis. Fusce tincidunt, tellus egestas viverra blandit, mi nisl elementum arcu, at consequat elit elit quis orci. Morbi euismod accumsan eleifend. Aenean mi erat, elementum ac luctus vitae, rhoncus nec purus. Phasellus sagittis sem nec enim placerat id iaculis quam hendrerit. Nulla in est tellus. Nam hendrerit imperdiet mauris eu tincidunt. Phasellus rutrum tincidunt ligula quis dictum. Fusce vulputate pretium nulla vel aliquam. Fusce porta posuere nisl, id pulvinar ipsum accumsan nec. Ut adipiscing pulvinar lacus, at aliquam risus sollicitudin vel. Mauris consectetur tempor massa non placerat. Suspendisse ipsum urna, feugiat eu aliquam in, tempus sed arcu. Sed in felis magna, sit amet aliquam lacus. Duis sed diam nulla, sit amet tincidunt tellus.</p>\r\n	<p>\r\n		Mauris lacinia pellentesque dolor, eu vehicula lacus euismod ac. Phasellus et nisl a orci accumsan tincidunt. Nam et vulputate est. Pellentesque adipiscing urna at lacus volutpat non suscipit tellus feugiat. Vestibulum iaculis auctor nibh, in ornare elit euismod eget. Proin venenatis purus tincidunt magna pellentesque cursus. Ut eget velit tellus, sit amet commodo ipsum. Suspendisse potenti. Vivamus massa nisl, mattis at consectetur non, auctor non nisi. Sed mattis molestie sem, id malesuada velit pharetra eu. Mauris lobortis porttitor dui, vitae eleifend nulla faucibus quis. Quisque pellentesque sodales sagittis. Phasellus vel sollicitudin metus. Etiam enim libero, mollis a lacinia eu, ornare a ligula.</p>\r\n</div>\r\n<p>\r\n	&nbsp;</p>\r\n', 1345569186, 1, 1);
");


mysql_query("INSERT INTO `".$prefix."settings` (`id`, `name`, `value`) VALUES
(1, 'homepage_title', 'Meine Homepage'),
(2, 'maintenance_mode', '0'),
(3, 'redirection', ''),
(4, 'disable_cache', 'off'),
(5, 'language', 'de-DE'),
(6, 'homepage_owner', 'Max Mustermann'),
(7, 'email', 'max@muster.de'),
(8, 'motto', 'Dies und Das'),
(9, 'date_format', 'd.m.Y H:i:s'),
(10, 'autor_text', 'Diese Seite wurde verfasst von Vorname Nachname'),
(11, 'max_news', '10'),
(12, 'meta_keywords', 'Stichwort 1, Stichwort 2, Stichwort 3'),
(13, 'meta_description', 'Eine kurzer Beschreibungstext'),
(14, 'logo_disabled', 'no'),
(15, 'logo_image', '0b27dc99b9875f306287bb3965c57304.png'),
(16, 'motd', 'Hinweis:\r\nDiese Installation wird alle 2 Stunden automatisch zurÃ¼ckgesetzt.'),
(17, 'visitors_can_register', 'on'),
(18, 'frontpage', 'willkommen'),
(40, 'comment_mode', 'off'),
(41, 'facebook_id', ''),
(42, 'disqus_id', ''),
(43, 'items_in_rss_feed', '10'),
(44, 'allowed_html', '<i><b><strong><em><ul><li><ol><a>');");

mysql_query("INSERT INTO `".$prefix."backend_menu_structure` (`id`, `action`, `label`, `position`) VALUES
(15, 'media', 'Medien', 3),
(14, 'home', 'Willkommen', 1),
(12, 'destroy', 'Logout', 10),
(24, 'system_update', 'Update', 7),
(23, 'contents', 'Inhalte', 2),
(18, 'templates', 'Templates', 5),
(19, 'info', 'Info', 9),
(20, 'settings_categories', 'Einstellungen', 8),
(21, 'modules', 'Module', 6),
(22, 'admins', 'Benutzer', 4);");

?>