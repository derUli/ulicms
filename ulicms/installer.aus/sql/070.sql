CREATE TABLE `{prefix}user_groups` 
( 
`user_id` INT NOT NULL , 
`group_id` INT NOT NULL, 
FOREIGN KEY (`user_id`) REFERENCES {prefix}users(id) on delete cascade,
FOREIGN KEY (`group_id`) REFERENCES {prefix}groups(id) on delete cascade
) ENGINE = InnoDB;
