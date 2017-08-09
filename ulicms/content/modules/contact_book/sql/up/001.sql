CREATE TABLE `{prefix}contact_book` 
( `id` INT NOT NULL AUTO_INCREMENT , 
`name` VARCHAR(200) NOT NULL , 
`firstname` VARCHAR(200) NULL , 
`phone` VARCHAR(30) NULL , 
`email` TEXT NULL , 
`public` BOOLEAN NOT NULL DEFAULT TRUE
ENGINE = MyISAM CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;