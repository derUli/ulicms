ALTER TABLE `{prefix}user_settings`
  ADD CONSTRAINT fk_user_settings_user
  FOREIGN KEY (user_id) 
  REFERENCES `{prefix}users`(id)
  ON DELETE CASCADE;