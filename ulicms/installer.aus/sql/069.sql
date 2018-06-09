ALTER TABLE `{prefix}content` ADD INDEX(`systemname`);
ALTER TABLE `{prefix}content` ADD INDEX(`language`);
ALTER TABLE `{prefix}content` ADD INDEX(`menu`);
ALTER TABLE `{prefix}content` ADD INDEX(`parent`);
ALTER TABLE `{prefix}content` ADD INDEX(`active`);
ALTER TABLE `{prefix}content` ADD INDEX(`deleted_at`);
ALTER TABLE `{prefix}content` ADD INDEX(`hidden`);
ALTER TABLE `{prefix}content` ADD INDEX(`type`);