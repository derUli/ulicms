CREATE TABLE `{prefix}dbtrack` ( `id` INT NOT NULL AUTO_INCREMENT , `component` VARCHAR(150) NOT NULL , `name` VARCHAR(150) NOT NULL , `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`))  ENGINE=InnoDB DEFAULT charset=utf8mb4;
