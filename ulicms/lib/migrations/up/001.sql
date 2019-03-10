SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE `{prefix}audio` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mp3_file` varchar(255) DEFAULT NULL,
  `ogg_file` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created` bigint(20) NOT NULL,
  `updated` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{prefix}banner` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `link_url` text NOT NULL,
  `image_url` text NOT NULL,
  `category` int(11) DEFAULT '1',
  `type` varchar(255) DEFAULT 'gif',
  `html` text,
  `language` varchar(255) DEFAULT 'all',
  `enabled` tinyint(4) NOT NULL DEFAULT '1',
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `{prefix}banner` (`id`, `name`, `link_url`, `image_url`, `category`, `type`, `html`, `language`, `enabled`, `date_from`, `date_to`) VALUES
(1, 'Content Management einfach gemacht mit UliCMS', 'http://www.ulicms.de', 'http://www.ulicms.de/content/images/banners/ulicms-banner.jpg', 1, 'gif', '', 'de', 1, NULL, NULL);

CREATE TABLE `{prefix}categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `{prefix}categories` (`id`, `name`, `description`) VALUES
(1, 'Allgemein', NULL);

CREATE TABLE `{prefix}comments` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `author_name` varchar(255) NOT NULL,
  `author_email` varchar(255) DEFAULT NULL,
  `author_url` varchar(255) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` text NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `ip` varchar(255) DEFAULT NULL,
  `useragent` text,
  `read` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `{prefix}content` (
  `id` int(11) NOT NULL,
  `systemname` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `alternate_title` varchar(255) DEFAULT '',
  `target` varchar(255) DEFAULT '_self',
  `category` int(11) DEFAULT '1',
  `content` mediumtext NOT NULL,
  `language` varchar(6) DEFAULT NULL,
  `menu_image` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `created` bigint(20) NOT NULL,
  `lastmodified` bigint(20) NOT NULL,
  `autor` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `lastchangeby` int(11) DEFAULT NULL,
  `views` int(11) NOT NULL,
  `redirection` varchar(255) NOT NULL,
  `menu` varchar(20) NOT NULL,
  `position` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `access` varchar(100) DEFAULT NULL,
  `meta_description` varchar(200) NOT NULL,
  `meta_keywords` varchar(200) NOT NULL,
  `deleted_at` bigint(20) DEFAULT NULL,
  `theme` varchar(200) DEFAULT NULL,
  `custom_data` text,
  `type` varchar(50) DEFAULT 'page',
  `og_title` varchar(255) DEFAULT '',
  `og_type` varchar(255) DEFAULT '',
  `og_image` varchar(255) DEFAULT '',
  `og_description` varchar(255) DEFAULT '',
  `module` varchar(200) DEFAULT NULL,
  `video` int(11) DEFAULT NULL,
  `audio` int(11) DEFAULT NULL,
  `text_position` varchar(10) DEFAULT 'before',
  `approved` tinyint(1) NOT NULL DEFAULT '1',
  `image_url` text,
  `show_headline` tinyint(1) NOT NULL DEFAULT '1',
  `cache_control` varchar(10) DEFAULT 'auto',
  `article_author_name` varchar(80) DEFAULT '',
  `article_author_email` varchar(80) DEFAULT '',
  `article_date` datetime DEFAULT NULL,
  `article_image` varchar(255) DEFAULT '',
  `excerpt` text,
  `only_admins_can_edit` tinyint(1) NOT NULL DEFAULT '0',
  `only_group_can_edit` tinyint(1) NOT NULL DEFAULT '0',
  `only_owner_can_edit` tinyint(1) NOT NULL DEFAULT '0',
  `only_others_can_edit` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `comment_homepage` varchar(255) DEFAULT NULL,
  `link_to_language` int(11) DEFAULT NULL,
  `comments_enabled` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `{prefix}custom_fields` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{prefix}dbtrack` (
  `id` int(11) NOT NULL,
  `component` varchar(150) NOT NULL,
  `name` varchar(150) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{prefix}forms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT '1',
  `email_to` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `fields` text,
  `required_fields` text,
  `mail_from_field` varchar(255) DEFAULT NULL,
  `target_page_id` int(11) DEFAULT NULL,
  `created` bigint(20) DEFAULT NULL,
  `updated` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{prefix}groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `permissions` mediumtext NOT NULL,
  `allowable_tags` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `{prefix}groups` (`id`, `name`, `permissions`, `allowable_tags`) VALUES
(1, 'Administrator', '{\r\n   \"audio\":true,\r\n   \"audio_create\":true,\r\n   \"audio_edit\":true,\r\n   \"banners\":true,\r\n   \"banners_create\":true,\r\n   \"banners_edit\":true,\r\n   \"cache\":true,\r\n   \"categories\":true,\r\n   \"categories_create\":true,\r\n   \"categories_edit\":true,\r\n   \"comments_manage\":true,\r\n   \"community_settings\":true,\r\n   \"dashboard\":true,\r\n   \"default_access_restrictions_edit\":true,\r\n   \"design\":true,\r\n   \"expert_settings\":true,\r\n   \"expert_settings_edit\":true,\r\n   \"extend_upgrade_helper\":true,\r\n   \"favicon\":true,\r\n   \"files\":true,\r\n   \"forms\":true,\r\n   \"forms_create\":true,\r\n   \"forms_edit\":true,\r\n   \"fortune2_get\":true,\r\n   \"fortune2_post\":true,\r\n   \"fortune_settings\":true,\r\n   \"groups\":true,\r\n   \"groups_create\":true,\r\n   \"groups_edit\":true,\r\n   \"images\":true,\r\n   \"info\":true,\r\n   \"install_packages\":true,\r\n   \"languages\":true,\r\n   \"list_packages\":true,\r\n   \"logo\":true,\r\n   \"module_settings\":true,\r\n   \"motd\":true,\r\n   \"oneclick_upgrade_settings\":true,\r\n   \"open_graph\":true,\r\n   \"other\":true,\r\n   \"pages\":true,\r\n   \"pages_activate_others\":true,\r\n   \"pages_activate_own\":true,\r\n   \"pages_change_owner\":true,\r\n   \"pages_create\":true,\r\n   \"pages_edit_others\":true,\r\n   \"pages_edit_own\":true,\r\n   \"pages_edit_permissions\":true,\r\n   \"pages_show_positions\":true,\r\n   \"patch_management\":true,\r\n   \"performance_settings\":true,\r\n   \"pkg_settings\":true,\r\n   \"privacy_settings\":true,\r\n   \"remove_packages\":true,\r\n   \"settings_simple\":true,\r\n   \"spam_filter\":true,\r\n   \"update_system\":true,\r\n   \"upload_patches\":true,\r\n   \"users\":true,\r\n   \"users_create\":true,\r\n   \"users_edit\":true,\r\n   \"videos\":true,\r\n   \"videos_create\":true,\r\n   \"videos_edit\":true\r\n}', '<a><abbr><address><area><article><aside><audio><b><bdi><bdo><blockquote><br/><br><button><canvas><caption><cite><code><col><colgroup><command><data><datalist><dd><del><details><dfn><div><dl><dt><em><embed><fieldset><figcaption><figure><font><footer><form><h1><h2><h3><h4><h5><h6><header><hgroup><hr><i><iframe><img><input><ins><kbd><keygen><label><legend><li><map><mark><math><menu><meter><nav><object><ol><optgroup><option><output><p><param><pre><progress><q><rp><rt><ruby><s><samp><section><select><small><source><span><strong><sub><summary><sup><svg><table><tbody><td><textarea><tfoot><th><thead><time><tr><track><u><ul><var><video><wbr>');

CREATE TABLE `{prefix}group_languages` (
  `group_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `{prefix}history` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{prefix}installed_patches` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{prefix}languages` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `language_code` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `{prefix}languages` (`id`, `name`, `language_code`) VALUES
(1, 'Deutsch', 'de'),
(2, 'English', 'en');

CREATE TABLE `{prefix}lists` (
  `content_id` int(11) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `menu` varchar(10) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order_by` varchar(30) DEFAULT 'title',
  `order_direction` varchar(30) DEFAULT 'asc',
  `limit` int(11) DEFAULT NULL,
  `use_pagination` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{prefix}log` (
  `id` int(11) NOT NULL,
  `zeit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `request_uri` varchar(255) DEFAULT NULL,
  `useragent` varchar(255) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `request_method` varchar(10) DEFAULT NULL,
  `http_host` varchar(100) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{prefix}mails` (
  `id` int(11) NOT NULL,
  `headers` text NOT NULL,
  `to` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` mediumtext NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{prefix}modules` (
  `name` varchar(100) NOT NULL,
  `version` varchar(20) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{prefix}password_reset` (
  `token` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `{prefix}settings` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `{prefix}settings` (`id`, `name`, `value`) VALUES
(1, 'homepage_title', 'Sample Page'),
(2, 'maintenance_mode', '0'),
(3, 'redirection', ''),
(4, 'homepage_owner', 'Ulrich Schmidt'),
(5, 'email', 'admin@deruli.de'),
(6, 'motto', 'Where do you want to go today?'),
(7, 'date_format', 'd.m.Y H:i'),
(8, 'autor_text', 'Diese Seite wurde verfasst von Vorname Nachname'),
(9, 'robots', 'index,follow'),
(10, 'meta_keywords', 'Stichwort 1, Stichwort 2, Stichwort 3'),
(11, 'meta_description', 'Eine kurzer Beschreibungstext'),
(12, 'logo_disabled', 'no'),
(13, 'logo_image', '1c7fc60b8ac709a661598ea1a236c155.png'),
(14, 'motd', '<p>Willkommen bei <strong>UliCMS</strong>!<br/>\r\nEine Dokumentation finden Sie unter <a href=\"http://www.ulicms.de\" target=\"_blank\">www.ulicms.de</a>.</p>'),
(15, 'visitors_can_register', 'off'),
(16, 'frontpage', 'willkommen'),
(17, 'contact_form_refused_spam_mails', '0'),
(18, 'default_language', '{language}'),
(19, 'system_language', '{language}'),
(20, 'country_blacklist', 'ru, cn, in'),
(21, 'spamfilter_enabled', 'yes'),
(25, 'spamfilter_words_blacklist', 'viagra\r\nvicodin\r\ncialis\r\nxanax\r\nmortgage\r\nrefinance\r\npharm\r\ndiploma\r\nenlargement\r\npills'),
(26, 'empty_trash_days', '30'),
(27, 'password_salt', '{salt}'),
(28, 'timezone', 'Europe/Berlin'),
(30, 'pkg_src', 'https://packages.ulicms.de/{version}/'),
(31, 'theme', 'impro17'),
(33, 'default_font', 'Arial, \'Helvetica Neue\', Helvetica, sans-serif'),
(34, 'font-size', '16px'),
(35, 'header-background-color', '#35A1E8'),
(36, 'body-background-color', '#FCFCFC'),
(37, 'body-text-color', '#153154'),
(39, 'title_format', '%homepage_title% > %title%'),
(40, 'cache_type', 'file'),
(43, 'domain_to_language', ''),
(44, 'frontpage_de', 'willkommen'),
(45, 'frontpage_en', 'welcome'),
(46, 'email_mode', 'internal'),
(47, 'ckeditor_skin', 'moono'),
(48, 'installed_at', '{time}'),
(49, 'cache_disabled', 'disabled'),
(50, 'locale', 'de_DE.UTF-8; de_DE; deu_deu'),
(51, 'locale_de', 'de_DE.UTF-8; de_DE; deu_deu'),
(52, 'locale_en', 'en_US.UTF-8; en_GB.UTF-8; en_US; en_GB; english-uk; eng; uk'),
(53, 'session_timeout', '60'),
(54, 'og_type', 'article'),
(55, 'ga_secret', '{ga_secret}'),
(56, 'allowed_html', '<i><u><b><strong><em><ul><li><ol><a><span>'),
(57, 'oneclick_upgrade_channel', 'slow'),
(58, 'min_time_to_fill_form', '0'),
(59, 'x_frame_options', 'SAMEORIGIN'),
(60, 'x_xss_protection', 'sanitize'),
(61, 'referrer_policy', 'no-referrer-when-downgrade'),
(62, 'minify_html', '1');

CREATE TABLE `{prefix}users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `old_encryption` tinyint(1) NOT NULL DEFAULT '0',
  `homepage` text,
  `about_me` text,
  `last_action` bigint(20) NOT NULL DEFAULT '0',
  `last_login` bigint(20) DEFAULT NULL,
  `password_changed` datetime DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `html_editor` varchar(100) DEFAULT 'ckeditor',
  `require_password_change` tinyint(1) DEFAULT '0',
  `admin` tinyint(1) DEFAULT '0',
  `failed_logins` int(11) DEFAULT '0',
  `default_language` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `{prefix}users` (`id`, `username`, `lastname`, `firstname`, `email`, `password`, `old_encryption`, `homepage`, `about_me`, `last_action`, `last_login`, `password_changed`, `group_id`, `locked`, `html_editor`, `require_password_change`, `admin`, `failed_logins`, `default_language`) VALUES
(1, '{admin_user}', '{admin_lastname}', '{admin_firstname}', '{admin_email}', '{encrypted_password}', 0, NULL, NULL, 0, NULL, '2019-03-08 18:23:12', 1, 0, 'ckeditor', 0, 1, 0, '{language}');

CREATE TABLE `{prefix}user_groups` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `{prefix}videos` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mp4_file` varchar(255) DEFAULT NULL,
  `ogg_file` varchar(255) DEFAULT NULL,
  `webm_file` varchar(255) DEFAULT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created` bigint(20) NOT NULL,
  `updated` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `{prefix}audio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audio_category` (`category_id`);

ALTER TABLE `{prefix}banner`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_id` (`category`);

ALTER TABLE `{prefix}categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{prefix}comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`);

ALTER TABLE `{prefix}content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `systemname` (`systemname`(191)),
  ADD KEY `language` (`language`),
  ADD KEY `menu` (`menu`),
  ADD KEY `parent` (`parent`),
  ADD KEY `active` (`active`),
  ADD KEY `deleted_at` (`deleted_at`),
  ADD KEY `hidden` (`hidden`),
  ADD KEY `type` (`type`),
  ADD KEY `fk_category` (`category`),
  ADD KEY `fk_autor` (`autor`),
  ADD KEY `fk_video` (`video`),
  ADD KEY `fk_audio` (`audio`),
  ADD KEY `fk_link_to_language` (`link_to_language`),
  ADD KEY `fk_content_group_id` (`group_id`);

ALTER TABLE `{prefix}custom_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_content_id` (`content_id`);

ALTER TABLE `{prefix}dbtrack`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{prefix}forms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_forms_category` (`category_id`),
  ADD KEY `fk_target_page_id` (`target_page_id`);

ALTER TABLE `{prefix}groups`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{prefix}group_languages`
  ADD KEY `fk_language` (`language_id`),
  ADD KEY `fk_group` (`group_id`);

ALTER TABLE `{prefix}history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`),
  ADD KEY `fk_content` (`content_id`);

ALTER TABLE `{prefix}installed_patches`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{prefix}languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `language_code` (`language_code`);

ALTER TABLE `{prefix}lists`
  ADD UNIQUE KEY `content_id` (`content_id`),
  ADD KEY `fk_lists_language` (`language`),
  ADD KEY `fk_lists_category` (`category_id`),
  ADD KEY `fk_lists_parent` (`parent_id`);

ALTER TABLE `{prefix}log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{prefix}mails`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{prefix}modules`
  ADD PRIMARY KEY (`name`);

ALTER TABLE `{prefix}password_reset`
  ADD PRIMARY KEY (`token`),
  ADD KEY `fk_user_id` (`user_id`);

ALTER TABLE `{prefix}settings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{prefix}users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_group_id` (`group_id`);

ALTER TABLE `{prefix}user_groups`
  ADD PRIMARY KEY (`user_id`,`group_id`),
  ADD KEY `fk_group_group_id` (`group_id`);

ALTER TABLE `{prefix}videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_video_category` (`category_id`);


ALTER TABLE `{prefix}audio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{prefix}banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `{prefix}categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `{prefix}comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{prefix}content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{prefix}custom_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{prefix}dbtrack`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{prefix}forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{prefix}groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `{prefix}history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{prefix}installed_patches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{prefix}languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `{prefix}log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{prefix}mails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{prefix}settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

ALTER TABLE `{prefix}users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `{prefix}videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `{prefix}audio`
  ADD CONSTRAINT `fk_audio_category` FOREIGN KEY (`category_id`) REFERENCES `{prefix}categories` (`id`) ON DELETE SET NULL;

ALTER TABLE `{prefix}banner`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category`) REFERENCES `{prefix}categories` (`id`) ON DELETE SET NULL;

ALTER TABLE `{prefix}comments`
  ADD CONSTRAINT `{prefix}comments_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `{prefix}content` (`id`) ON DELETE CASCADE;

ALTER TABLE `{prefix}content`
  ADD CONSTRAINT `fk_audio` FOREIGN KEY (`audio`) REFERENCES `{prefix}audio` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_autor` FOREIGN KEY (`autor`) REFERENCES `{prefix}users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category`) REFERENCES `{prefix}categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_content_group_id` FOREIGN KEY (`group_id`) REFERENCES `{prefix}groups` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `fk_content_language` FOREIGN KEY (`language`) REFERENCES `{prefix}languages` (`language_code`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_link_to_language` FOREIGN KEY (`link_to_language`) REFERENCES `{prefix}languages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_parent_content` FOREIGN KEY (`parent`) REFERENCES `{prefix}content` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_video` FOREIGN KEY (`video`) REFERENCES `{prefix}videos` (`id`) ON DELETE SET NULL;

ALTER TABLE `{prefix}custom_fields`
  ADD CONSTRAINT `fk_content_id` FOREIGN KEY (`content_id`) REFERENCES `{prefix}content` (`id`) ON DELETE CASCADE;

ALTER TABLE `{prefix}forms`
  ADD CONSTRAINT `fk_forms_category` FOREIGN KEY (`category_id`) REFERENCES `{prefix}categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_target_page` FOREIGN KEY (`target_page_id`) REFERENCES `{prefix}content` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_target_page_id` FOREIGN KEY (`target_page_id`) REFERENCES `{prefix}content` (`id`) ON DELETE SET NULL;

ALTER TABLE `{prefix}group_languages`
  ADD CONSTRAINT `fk_group` FOREIGN KEY (`group_id`) REFERENCES `{prefix}groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_language` FOREIGN KEY (`language_id`) REFERENCES `{prefix}languages` (`id`) ON DELETE CASCADE;

ALTER TABLE `{prefix}history`
  ADD CONSTRAINT `fk_content` FOREIGN KEY (`content_id`) REFERENCES `{prefix}content` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `{prefix}users` (`id`) ON DELETE SET NULL;

ALTER TABLE `{prefix}lists`
  ADD CONSTRAINT `fk_lists_category` FOREIGN KEY (`category_id`) REFERENCES `{prefix}categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lists_content` FOREIGN KEY (`content_id`) REFERENCES `{prefix}content` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lists_language` FOREIGN KEY (`language`) REFERENCES `{prefix}languages` (`language_code`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lists_parent` FOREIGN KEY (`parent_id`) REFERENCES `{prefix}content` (`id`) ON DELETE SET NULL;

ALTER TABLE `{prefix}password_reset`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `{prefix}users` (`id`) ON DELETE CASCADE;

ALTER TABLE `{prefix}users`
  ADD CONSTRAINT `fk_group_id` FOREIGN KEY (`group_id`) REFERENCES `{prefix}groups` (`id`) ON DELETE SET NULL;

ALTER TABLE `{prefix}user_groups`
  ADD CONSTRAINT `fk_group_group_id` FOREIGN KEY (`group_id`) REFERENCES `{prefix}groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_group_user_id` FOREIGN KEY (`user_id`) REFERENCES `{prefix}users` (`id`) ON DELETE CASCADE;

ALTER TABLE `{prefix}videos`
  ADD CONSTRAINT `fk_video_category` FOREIGN KEY (`category_id`) REFERENCES `{prefix}categories` (`id`) ON DELETE SET NULL;
