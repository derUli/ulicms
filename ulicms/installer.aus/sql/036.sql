CREATE TABLE `{prefix}custom_fields` ( `id` INT NOT NULL AUTO_INCREMENT , `content_id` INT NOT NULL , `name` VARCHAR(100) NOT NULL , `value` TEXT NOT NULL , PRIMARY KEY (`id`) ) ENGINE = InnoDB;