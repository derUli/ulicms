ALTER TABLE `{prefix}history`
  ADD CONSTRAINT fk_user
  FOREIGN KEY (user_id) 
  REFERENCES `{prefix}users`(id)
  ON DELETE Set NULL;