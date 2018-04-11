ALTER TABLE `{prefix}user_groups` 
ADD CONSTRAINT `fk_group_user_id` 
FOREIGN KEY (`user_id`)
REFERENCES `{prefix}users` (`id`)
on delete cascade
