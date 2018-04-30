CREATE TABLE `{prefix}entity_permissions` 
( `entity_name` VARCHAR(100) NOT NULL , 
`entity_id` INT NOT NULL , 
`owner_user_id` INT NOT NULL , 
`owner_group_id` INT NOT NULL , 
`only_admins_can_edit` BOOLEAN NOT NULL DEFAULT FALSE , 
`only_group_can_edit` BOOLEAN NOT NULL DEFAULT FALSE , 
`only_owner_can_edit` BOOLEAN NULL DEFAULT FALSE , 
`only_others_can_edit` BOOLEAN NOT NULL DEFAULT FALSE ) 
ENGINE = InnoDB;