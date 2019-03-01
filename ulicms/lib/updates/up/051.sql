ALTER TABLE `{prefix}comments` add COLUMN `read` tinyint(1) NOT NULL DEFAULT '0';

update `{prefix}comments` set `read` = 1;
