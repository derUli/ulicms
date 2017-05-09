ALTER TABLE `{prefix}password_reset`
  ADD CONSTRAINT fk_user_id
  FOREIGN KEY (user_id)
  REFERENCES `{prefix}users`(id)
  ON DELETE CASCADE;
