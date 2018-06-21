ALTER TABLE `{prefix}content` DROP INDEX(`systemname`);
ALTER TABLE `{prefix}content` DROP INDEX(`language`);
ALTER TABLE `{prefix}content` DROP INDEX(`menu`);
ALTER TABLE `{prefix}content` DROP INDEX(`parent`);
ALTER TABLE `{prefix}content` DROP INDEX(`active`);
ALTER TABLE `{prefix}content` DROP INDEX(`deleted_at`);
ALTER TABLE `{prefix}content` DROP INDEX(`hidden`);
ALTER TABLE `{prefix}content` DROP INDEX(`type`);