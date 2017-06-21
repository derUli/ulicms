ALTER TABLE `{prefix}content`
  ADD CONSTRAINT fk_autor 
  FOREIGN KEY (autor) 
  REFERENCES `{prefix}users`(id)
  ON DELETE Set NULL;