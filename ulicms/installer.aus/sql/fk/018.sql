ALTER TABLE `{prefix}users`
  ADD CONSTRAINT `fk_group_id`
  FOREIGN KEY (`group_id`) 
  REFERENCES `{prefix}groups`(id)
  ON DELETE SET NULL;