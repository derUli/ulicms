ALTER TABLE `{prefix}entity_permissions`
  ADD CONSTRAINT fk_entity_owner_group_id
  FOREIGN KEY (`owner_group_id`)
  REFERENCES `{prefix}groups`(id)
  ON DELETE SET NULL