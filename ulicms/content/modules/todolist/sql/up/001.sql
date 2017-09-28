CREATE TABLE `{prefix}todolist_items` 
( `id` INT NOT NULL AUTO_INCREMENT , 
`title` VARCHAR(255) NOT NULL , 
`done` TINYINT NOT NULL DEFAULT '0' , 
`user_id` INT NOT NULL , PRIMARY KEY (`id`)) 
ENGINE = InnoDB;