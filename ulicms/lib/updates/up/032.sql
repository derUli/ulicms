ALTER TABLE `{prefix}user_groups` 
ADD CONSTRAINT `fk_group_group_id` 
FOREIGN KEY (`group_id`)
REFERENCES `{prefix}groups` (`id`)
on delete cascade
