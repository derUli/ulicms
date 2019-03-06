ALTER TABLE `{prefix}forms` 
ADD `enabled` TINYINT NOT NULL DEFAULT '1' 
AFTER `name`;
