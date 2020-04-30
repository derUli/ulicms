ALTER TABLE `{prefix}content` DROP COLUMN `og_type`;
delete from `{prefix}settings` where name = 'og_type';