-- Indizes zur Datenbank von UliCMS hinzufügen um die Performance zu verbessern

ALTER TABLE `{prefix}content` ADD INDEX(`systemname`);
ALTER TABLE `{prefix}content` ADD INDEX(`language`);
ALTER TABLE `{prefix}content` ADD INDEX(`menu`);
ALTER TABLE `{prefix}content` ADD INDEX(`parent`);
ALTER TABLE `{prefix}content` ADD INDEX(`active`);
ALTER TABLE `{prefix}content` ADD INDEX(`deleted_at`);
ALTER TABLE `{prefix}content` ADD INDEX(`hidden`);
ALTER TABLE `{prefix}content` ADD INDEX(`type`);

-- Indexes für Blog-Modul
ALTER TABLE `{prefix}blog` ADD INDEX(`seo_shortname`);
ALTER TABLE `{prefix}blog` ADD INDEX(`language`);
ALTER TABLE `{prefix}blog` ADD INDEX(`entry_enabled`);
ALTER TABLE `{prefix}blog` ADD INDEX(`datum`);
ALTER TABLE `{prefix}blog` ADD INDEX(`datum`);
ALTER TABLE `{prefix}blog_comments` ADD INDEX(`post_id`);
ALTER TABLE `{prefix}blog_comments` ADD INDEX(`date`);