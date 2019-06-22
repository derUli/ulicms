ALTER TABLE `{prefix}content` ADD `posted2telegram` BOOLEAN NOT NULL DEFAULT FALSE;

update `{prefix}content` SET `posted2telegram` = 1 where posted2telegram = 0;