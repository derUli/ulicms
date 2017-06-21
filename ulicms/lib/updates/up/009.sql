ALTER TABLE `{prefix}group_languages`
  ADD CONSTRAINT `fk_group`
  FOREIGN KEY (`group_id`)
  REFERENCES `{prefix}groups`(id)
  ON DELETE CASCADE
