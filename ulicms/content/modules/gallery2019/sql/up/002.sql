CREATE TABLE `{prefix}gallery_images` 
( `id` INT NOT NULL AUTO_INCREMENT , 
`gallery_id` INT NULL , 
`path` VARCHAR(255) NOT NULL , 
`description` TEXT NULL , 
`order` INT NOT NULL DEFAULT '0' , 
PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8mb4 
COLLATE utf8mb4_general_ci;