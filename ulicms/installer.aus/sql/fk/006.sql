ALTER TABLE `{prefix}language`
  ADD CONSTRAINT fk_autor 
  FOREIGN KEY (autor) 
  REFERENCES `{prefix}users`(id)
  ON DELETE Set NULL;