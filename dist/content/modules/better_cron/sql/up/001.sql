CREATE TABLE `{prefix}cronjobs` 
(`name` VARCHAR(200) NOT NULL , 
`last_run` BIGINT NOT NULL DEFAULT '0' , 
PRIMARY KEY (`name`)) ENGINE = InnoDB;