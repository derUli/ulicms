ALTER TABLE `{prefix}entity_permissions`
  ADD CONSTRAINT fk_entity_owner_user_id
  FOREIGN KEY (`owner_user_id`)
  REFERENCES `{prefix}users`(id)
  ON DELETE SET NULL;