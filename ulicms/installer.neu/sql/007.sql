CREATE TABLE IF NOT EXISTS `{prefix}users` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`username` varchar(255) NOT NULL,
`lastname` varchar(255) NOT NULL,
`firstname` varchar(255) NOT NULL,
`email` varchar(255) NOT NULL,
`password` varchar(255) NOT NULL,
`group` int(11) NOT NULL,
`old_encryption` boolean NOT NULL DEFAULT '0',
`skype_id` varchar(32) NOT NULL,
`icq_id` varchar(20) NULL,
`twitter` varchar(15) NULL,
`homepage` text NULL,
`avatar_file` varchar(40) NULL,
`about_me` text NULL,
`last_action` bigint(20) NOT NULL DEFAULT 0,
`last_login` bigint(20) DEFAULT NULL,
`password_changed` DATETIME NULL,
`group_id` int(11) NULL,
`notify_on_login` tinyint(1) NOT NULL DEFAULT '0',
`locked` tinyint(1) NOT NULL DEFAULT '0',
`html_editor` varchar(100) NULL DEFAULT 'ckeditor',
`require_password_change` tinyint(1) NULL DEFAULT '0',
`admin` tinyint(1) NULL DEFAULT '0',
`failed_logins` int(11) DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;" ) or die ( mysqli_error ( $connection ) );

$create_table_groups_sql = "CREATE TABLE IF NOT EXISTS `" . $prefix . "groups` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`permissions` mediumtext NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

mysqli_query ( $connection, $create_table_groups_sql ) or die ( mysqli_error ( $connection ) );

$insert_group_query = 'INSERT INTO `' . $prefix . 'groups` (`id`, `name`, `permissions`) VALUES
(1, \'Administrator\', \'{"banners":true,"cache":true,"dashboard":true,"design":true,"expert_settings":true,"files":true,"groups":true, "categories" : true, "images":true,"info":true,"install_packages":true,"languages":true,"list_packages":true,"logo":true,"favicon":true,"module_settings":true,"motd":true,"other":true,"pages":true,"pkg_settings":true,"remove_packages":true,"settings_simple":true,"spam_filter":true,"update_system":true,"users":true,"export":true, "import" : true, "videos":true, "audio":true, "open_graph":true, "forms":true, "patch_management" : true, "upload_patches" : true, "pages_activate_own" : true, "pages_activate_others" : true, "pages_edit_own" : true, "pages_edit_others" : true, "pages_change_owner" : true }\')';

mysqli_query ( $connection, $insert_group_query );

$vorname = mysqli_real_escape_string ( $connection, $_POST ["firstname"] );
$nachname = mysqli_real_escape_string ( $connection, $_POST ["lastname"] );
$zusammen = mysqli_real_escape_string ( $connection, "$vorname $nachname" );
$email = mysqli_real_escape_string ( $connection, $_POST ["email"] );
$passwort = $_POST ["passwort"];
$admin_user = mysqli_real_escape_string ( $connection, $_POST ["admin_user"] );
$encrypted_passwort = hash ( "sha512", $salt . $passwort );
$encrypted_passwort = mysqli_real_escape_string ( $connection, $encrypted_passwort );

mysqli_query ( $connection, "INSERT INTO `" . $prefix . "users` (`id`, `old_encryption`,  `username`, `lastname`, `firstname`, `email`, `password`, `group`, `group_id`, `password_changed`, `admin`) VALUES
(1, 0, '" . $admin_user . "', '" . $nachname . "', '" . $vorname . "', '" . $email . "', '" . $encrypted_passwort . "',50, 1, NOW(), 1);" ) or die ( mysqli_error ( $connection ) );

mysqli_query ( $connection, "CREATE TABLE IF NOT EXISTS `" . $prefix . "banner` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` text NOT NULL,
`link_url` text NOT NULL,
`image_url` text NOT NULL,
`category` int(11) DEFAULT '1',
`type` varchar(255) DEFAULT 'gif',
`html` text DEFAULT '',
`language` VARCHAR( 255 ) NULL DEFAULT  'all',
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" ) or die ( mysqli_error ( $connection ) );

mysqli_query ( $connection, "CREATE TABLE IF NOT EXISTS `" . $prefix . "log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`zeit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`request_uri` varchar(255) DEFAULT NULL,
`useragent` varchar(255) DEFAULT NULL,
`referrer` varchar(255) DEFAULT NULL,
`request_method` varchar(10) DEFAULT NULL,
`http_host` varchar(100) DEFAULT NULL,
`ip` varchar(255) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );

mysqli_query ( $connection, "CREATE TABLE IF NOT EXISTS `" . $prefix . "content` (
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
`type` varchar(50) DEFAULT 'page' NULL,
`og_title` varchar(255) DEFAULT '',
`og_type` varchar(255) DEFAULT '',
`og_image` varchar(255) DEFAULT '',
`og_description` varchar(255) DEFAULT '',
`module` varchar(200) default null,
`video` int(11) default null,
`audio` int(11) default null,
`text_position` varchar(10) default 'before',
`approved` tinyint(1) NOT NULL DEFAULT '1',
`image_url` text default null,
show_headline tinyint(1) NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;
