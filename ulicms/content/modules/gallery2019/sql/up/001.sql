CREATE TABLE `{prefix}gallery` 
( `id` INT NOT NULL AUTO_INCREMENT , 
`title` VARCHAR(200) NOT NULL , 
`created` DATETIME NULL , 
`updated` DATETIME NULL , 
`createdby` INT NULL , 
`lastchangedby` INT NULL , PRIMARY KEY (`id`)) 
ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
